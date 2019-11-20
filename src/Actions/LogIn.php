<?php

namespace Lab19\Cart\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lab19\Cart\Models\User;
use Lab19\Cart\Services\SessionService;

class LogIn
{
    public function __construct(SessionService $sessionService, Request $request)
    {
        $this->sessionService = $sessionService;
        $this->request = $request;
    }

    public function handle(String $email, String $password)
    {

        // If the user already had a session,
        // allow this to merge if possible
        if ($token = $this->request->bearerToken()) {
            $this->sessionService->setFromToken($token);
        } else {
            // Persist a new session
            $this->sessionService->save();
        }

        if (Auth::guard('cart')->attempt([
            'email' => $email,
            'password' => $password,
        ])) {
            $user = Auth::guard('cart')->user();

            // Update the user's current token
            $user->session_token = $this->sessionService->getToken();
            $user->save();

            return [
                'user' => $user,
                'token' => $this->sessionService->getToken(),
            ];
        } else {
            return false;
        }
    }
}
