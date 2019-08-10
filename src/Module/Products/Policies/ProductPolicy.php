<?php

namespace Lab19\Cart\Module\Products\Policies;

use Lab19\Cart\Module\Products\Product;
use Lab19\Cart\Module\Users\User;

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
    public function update(User $me, Product $product)
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
    public function view(User $me, ?User $user = null ): bool
    {
        return true;
    }
}
