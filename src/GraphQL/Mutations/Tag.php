<?php

namespace Lab19\Cart\GraphQL\Mutations;

use \App;
use GraphQL\Type\Definition\ResolveInfo;
use Lab19\Cart\Actions\CreateTag;
use Lab19\Cart\Actions\DeleteTag;
use Lab19\Cart\Actions\UpdateTag;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Tag
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
        $createTag = App::make(CreateTag::class);
        $result = $createTag->handle($args['input']);
        return $result;
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $updateTag = App::make(UpdateTag::class);
        $result = $updateTag->handle($args['id'], $args['input']);
        return $result;
    }

    public function delete($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $deleteTag = App::make(DeleteTag::class);
        $result = $deleteTag->handle($args['id']);
        return ['success' => $result];
    }
}
