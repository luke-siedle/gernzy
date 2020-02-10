<?php

use Gernzy\Server\Models\Product;
use Gernzy\Server\Models\ProductFixedPrice;
use Gernzy\Server\Testing\TestCase;

/**
 * @group Products
 */
class TestCreateProductTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $response = $this->graphQL('
                mutation {
                    logIn(input:{
                        email:"admin@example.com",
                        password: "password"
                        }) {
                        token
                        user {
                            name
                            email
                            id
                        }
                    }
                }
            ');
        $result = $response->decodeResponseJson();

        // Set the global session token to use for the test
        $this->sessionToken = $result['data']['logIn']['token'];
    }

    public function createProduct($args)
    {
        return $this->graphQLWithSession('
                mutation {
                    createProduct(input:{
                        title:"' . $args['title'] . '"
                        }) {
                        id
                        title
                    }
                }
            ');
    }

    public function testAdminUserCanCreateProduct()
    {
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->createProduct(["title" => "Coffee dripper"]);

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'createProduct' => [
                    'id', 'title'
                ]
            ]
        ]);

        return $response->decodeResponseJson();
    }

    public function testAdminUserCanUpdateProduct(): void
    {
        $json = $this->createProduct(["title" => "Coffee dripper"])->decodeResponseJson();

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
                mutation {
                    updateProduct(id: "' . $json['data']['createProduct']['id'] . '", input:{
                        title:"Coffee Dripper x2"
                        }) {
                        id
                        title
                    }
                }
            ');

        $result = $response->decodeResponseJson();

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'updateProduct' => [
                    'id', 'title'
                ]
            ]
        ]);

        $response->assertDontSee('errors');

        $this->assertEquals($result['data']['updateProduct']['title'], 'Coffee Dripper x2');
    }

    public function testAdminUserCanDeleteProduct(): void
    {
        $json = $this->createProduct(["title" => "Coffee dripper"])->decodeResponseJson();

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
                mutation {
                    deleteProduct(id: "' . $json['data']['createProduct']['id'] . '") {
                        success
                    }
                }
            ');

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'deleteProduct' => [
                    'success'
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();

        $this->assertEquals($result['data']['deleteProduct']['success'], true);
    }

    public function testAdminUserCannotDeleteNonExistentProduct(): void
    {
        $response = $this->graphQLWithSession('
                mutation {
                    deleteProduct(id: 99) {
                        success
                    }
                }
            ');

        $response->assertSee('errors');
    }

    public function testAdminUserCanCreateProductWithFixedPrices()
    {
        // fixprices: ["EUR","AED","GBP","ZAR","AUD"]
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input:{
                        title:"Coffee dripper"
                        price_cents: 239
                        price_currency: "USD"
                        fixprices: [{currency: "EUR", price_cents: 2999 }, { currency: "AUD", price_cents: 2499 },{ currency: "AED"}]
                        }) {
                        id
                        title
                    }
                }
            ');

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'createProduct' => [
                    'id', 'title'
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();


        // Check from Product model side
        $product = Product::with('fixedPrices')->find(1);
        foreach ($product->fixedPrices as $fixedPrice) {
            $this->assertNotEmpty($fixedPrice);
        }

        // Check from ProductFixedPrice model side
        $productFixedPrice = ProductFixedPrice::find(1);
        $product = $productFixedPrice->product;
        $this->assertNotEmpty($product->title);

        // Check the database contains the info for the fixed prices
        $this->assertDatabaseHas('gernzy_product_prices', [
            'id' => $productFixedPrice->id,
            'product_id' => $product->id
        ]);
    }
}
