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
    public function test_user_can_create_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts', [
            'title' => Str::random(10),
            'body' => Str::random(50)
        ]);
        $response->assertJsonStructure(['data' => ['id', 'user_name', 'title', 'body']]);
        $response->assertStatus(201);
    }
    public function test_user_can_update_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user, 'sanctum')->putJson('api/posts/' . $post->id, [
            'title' => Str::random(10),
            'body' => Str::random(50)
        ]);

        $response->assertJsonStructure(['data' => ['id', 'user_name', 'title', 'body']]);
        $response->assertStatus(200);
    }
    public function test_user_can_delete_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/posts/' . $post->id);
        $response->assertJsonStructure(['message']);
        $response->assertStatus(200);
    }
    public function test_user_cannot_update_others_post(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser, 'sanctum')->putJson('api/posts/' . $post->id, [
            'title' => Str::random(10),
            'body' => Str::random(50)
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Unauthorized']);
    }
    public function test_user_cannot_delete_others_post(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser, 'sanctum')->deleteJson('/api/posts/' . $post->id);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Unauthorized']);
    }
    public function test_creating_post_without_title_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts', [
            'body' => Str::random(50)
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_creating_post_without_body_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts', [
            'title' => Str::random(10)
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['body']);
    }

    public function test_creating_post_with_short_body_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts', [
            'title' => Str::random(10),
            'body' => Str::random(10) // أقل من 30
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['body']);
    }

    public function test_updating_post_with_empty_title_fails_validation(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/posts/' . $post->id, [
            'title' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_updating_post_with_short_body_fails_validation(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/posts/' . $post->id, [
            'body' => Str::random(10)
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['body']);
    }

    public function test_updating_post_with_no_fields_is_allowed(): void
    {

        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/posts/' . $post->id, []);

        $response->assertStatus(200);
    }
}
