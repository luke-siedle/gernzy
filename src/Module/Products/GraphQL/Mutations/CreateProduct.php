<?php

namespace Lab19\Cart\Module\Products\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Exceptions\GenericException;
use Lab19\Cart\Module\Products\Actions\CreateProduct as CreateProductAction;
use Lab19\Cart\Module\Products\Actions\UpdateProduct as UpdateProductAction;
use Lab19\Cart\Module\Products\Actions\DeleteProduct as DeleteProductAction;
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

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $updateProduct = App::make(UpdateProductAction::class);

        $result = $updateProduct->handle( $args['id'], $args['input'] );

        return $result;
    }

    public function delete($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $deleteProduct = App::make(DeleteProductAction::class);
        $result = $deleteProduct->handle( $args['id'] );
        return ['success' => $result];
    }
}
