<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\ProductAttribute;
use Lab19\Cart\Actions\Helpers\Attributes;

class CreateProductVariant
{
    public static function handle( Int $id, Array $args ): Product
    {
        $parent = Product::findOrFail( $id );

        $product = new Product( $args );
        $product->status = 'IN_STOCK';
        $product->parent_id = $id;
        $product->save();

        $attributes = new Attributes( $product );
        $attributes
            ->meta( $args['meta'] ?? [] )
            ->sizes( $args['sizes'] ?? [] )
            ->dimensions( $args['dimensions'] ?? [])
            ->weight( $args['weight'] ?? [] )
            ->prices( $args['prices'] ?? [] );

        // Update the attributes
        $product->attributes()->createMany(
            $attributes->toArray()
        );

        return $product;
    }
}
