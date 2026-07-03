<?php

namespace App\Actions\Post;

use App\Models\Post;

class CreatePostAction
{
    public function execute(int $userId, array $data): Post
    {
        return Post::create([
            'user_id' => $userId,
            'title'   => $data['title'],
            'body'    => $data['body'],
        ]);
    }
}
