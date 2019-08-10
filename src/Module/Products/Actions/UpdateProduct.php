<?php

namespace Lab19\Cart\Module\Products\Actions;

use Lab19\Cart\Module\Products\Product;

class UpdateProduct
{
    public static function handle( Int $id, Array $args ): Product
    {
        $product = Product::findOrFail( $id );
        $product->fill( $args );
        $product->save();
        return $product;
    }
}
