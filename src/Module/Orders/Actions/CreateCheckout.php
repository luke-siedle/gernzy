<?php

namespace Lab19\Cart\Module\Orders\Actions;
use Lab19\Cart\Module\Orders\Order;
use Lab19\Cart\Module\Users\Services\SessionService;
use Lab19\Cart\Module\Orders\Services\CartService;

class CreateCheckout
{
    public function __construct( SessionService $session, CartService $cart ){
        $this->session = $session;
        $this->cart = $cart;
    }

    public function handle( $args ){

        if( $args['use_shipping_for_billing'] ){
            $billing = $args['shipping_address'];
        } else {
            $billing = $args['billing_address'];
        }

        $session = $this->session->raw();
        $cartId = $session->cart->id;

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

            "payment_method" => $args["payment_method"],
            "agree_to_terms" => (int)$args["agree_to_terms"],
            "notes" => $args["notes"],

            "cart_id" => $cartId,
        ]);

        $order->save();
        $this->cart->reset();
        return $order;
    }
}
