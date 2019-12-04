<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Lab19\Cart\Factories\OpenExchangeRatesFactory;
use Lab19\Cart\Models\Product;
use Lab19\Cart\Services\CurrencyConversionInterface;
use Lab19\Cart\Testing\TestCase;

// just an example of how the controller in laravel may inject the CurrencyConverter dependency
class ExampleObjectOrController
{
    protected $currency;

    /**
     *  constructor.
     *
     * @param CurrencyConversionInterface $currency
     */
    public function __construct(CurrencyConversionInterface $currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get rate.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->currency->getRate();
    }
}

class CurrencyConversionTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->availableCount = 11;

        $this->productPricesArray = factory(Product::class, $this->availableCount)->create();

        $this->app->bind('GuzzleHttp\Client', function ($app) {
            $json = [
                "disclaimer" => "https://openexchangerates.org/terms/",
                "license" => "https://openexchangerates.org/license/",
                "timestamp" => "1449877801",
                "base" => "USD",
                "rates" => [
                    "AED" => "3.672538",
                    "AFN" => "66.809999",
                    "EUR" => "125.716501",
                ],
            ];

            $json = json_encode($json);

            $mock = new MockHandler([
                new Response(200, [], $json),
            ]);

            $handler = HandlerStack::create($mock);

            return new Client([
                'handler' => $handler,
                'base_uri' => 'https://openexchangerates.org/api/',
                'timeout' => 2.0,
            ]);
        });
    }

    // Helpers
    public function setupCurrencySession()
    {
        // Create a session
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQL('
            mutation {
                createSession {
                    token
                }
            }
        ');

        $start = $response->decodeResponseJson();

        $token = $start['data']['createSession']['token'];

        // Set the session currency
        $response = $this->postGraphQL(['query' => '
                mutation {
                    setSessionCurrency(input: {
                        currency: "EUR"
                    }){
                        currency
                    }
                }
            ',], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'setSessionCurrency' => [
                    'currency',
                ],
            ],
        ]);

        return $token;
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testContractImplementationThroughController()
    {
        // Construct and pass the type of implementation.
        // In a controller it should be type hinted automatically throught the laravel service container
        // as it is bound in the register method of the cart service provider
        // I'm using a factory pattern to create the object
        $currency = new ExampleObjectOrController(OpenExchangeRatesFactory::create('EUR', 'USD'));

        $this->assertTrue(null !== $currency->index());
        $this->assertTrue(!empty($currency->index()));
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testContractImplementation()
    {
        $currency = OpenExchangeRatesFactory::create('EUR', 'USD');

        $this->assertTrue(null !== $currency->convertCurrency(10) && !empty($currency->convertCurrency(10)));
    }

    public function testSetCartCurrencySession(): void
    {
        $this->withoutExceptionHandling();

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQL('
                mutation {
                    createSession {
                        token
                    }
                }
            ');

        $start = $response->decodeResponseJson();

        $token = $start['data']['createSession']['token'];

        $response = $this->postGraphQL(['query' => '
                mutation {
                    setSessionCurrency(input: {
                        currency: "EUR"
                    }){
                        currency
                    }
                }
            '], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'setSessionCurrency' => [
                    'currency',
                ],
            ],
        ]);
    }

    public function testGuestUserCanViewInStockProductsWithChosenCurrency(): void
    {
        $token = $this->setupCurrencySession();

        $query = '
                query {
                    products(first:10) {
                        data {
                            id
                            title
                            price_cents
                            price_currency
                        }
                        paginatorInfo {
                            currentPage
                            lastPage
                        }
                    }
                }
            ';

        $response = $this->postGraphQL(['query' => $query], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertTrue(!empty($result['data']['products']['data']));

        $response->assertJsonStructure([
            'data' => [
                'products' => [
                    'data' => [
                        ['id', 'title'],
                    ],
                ],
            ],
        ]);
    }

    public function testProductPricesAreConverted(): void
    {
        $originalPrices = $this->productPricesArray->map(function ($item, $key) {
            return ['id' => $item->id, 'title' => $item->title, 'price_cents' => $item->price_cents];
        });

        $token = $this->setupCurrencySession();

        $query = '
                query {
                    products(first:10) {
                        data {
                            id
                            title
                            price_cents
                            price_currency
                        }
                        paginatorInfo {
                            currentPage
                            lastPage
                        }
                    }
                }
            ';

        $response = $this->postGraphQL(['query' => $query], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertFalse(empty($result['data']['products']['data']));

        /**
         * Nested foreach loop, to loop through the response and get each product price after it has been converted to a different
         * currency. Then compare these values to the original price of the corresponding prouct, to see that the value is indeed
         * different.
         *
         * TODO: Manipulate collection of original prices using laravel collection methods
         * https://laravel.com/docs/6.x/collections
         *
         */
        $convertedPrices = $result['data']['products']['data'];

        foreach ($convertedPrices as $key => $convertedValue) {
            foreach ($originalPrices as $key => $originalValue) {
                if ($convertedValue['title'] == $originalValue['title']) {
                    // print "\n" . $convertedValue['title'] . ' == ' . $originalValue['title'] . ".\n";
                    // This shouldn't be the case, as the value should be converted to according to different currency rate
                    $this->assertFalse($convertedValue['price_cents'] == $originalValue['price_cents']);
                    break;
                }
            }
        }

        $response->assertJsonStructure([
            'data' => [
                'products' => [
                    'data' => [
                        ['id', 'title'],
                    ],
                ],
            ],
        ]);
    }

    public function testSetCurrencyFailsWhenNoCurrenciesAreEnabled(): void
    {
        $this->withoutExceptionHandling();

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQL('
                mutation {
                    createSession {
                        token
                    }
                }
            ');

        $start = $response->decodeResponseJson();

        $token = $start['data']['createSession']['token'];

        $response = $this->postGraphQL(['query' => '
                mutation {
                    setSessionCurrency(input: {
                        currency: "some random string"
                    }){
                        currency
                    }
                }
            '], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertEquals($result['errors'][0]['message'], 'Currency is invalid.');
    }
}
