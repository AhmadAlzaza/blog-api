<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Actions\Comment\CreateCommentAction;
use App\Actions\Comment\UpdateCommentAction;
use App\Actions\Comment\DeleteCommentAction;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->comments()->with('user')->paginate(15);
        return CommentResource::collection($comments);
    }

    public function store(Post $post, StoreCommentRequest $request, CreateCommentAction $action)
    {
        $comment = $action->execute(
            $request->user()->id,
            $post->id,
            $request->only(['body'])
        );

        return (new CommentResource($comment->load('user', 'post')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Post $post, Comment $comment)
    {
        if ($comment->post_id !== $post->id) {
            abort(404);
        }

        return new CommentResource($comment->load('user', 'post'));
    }

    public function update(
        UpdateCommentRequest $request,
        Post $post,
        Comment $comment,
        UpdateCommentAction $action
    ) {
        if ($comment->post_id !== $post->id) {
            abort(404);
        }

        $this->authorize('update', $comment);

        $updatedComment = $action->execute($comment, $request->only(['body']));

        return new CommentResource($updatedComment->load('user', 'post'));
    }

    public function destroy(Post $post, Comment $comment, DeleteCommentAction $action)
    {
        if ($comment->post_id !== $post->id) {
            abort(404);
        }

        $this->authorize('delete', $comment);

        $action->execute($comment);

        return response()->json(['message' => 'Comment Deleted']);
    }
}
