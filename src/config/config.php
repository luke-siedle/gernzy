<?php

    return [

        /*
        |--------------------------------------------------------------------------
        | Payment providers
        |--------------------------------------------------------------------------
        |
        */
        'payment_providers' => [
            'EFT' => \Lab19\Cart\Packages\PaymentProviders\EFT::class
        ],

        /*
        |--------------------------------------------------------------------------
        | Tax
        |--------------------------------------------------------------------------
        |
        | Enable taxes and set configuration
        |
        */

        'tax' => [

            'enabled' => 1,

            // Prices are entered inclusive of VAT
            'inclusive' => 1,

            // Priorities for determining tax rates
            'determined_by' => [
                'billing_address',
                'shipping_address',
                'location',
            ]

        ],

        /*
        |--------------------------------------------------------------------------
        | Shipping
        |--------------------------------------------------------------------------
        |
        | Enable shipping, and set which countries are available to ship to
        | in case of exclusions. Bear in mind that shipping to certain regions
        | may require that you remit VAT/GST to that region
        |
        */

        'shipping' => [

            'enabled' => 1,

            // Ship to following countries
            'exclude_countries' => ['ZA'],

            // Exclude the following states
            'exclude_states' => ['US:CA'],

            // Uncomment this to include only certain countries
            // 'include_countries' => '*',

            // Uncomment this to include only certain states
            // 'include_states' => ['US:CA'],


        ]

    ];
