<?php

namespace Gernzy\Server\Services;

class GeolocationService
{
    // $geoLocation is the specific GeoCoding service implementation injected via the interface.
    // The default service bound into laravel is Maxmind
    protected $geoLocation;

    public function __construct(GeolocationInterface $geoLocation)
    {
        $this->geoLocation = $geoLocation;
    }

    /**
     * This injects the type of repo maxmind will use to lookup the geocoding info
     *
     * @param object
     */
    public function injectGeoRepositoryType($implementation)
    {
        $this->geoLocation->setGeoRepository($implementation);
        return $this;
    }

    public function findandSetRecordMatchingIpAddress($ip_address)
    {
        $this->geoLocation->setRecord($ip_address);
        return $this;
    }

    /**
     * Find the country code by the provided geocoding service
     *
     * @param object
     */
    public function findCountryIsoCodeByIP()
    {
        return $this->geoLocation->geoFindCountryISO();
    }


    public function getGeolocationRecord()
    {
        $record = $this->geoLocation->getRecord();
        return $record;
    }
}
