<?php

namespace App\Actions\Tag;

use App\Models\Post;
use App\Models\Tag;

class AttachTagAction
{
    public function execute(Post $post, string $tagName): Tag
    {
        $tag = Tag::firstOrCreate(['name' => $tagName]);

        if (!$post->tags()->where('tag_id', $tag->id)->exists()) {
            $post->tags()->attach($tag->id);
        }

        return $tag;
    }
}
