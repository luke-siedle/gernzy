<?php
use Lab19\Cart\Testing\TestCase;

/**
 *
 * @group Account
 */
class TestUserAccount extends TestCase
{
    public function testUserCanCreateAccount(): void
    {
        $email = 'test@test.com';

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQL('
                mutation {
                    createAccount(input: {
                            email:"' . $email . '",
                            password: "tester", name: "Luke"
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
                    'user' => ['id'],
                ],
            ],
        ]);

        $data = $response->decodeResponseJson();
        $token = $data['data']['createAccount']['token'];

        $this->assertDatabaseHas('cart_sessions', [
            'token' => $token,
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
    }

    public function testUserCannotCreateAccountWithExistingEmail(): void
    {
        $query = '
                mutation {
                    createAccount(input:{
                        email:"test@test.com", password: "password", name: "Luke"
                        }) {
                        token
                        user {
                            name
                            email
                            id
                        }
                    }
                }
            ';

        // Run this twice so the user already exists
        $response = $this->graphQL($query);
        $response = $this->graphQL($query);

        $response->assertSee('The input.email has already been taken');
    }

    public function testUserCanLogInToAccount(): void
    {
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQL('
                mutation {
                    logIn(input:{
                            email:"user@example.com",
                            password: "password"
                        }) {
                        user { id }
                        token
                    }
                }
            ');

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'logIn' => [
                    'token',
                    'user' => ['id'],
                ],
            ],
        ]);
    }

    /**
     *
     * @group Login
     */
    public function testUserCanFailToLoginAndSeeError(): void
    {
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQL('
                mutation {
                    logIn(input:{
                            email:"user@example.com",
                            password: "wrong"
                        }) {
                        user { id }
                        token
                    }
                }
            ');

        $response->assertSee('Invalid credentials');
    }

    public function testAdminUserCanLogInToAccount(): void
    {
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQL('
                mutation {
                    logIn(input:{
                            email:"admin@example.com",
                            password: "password"
                        }) {
                        user { id }
                        token
                    }
                }
            ');

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'logIn' => [
                    'token',
                    'user' => ['id'],
                ],
            ],
        ]);
    }

    public function testNormalUserCanReadItself(): void
    {
        $normalUserLoginResponse = $this->graphQL('
                mutation {
                    logIn(input: {
                            email:"user@example.com",
                            password: "password"
                        }) {
                        user { id }
                        token
                    }
                }
            ');

        $data = $normalUserLoginResponse->decodeResponseJson();
        $token = $data['data']['logIn']['token'];
        $id = $data['data']['logIn']['user']['id'];

        $this->assertNotNull($token);

        $response = $this->postGraphQL(['query' => '
                {
                    user(id:' . $id . ') {
                        id
                        name
                        email
                    }
                }
            '], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertDontSee('You are not authorized');

        $response->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
    }

    public function testNormalUserCannotReadUsers(): void
    {
        $normalUserLoginResponse = $this->graphQL('
                mutation {
                    logIn(input: {
                            email:"user@example.com",
                            password: "password"
                        }) {
                        user { id }
                        token
                    }
                }
            ');

        $data = $normalUserLoginResponse->decodeResponseJson();
        $token = $data['data']['logIn']['token'];

        $this->assertNotNull($token);

        $response = $this->postGraphQL(['query' => '
                {
                    users(first:10) {
                        data {
                            id
                        }
                    }
                }
            '], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertSee('You are not authorized to access users');

        $response = $this->postGraphQL(['query' => '
                {
                    user(id:1) {
                        id
                        name
                        email
                    }
                }
            '], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertSee('You are not authorized to access user');
    }

    public function testGuestUserCannotReadUsers(): void
    {
        $token = 'invalid-token';

        $response = $this->postGraphQL(['query' => '
                {
                    users(first:10) {
                        data {
                            id
                        }
                    }
                }
            '], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertSee('You are not authorized to access users');

        $response = $this->postGraphQL(['query' => '
                {
                    user(id:1) {
                        id
                        name
                        email
                    }
                }
            '], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertSee('You are not authorized to access user');
    }

    public function testAdminUserCanReadUsers(): void
    {
        $adminUserLoginResponse = $this->graphQL('
                mutation {
                    logIn(input:{
                            email:"admin@example.com",
                            password: "password"
                        }) {
                        user { id }
                        token
                    }
                }
            ');

        $data = $adminUserLoginResponse->decodeResponseJson();
        $token = $data['data']['logIn']['token'];

        $this->assertNotNull($token);

        $response = $this->postGraphQL(['query' => '
                {
                    users(count:10) {
                        data {
                            id
                        }
                    }
                }
            '], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertDontSee('You are not authorized to access users');

        $response = $this->postGraphQL(['query' => '
                {
                    user(id:2) {
                        id
                        name
                        email
                    }
                }
            '], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertDontSee('You are not authorized to access users');
    }

}
