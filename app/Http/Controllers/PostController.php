<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Actions\Post\CreatePostAction;
use App\Actions\Post\UpdatePostAction;
use App\Actions\Post\DeletePostAction;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PostResource::collection(Post::with('user')->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request, CreatePostAction $action)
    {
        $post = $action->execute($request->user()->id, $request->only(['title', 'body']));

        return (new PostResource($post))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findOrFail($id);
        return new PostResource($post->load('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post, UpdatePostAction $action)
    {
        $this->authorize('update', $post);

        $updatedPost = $action->execute($post, $request->only(['title', 'body']));

        return new PostResource($updatedPost->load('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, DeletePostAction $action)
    {
        $this->authorize('delete', $post);

        $action->execute($post);

        return response()->json(['message' => 'Post deleted']);
    }
}
