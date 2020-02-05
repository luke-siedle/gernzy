<?php

namespace Lab19\Cart\Actions;
use Lab19\Cart\Models\Order;
use Lab19\Cart\Models\Cart;

class SetOrderItems
{
    public function handle( Int $id, Array $items ){

        $order = Order::findOrFail( $id );
        $order->load('cart');

        $cart = $order->cart ?: new Cart();
        $cart->items = $items;
        $cart->order()->associate($order);
        $cart->save();

        return $order;
    }
}
