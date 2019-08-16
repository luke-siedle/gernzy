<?php
namespace Lab19\Cart\Actions\Managers;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\ProductAttribute;

abstract class ProductManager
{
    static function mergePricesWithAttributes( Array $prices, Array $attributes ): Array
    {
        foreach( $prices as $price ){
            $attributes[] = [
                'group' => 'prices',
                'key' => $price['currency'],
                'value' => $price['value']
            ];
        }
        return $attributes;
    }

    static function mergeSizesWithAttributes( Array $sizes, Array $attributes ): Array
    {
        foreach( $sizes as $size ){
            $attributes[] = [
                'group' => 'sizes',
                'key' => 'size',
                'value' => $size['size']
            ];
        }
        return $attributes;
    }

    static function mergeDimensionsWithAttributes( Array $dimensions, Array $attributes ): Array
    {
        foreach( $dimensions as $type => $value ){
            $attributes[] = [
                'group' => 'dimensions',
                'key' => $type,
                'value' => $value
            ];
        }
        return $attributes;
    }

    static function mergeWeightWithAttributes( Array $weight, Array $attributes ): Array
    {
        foreach( $weight as $type => $value ){
            $attributes[] = [
                'group' => 'weight',
                'key' => $type,
                'value' => $value
            ];
        }
        return $attributes;
    }
}
