<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\User;
use Gernzy\Server\Services\OrderService;
use Gernzy\Server\Services\SessionService;

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
