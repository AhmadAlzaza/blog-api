<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;

class TagTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_index(): void
    {
        $tag = Tag::factory()->create();
        $response = $this->getJson('/api/tags');
        $response->assertJsonStructure(['data' => [['id', 'name']]]);
        $response->assertStatus(200);
    }
    public function test_user_can_create_tag(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts/' . $post->id . '/tags', [
            'name' => Str::random(12)
        ]);
        $response->assertJsonStructure(['data' => ['id', 'name']]);
        $response->assertStatus(201);
    }
    public function test_user_can_update_tag(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $tag = Tag::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->putJson('/api/posts/' . $post->id . '/tags/' . $tag->id, [
            'name' => Str::random(12)
        ]);
        $response->assertJsonStructure(['data' => ['id', 'name']]);
        $response->assertStatus(201);
    }
    public function test_user_can_delete_tag(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $tag = Tag::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/posts/' . $post->id . '/tags/' . $tag->id);
        $response->assertJsonStructure(['message']);
        $response->assertStatus(200);
    }
}
