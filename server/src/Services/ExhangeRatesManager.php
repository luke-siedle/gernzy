<?php

namespace Gernzy\Server\Services;

use Illuminate\Support\Facades\Cache;

class ExhangeRatesManager
{
    protected $result; //products array
    protected $targetCurrency;
    protected $token;
    protected $currencyConverter;
    protected $cachedRate;
    protected $repository;

    public function __construct(CurrencyConversionInterface $currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    /*------------------Setters------------------*/
    /**
     * Set's the object result
     *
     * @param string
     */
    public function setPrices($result)
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
     * Set's the object targetCurrency
     *
     * @param string
     */
    public function setTargetCurrency($targetCurrency)
    {
        $this->targetCurrency = $targetCurrency;
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

        // This is a check to see if we're converting the cart total
        if (isset($result['cart']) && !empty($result['cart']->cart_total)) {
            return $this->convertPriceCartTotal($result);
        }

        // Need to ascertain if array of objects or just an object
        try {
            if (count($this->result) > 0) {
                return $this->convertMultiplePrices($result);
            }
        } catch (\Throwable $th) {
            return $this->convertSinglePrice($result);
        }
    }

    public function convertPriceCartTotal($result)
    {
        $defaultCurrency = config('currency.default_currency.iso_code');
        $cartTotal = $result['cart']->cart_total;

        $result['cart']->cart_total = $this->getApiRateAndConvertPrice($defaultCurrency, $cartTotal);
        return $result;
    }

    public function convertMultiplePrices($result)
    {
        // TODO: Probably a good scenario for a singleton object
        foreach ($result as $key => $value) {
            $productCurrency = $result[$key]['price_currency']; //This becomes the base to convert from
            $productPriceCents = $result[$key]['price_cents'];

            // If either values not set then bail
            if (!isset($productCurrency) || !isset($productPriceCents)) {
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

    public function convertSinglePrice($result)
    {
        $result = $this->convertMultiplePrices([$result]);
        return $result[0];
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
        $currencyConverter = $this->create($this->targetCurrency, $productCurrency); //($currencyCode, $productBaseCurrency);

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

    public function create($currency, $base)
    {
        $this->currencyConverter->setCurrency($currency);
        $this->currencyConverter->setBaseCurrency($base);
        $this->currencyConverter->makeApiRequest(); //This function does the api call
        $this->currencyConverter->setRate();
        return $this->currencyConverter;
    }
}
