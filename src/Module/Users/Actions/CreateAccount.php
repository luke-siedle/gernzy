<?php

namespace Lab19\Cart\Module\Users\Actions;

use Lab19\Cart\Module\Users\User;
use Lab19\Cart\Module\Users\Services\SessionService;

class CreateAccount
{
    public function __construct( SessionService $session ){
        $this->session = $session;
    }

    public function withSession( $args ){
        $user = static::createUser( $args );
        $user->session_token = $this->session->getToken();
        $user->save();
        $this->session->mergeWithUser( $user );
        return [
            'user' => $user,
            'token' => $this->session->getToken(),
        ];
    }

    public static function createUser( $args ){
        return new User([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => $args['password']
        ]);
    }
}
