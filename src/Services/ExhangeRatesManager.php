<?php

namespace Lab19\Cart\Services;

use Illuminate\Support\Facades\Cache;

class ExhangeRatesManager
{
    protected $result; //products array
    protected $sessionCurrency;
    protected $token;
    protected $currencyConverter;
    protected $cachedRate;
    protected $repository;

    /*------------------Setters------------------*/
    /**
     * Set's the object result
     *
     * @param string
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Set's the object token
     *
     * @param string
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Set's the object sessionCurrency
     *
     * @param string
     */
    public function setSessionCurrency($sessionCurrency)
    {
        $this->sessionCurrency = $sessionCurrency;
        return $this;
    }

    /**
     * Set's the cached rate
     *
     * @param string
     */
    public function setCachedRate($cachedRate)
    {
        $this->cachedRate = $cachedRate;
        return $this;
    }

    /**
     * Set's the cached rate
     *
     * @param string
     */
    public function setRpository($repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Set's the converter object
     *
     * @param string
     */
    public function setConverter($currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;

        return $this;
    }

    /*------------------Methods------------------*/
    /**
     * Convert between currency
     *
     * @param int
     */
    public function convertPrices()
    {
        $result = $this->result;

        // TODO: Probably a good scenario for a singleton object
        foreach ($result as $key => $value) {
            $productCurrency = $result[$key]['price_currency']; //This becomes the base to convert from
            $productPriceCents = $result[$key]['price_cents'];

            if (!isset($productCurrency) && !isset($productPriceCents)) {
                continue;
            }

            if (isset($this->cachedRate)) {
                // Convert according to the cached rate, this does assume that each product in the
                // result array has the same currency
                $result[$key]['price_cents'] = $this->multiplyPriceByRate($this->cachedRate, $productPriceCents);
                continue;
            }

            // At this point there is no cache and a new api call will be made, this should only be hit once in the loop
            $result[$key]['price_cents'] = $this->getApiRateAndConvertPrice($productCurrency, $productPriceCents);
        }

        return $result;
    }

    /**
     * Convert between currency
     *
     * @param int
     */
    public function multiplyPriceByRate($rate, $amount)
    {
        return floor($amount * $rate);
    }

    /**
     * Use the conversion service and getApiRateAndConvertPrice
     *
     * @param int
     */
    public function getApiRateAndConvertPrice($productCurrency, $productPriceCents)
    {
        // At this point there is no cached rate, and all variables are set so new up a currency object and convert price.
        // note that this makes the api call, thus caching the result afterwards reduces api usage
        $currencyConverter = $this->currencyConverter::create($this->sessionCurrency, $productCurrency);

        // Set the cache with the rate for the user
        if (isset($this->token)) {
            $this->cachedRate = $currencyConverter->getRate();
            $this->saveToRepository($this->token, $this->cachedRate, 1800);
        }

        return $this->multiplyPriceByRate($currencyConverter->getRate(), $productPriceCents);
    }

    /**
     * Convert between currency
     *
     * @param int
     */
    public function saveToRepository($token, $rate, $time)
    {
        $this->repository::put($token, $rate, $time);
    }
}
