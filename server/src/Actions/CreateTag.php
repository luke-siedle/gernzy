<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Tag;

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
