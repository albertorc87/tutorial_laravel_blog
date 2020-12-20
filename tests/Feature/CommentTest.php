<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateComment()
    {
        $user = User::factory()->create();
        $test_post = [
            'title' => 'My test post with comment',
            'body' => 'This is a test functional post',
            'is_draft' => false
        ];
        $response = $this->actingAs($user)->post('/admin/posts', $test_post);
        $response->assertSessionHas('status', 'Post has been created sucessfully');

        $post = Post::where('title', $test_post['title'])->first();

        $comment = 'This is a test comment';
        $test_comment = [
            'post_id' => $post->id,
            'comment' => $comment
        ];
        $response = $this->actingAs($user)->post('/comment', $test_comment);
        $response->assertSessionHas('status', 'Comment has been created sucessfully');

        $comment = Comment::where('user_id', $user->id)
        ->where('post_id', $post->id)
        ->where('comment', $comment)->first();

        $this->assertNotNull($comment);

        $this->post('/logout');

        // Ahora probamos con un usuario sin loguear
        $response = $this->post('/comment', $test_comment);
        $response->assertStatus(403);
    }
}
