<?php

namespace App\Actions\Comment;

use App\Models\Comment;

class UpdateCommentAction
{
    public function execute(Comment $comment, array $data): Comment
    {
        $comment->update(['body' => $data['body']]);
        return $comment->fresh();
    }
}
