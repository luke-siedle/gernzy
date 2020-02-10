<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\User;

class UpdateUser
{
    public function handle( Int $id, $args ){
        $user = User::find( $id );
        $user->fill( $args );
        $user->save();
        return $user;
    }
}
