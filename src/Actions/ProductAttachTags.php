<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\Tag;

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
