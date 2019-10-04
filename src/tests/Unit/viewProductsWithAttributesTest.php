<?php
    use Lab19\Cart\Actions\Helpers\Attributes;
use Lab19\Cart\Models\Product;
use Lab19\Cart\Testing\TestCase;

/**
     * @group ProductAttributes
     */
    class TestViewProductsWithAttributes extends TestCase
    {
        public function setUp(): void
        {
            parent::setUp();
            factory(Product::class, 6)->create()->each(function ($product) {
                $attributes = new Attributes($product);
                $attributes->sizes([
                    [ 'size' => 'Small' ]
                ])->meta([
                    [
                        'key' => 'milk',
                        'value' => 'No Milk'
                    ],
                ]);

                $product->title = 'Coffee pod';
                $product->status = 'IN_STOCK';
                $product->published = 1;
                $product->save();
                $product->attributes()->createMany(
                    $attributes->toArray()
                );
            });
        }

        public function testGuestUserCanSearchProductsByAttributesAndPaginateThem(): void
        {
            $response = $this->graphQL('
                query {
                    products(count:7, input: {
                        attributes: [{
                            name: "size"
                            value: "Small"
                        },{
                            name: "milk"
                            value: "With Milk"
                        }]
                    }) {
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

            $this->assertCount(6, $result['data']['products']['data']);

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

        public function testGuestUserCanSearchProductsByNonExistentAttributesAndGetZeroResults(): void
        {
            $response = $this->graphQL('
                query {
                    products(count:7, input: {
                        attributes: [{
                            name: "size"
                            value: "Medium"
                        },{
                            name: "milk"
                            value: "With Milk"
                        }]
                    }) {
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

            $this->assertCount(0, $result['data']['products']['data']);

            $response->assertJsonStructure([
                'data' => [
                    'products' => [
                        'data' => []
                    ]
                ]
            ]);
        }
    }
