<?php

namespace Gernzy\Server\GraphQL\Builders;

use GraphQL\Type\Definition\ResolveInfo;
use Gernzy\Server\Models\Product;

class ProductsBuilder
{
    public function search($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        $query = Product::query();
        if (isset($args['input'])) {
            if (isset($args['input']['keyword'])) {
                $query = $query->searchByKeyword($args['input']['keyword']);
            }
            if (isset($args['input']['attributes'])) {
                $query = $query->searchByAttributes($args['input']['attributes']);
            }
        }
        return $query;
    }

    public function byCategory($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        if (isset($args['input'])) {
            if (isset($args['input']['ids'])) {
                $query = Product::byCategoryIds($args['input']['ids']);
            } elseif (isset($args['input']['titles'])) {
                $query = Product::byCategoryTitles($args['input']['titles']);
            }
            return $query;
        }
        return Product::query();
    }

    public function productsByTag($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        if (isset($args['tag'])) {
            $query = Product::searchByTag([$args['tag']]);
        } else {
            $query = Product::query();
        }
        return $query;
    }

    //TODO: Could merge productsByTag with productsByTags, as the only difference is parameter type. E.g array or int
    public function productsByTags($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        if (isset($args['tags'])) {
            $query = Product::searchByTag($args['tags']);
        } else {
            $query = Product::query();
        }
        return $query;
    }
}
