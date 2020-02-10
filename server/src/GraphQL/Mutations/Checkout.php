<?php

namespace Gernzy\Server\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Exceptions\GenericException;
use Gernzy\Server\Services\CartService;
use Gernzy\Server\Services\SessionService;
use Gernzy\Server\Actions\CreateCheckout;
use \App;

class Checkout
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
    public function checkout($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $cartService = App::make(CartService::class);
        $sessionService = App::make(SessionService::class);

        if( !$sessionService->exists() ){
            return false;
        }

        if( $cartService->hasItems() ){
            $createCheckout = App::make(CreateCheckout::class);
            $order = $createCheckout->handle( $args['input'] );
            return [
                'order' => $order
            ];
        } else {
            throw new GenericException(
                'Cannot checkout without items in the cart'
            );
        }
    }
}
