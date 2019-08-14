<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\ProductAttribute;

class CreateProduct
{
    public static function handle( $args ): Product
    {
        $product = new Product([
            'title' => $args['title'],
            'price_cents' => $args['price_cents'] ?? "",
            'price_currency' => $args['price_currency'] ?? "",
            'status' => 'IN_STOCK',
            'published' => 0
        ]);

        $product->save();

        // Set the attributes
        if(isset($args['attributes']) && count($args['attributes']) > 0){
            $product->attributes()->createMany(
                $args['attributes']
            );
        }

        return $product;
    }
}
