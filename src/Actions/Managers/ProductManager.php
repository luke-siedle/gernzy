<?php
namespace Lab19\Cart\Actions\Managers;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\ProductAttribute;

abstract class ProductManager
{
    static function mergePricesWithAttributes( $prices, $attributes ){
        foreach( $prices as $price ){
            $attributes[] = [
                'group' => 'prices',
                'key' => $price['currency'],
                'value' => $price['value']
            ];
        }
        return $attributes;
    }
}
