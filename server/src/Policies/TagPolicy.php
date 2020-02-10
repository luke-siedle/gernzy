<?php

namespace Gernzy\Server\Policies;

use Gernzy\Server\Models\User;

class TagPolicy
{

    /**
     * Determine if a tag can be created by the user
     *
     * @param  $me
     * @return bool
     */
    public function create(User $me)
    {
        return $me->isAdmin();
    }

    /**
     * Determine if the given tag can be updated by the user.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function update(User $me)
    {
        return $me->isAdmin();
    }

    /**
     * Determine if the given tag can be read by the user.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function view(User $me, ?User $user = null): bool
    {
        return true;
    }

    /**
     * Determine if the given tag can be deleted by the user.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function delete(User $me): bool
    {
        return $me->isAdmin();
    }
}
