<?php

namespace Database\Factories;

use App\Models\Earn\Support;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Earn\Support>
 */
class SupportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Support::class;

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
            'target_amount' => fake()->numberBetween(100, 10000),
            'collected_amount' => fake()->numberBetween(0, 5000),
            'status' => 1, // Active
            'remaining_views' => fake()->numberBetween(0, 1000),
        ];
    }
}