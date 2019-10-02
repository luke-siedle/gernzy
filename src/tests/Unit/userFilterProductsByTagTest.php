<?php

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\Tag;
use Lab19\Cart\Testing\TestCase;

/**
 * @group Products
 */
class TestFilterProducts extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->availableCount = 11;


        factory(Tag::class, $this->availableCount)->create()->each(function ($tag) {
            $tag->save();
        });


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


    public function testProductsByTag(): void
    {
        factory(Product::class, 4)->create()->each(function ($product) {
            $product->addTag(1);
            $product->save();
        });


        $response = $this->graphQL('
        query {
            productsByTag(count:10, page:1, tag: 1) {
                data {
                    id
                    title
                    short_description
                    status
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

        $this->assertCount(4, $result['data']['productsByTag']['data']);

        $response->assertJsonStructure([
            'data' => [
                'productsByTag' => [
                    'data' => [
                        ['id', 'title', 'short_description', 'status']
                    ]
                ]
            ]
        ]);
    }

    public function testProductsByMultipleTags(): void
    {
        factory(Product::class, 5)->create()->each(function ($product) {
            $product->addTag(rand(1, 5));
            $product->save();
        });


        $response = $this->graphQL('
        query {
            productsByTags(count:10, page:1, tags: [1,2,3,4,5]) {
                data {
                    id
                    title
                    short_description
                    status
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

        $this->assertCount(5, $result['data']['productsByTags']['data']);

        $response->assertJsonStructure([
            'data' => [
                'productsByTags' => [
                    'data' => [
                        ['id', 'title', 'short_description', 'status']
                    ]
                ]
            ]
        ]);
    }

    public function testQueryTagAndRetrieveProducts(): void
    {
        factory(Product::class, 100)->create()->each(function ($product) {
            $product->addTag(1);
            $product->save();
        });


        $response = $this->graphQL('
                query {
                    tag(id: 1) {
                        products(count: 10, page: 1) {
                            data {
                                id
                                title
                                short_description
                            }
                            paginatorInfo {
                                currentPage
                                lastPage
                            }
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertCount(10, $result['data']['tag']['products']['data']);

        $response->assertJsonStructure([
            'data' => [
                'tag' => [
                    'products' => [
                        'data' => [
                            ['id', 'title', 'short_description']
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testQueryProductAndRetrieveTags(): void
    {
        $product = Product::find(1);

        $tag1 = Tag::find(1);
        $tag2 = Tag::find(2);
        $tag3 = Tag::find(3);

        $product->addTag($tag1);
        $product->addTag($tag2);
        $product->addTag($tag3);

        $response = $this->graphQL('
                query {
                    product(id: 1) {
                        tags {
                            id
                            name
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertCount(3, $result['data']['product']['tags']);

        $this->assertTrue($product->tags->contains('id', $tag1->id));

        $response->assertJsonStructure([
            'data' => [
                'product' => [
                    'tags' => [
                        ['id', 'name']
                    ]
                ]
            ]
        ]);
    }

    public function testQueryTags(): void
    {
        $response = $this->graphQL('
                query {
                    tags {
                        id
                        name
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $response->assertJsonStructure([
            'data' => [
                'tags' => [
                    ['id', 'name']
                ]
            ]
        ]);
    }

    public function testQueryProductsEloguent(): void
    {
        factory(Product::class, 100)->create()->each(function ($product) {
            $product->addTag(1);
            $product->save();
        });

        $product = Product::find(1);
        $product->addTag(1);
        $product->addTag(2);
        $product->addTag(3);

        $tags = $product->tags;

        $this->assertNotEmpty($tags);
    }

    public function testQueryTagsEloguent(): void
    {
        factory(Product::class, 100)->create()->each(function ($product) {
            $product->addTag(1);
            $product->save();
        });

        $tag = Tag::find(1);

        $result = $tag->products;

        $this->assertNotEmpty($result);
    }
}
