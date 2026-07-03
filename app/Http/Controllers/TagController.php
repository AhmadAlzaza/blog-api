<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Actions\Tag\AttachTagAction;
use App\Actions\Tag\UpdateTagAction;
use App\Actions\Tag\DetachTagAction;

class TagController extends Controller
{
    /**
     * عرض جميع الوسوم (عام).
     */
    public function index()
    {
        return TagResource::collection(Tag::paginate(15));
    }

    /**
     * إضافة وسم إلى منشور (فقط لمالك المنشور).
     */
    public function store(StoreTagRequest $request, Post $post, AttachTagAction $action)
    {
        $this->authorize('update', $post);

        // فحص ما إذا كان التاغ بنفس الاسم مرتبطاً مسبقاً
        if ($post->tags()->where('name', $request->name)->exists()) {
            return response()->json(['message' => 'Tag already attached to this post'], 409);
        }

        $tag = $action->execute($post, $request->name);

        return (new TagResource($tag))->response()->setStatusCode(201);
    }

    /**
     * عرض وسم مرتبط بمنشور معين.
     */
    public function show(Post $post, Tag $tag)
    {
        if (!$post->tags()->where('tag_id', $tag->id)->exists()) {
            abort(404);
        }

        return new TagResource($tag);
    }

    /**
     * تعديل اسم وسم مرتبط بمنشور (فقط لمالك المنشور).
     */
    public function update(UpdateTagRequest $request, Post $post, Tag $tag, UpdateTagAction $action)
    {
        $this->authorize('update', $post);

        if (!$post->tags()->where('tag_id', $tag->id)->exists()) {
            return response()->json(['message' => 'Tag not found for this post'], 404);
        }

        $updatedTag = $action->execute($tag, $request->name);

        return (new TagResource($updatedTag))->response()->setStatusCode(200);
    }

    /**
     * حذف وسم من منشور (فقط لمالك المنشور).
     */
    public function destroy(Post $post, Tag $tag, DetachTagAction $action)
    {
        $this->authorize('delete', $post);

        if (!$post->tags()->where('tag_id', $tag->id)->exists()) {
            return response()->json(['message' => 'This tag not belong to this post'], 404);
        }

        $action->execute($post, $tag);

        return response()->json(['message' => 'Tag deleted'], 200);
    }
}
