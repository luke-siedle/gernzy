<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\Image;
use Lab19\Cart\Actions\Helpers\Attributes;

class ProductAttachImages
{
    public static function handle( Int $productId, Array $images ): Product
    {
        $product = Product::findOrFail($productId);
        $images = Image::findMany( $images );
        $product->images()->saveMany( $images );
        return $product;
    }

}
