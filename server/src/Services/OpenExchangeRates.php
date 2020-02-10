<?php

namespace Gernzy\Server\Services;

use Gernzy\Server\Exceptions\GernzyException;

class OpenExchangeRates implements CurrencyConversionInterface
{
    protected $currency;
    protected $rate;
    protected $baseCurrency;
    protected $timestamp;
    protected $api_response;
    protected const API_BASE_PATH = "https://openexchangerates.org/api/";

    /*------------------Setters------------------*/
    /**
     * Set's the object currency
     *
     * @param string
     */
    public function setCurrency($currency = '')
    {
        $this->currency = $currency;
    }

    /**
     * This function makes the api request to open exchange api. It then sets the response to
     * the api_response object property. It reads the api token from the env file.
     *
     * @param string
     */
    public function makeApiRequest()
    {
        // open exhange api token is set in a .env file so that is doesn't live in the code base
        $token = env('currency_api_token', '');
        $endpoint = "latest.json?app_id=" . $token . "&base=" . $this->baseCurrency;

        // Make sure the token and base currency is available
        if (!isset($token) && !isset($this->baseCurrency)) {
            throw new GernzyException(
                'An exception occured.',
                'API Token or baseCurrency were not set.'
            );
        }

        // Resolve the guzzle instance out of the container
        $client = resolve('GuzzleHttp\Client');

        $response = $client->request('GET', $endpoint);
        $response = json_decode($response->getBody());
        $this->api_response = $response;
    }

    /**
     * Set's a conversion rate by it's currency
     *
     * @param int
     */
    public function setRate()
    {
        if (!isset($this->currency) || !isset($this->api_response)) {
            throw new GernzyException(
                'An exception occured.',
                'Currency or api response were not set.'
            );
        }

        $currency = $this->currency;
        $api_response = $this->api_response;
        $rate = $api_response->rates->$currency;

        $this->rate = $rate;
    }

    /**
     * Set's the object base currency
     *
     * @param string
     */
    public function setBaseCurrency($baseCurrency = '')
    {
        $this->baseCurrency = $baseCurrency;
    }

    /*------------------Getters------------------*/

    /**
     * Get's a conversion rate by it's currency
     *
     * @param string
     */
    public function getRate()
    {
        return $this->rate;
    }

    /*------------------Methods------------------*/
    /**
     * Convert between currency
     *
     * @param int
     */
    public function convertCurrency($amount)
    {
        return floor($amount * $this->rate);
    }
}
