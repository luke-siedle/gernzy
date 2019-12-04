<?php

use Lab19\Cart\Models\Product;
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

        factory(Product::class, 5)->create()->each(function ($product) {
            $product->status = 'IN_STOCK';
            $product->title = 'Coffee pod';
            $product->published = 1;
            $product->save();
        });

        $response = $this->graphQLWithSession($this->addToProductsMutation);
        $this->graphQLCreateAccountWithSession('new@example.com', 'password');
    }

    /**
     *
     * @group Cart
     */
    public function testUserCanLoginAndStillViewProductsAndQuantitiesInCart(): void
    {
        $response = $this->graphQLWithSession($this->getCartQueryAuthenticated);
        $result = $response->decodeResponseJson();

        $this->assertEquals(5, $result['data']['me']['cart']['items'][0]['quantity']);

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
