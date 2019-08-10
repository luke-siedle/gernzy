<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\User;
use Lab19\Cart\Services\SessionService;
use Lab19\Cart\Services\OrderService;

class CreateAccount
{
    public function __construct( SessionService $session, OrderService $orderService ){
        $this->session = $session;
        $this->orderService = $orderService;
    }

    public function withSession( $args ){
        $user = static::createUser( $args );
        $user->session_token = $this->session->getToken();
        $user->save();
        $this->session->mergeWithUser( $user );
        $this->orderService->mergePreviousOrdersWithUser( $user );
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
