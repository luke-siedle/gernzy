<?php
    use Gernzy\Server\Testing\TestCase;

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

        /**
         *
         * @group Session
         */
        public function testCanDeleteExistingSession(): void
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

            $response = $this->postGraphQL(['query' => '
                mutation {
                    logOut {
                        success
                    }
                }
            '], [
                'HTTP_Authorization' => 'Bearer ' . $token
            ]);

            $response->assertDontSee('errors');

            $logOut = $response->decodeResponseJson();

            $this->assertTrue($logOut['data']['logOut']['success']);

            $response->assertJsonStructure([
                'data' => [
                    'logOut' => [ 'success' ]
                ]
            ]);
        }

        /**
         *
         * @group Session
         */
        public function testCannotDeleteInvalidSession(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                mutation {
                    createSession {
                        token
                    }
                }
            ');

            $response = $this->postGraphQL(['query' => '
                mutation {
                    logOut {
                        success
                    }
                }
            '], [
                'HTTP_Authorization' => 'Bearer ' . 'invalidToken'
            ]);

            $response->assertDontSee('errors');

            $logOut = $response->decodeResponseJson();

            $this->assertFalse($logOut['data']['logOut']['success']);
        }

        /**
         *
         * @group Session1
         */
        public function testCanMergeSessionToUser(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                mutation {
                    createSession {
                        token
                    }
                }
            ');

            $result = $response->decodeResponseJson();

            $token = $result['data']['createSession']['token'];

            $response = $this->graphQLCreateAccountWithSession('merge@example.com', 'password', $token );

            $result = $response->decodeResponseJson();

            $this->assertEquals( $result['data']['createAccount']['token'], $token );
        }
    }
