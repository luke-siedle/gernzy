<?php
    use Lab19\Cart\Testing\TestCase;

    class TestSession extends TestCase
    {
        public function testCanStoreSessionData(): void
        {
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
                    setSession(input: {
                        products: [{
                            id: 1,
                            quantity: 1
                        }]
                    }){
                        cart_uuid
                        products {
                            id
                        }
                    }
                }
            '], [
                'HTTP_Authorization' => 'Bearer ' . $token
            ]);

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [
                    'setSession' => [
                        'products', 'cart_uuid'
                    ]
                ]
            ]);
        }
    }
