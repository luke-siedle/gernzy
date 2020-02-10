<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Product;
use Gernzy\Server\Models\Image;
use Gernzy\Server\Actions\Helpers\Attributes;

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
