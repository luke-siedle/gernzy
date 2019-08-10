<?php

namespace Lab19\Cart\Module\Products\Actions;

use Lab19\Cart\Module\Products\Product;

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
