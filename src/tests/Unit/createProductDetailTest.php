<?php
    use Lab19\Cart\Testing\TestCase;

    /**
     * @group Products
     */
    class TestCreateProductDetailTest extends TestCase
    {

        protected $createProductDetailedMutation = '
            mutation {
                createProduct(input: {
                    title: "1x Cappuccino",
                    short_description: "A Cappuccino is a espresso-based coffee drink originating from Italy.",
                    long_description: "A Cappuccino is an espresso-based coffee drink that originated in Italy, and is traditionally prepared with steamed milk foam (microfoam). \n\nThis description can support newlines too!"
                }){
                    short_description
                    long_description
                }
            }
        ';


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

        public function testAdminUserCanCreateProductWithDetailedFields(): void
        {
            $response = $this->graphQLWithSession($this->createProductDetailedMutation);
            $response->assertDontSee('errors');
            $result = $response->decodeResponseJson();
            $this->assertStringStartsWith( "A Cappuccino", $result['data']['createProduct']['short_description']);
            $this->assertStringStartsWith( "A Cappuccino", $result['data']['createProduct']['long_description']);
        }

        public function testAdminUserCanUpdateProductWithDetailedFields(): void
        {
            $product = $this->createProduct()->decodeResponseJson();
            $id = $product['data']['createProduct']['id'];
            $response = $this->graphQLWithSession('
                mutation {
                    updateProduct(id: ' . $id . ', input: {
                        short_description: "A Cappuccino is a espresso-based coffee drink originating from Italy.",
                        long_description: "A Cappuccino is an espresso-based coffee drink that originated in Italy, and is traditionally prepared with steamed milk foam (microfoam). \n\nThis description can support newlines too!"
                    }){
                        short_description
                        long_description
                    }
                }
            ');

            $response->assertDontSee('errors');
            $result = $response->decodeResponseJson();
            $this->assertStringStartsWith( "A Cappuccino", $result['data']['updateProduct']['short_description']);
            $this->assertStringStartsWith( "A Cappuccino", $result['data']['updateProduct']['long_description']);
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
        public function testAdminUserCanCreateProductWithArbitraryAttributes(): void
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
        public function testAdminUserCanUpdateProductWithArbitraryAttributes(): void
        {
            $product = $this->createProduct()->decodeResponseJson();
            $id = $product['data']['createProduct']['id'];

            $mutation = '
                mutation {
                    updateProduct(id: ' . $id . ', input: {
                        attributes: [{
                            group: "beans",
                            key: "bean",
                            value: "Medium roast"
                        }]
                    }){
                        attributes {
                            group
                            key
                            value
                        }
                    }
                }
            ';

            // Run this twice, so we can be sure the attributes are removed
            $this->graphQLWithSession( $mutation );
            $response = $this->graphQLWithSession( $mutation );

            $response->assertDontSee('errors');
            $result = $response->decodeResponseJson();

            $this->assertCount(1, $result['data']['updateProduct']['attributes']);
            $this->assertEquals( $result['data']['updateProduct']['attributes'][0]['value'], "Medium roast" );
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

        /**
         * @group ProductAttributes
         */
        public function testAdminUserCanUpdateProductWithDetailedPricing(): void
        {
            $mutation = '
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        price_cents: 200,
                        price_currency: "EUR",
                        prices: [{
                            currency: "GBP",
                            value: 300
                        }]
                    }){
                        prices {
                            currency
                            value
                        }
                    }
                }
            ';

            // Run this twice, so we can be sure that price data isn't being merged
            $this->graphQLWithSession( $mutation );
            $response = $this->graphQLWithSession( $mutation );

            $response->assertDontSee('errors');
            $result = $response->decodeResponseJson();

            $this->assertCount( 1, $result['data']['createProduct']['prices'] );
            $this->assertEquals( $result['data']['createProduct']['prices'][0]['currency'], "GBP" );
        }

        /**
         * @group ProductVariants
         */
        public function testAdminUserCanCreateProductVariant(): void
        {
            $product = $this->createProduct()->decodeResponseJson();
            $id = $product['data']['createProduct']['id'];
            $response = $this->graphQLWithSession('
                mutation {
                    createProductVariant( id: ' . $id . ', input: {
                        title: "1x Cappuccino (Small)",
                        price_cents: 200,
                        price_currency: "EUR",
                        sizes: [{
                            size: "Small"
                        }]
                    }) {
                        id
                        parent_id
                        sizes {
                            size
                        }
                    }
                }
            ');

            $response->assertDontSee('errors');
            $response = $this->graphQLWithSession('
                {
                    product(id:' . $id . '){
                        variants {
                            id
                            sizes {
                                size
                            }
                        }
                    }
                }
            ');

            $result = $response->decodeResponseJson();
            $response->assertDontSee('errors');
            $this->assertEquals($result['data']['product']['variants'][0]['sizes'][0]['size'], "Small");
        }

        /**
         * @group ProductCategory
         */
        public function testAdminUserCanCreateCategoryOnProductWithOrWithoutCategoryExisting(): void
        {
            $product = $this->createProduct()->decodeResponseJson();
            $id = $product['data']['createProduct']['id'];
            $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        categories: [{
                            title: "Coffee"
                        }]
                    }){
                        categories {
                            id
                            title
                        }
                    }
                }
            ');

            $response->assertDontSee('errors');
            $response->assertJsonStructure([
                'data' => [
                    'createProduct' => [
                        'categories' => [['id', 'title']]
                    ]
                ]
            ]);

            $json = $response->decodeResponseJson();

            $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        categories: [{
                            id: ' . $json['data']['createProduct']['categories'][0]['id'] .  '
                        }]
                    }){
                        categories {
                            id
                            title
                        }
                    }
                }
            ');

            $response->assertDontSee('errors');
            $response->assertJsonStructure([
                'data' => [
                    'createProduct' => [
                        'categories' => [['id', 'title']]
                    ]
                ]
            ]);

        }

        /**
         * @group ProductDimensions
         */
        public function testAdminUserCanSetDimensionsAndWeightOfProduct(): void
        {
            $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input: {
                        title: "1x Cappuccino",
                        dimensions: {
                            length: 20,
                            width: 10,
                            height: 5,
                            unit: "cm"
                        },
                        weight: {
                            weight: 0.25,
                            unit: "kg"
                        }
                    }){
                        dimensions {
                            width
                            length
                            height
                        }
                        weight {
                            weight
                        }
                    }
                }
            ');

            $response->assertDontSee('errors');
            $response->assertJsonStructure([
                'data' => [
                    'createProduct' => [
                        'dimensions' => ['length', 'width', 'height'],
                        'weight' => ['weight']
                    ]
                ]
            ]);

            $result = $response->decodeResponseJson();

            $this->assertEquals( $result['data']['createProduct']['dimensions']['width'], 10 );

        }
    }
