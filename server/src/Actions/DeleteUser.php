<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\User;

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
