<?php
use Gernzy\Server\Models\Product;
use Gernzy\Server\Testing\TestCase;

/**
 * @group Products
 */
class TestViewProducts extends TestCase
{
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

    public function testGuestUserCanViewInStockProducts(): void
    {
        $response = $this->graphQL('
                query {
                    products(first:100) {
                        data {
                            id
                            title
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertCount($this->availableCount, $result['data']['products']['data']);

        $response->assertJsonStructure([
            'data' => [
                'products' => [
                    'data' => [
                        ['id', 'title'],
                    ],
                ],
            ],
        ]);
    }

    public function testGuestUserCannotViewOutOfStockProducts(): void
    {
        $response = $this->graphQL('
                query {
                    products(first:100) {
                        data {
                            id
                            title
                            status
                            published
                        }
                        paginatorInfo {
                            total
                            hasMorePages
                            currentPage
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertCount($this->availableCount, $result['data']['products']['data']);

        $response->assertDontSee('"published":0');
        $response->assertDontSee('"status":"OUT_OF_STOCK"');

        $response->assertJsonStructure([
            'data' => [
                'products' => [
                    'data' => [
                        ['id', 'title', 'status', 'published'],
                    ],
                ],
            ],
        ]);
    }

    public function testGuestUserCanSearchProductsByKeywordAndPaginateThem(): void
    {
        $response = $this->graphQL('
                query {
                    products(first:7, page:2, input: {keyword : "pod"} ) {
                        data {
                            id
                            title
                        }
                        paginatorInfo {
                            currentPage
                            lastPage
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertCount(4, $result['data']['products']['data']);

        $response->assertJsonStructure([
            'data' => [
                'products' => [
                    'data' => [
                        ['id', 'title'],
                    ],
                ],
            ],
        ]);
    }
}
