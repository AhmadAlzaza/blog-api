<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\Post;
class PostTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_index(): void
    {

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200);
    }
    public function test_user_can_create_post():void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user,'sanctum')->postJson('/api/posts',[
            'title' => Str::random(10),
            'body' => Str::random(50)
        ]);
        $response->assertJsonStructure(['data'=>['id','user_name','title','body']]);
        $response->assertStatus(201);
    }
    public function test_user_can_update_post():void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user,'sanctum')->putJson('api/posts/' . $post->id,[
            'title' => Str::random(10),
            'body' => Str::random(50)
        ]);

        $response->assertJsonStructure(['data' => ['id','user_name','title','body']]);
        $response->assertStatus(200);
    }
    public function test_user_can_delete_post():void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id'=>$user->id]);
        $response = $this->actingAs($user,'sanctum')->deleteJson('/api/posts/'.$post->id);
        $response->assertJsonStructure(['message']);
        $response->assertStatus(200);
    }

}
