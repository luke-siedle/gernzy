<?php
    use Gernzy\Server\Testing\TestCase;

    class TestUserAdminAccount extends TestCase
    {
        public function testCannotCreateAdminPrivileges(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                mutation {
                    createAccount(input:{
                        email:"test@test.com",
                        password: "tester",
                        name: "Luke",
                        is_admin: 1
                        }) {
                        token
                        user {
                            name
                            email
                            id
                            is_admin
                        }
                    }
                }
            ');

            $response->assertSee('errors');
        }

        public function testCanLoginAsAdminUser(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
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

            $response->assertDontSee('errors');
        }
    }
