<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Product;
use Gernzy\Server\Actions\Helpers\Attributes;

class UpdateProduct
{
    public static function handle( Int $id, Array $args ): Product
    {
        $product = Product::findOrFail( $id );
        $product->fill( $args );
        $product->save();

        $categories = $args['categories'] ?? [];

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
