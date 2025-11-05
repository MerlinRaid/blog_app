<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //User::factory()->create([
            //name' => 'Test User',
            //'email' => 'test@example.com',
        //]);

        $this->call(RolesAndPermissionsSeeder::class);

        Category::factory(5)->create();
        Tag::factory(12)->create();
        Post::factory(25)->create();
        Comment::factory(60)->create();
    }
}
