<?php

    return [

        'packages' => [
            /*
                Provides the order functionality and forms
                Allows creation of an order from backend
            */
            // 'orders' => [
            //     \Gernzy\Server\Provider\Orders::class
            // ],

            /*
                Allows for creation of products and catalogs
            */
            'products' => [
                \Gernzy\Server\Module\Products::class
            ],

            /*
                Allows for creation of shop configuration
            */
            // 'shop' => [
            //     \Gernzy\Server\Provider\ShopConfiguration::class
            // ],

            /*
                Stripe payments integration
            */
            // 'stripe' => [
            //     \Gernzy\Server\Provider\Stripe::class
            // ],

            /*
                Xero invoicing integration
            */
            // 'xero' => [
            //     \Gernzy\Server\Provider\Xero::class
            // ],
        ]

    ];
