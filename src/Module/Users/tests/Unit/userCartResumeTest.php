<?php

use Lab19\Cart\Testing\TestCase;

class TestResumeCart extends TestCase
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

    public function setUp(): void
    {
        parent::setUp();

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession($this->addToProductsMutation);
        $response = $this->graphQLWithSession($this->getCartQuery);
        $response->assertDontSee('You are not authorized');
        $response->assertDontSee('errors');
        $result = $response->decodeResponseJson();

        $this->assertEquals(5, $result['data']['me']['cart']['items'][0]['quantity'] );
        $this->graphQLCreateAccountWithSession('new@example.com', 'password');

        // Ensure that the user service request is refreshed
        // PHPUnit doesn't refresh this in a single test when session state changes
        // TODO: Need some advice on how to do this better
        $userService = \App::make('Lab19\UserService');
        $userService->request->session->refresh();
    }

    /**
     *
     * @group Cart
     */
    public function testUserCanLoginAndStillViewProductsAndQuantitiesInCart(): void
    {
        $response = $this->graphQLWithSession($this->getCartQueryAuthenticated);
        $result = $response->decodeResponseJson();

        $this->assertEquals(5, $result['data']['me']['cart']['items'][0]['quantity'] );

        $response->assertJsonStructure([
            'data' => [
                'me' => [
                    'cart' => [
                        'items' => [ ['product_id', 'quantity'] ]
                    ]
                ]
            ]
        ]);
    }

}

