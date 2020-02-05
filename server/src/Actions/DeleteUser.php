<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\User;

class DeleteUser
{
    public static function handle( Int $id ): bool
    {
        $user = User::find( $id );
        if( $user->id ){
            return $user->delete();
        } else {
            return false;
        }
    }
}
