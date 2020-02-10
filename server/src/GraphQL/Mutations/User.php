<?php

namespace Gernzy\Server\GraphQL\Mutations;

use \App;
use GraphQL\Type\Definition\ResolveInfo;
use Gernzy\Server\Actions\CreateUser;
use Gernzy\Server\Actions\DeleteUser;
use Gernzy\Server\Actions\DemoteUser;
use Gernzy\Server\Actions\ElevateUser;
use Gernzy\Server\Actions\ResetPassword;
use Gernzy\Server\Actions\SendPasswordReset;
use Gernzy\Server\Actions\UpdateUser;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

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
        $result = $createUser->handle($args['input']);
        return $result;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $updateUser = App::make(UpdateUser::class);
        $result = $updateUser->handle($args['id'], $args['input']);
        return $result;
    }

    public function delete($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $deleteUser = App::make(DeleteUser::class);
        $result = $deleteUser->handle($args['id']);
        return [
            'success' => $result
        ];
    }

    public function elevate($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $elevateUser = App::make(ElevateUser::class);
        $result = $elevateUser->handle($args['id']);
        return $result;
    }

    public function demote($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $demoteUser = App::make(DemoteUser::class);
        $result = $demoteUser->handle($args['id']);
        return $result;
    }

    public function resetPasswordLink($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $sendPasswordReset = App::make(SendPasswordReset::class);
        $result = $sendPasswordReset->handle($args['email']);
        return ['success' => $result];
    }

    public function resetPassword($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $passwordReset = App::make(ResetPassword::class);
        $result = $passwordReset->handle($args['input']);
        return ['success' => $result];
    }
}
