<?php

namespace Tests\Feature;

use Gernzy\Server\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class EnabledCurrencyTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testViewProductFixedPricesGraphql(): void
    {
        $response = $this->graphQL('
                query {
                    shopConfig {
                        enabled_currencies
                    }
                }
            ');

        $response->assertDontSee('errors');

        $result = $response->decodeResponseJson();

        // Check that returned array not empty
        $this->assertNotEmpty($result['data']['shopConfig']['enabled_currencies']);

        $response->assertJsonStructure([
            'data' => [
                'shopConfig' => [
                    'enabled_currencies' => []
                ]
            ]
        ]);
    }
}
