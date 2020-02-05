<?php

namespace Lab19\Cart\Testing;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Lab19\Cart\Testing\Seeds\UsersSeeder;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;
    use DatabaseMigrations;

    protected $sessionToken = null;

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app->useEnvironmentPath(__DIR__ . '/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);
    }

    protected function getPackageProviders($app)
    {
        // Setup required packages
        return [
            'Lab19\\Cart\\CartServiceProvider',
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->withFactories(dirname(__DIR__) . '/database/factories');
        $this->seed(UsersSeeder::class);
        $this->withoutExceptionHandling();


        // Mocking the api request to openexchange rates through the guzzle mock handler
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
                    "GBP" => "130.716501",
                    "ZAR" => "12.716501",
                    "AUD" => "15.716501",
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

    public function graphQLWithSession(String $query)
    {
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        if (!$this->sessionToken) {
            $response = $this->graphQL('
                    mutation {
                        createSession {
                            token
                        }
                    }
                ');

            $result = $response->decodeResponseJson();
            $this->sessionToken = $result['data']['createSession']['token'];
        }

        return $this->postGraphQL(['query' => $query], [
            'HTTP_Authorization' => 'Bearer ' . $this->sessionToken,
        ]);
    }

    public function graphQLCreateAccountWithSession($email = 'test@test.com', $password = 'password', $token = null)
    {

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->postGraphQL(['query' => '
                mutation {
                    createAccount(input: {
                        email:"' . $email . '",
                        password: "' . $password . '",
                        name: "Luke"
                        }) {
                        token
                        user {
                            name
                            email
                            id
                        }
                    }
                }
            ',], [
            'HTTP_Authorization' => 'Bearer ' . ($token ?? $this->sessionToken),
        ]);

        return $response;
    }

    /**
     * Send a multipart form request to GraphQL.
     *
     * This is used for file uploads conforming to the specification:
     * https://github.com/jaydenseric/graphql-multipart-request-spec
     *
     * @param  mixed[]  $parameters
     * @param  mixed[]  $files
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function multipartGraphQLWithSession(array $parameters, array $files): TestResponse
    {
        return $this->call(
            'POST',
            $this->graphQLEndpointUrl(),
            $parameters,
            [],
            $files,
            $this->transformHeadersToServerVars([
                'Content-Type' => 'multipart/form-data',
                'Authorization' => 'Bearer ' . $this->sessionToken,
            ])
        );
    }
}
