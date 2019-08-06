<?php

namespace Lab19\Cart\Module\Users\Policies;

use Lab19\Cart\Module\Users\User;

class UserPolicy
{
    /**
     * Determine if the given post can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return bool
     */
    public function update(User $me)
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
    public function view(User $me, ?User $user = null ): bool
    {
        if( $user && $me->id === $user->id ){
            return true;
        }

        return $me->is_admin === 1;
    }
}
