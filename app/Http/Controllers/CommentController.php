<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $post)
    {
        $post = Post::findOrFail($post);
        $comment = $post->comments()->with('user', 'post')->paginate(15);
        return CommentResource::collection($comment);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $post, StoreCommentRequest $request)
    {
        $post = Post::findOrFail($post);
        $comment = Comment::create(
            [
                'user_id' => $request->user()->id,
                'post_id' => $post->id,
                'body' => $request['body']
            ]
        );
        return (new CommentResource($comment->load('user', 'post')))->response()->setStatusCode(201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $post, string $comment)
    {
        $post = Post::findOrFail($post);
        $comment = Comment::findOrFail($comment);
        if ($comment->post_id != $post->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return new CommentResource($comment->load('user', 'post'));
    }

    /**
     * Update the specified resource in storage.
     */



    public function update(UpdateCommentRequest $request, Post $post, Comment $comment)
    {

        if ($comment->post_id !== $post->id) {
            abort(404);
        }

        $this->authorize('update', $comment);

        $comment->update(['body' => $request->body]);

        return new CommentResource($comment->load('user', 'post'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, Comment $comment)
    {
        if ($comment->post_id !== $post->id) {
            abort(404);
        }

        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment Deleted']);
    }
}
