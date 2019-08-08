<?php

namespace Lab19\Cart\Module\Users\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Illuminate\Support\Str;
use Lab19\Cart\Module\Users\Actions\LogIn;
use Lab19\Cart\Module\Users\Actions\LogOut;
use \App;

class Account
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function logIn($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $login = App::make(LogIn::class);

        $result = $login->handle(
            $args['email'],
            $args['password']
        );

        if( !$result ){
            throw new AuthenticationException(
                'Invalid credentials'
            );
        }

        return [
            'user' => $result['user'],
            'token' => $result['token']
        ];
    }

    public function logOut($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {

        $logOut = App::make(LogOut::class);

        $result = $logOut->handle();

        return [
            'success' => $result
        ];
    }

    public function me($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $userService = App::make('Lab19\UserService');
        $sessionService = App::make('Lab19\SessionService');
        $me = $userService->getUser();
        if( ! $me ){
            $cartService = App::make('Lab19\CartService');
            $me = [
                'cart' => $cartService->getCart(),
                'session' => $sessionService->session
            ];
        }

        return $me;
    }
}
