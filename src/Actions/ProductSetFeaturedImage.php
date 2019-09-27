<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\Image;
use Lab19\Cart\Actions\Helpers\Attributes;

class ProductSetFeaturedImage
{
    public static function handle( Int $productId, Int $imageId ): Product
    {
        $product = Product::findOrFail($productId);
        $image = Image::findOrFail( $imageId );
        $attributes = new Attributes($product);
        $attributes->featuredImage($image);
        $product->attributes()->createMany(
            $attributes->toArray()
        );
        return $product;
    }

}
