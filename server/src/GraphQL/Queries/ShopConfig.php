<?php

namespace Lab19\Cart\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Lab19\Cart\Exceptions\GernzyException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ShopConfig
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

    public function enabledCurrencies($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // Throw error when there are missing values from the config, thus no currency specified
        if (!$enabledCurrrencies = config('currency.enabled')) {
            throw new GernzyException(
                'An error occured.',
                'An error occured when determining the currency. None specified.'
            );
        }

        return $enabledCurrrencies;
    }
}
