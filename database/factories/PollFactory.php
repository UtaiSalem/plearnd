<?php

namespace Database\Factories;

use App\Models\Play\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Play\Poll>
 */
class PollFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Poll::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1, // Assuming user with ID 1 exists
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'end_date' => fake()->dateTimeBetween('now', '+1 month'),
            'is_active' => 1,
            'is_public' => 1,
            'max_votes' => fake()->numberBetween(1, 100),
            'is_multiple_choice' => fake()->boolean(),
            'total_votes' => fake()->numberBetween(0, 50),
            'image_url' => fake()->imageUrl(),
        ];
    }
}