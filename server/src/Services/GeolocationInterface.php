<?php

namespace Gernzy\Server\Services;

interface GeolocationInterface
{
    public function geoFindCountryISO();
    public function setGeoRepository($implementation);
    public function getLatitude();
    public function getLongitude();
    public function getCityName();
    public function getRecord();
}
