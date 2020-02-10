<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Product;

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
