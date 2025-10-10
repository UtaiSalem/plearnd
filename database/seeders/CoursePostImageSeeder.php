<?php

namespace Database\Seeders;

use App\Models\CoursePost;
use App\Models\CoursePostImage;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CoursePostImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coursePosts = CoursePost::all();

        $coursePosts->each(function ($coursePost) {
            $coursePost->post_images()->saveMany(
                CoursePostImage::factory()->count(rand(1, 3))->make()
            );
        });
    }
}
