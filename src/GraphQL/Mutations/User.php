<?php

namespace Lab19\Cart\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Lab19\Cart\Actions\CreateUser;
use Lab19\Cart\Actions\UpdateUser;
use Lab19\Cart\Actions\DeleteUser;
use Lab19\Cart\Actions\ElevateUser;
use Lab19\Cart\Actions\DemoteUser;
use \App;

class User
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
        $createUser = App::make(CreateUser::class);
        $result = $createUser->handle( $args['input'] );
        return $result;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $updateUser = App::make(UpdateUser::class);
        $result = $updateUser->handle( $args['id'], $args['input'] );
        return $result;
    }

    public function delete($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $deleteUser = App::make(DeleteUser::class);
        $result = $deleteUser->handle( $args['id'] );
        return [
            'success' => $result
        ];
    }

    public function elevate($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $elevateUser = App::make(ElevateUser::class);
        $result = $elevateUser->handle( $args['id'] );
        return $result;
    }

    public function demote($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $demoteUser = App::make(DemoteUser::class);
        $result = $demoteUser->handle( $args['id'] );
        return $result;
    }
}
