<?php

namespace Database\Factories;

use App\Models\CoursePost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoursePostImage>
 */
class CoursePostImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $coursePost = CoursePost::inRandomOrder()->first();
        $image = $this->faker->imageUrl();
        return [
            'course_post_id' => $coursePost->id,
            'filename' => $image,
            'order' => $this->faker->numberBetween(1, 10),
            'likes' => $this->faker->numberBetween(1, 100),
            'dislikes' => $this->faker->numberBetween(1, 100),
            'comments' => $this->faker->numberBetween(1, 100),
            'shares' => $this->faker->numberBetween(1, 100),
            'views' => $this->faker->numberBetween(1, 100),
        ];
    }
}
