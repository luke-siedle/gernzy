<?php

return array(
    //Default Cache Driver
    'driver' => 'database',

    //File Cache Location
    'path' => storage_path() . '/cache',

    //Database Cache Connection
    'connection' => null,
    //Database Cache Table
    'table' => 'cart_cache',

    //Memcached Servers
    'memcached' => array(
        array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
    ),

    //Cache Key Prefix
    'prefix' => 'laravel',
);
