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
        $post = Post::FindOrFail($post);
        $comment = $post->comments()->with('user','post')->paginate(15);
        return CommentResource::collection($comment);



    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $post,StoreCommentRequest $request)
    {
        $post = Post::FindOrFail($post);
        $comment = Comment::create(
            [
                'user_id' => $request->user()->id,
                'post_id' => $post->id,
                'body' => $request['body']
            ]
        );
        return new CommentResource($comment);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $post,string $comment)
    {

        $comment = Comment::FindOrFail($comment);
        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     */



     public function update(UpdateCommentRequest $request, string $post,string $comment)
     {
         $post = Post::FindOrFail($post);
         $comment = Comment::FindOrFail($comment);
         if ($comment->post_id != $post->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

         if($request->user()->id != $comment->user_id)
         {
             return response()->json(['message' => 'Unauthorized'], 403);
         }
         $comment->update([
             'body' => $request['body']
         ]);
         return new CommentResource($comment);
     }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $post,Request $request,string $comment)
    {

        $comment = Comment::FindOrFail($comment);
        if ($comment->post_id != $post) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        if($request->user()->id != $comment->user_id)
        {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $comment->delete();
        return response()->json(['message'=>'comment deleted']);
    }
}
