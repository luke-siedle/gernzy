<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Order;

class DeleteOrder
{
    public static function handle( Int $id ): bool
    {
        $order = Order::find( $id );
        if( $order->id ){
            return $order->delete();
        } else {
            return false;
        }
    }
}
