<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Lab19\Cart\Testing\TestCase;

class PaymentGatewayTest extends TestCase
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
    // public function testBasicStripe()
    // {
    //     // Set your secret key: remember to change this to your live secret key in production
    //     // See your keys here: https://dashboard.stripe.com/account/apikeys
    //     \Stripe\Stripe::setApiKey(env('sk_test_', null));

    //     $charge = \Stripe\Charge::create([
    //         'amount' => 1000,
    //         'currency' => 'usd',
    //         'source' => 'tok_visa',
    //         'receipt_email' => 'jenny.rosen@example.com',
    //     ]);


    //     $this->assertTrue($charge->captured);
    // }

    // public function testCreateChargeWithCheckout()
    // {
    //     // Set your secret key: remember to change this to your live secret key in production
    //     // See your keys here: https://dashboard.stripe.com/account/apikeys
    //     \Stripe\Stripe::setApiKey('sk_test_FC4eAQOlFeVpzvvs5VVDeqVt00SdGW3dai');

    //     $session = \Stripe\Checkout\Session::create([
    //         'payment_method_types' => ['card'],
    //         'line_items' => [[
    //             'name' => 'T-shirt',
    //             'description' => 'Comfortable cotton t-shirt',
    //             'images' => ['https://example.com/t-shirt.png'],
    //             'amount' => 500,
    //             'currency' => 'usd',
    //             'quantity' => 1,
    //         ]],
    //         'success_url' => 'https://example.com/success?session_id={CHECKOUT_SESSION_ID}',
    //         'cancel_url' => 'https://example.com/cancel',
    //     ]);
    // }
}
