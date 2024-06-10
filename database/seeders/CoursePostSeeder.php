<?php

namespace Database\Seeders;

use App\Models\CoursePost;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CoursePostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $course_post = CoursePost::factory()->count(50)->create();

        foreach ($course_post as $post) {
            $post->activity()->create([
                'user_id' => $post->user_id,
                'activity_type' => 'createpost',
                'action' => 'createpost',
                'activity_details' => 'created a post',
            ]);
        }
    }
}
