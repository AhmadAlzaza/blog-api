<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Post::paginate(15)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(Request $request)
        {
            $validate = $request->validate([
                'title' => 'required',
                    'body' => 'required | min:30'
            ]);
                $post = Post::create([
                'user_id' => $request->user()->id,
                'title' => $validate['title'],
                'body' => $validate['body']
                ]);
                return response()->json($post);

            }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::FindOrFail($id);
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::FindOrFail($id);
        $validate = $request->validate([
            'title' => 'required',
                'body' => 'required | min:30'
        ]);
        if($post->user_id != $request->user()->id)
        {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->update([
           'title' => $validate['title'],
                'body' => $validate['body']

        ]);

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $post = Post::FindOrFail($id);
        if($post->user_id != $request->user()->id)
        {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $post->delete();
        return response()->json('Post deleted');

    }
}
