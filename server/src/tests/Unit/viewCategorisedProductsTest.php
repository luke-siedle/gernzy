<?php
use Lab19\Cart\Models\Category;
use Lab19\Cart\Models\Product;
use Lab19\Cart\Testing\TestCase;

/**
 * @group ProductCategories
 */
class TestViewCategorisedProducts extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->availableCount = 11;
        $cat1 = new Category(['title' => 'Coffee']);
        $cat2 = new Category(['title' => 'Kitchen']);
        $cat1->save();
        $cat2->save();
        $this->cat1 = $cat1;
        $this->cat2 = $cat2;

        factory(Product::class, $this->availableCount)->create()->each(function ($product) use ($cat1, $cat2) {
            $product->status = 'IN_STOCK';
            $product->title = 'Coffee pod';
            $product->published = 1;
            $product->categories()->attach($cat1);
            $product->categories()->attach($cat2);
            $product->save();
        });
    }

    public function testGuestUserCanQueryProductsByCategoryTitles(): void
    {
        $response = $this->graphQL('
                query {
                    productsByCategories(first: 11, input: {titles: ["kitchen"] }) {
                        data {
                            id
                            categories {
                                id
                                title
                            }
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertCount($this->availableCount, $result['data']['productsByCategories']['data']);

        $response->assertJsonStructure([
            'data' => [
                'productsByCategories' => [
                    'data' => [
                        ['id', 'categories' => [['id', 'title']]],
                    ],
                ],
            ],
        ]);
    }

    public function testGuestUserCanQueryProductsByCategoryIds(): void
    {
        $response = $this->graphQL('
                query {
                    productsByCategories(first: 11, input: {ids: [' . $this->cat1->id . ', ' . $this->cat2->id . '] }) {
                        data {
                            id
                            categories {
                                id
                                title
                            }
                        }
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        $this->assertCount($this->availableCount, $result['data']['productsByCategories']['data']);

        $response->assertJsonStructure([
            'data' => [
                'productsByCategories' => [
                    'data' => [
                        ['id', 'categories' => [['id', 'title']]],
                    ],
                ],
            ],
        ]);
    }
}
