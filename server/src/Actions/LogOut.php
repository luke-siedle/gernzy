<?php

namespace Gernzy\Server\Actions;

use Illuminate\Http\Request;
use Gernzy\Server\Models\User;
use Gernzy\Server\Services\SessionService;

class LogOut
{
    public function __construct(SessionService $sessionService, Request $request)
    {
        $this->sessionService = $sessionService;
        $this->request = $request;
    }

    public function handle()
    {
        $token = $this->request->bearerToken();

        if ($token) {
            $user = User::where('session_token', '=', $token)->first();
            if ($user) {
                // Disaccociate the session
                // and delete it
                $user->session_token = null;
                $user->save();
                return $this->sessionService->close();
            }

            // Where it's a user-less session
            // and the session exists
            if ($this->sessionService->exists() && $token === $this->sessionService->getToken()) {
                return $this->sessionService->close();
            }
        }

        return false;
    }
}
