<?php

namespace Lab19\Cart\Module\Users\Actions;

use Lab19\Cart\Module\Users\User;
use Lab19\Cart\Module\Users\Services\SessionService;
use Lab19\Cart\Module\Orders\Services\OrderService;

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
