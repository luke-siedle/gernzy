<?php

namespace Lab19\Cart\GraphQL\Builders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use GraphQL\Type\Definition\ResolveInfo;
use Lab19\Cart\Models\Product;

class ProductsBuilder
{
    public function search($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        if(isset($args['input'])){
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
        } else {
            $query = Product::query();
        }

        return $query;
    }
}
