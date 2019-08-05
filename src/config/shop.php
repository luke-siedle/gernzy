<?php

    return [

        'packages' => [
            /*
                Provides the order functionality and forms
                Allows creation of an order from backend
            */
            // 'orders' => [
            //     \Lab19\Cart\Provider\Orders::class
            // ],

            /*
                Allows for creation of products and catalogs
            */
            'products' => [
                \Lab19\Cart\Module\Products::class
            ],

            /*
                Allows for creation of shop configuration
            */
            // 'shop' => [
            //     \Lab19\Cart\Provider\ShopConfiguration::class
            // ],

            /*
                Stripe payments integration
            */
            // 'stripe' => [
            //     \Lab19\Cart\Provider\Stripe::class
            // ],

            /*
                Xero invoicing integration
            */
            // 'xero' => [
            //     \Lab19\Cart\Provider\Xero::class
            // ],
        ]

    ];
