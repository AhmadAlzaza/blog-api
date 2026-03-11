<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $post)
    {
        $post = Post::FindOrFail($post);
        $comments = $post->comments()->paginate(15);
        return response()->json($comments);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $post,Request $request)
    {
        $post = Post::FindOrFail($post);
        $validate = $request->validate(['body'=>'required']);
        $comment = Comment::create(
            [
                'user_id' => $request->user()->id,
                'post_id' => $post->id,
                'body' => $validate['body']
            ]
        );
        return response()->json($comment);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $post,string $comment)
    {

        $comment = Comment::FindOrFail($comment);
        return response()->json($comment);
    }

    /**
     * Update the specified resource in storage.
     */



     public function update(Request $request, string $post,string $comment)
     {
         $post = Post::FindOrFail($post);
         $comment = Comment::FindOrFail($comment);
         if ($comment->post_id != $post->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }
         $validate = $request->validate([
             'body' => 'required'
         ]);
         if($request->user()->id != $comment->user_id)
         {
             return response()->json(['message' => 'Unauthorized'], 403);
         }
         $comment->update([
             'body' => $validate['body']
         ]);
         return response()->json($comment);
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
        return response()->json('comment deleted');
    }
}
