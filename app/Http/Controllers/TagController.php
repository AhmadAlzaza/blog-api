<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Post;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tag = Tag::paginate(15);
        return response()->json($tag);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request,string $post_id)
    {
        $post = Post::FindOrFail($post_id);
         $tag = Tag::firstOrCreate(['name' => $request['name'] ]);
        $post->tags()->attach($tag->id);
        return response()->json($post->tags);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $post,string $id)
    {
        $tag = Tag::FindOrFail($id);
        $post = Post::FindOrFail($post);
        if($post->tags()->where('tag_id',$tag->id)->exists())
        {
            return response()->json($tag);
        }
        else{
            return response()->json(['message' => 'No tag for this post']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request,string $post, string $id)
    {

        $post = Post::FindOrFail($post);
        $tag = Tag::FindorFail($id);
        $post->tags()->detach($tag->id);
        if($tag->posts()->count()==0)
        {
            $tag->delete();
        }
        $newtag = Tag::firstOrCreate(['name' => $request['name'] ]);
        $post->tags()->attach($newtag->id);

        return response()->json($newtag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $post,string $id)
    {
        $tag = Tag::FindOrFail($id);
        $post = Post::FindOrFail($post);
        if($post->tags()->where('tag_id',$tag->id)->exists())
        {
            $post->tags()->detach($tag->id);
            return response()->json(['message'=>'Tag deleted']);
        }
        else
        {
            return response()->json(['message'=>'This tag not belong to this post ']);
        }
    }
}
