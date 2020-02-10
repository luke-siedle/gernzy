<?php

namespace Gernzy\Server\Policies;

use Gernzy\Server\Models\User;

class UserPolicy
{

    /**
     * Determine if users can be created by the user.
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
     * Determine if the given post can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return bool
     */
    public function update(User $me, User $user = null)
    {
        if ($user) {
            return $me->isAdmin() || $me->id === $user->id;
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
    public function view(User $me, ?User $user = null): bool
    {
        // If a model is available, we can check, but this isn't always the case
        // for example, when fetching a list of users
        // Discussed in some detail here
        // https://github.com/nuwave/lighthouse/issues/244
        if ($user) {
            return $me->isAdmin() || $me->id === $user->id;
        }

        return $me->isAdmin();
    }

    /**
     * Determine if users can delete a user.
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
