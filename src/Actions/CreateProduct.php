<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\ProductAttribute;
use Lab19\Cart\Actions\Managers\ProductManager;
use Lab19\Cart\Models\Category;

class CreateProduct extends ProductManager
{
    public static function handle( $args ): Product
    {
        $product = new Product([
            'title' => $args['title'],
            'price_cents' => $args['price_cents'] ?? "",
            'price_currency' => $args['price_currency'] ?? "",
            'short_description' => $args['short_description'] ?? "",
            'long_description' => $args['long_description'] ?? "",
            'status' => 'IN_STOCK',
            'published' => 0
        ]);

        $product->save();

        $attributes = $args['attributes'] ?? [];
        $prices = $args['prices'] ?? [];
        $sizes = $args['sizes'] ?? [];
        $categories = $args['categories'] ?? [];
        $dimensions = $args['dimensions'] ?? [];
        $weight = $args['weight'] ?? [];

        $createCategories = [];
        foreach( $categories as $category ){
            if( isset($category['id']) ){
                $cat = Category::find($category['id']);
                if( $cat ){
                    $product->categories()->attach( $cat );
                }
            } else if(isset($category['title'])) {
                $createCategories[] = [
                    'title' => $category['title']
                ];
            }
        }

        $product->categories()->createMany($createCategories);

        $attributes = static::mergePricesWithAttributes(
            $prices,
            $attributes
        );

        $attributes = static::mergeSizesWithAttributes(
            $sizes,
            $attributes
        );

        $attributes = static::mergeDimensionsWithAttributes(
            $dimensions,
            $attributes
        );

        $attributes = static::mergeWeightWithAttributes(
            $weight,
            $attributes
        );

        $product->attributes()->createMany(
            $attributes
        );

        return $product;
    }

}
