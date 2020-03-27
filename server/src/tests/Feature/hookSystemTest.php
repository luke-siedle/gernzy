<?php

namespace Tests\Feature;

use Gernzy\Server\Listeners\BeforeCheckout;
use Gernzy\Server\Packages\BarBeforeCheckout;
use Gernzy\Server\Packages\FooBeforeCheckout;
use Gernzy\Server\Packages\StripeBeforeCheckout;
use Gernzy\Server\Services\EventService;
use Gernzy\Server\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class GernzyHookSystemTest extends TestCase
{
    use WithFaker;

    public $checkoutData = [
        "name" => "Luke",
        "email" => "cart@example.com",
        "telephone" => "082456748",
        "mobile" => "08357684758",
        "billing_address" => [
            "line_1" => "1 London Way",
            "line_2" => "",
            "state" => "London",
            "postcode" => "SW1A 1AA",
            "country" => "UK"
        ],
        "shipping_address" => [
            "line_1" => "1 London Way",
            "line_2" => "",
            "state" => "London",
            "postcode" => "SW1A 1AA",
            "country" => "UK"
        ],
        "use_shipping_for_billing" => true,
        "payment_method" => "",
        "agree_to_terms" => true,
        "notes" => ""
    ];

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Actions listen to an Event, so when an event is triggered, an Action that is listening
     * to that event will also trigger. The third party developer will register Actions for an Event. These Actions will Listen
     * for the Event to fire and then execute.
     *
     * @return void
     */
    public function testEventService()
    {
        // Set actions for event at run time, for testing purposes
        config(['events.' . BeforeCheckout::class => [StripeBeforeCheckout::class, FooBeforeCheckout::class, BarBeforeCheckout::class]]);

        // Trigger the event through EventService
        $eventService = EventService::triggerEvent(BeforeCheckout::class);

        // Preventing defaults
        if (!$eventService->isEventPreventDefault()) {
        }

        // All the actions that we're called
        $actions = $eventService->getMeta();

        // Prepare array for essertContains (flatten array)
        $actions = array_column($actions, 'action');

        // Check that action names were added to the meta
        $this->assertContains(StripeBeforeCheckout::class, $actions, "actionsArray doesn't contain StripeBeforeCheckout");
        $this->assertContains(FooBeforeCheckout::class, $actions, "actionsArray doesn't contain FooBeforeCheckout");
        $this->assertContains(BarBeforeCheckout::class, $actions, "actionsArray doesn't contain BarBeforeCheckout");
    }

    public function testEventServiceWithData()
    {
        // Set actions for event at run time, for testing purposes
        config(['events.' . BeforeCheckout::class => [StripeBeforeCheckout::class, FooBeforeCheckout::class, BarBeforeCheckout::class]]);

        // Trigger the event through EventService
        $eventService = EventService::triggerEvent(BeforeCheckout::class, $this->checkoutData);

        // Preventing defaults
        if (!$eventService->isEventPreventDefault()) {
        }

        // Check that the last element of the array contains modified data from each action that associated
        $lastModifiedData = $eventService->getLastModifiedData();
        $this->assertNotEmpty($lastModifiedData);
        $this->assertArrayHasKey('coupon', $lastModifiedData[0]);
        $this->assertArrayHasKey('user_id_foo', $lastModifiedData[1]);
        $this->assertArrayHasKey('token_bar', $lastModifiedData[2]);

        // Check that the 'history' array contains modified data  from each action that associated
        $historyOfAllModifiedData = $eventService->getAllModifiedData();
        $this->assertNotEmpty($historyOfAllModifiedData);
        $this->assertEquals(3, count($historyOfAllModifiedData));
        $this->assertArrayHasKey('coupon', $historyOfAllModifiedData[0]['data'][0]);
        $this->assertArrayHasKey('user_id_foo', $historyOfAllModifiedData[1]['data'][1]);
        $this->assertArrayHasKey('token_bar', $historyOfAllModifiedData[2]['data'][2]);
    }


    public function testExamplePackageProvider()
    {
        // Trigger the event through EventService
        $eventService = EventService::triggerEvent(BeforeCheckout::class, $this->checkoutData);

        // Check that the last element of the array contains modified data from each action that associated
        $lastModifiedData = $eventService->getLastModifiedData();
        $this->assertNotEmpty($lastModifiedData);

        // Check that the 'history' array contains modified data  from each action that associated
        $historyOfAllModifiedData = $eventService->getAllModifiedData();
        $this->assertNotEmpty($historyOfAllModifiedData);
    }
}
