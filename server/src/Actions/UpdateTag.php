<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Tag;

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
