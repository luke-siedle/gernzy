<?php

    use Lab19\Cart\Testing\TestCase;
    use Lab19\Cart\Module\Orders\Order;

    class TestOrderLoadMigrations extends TestCase
    {
        public function setUp(): void
        {
            parent::setUp();
            $this->withFactories(dirname(dirname(__DIR__)) . '/Module/Orders/factories');
            factory(Order::class, 10)->create();
        }

        public function testCanLoadMigrationsTest(): void
        {

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                {
                    orders(count:10) {
                        data {
                            id
                        }
                        paginatorInfo {
                            currentPage
                            lastPage
                        }
                    }
                }
            ');

            $response->assertDontSee('errors');

            $response->assertStatus(200);

            $response->assertJsonCount(10, 'data.orders.data');

            $response->assertJsonStructure([
                'data' => [
                    'orders' => [
                        'data' => [[ 'id' ]]
                    ]
                ]
            ]);
        }
    }
