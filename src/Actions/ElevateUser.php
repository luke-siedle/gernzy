<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\User;

class ElevateUser
{
    public function handle( Int $id ){
        $user = User::find( $id );
        $user->is_admin = 1;
        $user->save();
        return $user;
    }
}
