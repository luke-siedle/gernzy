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

        public function testCanCreateProduct(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession('
                mutation {
                    createProduct(input:{
                        title:"Coffee Dripper"
                        }) {
                        id
                        title
                    }
                }
            ');

            $response->assertDontSee('errors');
        }
    }
