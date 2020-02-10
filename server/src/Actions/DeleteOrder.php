<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Order;

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
