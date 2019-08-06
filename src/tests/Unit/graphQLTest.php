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
                  __type(name: "User") {
                    name
                  }
                }
            ');

            $response->assertDontSee('errors');

            $response->assertJsonStructure([
                'data' => [ '__type' => [ 'name' ] ]
            ]);
        }
    }
