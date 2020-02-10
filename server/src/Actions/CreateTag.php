<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Tag;

class CreateTag
{
    public static function handle($args): Tag
    {
        $tag = Tag::create([
            'name' => $args['name']
        ]);

        return $tag;
    }
}
