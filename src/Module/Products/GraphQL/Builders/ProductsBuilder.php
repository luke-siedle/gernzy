<?php

namespace Lab19\Cart\Module\Products\GraphQL\Builders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use GraphQL\Type\Definition\ResolveInfo;
use Lab19\Cart\Module\Products\Product;

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
}
