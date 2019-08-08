<?php

namespace Lab19\Cart\Module\Users\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Illuminate\Support\Str;
use Lab19\Cart\Module\Orders\Services\OrderService;
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
    public function me($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $sessionService = App::make('Lab19\SessionService');
        $me = $sessionService->getUser();
        if( !$me->id ) {
            $cartService = App::make('Lab19\CartService');
            $me = [
                'cart' => $cartService->getCart(),
                'session' => $sessionService->session
            ];
        }

        return $me;
    }

    public function myOrders($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $orderService = App::make(OrderService::class);
        $orders = $orderService->myOrders();
        return $orders;
    }

}
