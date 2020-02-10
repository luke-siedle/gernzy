<?php

namespace Gernzy\Server\Services;

interface CurrencyConversionInterface
{
    /**
     * Get's a conversion rate by it's currency
     *
     * @param string
     */
    public function getRate();

    /**
     * Set's a conversion rate by it's currency
     *
     * @param int
     */
    public function setRate();

    /**
     * Set's a currecy in the cart session object
     *
     * @param int
     */
    public function convertCurrency($amount);

    /**
     * Set's a currency in the object
     *
     * @param int
     */
    public function setCurrency($currency);

    /**
     * Set's the base currency in the object
     *
     * @param int
     */
    public function setBaseCurrency($baseCurrency);
}
