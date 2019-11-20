<?php

namespace Lab19\Cart\Middleware;

use App;
use Illuminate\Support\Facades\Auth;
use Lab19\Cart\Models\Session;
use Lab19\Cart\Models\User;

class CartMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $request = $this->addUserToRequest($request);
        return $next($request);
    }

    private function addUserToRequest(\Illuminate\Http\Request $request): \Illuminate\Http\Request
    {
        $token = $request->bearerToken();

        if ($token) {
            $userService = App::make('Lab19\UserService');
            $user = $userService->getFromToken($token);

            if ($user instanceof User) {
                $request->merge(['user' => $user]);
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
            }

            $sessionService = App::make('Lab19\SessionService');
            $sessionFromToken = $sessionService->getFromToken($token);
            if ($sessionFromToken instanceof Session) {
                $request->merge(['session' => $sessionFromToken]);
            }
        }

        return $request;
    }
}
