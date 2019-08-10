<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;

class DeleteProduct
{
    public static function handle( Int $id ): bool
    {
        $product = Product::find( $id );
        if( $product->id ){
            return $product->delete();
        } else {
            return false;
        }
    }
}
