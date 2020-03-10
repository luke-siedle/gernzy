<?php

namespace Gernzy\Server\Observers;

use Gernzy\Server\Models\Cart;

class CartObserver
{

    /**
     * Handle the Cart "saving" event.
     *
     * @param  \App\Cart  $cart
     * @return void
     */
    public function saving(Cart $cart)
    {
        $cartTotal = $cart->calcCartTotal();
        $cart->cart_total = $cartTotal;
    }
}
