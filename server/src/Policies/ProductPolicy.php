<?php

namespace Gernzy\Server\Policies;

use Gernzy\Server\Models\Product;
use Gernzy\Server\Models\User;

class ProductPolicy
{

    /**
     * Determine if a product can be created by the user
     *
     * @param  $me
     * @param  $product
     * @return bool
     */
    public function create(User $me)
    {
        return $me->isAdmin();
    }

    /**
     * Determine if the given post can be updated by the user.
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
    public function view(User $me, ?User $user = null): bool
    {
        return true;
    }

    /**
     * Determine if the given user can be read by the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return bool
     */
    public function delete(User $me): bool
    {
        return $me->isAdmin();
    }
}
