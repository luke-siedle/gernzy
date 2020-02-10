<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Product;
use Gernzy\Server\Models\ProductAttribute;
use Gernzy\Server\Actions\Helpers\Attributes;

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
