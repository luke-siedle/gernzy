<?php

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\ProductFixedPrice;
use Lab19\Cart\Testing\TestCase;

/**
 * @group Products
 */
class TestProductFixedPrices extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->availableCount = 5;

        factory(Product::class, $this->availableCount)->create();

        $product = Product::find(1);
        $product->fixedPrices()->saveMany([
            new ProductFixedPrice(['country_code' => 'EUR', 'price' => '100.99',]),
            new ProductFixedPrice(['country_code' => 'ZAR', 'price' => '140.99',]),
            new ProductFixedPrice(['country_code' => 'AED', 'price' => '155.99',])
        ]);
    }

    public function testSavingOneToManyFixedPricesEloquent(): void
    {
        $product = factory(Product::class)->create();

        $productFixedPrice = factory(ProductFixedPrice::class)->make();

        // Create the relationship
        $product->fixedPrices()->save($productFixedPrice);

        $this->assertDatabaseHas('cart_products', [
            'id' => $product->id,
        ]);

        $this->assertDatabaseHas('cart_product_prices', [
            'id' => $productFixedPrice->id,
            'product_id' => $product->id
        ]);
    }

    public function testRetrievingOneToManyFixedPricesList(): void
    {
        $product = Product::with('fixedPrices')->find(1);
        foreach ($product->fixedPrices as $fixedPrice) {
            $this->assertNotEmpty($fixedPrice);
        }
    }

    public function testRetrievingOneToManyProduct(): void
    {
        // Find fixed price and related product
        $productFixedPrice = ProductFixedPrice::find(1);
        $product = $productFixedPrice->product;
        $this->assertNotEmpty($product->title);
    }

    public function testViewProductFixedPricesGraphql(): void
    {
        $product = Product::find(1);
        $productFixedPrice = ProductFixedPrice::find(1);

        $response = $this->graphQL('
                query {
                    product(id: 1) {
                        fixedPrices {
                            id
                            price
                            country_code
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertCount(3, $result['data']['product']['fixedPrices']);

        $this->assertTrue($product->fixedPrices->contains('id', $productFixedPrice->id));

        $response->assertJsonStructure([
            'data' => [
                'product' => [
                    'fixedPrices' => [
                        ['id', 'price', 'country_code']
                    ]
                ]
            ]
        ]);
    }
}
