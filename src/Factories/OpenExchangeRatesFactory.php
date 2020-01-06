<?php

namespace Lab19\Cart\Factories;

use Lab19\Cart\Services\OpenExchangeRates;

class OpenExchangeRatesFactory
{
    public static function create($currency, $base)
    {
        $currencyObject = new OpenExchangeRates($currency, $base);
        $currencyObject->setCurrency($currency);
        $currencyObject->setBaseCurrency($base);
        $currencyObject->makeApiRequest(); //This function does the api call
        $currencyObject->setRate();
        return $currencyObject;
    }
}
