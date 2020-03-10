<?php

namespace Tests\Feature;

use Gernzy\Server\Models\Cart;
use Gernzy\Server\Models\Product;
use Gernzy\Server\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class CartTotalTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        // create products
        $this->availableCount = 11;

        factory(Product::class, $this->availableCount)->create()->each(function ($product) {
            $product->status = 'IN_STOCK';
            $product->title = 'Coffee pod';
            $product->published = 1;
            $product->save();
        });

        factory(Product::class, $this->availableCount + 10)->create()->each(function ($product) {
            $product->status = 'OUT_OF_STOCK';
            $product->save();
        });
    }

    protected $addToCartMutation = '
            mutation {
                addToCart(input: {
                        items: [
                            { product_id: 1, quantity: 5 },
                            { product_id: 2, quantity: 1 }
                            { product_id: 3, quantity: 9 }
                            { product_id: 4, quantity: 2 }
                            { product_id: 5, quantity: 3 }
                            { product_id: 6, quantity: 3 }
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

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCartTotal()
    {
        $response = $this->graphQLWithSession($this->addToCartMutation);

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'addToCart' => [
                    'cart' => [
                        'items' =>
                        [
                            0 => ['product_id', 'quantity']
                        ]
                    ]
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();


        $this->assertNotEmpty($result['data']['addToCart']['cart']['items'][0]);
    }

    public function testAddToCart()
    {
        $response = $this->graphQLWithSession($this->addToCartMutation);

        $query = '{
            me {
                cart {
                    cart_total
                }
            }
        }';

        $response = $this->graphQLWithSession($query);

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'me' => [
                    'cart' => [
                        'cart_total'
                    ]
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();

        $total = $result['data']['me']['cart']['cart_total'];

        $this->assertNotEmpty($total);

        $this->assertGreaterThan(0, $total);

        $this->assertDatabaseHas('gernzy_carts', [
            'cart_total' => $total,
        ]);

        return $total;
    }

    public function testRemoveFromCart()
    {
        // Add to cart and return the total
        $total = $this->testAddToCart();

        // Now remove from cart
        $response = $this->graphQLWithSession($this->removeFromCartMutation);
        $response->assertDontSee('You are not authorized');
        $response->assertDontSee('errors');
        $result = $response->decodeResponseJson();

        $this->assertCount(5, $result['data']['removeFromCart']['cart']['items']);

        // Query cart total and check that it is not the same
        $query = '{
            me {
                cart {
                    cart_total
                }
            }
        }';

        $response = $this->graphQLWithSession($query);

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'me' => [
                    'cart' => [
                        'cart_total'
                    ]
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();

        $totalAfterRemove = $result['data']['me']['cart']['cart_total'];

        $this->assertNotEquals($total, $totalAfterRemove);

        // Check that the total has decreased after remove mutation was executed
        $this->assertLessThan($total, $totalAfterRemove);
    }

    public function testUpdateCartQuantity()
    {
        // Add to cart and return the total
        $total = $this->testAddToCart();

        // Now update cart
        $response = $this->graphQLWithSession($this->updateQuantityMutation);
        $response->assertDontSee('You are not authorized');
        $response->assertDontSee('errors');
        $result = $response->decodeResponseJson();

        $this->assertEquals(12, $result['data']['updateCartQuantity']['cart']['items'][0]['quantity']);

        // Query cart total and check that it is not the same
        $query = '{
            me {
                cart {
                    cart_total
                }
            }
        }';

        $response = $this->graphQLWithSession($query);

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'me' => [
                    'cart' => [
                        'cart_total'
                    ]
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();

        $totalAfterUpdate = $result['data']['me']['cart']['cart_total'];

        $this->assertNotEquals($total, $totalAfterUpdate);

        // More were added to the cart on update so the new total should be bigger than the older
        $this->assertGreaterThan($total, $totalAfterUpdate);
    }


    public function testCurrencyConvertOnCart()
    {

        // Add items to cart
        $response = $this->graphQLWithSession($this->addToCartMutation);

        // Setup currency
        $currencyQuery = '
        mutation {
            setSessionCurrency(input: {
                currency: "EUR"
            }){
                currency
            }
        }';

        $response = $this->graphQLWithSession($currencyQuery);


        // Query the total
        $query = '{
            me {
                cart {
                    cart_total
                }
            }
        }';

        $response = $this->graphQLWithSession($query);

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'me' => [
                    'cart' => [
                        'cart_total'
                    ]
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();

        $total = $result['data']['me']['cart']['cart_total'];

        $this->assertNotEmpty($total);

        $this->assertGreaterThan(0, $total);

        // Lookup cart total
        $cart = Cart::findOrFail(1);

        $this->assertNotEmpty($cart->cart_total);
        $this->assertNotEquals($cart->cart_total, $total);
    }
}
