<?php

namespace Gernzy\Server\GraphQL\Mutations;

use \App;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateSession
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
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $sessionService = App::make('Gernzy\SessionService');

        if (!$sessionService->get('cart_uuid')) {
            // Creates a uuid that will be associated with the cart
            $sessionService->update([
                'cart_uuid' => Str::uuid(),
            ]);
        }

        $result = [
            'token' => $sessionService->getToken(),
            'cart_uuid' => $sessionService->get('cart_uuid'),
        ];

        $sessionService->save();

        return $result;
    }
}
