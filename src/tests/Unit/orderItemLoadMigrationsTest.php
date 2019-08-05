<?php

    use Lab19\Cart\Testing\TestCase;
    use Lab19\Cart\Module\Orders\OrderItem;

    class TestOrderItemLoadMigrations extends TestCase
    {
        public function setUp(): void
        {
            parent::setUp();
            $this->withFactories(dirname(dirname(__DIR__)) . '/Module/Orders/factories');
            factory(OrderItem::class, 10)->create();
        }

        public function testCanQueryOrderItemsTest(): void
        {

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                {
                    order_items(count:10) {
                        data {
                            id
                            order_id
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

            $response->assertJsonStructure([
                'data' => [
                    'order_items' => [
                        'data' => [[ 'id' ]]
                    ]
                ]
            ]);

            $response->assertJsonCount(10, 'data.order_items.data');
        }
    }
