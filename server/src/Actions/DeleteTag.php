<?php

namespace Gernzy\Server\Actions;

use Gernzy\Server\Models\Tag;

class DeleteTag
{
    public static function handle(Int $id): bool
    {
        $tag = Tag::find($id);
        if ($tag->id) {
            return $tag->delete();
        } else {
            return false;
        }
    }
}
