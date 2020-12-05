<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Alber',
            'email' => 'alber@cosasdedevs.com',
            'password' => bcrypt('admin123'),
            'is_admin' => true,
            'is_staff' => true,
        ]);

        Post::factory()->count(50)->create();
    }
}
