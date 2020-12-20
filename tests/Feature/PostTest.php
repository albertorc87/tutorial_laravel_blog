<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PostTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testMainPage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Testing create posts and checked if exists and can be visualed
     *
     * @return void
     */
    public function testCreatePost()
    {
        $user = User::factory()->create();
        $title = 'My test post';
        $response = $this->actingAs($user)->post('/admin/posts', [
            'title' => $title,
            'body' => 'This is a test functional post',
            'is_draft' => false
        ]);
        $response->assertSessionHas('status', 'Post has been created sucessfully');
        $response->assertRedirect();

        // Con unique == false no revisa en la base posts y entonces no se raya con que sea un duplicado
        $slug_url = SlugService::createSlug(Post::class, 'slug', $title, ['unique' => false]);

        $response = $this->get('/posts/' . $slug_url);
        $response->assertStatus(200);

        $title .= '2';
        $response = $this->actingAs($user)->post('/admin/posts', [
            'title' => $title,
            'body' => 'This is a test functional post',
            'is_draft' => true
        ]);
        $response->assertSessionHas('status', 'Post has been created sucessfully');
        $response->assertRedirect();

        // Con unique == false no revisa en la base posts y entonces no se raya con que sea un duplicado
        $slug_url = SlugService::createSlug(Post::class, 'slug', $title, ['unique' => false]);

        $response = $this->get('/posts/' . $slug_url);
        $response->assertStatus(404);

        $response = $this->actingAs($user)->post('/admin/posts', []);
        $response->assertSessionHasErrors([
            'title' => 'A title is required',
            'body' => 'You must sent a body',
            'is_draft' => 'You must sent if is draft or not'
        ]);
    }

    /**
     * Testing create posts and checked if exists and can be visualed
     *
     * @return void
     */
    public function testEditPost()
    {
        $user = User::factory()->create();
        $test_post = [
            'title' => 'test edit post',
            'body' => 'This is a test functional post',
            'is_draft' => false
        ];
        $response = $this->actingAs($user)->post('/admin/posts', $test_post);
        $response->assertSessionHas('status', 'Post has been created sucessfully');

        $test_post_update = [
            'title' => 'test post update',
            'body' => 'This is a test functional post update',
            'is_draft' => true
        ];
        // Como usamos el RefreshDatabase elimina los datos en cada test así que es el 1 si o si
        $response = $this->actingAs($user)->put('admin/posts/1', $test_post_update);
        $response->assertSessionHas('status', 'Post has been updated sucessfully');

        $ddbb_post = Post::find(1);

        $this->assertEquals($ddbb_post['title'], $test_post_update['title']);
        $this->assertEquals($ddbb_post['body'], $test_post_update['body']);
        $this->assertEquals($ddbb_post['is_draft'], $test_post_update['is_draft']);
    }

    /**
     * Testing create posts and checked if exists and can be visualed
     *
     * @return void
     */
    public function testDeletePost()
    {
        $user = User::factory()->create();
        $test_post = [
            'title' => 'test post delete',
            'body' => 'This is a test functional post',
            'is_draft' => false
        ];
        $response = $this->actingAs($user)->post('/admin/posts', $test_post);
        $response->assertSessionHas('status', 'Post has been created sucessfully');

        $post = Post::where('title', $test_post['title'])->first();

        $response = $this->actingAs($user)->delete('admin/posts/' . $post->id);
        $response->assertSessionHas('status', 'Post has been deleted sucessfully');

        $ddbb_post = Post::find($post->id);

        $this->assertNull($ddbb_post);
    }

    public function testEditPostStaffMember()
    {
        $user_admin = User::factory()->create();
        $test_post = [
            'title' => 'test post admin',
            'body' => 'This is a test functional post',
            'is_draft' => false
        ];
        $response = $this->actingAs($user_admin)->post('/admin/posts', $test_post);
        $response->assertSessionHas('status', 'Post has been created sucessfully');

        $post = Post::where('title', $test_post['title'])->first();

        $user_staff = $this->createStaffUser();

        $test_post_update = [
            'title' => 'test post update',
            'body' => 'This is a test functional post update',
            'is_draft' => true
        ];
        $response = $this->actingAs($user_staff)->put('admin/posts/' . $post->id, $test_post_update);
        $response->assertStatus(401);

        $ddbb_post = Post::find($post->id);

        $this->assertEquals($ddbb_post['title'], $test_post['title']);
        $this->assertEquals($ddbb_post['body'], $test_post['body']);
        $this->assertEquals($ddbb_post['is_draft'], $test_post['is_draft']);
    }

    public function testDeletePostStaffMember()
    {
        $user_admin = User::factory()->create();
        $test_post = [
            'title' => 'test post delete admin',
            'body' => 'This is a test functional post',
            'is_draft' => false
        ];
        $response = $this->actingAs($user_admin)->post('/admin/posts', $test_post);
        $response->assertSessionHas('status', 'Post has been created sucessfully');

        $post = Post::where('title', $test_post['title'])->first();

        $user_staff = $this->createStaffUser();

        $post = Post::where('title', $test_post['title'])->first();

        $response = $this->actingAs($user_staff)->delete('admin/posts/' . $post->id);

        $ddbb_post = Post::find($post->id);

        $this->assertNotNull($ddbb_post);
        $response->assertStatus(401);
    }

    private function createStaffUser()
    {
        $user_staff = new User;
        $user_staff->name = rand(1, 99) . 'Staff user';
        $user_staff->email = rand(1, 99) . 'staff@cosasdedevs.com';
        $user_staff->password = bcrypt('123456');
        $user_staff->is_staff = true;
        $user_staff->save();
        return $user_staff;
    }
}
