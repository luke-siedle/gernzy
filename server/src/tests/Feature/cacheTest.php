<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Gernzy\Server\Testing\TestCase;

class GernzyCacheTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCache()
    {
        $expected = "abcd";

        Cache::put('some key', $expected, 30);

        $this->assertTrue(Cache::has('some key'));

        $actual = Cache::get('some key');

        // Assert function to test whether expected
        // value is equal to actual or not
        $this->assertEquals(
            $expected,
            $actual,
            "actual value is not equals to expected"
        );
    }
}
