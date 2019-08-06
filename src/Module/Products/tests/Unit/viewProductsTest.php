<?php
    use Lab19\Cart\Testing\TestCase;
    use Lab19\Cart\Module\Products\Product;

    /**
     * @group Products
     */

    class TestViewProducts extends TestCase
    {

        public function setUp(): void
        {
            parent::setUp();
            $this->availableCount = 3;

            factory(Product::class, $this->availableCount)->create()->each( function( $product ){
                $product->status = 'IN_STOCK';
                $product->published = 1;
                $product->save();
            });

            factory(Product::class, $this->availableCount + 10 )->create()->each( function( $product ){
                $product->status = 'OUT_OF_STOCK';
                $product->save();
            });
        }

        public function testGuestUserCanViewInStockProducts(): void
        {
            $response = $this->graphQL('
                query {
                    products(count:100) {
                        data {
                            id
                            title
                        }
                    }
                }
            ');

            $response->assertDontSee('errors');

            $result = $response->decodeResponseJson();

            $this->assertCount($this->availableCount, $result['data']['products']['data'] );

            $response->assertJsonStructure([
                'data' => [
                    'products' => [
                        'data' => [
                            ['id', 'title'],
                        ]
                    ]
                ]
            ]);
        }

        public function testGuestUserCannotViewOutOfStockProducts(): void
        {
            $response = $this->graphQL('
                query {
                    products(count:100) {
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
                            ['id', 'title', 'status', 'published' ],
                        ]
                    ]
                ]
            ]);
        }
    }
