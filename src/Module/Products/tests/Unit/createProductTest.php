<?php
    use Lab19\Cart\Testing\TestCase;

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

        public function createProduct( $args ){
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
    }
