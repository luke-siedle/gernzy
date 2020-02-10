<?php

namespace Gernzy\Server\Actions;
use Gernzy\Server\Models\Order;

class UpdateOrder
{
    public function handle( Int $id, Array $args ){

        $order = Order::findOrFail( $id );

        // Only update the passed arguments
        foreach( $args as $key => $arg ){
            $order->{$key} = $arg;
            if( $key === 'shipping_address'
                || $key === 'billing_address' ){
                foreach( $arg as $innerKey => $innerArg ){
                    $order->{$innerKey} = $innerArg;
                }
            }
        }

        $order->save();
        return $order;
    }
}
