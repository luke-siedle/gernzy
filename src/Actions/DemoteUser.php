<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\User;

class DemoteUser
{
    public function handle( Int $id ){
        $user = User::find( $id );
        $user->is_admin = 0;
        $user->save();
        return $user;
    }
}
