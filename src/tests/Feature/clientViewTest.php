<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Lab19\Cart\Models\Product;
use Lab19\Cart\Testing\TestCase;

class GernzyPasswordResetTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
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

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testShopPage()
    {
        $this->withoutMiddleware();

        $response = $this->get('/shop');
        $response->assertStatus(200);
        $response->assertSuccessful();

        $this->assertStringContainsString('div', $response->content());
    }

    public function testCartPage()
    {
        $this->withoutMiddleware();

        $response = $this->get('/cart');
        $response->assertStatus(200);
        $response->assertSuccessful();

        $this->assertStringContainsString('div', $response->content());
    }

    public function testCheckoutPage()
    {
        $this->withoutMiddleware();

        $response = $this->get('/checkout');
        $response->assertStatus(200);
        $response->assertSuccessful();

        $this->assertStringContainsString('div', $response->content());
    }
}
