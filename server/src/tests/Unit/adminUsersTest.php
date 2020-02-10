<?php
    use Gernzy\Server\Testing\TestCase;

    /**
     * @group Users
     */
    class TestAdminCreateUsersTest extends TestCase
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

        public function createUser( $args ){
            return $this->graphQLWithSession('
                mutation {
                    createUser(input:{
                        name:"' . $args['name'] . '",
                        email: "' . $args['email'] . '"
                        password: "password"
                        }) {
                        id
                        name
                    }
                }
            ');
        }

        public function testAdminUserCanCreateUser()
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->createUser([
                "name" => "Luke Siedle",
                "email" => "luke@example.com"
            ]);

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [
                    'createUser' => [
                        'id', 'name'
                    ]
                ]
            ]);

            return $response->decodeResponseJson();
        }

        public function testAdminUserCanUpdateUser(): void
        {
            $json = $this->createUser([
                "name" => "Luke Siedle",
                "email" => "luke@example.com"
            ])->decodeResponseJson();

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession('
                mutation {
                    updateUser(id: "' . $json['data']['createUser']['id'] . '", input:{
                        name:"Luke Jonathan Siedle"
                        }) {
                        id
                        name
                    }
                }
            ');

            $result = $response->decodeResponseJson();

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [
                    'updateUser' => [
                        'id', 'name'
                    ]
                ]
            ]);

            $response->assertDontSee('errors');

            $this->assertEquals($result['data']['updateUser']['name'], 'Luke Jonathan Siedle');
        }

        public function testAdminUserCanDeleteUser(): void
        {
            $json = $this->createUser([
                "name" => "Luke Siedle",
                "email" => "luke@example.com"
            ])->decodeResponseJson();

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession('
                mutation {
                    deleteUser(id: "' . $json['data']['createUser']['id'] . '") {
                        success
                    }
                }
            ');

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [
                    'deleteUser' => [
                        'success'
                    ]
                ]
            ]);

            $result = $response->decodeResponseJson();

            $this->assertEquals($result['data']['deleteUser']['success'], true);
        }

        public function testAdminUserCannotDeleteNonExistentUser(): void
        {
            $response = $this->graphQLWithSession('
                mutation {
                    deleteUser(id: 99) {
                        success
                    }
                }
            ');

            $response->assertSee('errors');
        }

        public function testAdminUserCanAssignAdminPermissions(): Array
        {
            $json = $this->createUser([
                "name" => "Luke Siedle",
                "email" => "luke@example.com"
            ])->decodeResponseJson();

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession('
                mutation {
                    elevateUser(id: "' . $json['data']['createUser']['id'] . '"){
                        id
                        name
                        is_admin
                    }
                }
            ');

            $result = $response->decodeResponseJson();

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [
                    'elevateUser' => [
                        'id', 'name', 'is_admin'
                    ]
                ]
            ]);

            $response->assertDontSee('errors');

            $this->assertEquals($result['data']['elevateUser']['is_admin'], 1);

            return $json;
        }

        public function testAdminUserCanRevokeAdminPermissions(): void
        {
            $json = $this->testAdminUserCanAssignAdminPermissions();

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession('
                mutation {
                    demoteUser(id: "' . $json['data']['createUser']['id'] . '"){
                        id
                        name
                        is_admin
                    }
                }
            ');

            $result = $response->decodeResponseJson();

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [
                    'demoteUser' => [
                        'id', 'name', 'is_admin'
                    ]
                ]
            ]);

            $response->assertDontSee('errors');

            $this->assertEquals($result['data']['demoteUser']['is_admin'], 0);
        }
    }
