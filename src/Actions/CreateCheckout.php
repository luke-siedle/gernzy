<?php

namespace Lab19\Cart\Actions;

use \App;
use Lab19\Cart\Models\Order;
use Lab19\Cart\Services\CartService;
use Lab19\Cart\Services\SessionService;

class CreateCheckout
{
    public function __construct(SessionService $sessionServiceRawService, CartService $cartService)
    {
        $this->sessionService = $sessionServiceRawService;
        $this->cartService = $cartService;
    }

    public function handle($args)
    {
        if ($args['use_shipping_for_billing']) {
            $billing = $args['shipping_address'];
        } else {
            $billing = $args['billing_address'];
        }

        $sessionServiceRaw = $this->sessionService->raw();
        $user = $this->sessionService->getUser();
        $cart = $sessionServiceRaw->cart;

        $order = new Order([
            "name" => $args["name"],
            "email" => $args["email"],
            "telephone" => $args["telephone"],
            "mobile" => $args["mobile"],

            "shipping_address_line_1" => $args["shipping_address"]["line_1"],
            "shipping_address_line_2" => $args["shipping_address"]["line_2"],
            "shipping_address_postcode" => $args["shipping_address"]["postcode"],
            "shipping_address_state" => $args["shipping_address"]["state"],
            "shipping_address_country" => $args["shipping_address"]["country"],

            "billing_address_line_1" => $billing["line_1"],
            "billing_address_line_2" => $billing["line_2"],
            "billing_address_postcode" => $billing["postcode"],
            "billing_address_state" => $billing["state"],
            "billing_address_country" => $billing["country"],

            "agree_to_terms" => (int) $args["agree_to_terms"],
            "notes" => $args["notes"],
        ]);

        // Create the payment method
        if (!isset($args["payment_method"])) {
            throw new Exception('Payment method was not defined');
        }

        $createPayment = App::make(CreatePayment::class);
        $paymentMethod = $createPayment->create($args['payment_method']);

        // Associate the order to the user
        // and cart to the order, and save
        // Also save the payment method
        $order->user()->associate($user);
        $order->cart()->save($cart);
        $order->save();
        $paymentMethod->cents_amount = $this->cartService->calculateTotalsCents();
        $order->payment()->save($paymentMethod);

        $this->cartService->setOrder($order);
        $this->cartService->reset();

        return $order;
    }
}
