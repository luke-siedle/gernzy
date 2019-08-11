<?php

namespace Lab19\Cart\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Lab19\Cart\Actions\CreateOrder;
use Lab19\Cart\Actions\UpdateOrder;
use Lab19\Cart\Actions\DeleteOrder;
use \App;

class Order
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
        $createOrder = App::make(CreateOrder::class);
        $result = $createOrder->handle( $args['input'] );
        return $result;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $updateOrder = App::make(UpdateOrder::class);
        $result = $updateOrder->handle( $args['id'], $args['input'] );
        return $result;
    }

    public function delete($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $deleteOrder = App::make(DeleteOrder::class);
        $result = $deleteOrder->handle( $args['id'] );
        return [
            'success' => $result
        ];
    }
}
