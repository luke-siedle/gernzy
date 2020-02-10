<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Product;
use Gernzy\Server\Models\Image;
use Gernzy\Server\Actions\Helpers\Attributes;

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
