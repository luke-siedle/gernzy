<?php

use Gernzy\Server\Testing\TestCase;

/**
 * @group Orders
 */
class TestOrderViewTest extends TestCase
{
    protected $checkoutMutation = '
            mutation {
                checkout(input: {
                    name: "Luke",
                    email: "cart@example.com",
                    telephone: "",
                    mobile: "",
                    billing_address: {
                        line_1: "1 London Way",
                        line_2: "",
                        state: "London",
                        postcode: "SW1A 1AA",
                        country: "UK"
                    },
                    shipping_address: {
                        line_1: "1 London Way",
                        line_2: "",
                        state: "London",
                        postcode: "SW1A 1AA",
                        country: "UK"
                    },
                    use_shipping_for_billing: true,
                    payment_method: "",
                    agree_to_terms: true,
                    notes: ""
                }){
                    order {
                        id
                    }
                }
            }
        ';

    protected $addToCartMutation = '
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

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGuestCannotCheckoutWithoutSessionToken(): void
    {
        $response = $this->graphQL($this->checkoutMutation);
        $response->assertSee('You need a session token');
    }

    public function testUserCannotCheckoutWithoutItemsInCart(): void
    {
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession($this->checkoutMutation);
        $response->assertSee('Cannot checkout without items');
    }

    public function testUserCanCheckoutWithItemsInCart(): void
    {
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession($this->addToCartMutation);
        $response = $this->graphQLWithSession($this->checkoutMutation);
        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $response->assertJsonStructure([
            'data' => [
                'checkout' => [
                    'order' => [
                        'id',
                    ],
                ],
            ],
        ]);

        $this->assertIsNumeric($result['data']['checkout']['order']['id']);
    }

    public function testUserCartIsEmptyAfterCheckoutCompletes(): void
    {
        $this->testUserCanCheckoutWithItemsInCart();

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
                {
                    me {
                        cart {
                            id
                            items {
                                product_id
                                quantity
                            }
                        }
                    }
                }
            ');

        $result = $response->decodeResponseJson();

        $this->assertCount(0, $result['data']['me']['cart']['items']);
    }

    /**
     * @group Checkout1
     */
    public function testUserCanViewSingleOrderTheyCreated(): void
    {
        $this->graphQLWithSession($this->addToCartMutation);
        $this->graphQLWithSession($this->checkoutMutation);
        $this->graphQLCreateAccountWithSession('order@example.com', 'password');

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
                {
                    order(id:1){
                        name
                        cart {
                            items {
                                product_id
                                quantity
                            }
                        }
                    }
                }
            ');

        $result = $response->decodeResponseJson();

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'order' => [
                    'name',
                ],
            ],
        ]);
    }
}
