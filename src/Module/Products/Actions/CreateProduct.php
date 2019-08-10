<?php

namespace Lab19\Cart\Module\Products\Actions;

use Lab19\Cart\Module\Products\Product;

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
