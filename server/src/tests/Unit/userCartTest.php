<?php
    use Gernzy\Server\Testing\TestCase;

    class TestCart extends TestCase
    {

        protected $addToProductsMutation = '
            mutation {
                addToCart(input: {
                        items: [
                            { product_id: 1, quantity: 5 },
                            { product_id: 2, quantity: 4 }
                        ]
                    }) {
                    cart {
                        items {
                            product_id
                            quantity
                        }
                    }
                }
            }
        ';

        protected $removeFromCartMutation = '
            mutation {
                removeFromCart(input: {
                    product_id: 1,
                    quantity: 1
                    }) {
                    cart {
                        items {
                            product_id
                            quantity
                        }
                    }
                }
            }
        ';

        protected $updateQuantityMutation = '
            mutation {
                updateCartQuantity(input: {
                    product_id: 1,
                    quantity: 12
                    }) {
                    cart {
                        items {
                            product_id
                            quantity
                        }
                    }
                }
            }
        ';

        protected $getCartQuery = '
            {
                me {
                    cart {
                        items {
                            product_id
                            quantity
                        }
                    }
                }
            }
        ';

        protected $getCartQueryAuthenticated = '
            {
                me {
                    id
                    cart {
                        items {
                            product_id
                            quantity
                        }
                    }
                }
            }
        ';

        /**
         * @group Cart
         */
        public function testSessionlessUserCannotAddProductsToCart(): void
        {
            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQL($this->addToProductsMutation);
            $response->assertSee('You need a session token');
        }

        /**
         * @group Cart
         */
        public function testGuestUserCanAddProductsToCart(): void
        {

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession($this->addToProductsMutation);
            $response->assertDontSee('You are not authorized');
            $response->assertDontSee('errors');
            $result = $response->decodeResponseJson();

            $response->assertJsonStructure([
                'data' => [
                    'addToCart' => [
                        'cart' => [
                            'items' => [ ['product_id', 'quantity'] ]
                        ]
                    ]
                ]
            ]);
        }

        /**
         * @group Cart
         */
        public function testGuestUserCanRemoveProductsFromCart(): void
        {

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession($this->addToProductsMutation);
            $response = $this->graphQLWithSession($this->removeFromCartMutation);
            $response->assertDontSee('You are not authorized');
            $response->assertDontSee('errors');
            $result = $response->decodeResponseJson();

            $this->assertCount(1, $result['data']['removeFromCart']['cart']['items'] );
        }

        /**
         * @group Cart
         */
        public function testGuestUserCanUpdateQuantityOfProductsInCart(): void
        {

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession($this->addToProductsMutation);
            $response = $this->graphQLWithSession($this->updateQuantityMutation);
            $response->assertDontSee('You are not authorized');
            $response->assertDontSee('errors');
            $result = $response->decodeResponseJson();

            $this->assertEquals(12, $result['data']['updateCartQuantity']['cart']['items'][0]['quantity'] );
        }

        /**
         *
         * @group Cart
         */
        public function testGuestUserCanViewProductsAndQuantitiesInCart(): void
        {

            /** @var \Illuminate\Foundation\Testing\TestResponse $response */
            $response = $this->graphQLWithSession($this->addToProductsMutation);
            $response = $this->graphQLWithSession($this->getCartQuery);
            $response->assertDontSee('You are not authorized');
            $response->assertDontSee('errors');
            $result = $response->decodeResponseJson();

            $this->assertEquals(5, $result['data']['me']['cart']['items'][0]['quantity'] );
        }

    }
