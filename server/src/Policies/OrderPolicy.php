<?php

namespace Gernzy\Server\Policies;

use Gernzy\Server\Models\Order;
use Gernzy\Server\Models\User;

class OrderPolicy
{

    /**
     * Determine if the user can create the order
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return bool
     */
    public function create(User $me)
    {
        return $me->isAdmin();
    }

    /**
     * Determine if the given order can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return bool
     */
    public function update(User $me)
    {
        return $me->isAdmin();
    }

    /**
     * Determine if the given user can be read by the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return bool
     */
    public function view(User $me, ?Order $order = null): bool
    {
        // If a model is available, we can check, but this isn't always the case
        // for example, when fetching a list of users
        // Discussed in some detail here
        // https://github.com/nuwave/lighthouse/issues/244
        if ($order) {
            return $me->isAdmin() || $me->id === $order->user_id;
        }

        return $me->isAdmin();
    }

    /**
     * Determine if the user can delete the order
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return bool
     */
    public function delete(User $me)
    {
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
