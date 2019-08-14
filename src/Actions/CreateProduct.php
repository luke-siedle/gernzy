<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Product;
use Lab19\Cart\Models\ProductAttribute;

class CreateProduct
{
    public static function handle( $args ): Product
    {
        $product = new Product([
            'title' => $args['title'],
            'price_cents' => $args['price_cents'] ?? "",
            'price_currency' => $args['price_currency'] ?? "",
            'status' => 'IN_STOCK',
            'published' => 0
        ]);

        $product->save();

        $attributes = $args['attributes'] ?? [];
        $prices = $args['prices'] ?? [];

        $attributes = static::mergePricesWithAttributes(
            $prices,
            $attributes
        );

        $product->attributes()->createMany(
            $attributes
        );

        return $product;
    }

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
