<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\User;

class CreateUser
{
    public function handle( $args ){
        $user = static::createUser( $args );
        $user->save();
        return $user;
    }

    public static function createUser( $args ){
        return new User([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => $args['password']
        ]);
    }
}
