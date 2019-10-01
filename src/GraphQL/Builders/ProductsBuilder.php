<?php

namespace Lab19\Cart\GraphQL\Builders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use GraphQL\Type\Definition\ResolveInfo;
use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\Tag;

class ProductsBuilder
{
    public function search($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        if (isset($args['input'])) {
            $query = Product::searchByKeyword($args['input']['keyword']);
        } else {
            $query = Product::query();
        }
        return $query;
    }

    public function byCategory($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        if(isset($args['input'])){
            if( isset($args['input']['ids'])){
                $query = Product::byCategoryIds($args['input']['ids']);
            } else if(isset($args['input']['titles'])) {
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
