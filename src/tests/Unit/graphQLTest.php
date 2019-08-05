<?php

    use Lab19\Cart\Testing\TestCase;
    use Lab19\Cart\Module\Users\User;

    class TestGraphQL extends TestCase
    {

        public function setUp(): void
        {
            parent::setUp();
            $this->withFactories(dirname(dirname(__DIR__)) . '/Module/Users/factories');
            factory(User::class, 10)->create();
        }

        public function testCanQueryGraphQLTest(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                {
                    users(count:10) {
                        data {
                            id
                            name
                        }
                        paginatorInfo {
                            currentPage
                            lastPage
                        }
                    }
                }
            ');

            $response->assertDontSee('errors');

            $response->assertJsonCount(10, 'data.users.data');

            $response->assertJsonStructure([
                'data' => [
                    'users' => [
                        'data' => [[ 'id', 'name' ]]
                    ]
                ]
            ]);
        }
    }
