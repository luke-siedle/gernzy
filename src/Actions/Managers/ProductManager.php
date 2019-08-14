<?php
namespace Lab19\Cart\Actions\Managers;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\ProductAttribute;

abstract class ProductManager
{
    static function mergePricesWithAttributes( Array $prices, Array $attributes ){
        foreach( $prices as $price ){
            $attributes[] = [
                'group' => 'prices',
                'key' => $price['currency'],
                'value' => $price['value']
            ];
        }
        return $attributes;
    }

    static function mergeSizesWithAttributes( Array $sizes, Array $attributes ){
        foreach( $sizes as $size ){
            $attributes[] = [
                'group' => 'sizes',
                'key' => 'size',
                'value' => $size['size']
            ];
        }
        return $attributes;
    }
}
