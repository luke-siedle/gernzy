<?php

namespace Lab19\Cart\Actions;

use Lab19\Cart\Models\Tag;

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
