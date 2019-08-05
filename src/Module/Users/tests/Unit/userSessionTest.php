<?php
    use Lab19\Cart\Testing\TestCase;

    class TestUserSession extends TestCase
    {
        public function testUserCanInitiateAndResumeSession(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                mutation {
                    createSession {
                        token
                    }
                }
            ');

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [ 'createSession' => ['token'] ]
            ]);

            $start = $response->decodeResponseJson();

            $this->assertNotNull( $start['data']['createSession']['token'] );

            $response = $this->graphQL('
                mutation {
                    createSession {
                        token
                    }
                }
            ');

            $resume = $response->decodeResponseJson();

            $this->assertNotNull( $resume['data']['createSession']['token'] );

            $this->assertSame(
                $start['data']['createSession']['token'],
                $resume['data']['createSession']['token']
            );
        }

        public function testUserCanCreateAccount(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                mutation {
                    createAccount(input:{
                        email:"test@test.com", password: "tester", name: "Luke"
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

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [
                    'createAccount' => [
                        'token',
                        'user' => ['id']
                    ]
                ]
            ]);
        }

        public function testUserCannotCreateAccountWithBadEmail(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                mutation {
                    createAccount(input:{
                        email:"funkyemail@", password: "password", name: "Luke"
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

            $response->assertSee('The input.email must be a valid email address');

            $response->assertJsonStructure([
                'data' => [
                    'createAccount' => [ 'errors' ]
                ]
            ]);
        }
    }
