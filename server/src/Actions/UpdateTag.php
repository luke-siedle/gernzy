<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Tag;

class UpdateTag
{
    public static function handle(Int $id, array $args): Tag
    {
        $tag = Tag::findOrFail($id);
        $tag->fill($args);
        $tag->save();

        return $tag;
    }
}
