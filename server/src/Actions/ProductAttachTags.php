<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Product;
use Gernzy\Server\Models\Tag;

class ProductAttachTags
{
    public static function handle(Int $productId, array $tags): Product
    {
        $product = Product::findOrFail($productId);
        $tags = Tag::findMany($tags);
        $product->tags()->saveMany($tags);
        return $product;
    }
}
