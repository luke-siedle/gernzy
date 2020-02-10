<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Product;
use Gernzy\Server\Models\Image;
use Gernzy\Server\Actions\Helpers\Attributes;

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
