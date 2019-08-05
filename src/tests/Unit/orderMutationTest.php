<?php

    use Lab19\Cart\Testing\TestCase;
    use Lab19\Cart\Module\Orders\Order;

    class TestOrderMutation extends TestCase
    {
        public function setUp(): void
        {
            parent::setUp();
            //$this->withoutExceptionHandling();
        }

        public function testCanMutateOrdersTest(): void
        {

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL('
                mutation {
                    createOrder(input: {
                        cart: {
                            create: {
                                item_count: 0
                            }
                        }
                    }){
                        id
                        cart {
                            id
                            order_id
                        }
                    }
                }
            ');

            $response->assertDontSee('errors');

            $response->assertStatus(200);

            $response->assertJsonStructure([
                'data' => [
                    'createOrder' => [
                        'id',
                        'cart' => [
                            'id',
                            'order_id'
                        ]
                    ]
                ]
            ]);
        }

        // public function testCannotAssignProtectedValues(): void
        // {

        // }
    }
