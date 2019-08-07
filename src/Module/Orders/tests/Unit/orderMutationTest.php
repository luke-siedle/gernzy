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

        public function testSessionlessUserCannotCreateOrdersTest(): void
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

            $response->assertSee('errors');
        }
    }
