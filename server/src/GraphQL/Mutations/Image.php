<?php

namespace Gernzy\Server\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Gernzy\Server\Models\Image as ImageModel;
use \App;

class Image
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
         /** @var \Illuminate\Http\UploadedFile $file */
        // $file = $args['input']['file'];
        // $url = Storage::url($file->hashname());
        $file = $args['input']['file'];
        $name = $file->hashname();
        $image = new ImageModel([
            "name" => $name,
            "url" => Storage::url($name),
            "type" => $file->extension()
        ]);
        $image->save();
        return $image;
    }
}
