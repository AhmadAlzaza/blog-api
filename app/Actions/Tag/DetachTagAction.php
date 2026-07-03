<?php

namespace App\Actions\Tag;

use App\Models\Post;
use App\Models\Tag;

class DetachTagAction
{
    public function execute(Post $post, Tag $tag): void
    {
        $post->tags()->detach($tag->id);

        if ($tag->posts()->count() === 0) {
            $tag->delete();
        }
    }
}
