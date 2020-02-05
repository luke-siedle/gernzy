<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\User;
use Lab19\Cart\Services\OrderService;
use Lab19\Cart\Services\SessionService;

class CreateAccount
{
    public function __construct(SessionService $sessionService, OrderService $orderService)
    {
        $this->sessionService = $sessionService;
        $this->orderService = $orderService;
    }

    public function withSession($args)
    {
        $user = static::createUser($args);
        $user->session_token = $this->sessionService->getToken();
        $user->save();
        $this->sessionService->mergeWithUser($user);
        $this->orderService->mergePreviousOrdersWithUser($user);
        return [
            'user' => $user,
            'token' => $this->sessionService->getToken(),
        ];
    }

    public static function createUser($args)
    {
        return new User([
            'name' => $args['name'],
            'email' => $args['email'],
            'password' => $args['password'],
        ]);
    }
}
