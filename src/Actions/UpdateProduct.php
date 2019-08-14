<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Actions\Managers\ProductManager;

class UpdateProduct extends ProductManager
{
    public static function handle( Int $id, Array $args ): Product
    {
        $product = Product::findOrFail( $id );
        $product->fill( $args );
        $product->save();

        $attributes = $args['attributes'] ?? [];
        $prices = $args['prices'] ?? [];

        // Update pricing
        $attributes = static::mergePricesWithAttributes(
            $prices,
            $attributes
        );

        // Clear existing attributes
        $product->attributes()->delete();

        // Create new attributes
        $product->attributes()->createMany(
            $attributes
        );

        return $product;
    }
}
