<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\User;

class UpdateUser
{
    public function handle( Int $id, $args ){
        $user = User::find( $id );
        $user->fill( $args );
        $user->save();
        return $user;
    }
}
