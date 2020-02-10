<?php

namespace Tests\Feature;

use \App;
use GeoIp2\Database\Reader;
use Gernzy\Server\Services\MaxmindGeoIP2;
use Gernzy\Server\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class GelocationTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
    }


    // Helpers
    public function setupGeocodingSession()
    {
        // Create a session
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQL('
                mutation {
                    createSession {
                        token
                    }
                }
            ');

        $start = $response->decodeResponseJson();

        $token = $start['data']['createSession']['token'];

        // Set the session currency
        $response = $this->postGraphQL(['query' => '
                    mutation {
                        setSessionGeoLocation {
                            geolocation_record
                        }
                    }
                ',], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'setSessionGeoLocation' => [
                    'geolocation_record',
                ],
            ],
        ]);

        return $token;
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGeoLocation()
    {
        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
        mutation {
            setSessionGeoLocation {
                geolocation_record
            }
        }
        ');

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'setSessionGeoLocation' => [
                    'geolocation_record',
                ],
            ],
        ]);

        $result = $response->decodeResponseJson();

        $geoLocationRecord = $result['data']['setSessionGeoLocation']['geolocation_record'];

        $this->assertTrue(isset($geoLocationRecord) && !empty($geoLocationRecord));
    }

    public function testDatabaseHasGeocodingSessionInformation()
    {
        $token = $this->setupGeocodingSession();

        $sessionService = App::make('Gernzy\SessionService');

        $geoLocationRecord = $sessionService->get('geolocation_record');

        $this->assertDatabaseHas('gernzy_sessions', [
            'token' => $token,
        ]);

        $this->assertTrue(isset($geoLocationRecord) && !empty($geoLocationRecord));
    }

    public function testMaxminGeoIP2()
    {
        $maxmind = new MaxmindGeoIP2;
        $maxmind
            ->setGeoRepository((new Reader(config('db.maxmind_city_db'))))
            ->setRecord('41.246.26.94');

        $this->assertTrue(null != $maxmind->geoFindCountryISO() && !empty($maxmind->geoFindCountryISO()));
        $this->assertTrue(null != $maxmind->getLatitude() && !empty($maxmind->getLatitude()));
        $this->assertTrue(null != $maxmind->getLongitude() && !empty($maxmind->getLongitude()));
        $this->assertTrue(null != $maxmind->getCityName() && !empty($maxmind->getCityName()));
        $this->assertTrue(null != $maxmind->getCityPostalCode() && !empty($maxmind->getCityPostalCode()));
        $this->assertTrue(null != $maxmind->getCountryName() && !empty($maxmind->getCountryName()));
        $this->assertTrue(null != $maxmind->getRecord() && !empty($maxmind->getRecord()));
    }
}
