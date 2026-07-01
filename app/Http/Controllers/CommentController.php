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



    public function update(UpdateCommentRequest $request, string $post, string $comment)
    {
        $post = Post::findOrFail($post);
        $comment = Comment::findOrFail($comment);
        if ($comment->post_id != $post->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        if ($request->user()->id != $comment->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $comment->update([
            'body' => $request['body']
        ]);
        return new CommentResource($comment->load('user', 'post'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $post, Request $request, string $comment)
    {

        $comment = Comment::findOrFail($comment);
        $post = Post::findOrFail($post);
        if ($comment->post_id != $post->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        if ($request->user()->id != $comment->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $comment->delete();
        return response()->json(['message' => 'Comment Deleted']);
    }
}
