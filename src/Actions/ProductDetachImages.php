<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\Image;
use Lab19\Cart\Actions\Helpers\Attributes;

class ProductDetachImages
{
    public static function handle( Int $productId, Array $images, Boolean $delete = false ): Product
    {
        $product = Product::findOrFail($productId);
        $images = $product->images()->where('id', '=', $images );
        if( $delete ){
            $images->delete();
        } else {
            $images->detach();
        }
        return $product;
    }

}
