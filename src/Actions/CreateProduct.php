<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\ProductAttribute;
use Lab19\Cart\Actions\Helpers\Attributes;
use Lab19\Cart\Models\Category;

class CreateProduct
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

        $categories = $args['categories'] ?? [];

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

        $attributes = new Attributes();
        $attributes
            ->meta( $args['meta'] ?? [] )
            ->sizes( $args['sizes'] ?? [] )
            ->dimensions( $args['dimensions'] ?? [])
            ->weight( $args['weight'] ?? [] )
            ->prices( $args['prices'] ?? [] );

        $product->attributes()->createMany(
            $attributes->toArray()
        );

        return $product;
    }

}
