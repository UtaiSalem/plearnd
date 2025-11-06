<?php

namespace Database\Factories;

use App\Models\Play\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Play\Post>
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1, // Assuming user with ID 1 exists
            'content' => fake()->paragraph(),
            'status' => 1, // Published
            'privacy_settings' => fake()->randomElement([2, 3]), // Friends or Public
        ];
    }
}