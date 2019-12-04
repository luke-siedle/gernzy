<?php

namespace Lab19\Cart\Services;

class MaxmindGeoIP2 implements GeolocationInterface
{
    protected $implementation; //This is injected as either a local DB or api service
    protected $record;

    /**
     * Set Repository
     *
     * @param object
     */
    public function setGeoRepository($implementation)
    {
        $this->implementation = $implementation;
        return $this;
    }

    public function setRecord($ip_address)
    {
        $record = $this->implementation->city($ip_address);

        $this->record = $record;

        return $this;
    }

    /**
     * Country code lookup in Repository
     *
     * @param $var
     */
    public function geoFindCountryISO()
    {
        $isoCode = $this->record->country->isoCode;
        return $isoCode;
    }

    public function getLatitude()
    {
        return $this->record->location->latitude;
    }

    public function getLongitude()
    {
        return $this->record->location->longitude;
    }

    public function getCityName()
    {
        $cityName = $this->record->city->name;
        return $cityName;
    }

    public function getCityPostalCode()
    {
        $cityPostalCode = $this->record->postal->code;
        return $cityPostalCode;
    }

    public function getCountryName()
    {
        $countryName = $this->record->country->name;
        return $countryName;
    }

    /**
     * This returns the entire record found for the given IP
     * which contains geolocation details.
     * @param $var
     */
    public function getRecord()
    {
        $record = $this->record;
        return $record;
    }
}
