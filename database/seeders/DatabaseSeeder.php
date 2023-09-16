<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Poll;
use App\Models\Post;
use App\Models\User;
use App\Models\Activity;
use App\Models\LikedPost;
use App\Models\PostComment;
use App\Models\DislikedPost;
use Illuminate\Database\Seeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\LessonSeeder;
use Database\Seeders\ActivitySeeder;
use Database\Seeders\LikePostSeeder;
use Database\Seeders\UserTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        // User::factory(5)->create();
        // Post::factory(15)->create();
        // Poll::factory(8)->create();
        // LikedPost::factory(10)->create();
        // DislikedPost::factory(10)->create();
        // PostComment::factory(20)->create();
        // Activity::factory(20)->create();

        // $this->call(UserTableSeeder::class);
        // $this->call(PostSeeder::class);
        // $this->call(PollSeeder::class);
        // $this->call(ActivitySeeder::class);
        // $this->call(LikePostSeeder::class);
        $this->call(LessonSeeder::class);
    }
}
