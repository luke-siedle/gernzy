<?php

namespace Gernzy\Server\Middleware;

use App;
use Gernzy\Server\Models\Session;
use Gernzy\Server\Models\User;

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
            $userService = App::make('Gernzy\UserService');
            $user = $userService->getFromToken($token);

            if ($user instanceof User) {
                $request->merge(['user' => $user]);
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
            }

            $sessionService = App::make('Gernzy\SessionService');
            $sessionFromToken = $sessionService->getFromToken($token);
            if ($sessionFromToken instanceof Session) {
                $request->merge(['session' => $sessionFromToken]);
            }
        }

        return $request;
    }
}
