<?php

namespace Lab19\Cart\Module\Products\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Lab19\Cart\Module\Products\Actions\CreateProduct as CreateProductAction;
use Lab19\Cart\Module\Users\Services\SessionService;
use \App;

class CreateProduct
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
        $createProduct = App::make(CreateProductAction::class);

        $result = $createProduct->handle( $args['input'] );

        return $result;
    }
}
