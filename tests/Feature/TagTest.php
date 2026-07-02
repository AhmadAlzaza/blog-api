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
        $response->assertStatus(200);
    }
    public function test_attaching_same_tag_twice_returns_conflict(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $tagName = Str::random(12);

        $this->actingAs($user, 'sanctum')->postJson('/api/posts/' . $post->id . '/tags', [
            'name' => $tagName,
        ])->assertStatus(201);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts/' . $post->id . '/tags', [
            'name' => $tagName,
        ]);

        $response->assertStatus(409);
        $response->assertJson(['message' => 'Tag already attached to this post']);
    }

    public function test_user_can_delete_tag(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $tag = Tag::factory()->create();
        $post->tags()->attach($tag->id);
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/posts/' . $post->id . '/tags/' . $tag->id);
        $response->assertJsonStructure(['message']);
        $response->assertStatus(200);
    }
    public function test_user_cannot_create_tag_on_others_post(): void
    {
        $postOwner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $postOwner->id]);

        $response = $this->actingAs($otherUser, 'sanctum')->postJson('/api/posts/' . $post->id . '/tags', [
            'name' => Str::random(12)
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Unauthorized']);
    }
    public function test_user_cannot_update_tag_on_others_post(): void
    {
        $postOwner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $postOwner->id]);
        $tag = Tag::factory()->create();
        $post->tags()->attach($tag->id);

        $response = $this->actingAs($otherUser, 'sanctum')->putJson('/api/posts/' . $post->id . '/tags/' . $tag->id, [
            'name' => Str::random(12)
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Unauthorized']);
    }
    public function test_user_cannot_delete_tag_on_others_post(): void
    {
        $postOwner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $postOwner->id]);
        $tag = Tag::factory()->create();
        $post->tags()->attach($tag->id);

        $response = $this->actingAs($otherUser, 'sanctum')->deleteJson('/api/posts/' . $post->id . '/tags/' . $tag->id);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Unauthorized']);
    }
    public function test_creating_tag_without_name_fails_validation(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts/' . $post->id . '/tags', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_updating_tag_with_empty_name_fails_validation(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $tag = Tag::factory()->create();
        $post->tags()->attach($tag->id);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/posts/' . $post->id . '/tags/' . $tag->id, [
            'name' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }
}
