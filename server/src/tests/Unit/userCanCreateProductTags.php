<?php
    use Lab19\Cart\Testing\TestCase;

/**
     * @group Tags
     */
    class TestCreateTagTest extends TestCase
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

        public function createTag($args)
        {
            return $this->graphQLWithSession('
                mutation {
                    createTag(input:{
                        name:"' . $args['name'] . '"
                        }) {
                        id
                        name
                    }
                }
            ');
        }

        public function testAdminUserCanCreateTag()
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->createTag(["name" => "electronics"]);

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [
                    'createTag' => [
                        'id', 'name'
                    ]
                ]
            ]);

            return $response->decodeResponseJson();
        }

        public function testAdminUserCanUpdateTag(): void
        {
            $json = $this->createTag(["name" => "Coffee dripper"])->decodeResponseJson();

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession('
                mutation {
                    updateTag(id: "' . $json['data']['createTag']['id'] . '", input:{
                        name:"Hardware"
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
                    'updateTag' => [
                        'id', 'name'
                    ]
                ]
            ]);

            $response->assertDontSee('errors');

            $this->assertEquals($result['data']['updateTag']['name'], 'Hardware');
        }

        public function testAdminUserCanDeleteTag(): void
        {
            $json = $this->createTag(["name" => "Coffee dripper"])->decodeResponseJson();

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession('
                mutation {
                    deleteTag(id: "' . $json['data']['createTag']['id'] . '") {
                        success
                    }
                }
            ');

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [
                    'deleteTag' => [
                        'success'
                    ]
                ]
            ]);

            $result = $response->decodeResponseJson();

            $this->assertEquals($result['data']['deleteTag']['success'], true);
        }

        public function testAdminUserCannotDeleteNonExistentTag(): void
        {
            $response = $this->graphQLWithSession('
                mutation {
                    deleteTag(id: 999) {
                        success
                    }
                }
            ');

            $response->assertSee('errors');
        }
    }
