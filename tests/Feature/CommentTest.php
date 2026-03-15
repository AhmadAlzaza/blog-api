<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\Comment;

class CommentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_can_user_make_index(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id'=>$user->id]);
        $response = $this->actingAs($user,'sanctum')->getJson('/api/posts/'.$post->id.'/comments');
        $response->assertStatus(200);
    }
    public function test_can_user_create_comment():void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id'=>$user->id]);
        $response = $this->actingAs($user,'sanctum')->postJson('/api/posts/'.$post->id.'/comments',[
            'body' => Str::random(50)
        ]);
        $response->assertJsonStructure(['data'=>['id','user_name','post_title','body']]);
        $response->assertStatus(201);
    }
    public function test_user_can_update_comment():void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id'=>$user->id]);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        $response = $this->actingAs($user,'sanctum')->putJson('/api/posts/'.$post->id.'/comments/'.$comment->id,[
            'body' => Str::random(50)
        ]);
        $response->assertJsonStructure(['data'=>['id','user_name','post_title','body']]);
        $response->assertStatus(200);
    }
    public function test_user_can_delete_comment():void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id'=>$user->id]);
        $comment = Comment::factory()->create(['user_id'=>$user->id,'post_id'=>$post->id]);
        $response = $this->actingAs($user,'sanctum')->deleteJson('/api/posts/'.$post->id.'/comments/'.$comment->id);
        $response->assertJsonStructure(['message']);
        $response->assertStatus(200);
    }
}
