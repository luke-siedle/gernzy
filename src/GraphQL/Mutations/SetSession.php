<?php

namespace Lab19\Cart\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Cache;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use \App;

class SetSession
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
    public function set($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $sessionService = App::make('Lab19\SessionService');

        $sessionService->update($args['input']);

        return $sessionService->get();
    }

    public function setCurrency($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $sessionService = App::make('Lab19\SessionService');

        $currency = $args['input']['currency'];

        $sessionService->update(['currency' => $currency]);

        // Clear the previous rate for the user as a new currency has been chosen
        Cache::forget($sessionService->getToken());

        return $sessionService->get();
    }
}
