<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $course = Course::inRandomOrder()->first();
        return [
            'course_id' => $course->id,
            'creater' => $user->id,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'content' => $this->faker->text,
            'duration' => $this->faker->randomElement(['30 minutes', '1 hour', '2 hours']),
            'order' => $this->faker->randomNumber(2),
            'status' => $this->faker->randomElement(['Draft', 'Published', 'Retired']),
        ];
    }
}
