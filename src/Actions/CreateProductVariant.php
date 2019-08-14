<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\ProductAttribute;
use Lab19\Cart\Actions\Managers\ProductManager;

class CreateProductVariant extends ProductManager
{
    public static function handle( Int $id, Array $args ): Product
    {
        $parent = Product::findOrFail( $id );

        $product = new Product( $args );
        $product->status = 'IN_STOCK';
        $product->parent_id = $id;
        $product->save();

        $attributes = $args['attributes'] ?? [];
        $prices = $args['prices'] ?? [];
        $sizes = $args['sizes'] ?? [];

        $attributes = static::mergePricesWithAttributes(
            $prices,
            $attributes
        );

        $attributes = static::mergeSizesWithAttributes(
            $sizes,
            $attributes
        );

        $product->attributes()->createMany(
            $attributes
        );

        return $product;
    }
}
