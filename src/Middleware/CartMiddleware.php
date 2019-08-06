<?php

    namespace Lab19\Cart\Middleware;

    use Lab19\Cart\Module\Users\User;
    use Illuminate\Support\Facades\Auth;
    use App;

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
            $userService = App::make('Lab19\UserService');
            $token = $request->bearerToken();
            if( $token ){
                $user = $userService->getFromToken( $token );
                if( $user instanceof User ){
                    $request->merge(['user' => $user]);
                    $request->setUserResolver(function () use ($user) {
                        return $user;
                    });
                }
            }
            return $request;
        }
    }
