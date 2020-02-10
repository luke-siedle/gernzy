<?php

namespace Gernzy\Server\Actions;
use Gernzy\Server\Models\Order;
use Gernzy\Server\Models\Cart;

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
