<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\User;

class ElevateUser
{
    public function handle( Int $id ){
        $user = User::find( $id );
        $user->is_admin = 1;
        $user->save();
        return $user;
    }
}
