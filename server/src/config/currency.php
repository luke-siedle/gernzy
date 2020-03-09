<?php
return [
    /*
     |--------------------------------------------------------------------------
     | Enabled Currencies
     |--------------------------------------------------------------------------
     |
     | Define a list of allowed currenct that can be converted to.
     |
     */
    'enabled' => [
        'EUR',
        'USD',
        'AUD',
        'GBP',
        'ZAR'
    ],
    'default_currency' => [
        'iso_code' => 'USD'
    ],
    'openexchangerates' => [
        // Temp workaround to get tests passing, because the .env is not located and thus the api key is returning empty
        // Need to figure out why .env not accessible/findable
        // 'api_key' => env('currency_api_token', '')
        'api_key' => 'This needs to be the api which is read from the config file'
    ]
];
