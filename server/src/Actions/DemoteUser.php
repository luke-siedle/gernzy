<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\User;

class DemoteUser
{
    public function handle( Int $id ){
        $user = User::find( $id );
        $user->is_admin = 0;
        $user->save();
        return $user;
    }
}
