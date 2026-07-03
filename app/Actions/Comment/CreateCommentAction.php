<?php

namespace App\Actions\Comment;

use App\Models\Comment;

class CreateCommentAction
{
    public function execute(int $userId, int $postId, array $data): Comment
    {
        return Comment::create([
            'user_id' => $userId,
            'post_id' => $postId,
            'body'    => $data['body'],
        ]);
    }
}
