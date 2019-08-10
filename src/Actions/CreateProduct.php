<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;

class CreateProduct
{
    public static function handle( $args ): Product
    {
        $product = new Product([
            'title' => $args['title'],
            'status' => 'IN_STOCK',
            'published' => 0
        ]);
        $product->save();
        return $product;
    }
}
