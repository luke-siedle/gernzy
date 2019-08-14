<?php
    use Lab19\Cart\Testing\TestCase;

    /**
     * @group Products
     */
    class TestCreateProductDetailTest extends TestCase
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
                    }
                }
            ');
            $result = $response->decodeResponseJson();

            // Set the global session token to use for the test
            $this->sessionToken = $result['data']['logIn']['token'];
        }

        public function createProduct()
        {
            return $this->graphQLWithSession('
                mutation {
                    createProduct(input:{
                        title: "1x Cappuccino"
                        }) {
                        id
                    }
                }
            ');
        }

        public function testAdminUserCanCreateProductWithADefaultPrice(): void
        {
            $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        price_cents: 200,
                        price_currency: "EUR"
                    }){
                        id
                        price_cents
                        price_currency
                    }
                }
            ');

            $response->assertDontSee('errors');

            $result = $response->decodeResponseJson();

            $this->assertEquals( $result['data']['createProduct']['price_cents'], 200 );
            $this->assertEquals( $result['data']['createProduct']['price_currency'], "EUR" );
        }

        public function testAdminUserCanUpdateProductWithADefaultPrice(): void
        {
            $product = $this->createProduct()->decodeResponseJson();
            $response = $this->graphQLWithSession('
                mutation {
                    updateProduct(id: '. $product['data']['createProduct']['id'] . 'input: {
                        title: "1x Cappuccino",
                        price_cents: 200,
                        price_currency: "EUR"
                    }){
                        id
                        price_cents
                        price_currency
                    }
                }
            ');

            $response->assertDontSee('errors');

            $result = $response->decodeResponseJson();

            $this->assertEquals( $result['data']['updateProduct']['price_cents'], 200 );
            $this->assertEquals( $result['data']['updateProduct']['price_currency'], "EUR" );
        }

        /**
         * @group ProductAttributes
         */
        public function testAdminUserCanUpdateProductWithArbitraryAttributes(): void
        {
            $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        price_cents: 200,
                        price_currency: "EUR",
                        attributes: [{
                            group: "beans",
                            key: "bean",
                            value: "Light roast"
                        },{
                            group: "beans",
                            key: "bean",
                            value: "Medium roast"
                        },{
                            group: "beans",
                            key: "bean",
                            value: "Dark roast"
                        }]
                    }){
                        id
                        price_cents
                        price_currency
                        attributes {
                            group
                            key
                            value
                        }
                    }
                }
            ');

            $response->assertDontSee('errors');
            $result = $response->decodeResponseJson();

            $this->assertEquals( $result['data']['createProduct']['attributes'][0]['value'], "Light roast" );
        }

        /**
         * @group ProductAttributes
         */
        public function testAdminUserCanCreateProductWithDetailedPricing(): void
        {
            $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        price_cents: 200,
                        price_currency: "EUR",
                        prices: [{
                            currency: "GBP",
                            value: 300
                        },{
                            currency: "USD",
                            value: 250
                        }]
                    }){
                        prices {
                            currency
                            value
                        }
                    }
                }
            ');

            $response->assertDontSee('errors');
            $result = $response->decodeResponseJson();

            $this->assertCount( 2, $result['data']['createProduct']['prices'] );
            $this->assertEquals( $result['data']['createProduct']['prices'][0]['currency'], "GBP" );
        }

        /*
        public function testAdminUserCanCreateProductVariant(): void
        {
            $response = $this->graphQLWithSession('
                mutation {
                    createProductVariant(id: 1, input: {
                        title: "1x Cappuccino (Small)",
                        price: "2.00",
                        attributes: [{
                            group: "sizes",
                            key: "sizes",
                            value: "Small"
                        }]
                    }) {
                        id
                        title
                    }
                }
            ');

            $response->assertDontSee('errors');
        }
        */
    }
