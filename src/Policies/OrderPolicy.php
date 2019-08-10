<?php

namespace Lab19\Cart\Policies;

use Lab19\Cart\Models\User;
use Lab19\Cart\Models\Order;

class OrderPolicy
{
    /**
     * Determine if the given post can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return bool
     */
    public function update(User $me, Order $order)
    {
        if( $user ){
            return $me->isAdmin() || $me->id === $order->user_id;
        }

        return $me->isAdmin();
    }

    /**
     * Determine if the given user can be read by the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return bool
     */
    public function view(User $me, ?Order $order = null ): bool
    {
        // If a model is available, we can check, but this isn't always the case
        // for example, when fetching a list of users
        // Discussed in some detail here
        // https://github.com/nuwave/lighthouse/issues/244
        if( $order ){
            return $me->isAdmin() || $me->id === $order->user_id;
        }

        return $me->isAdmin();
    }

    /**
     * Placeholder method. GraphQL will only arrive here
     * if the user has authorized.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return bool
     */
    public function addToCart(User $me): bool
    {
        return $me->id > 0;
    }
}
