<?php

namespace Database\Factories;

use App\Models\Earn\DonateRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Earn\DonateRecipient>
 */
class DonateRecipientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DonateRecipient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1, // Assuming user with ID 1 exists
            'name' => fake()->name(),
            'description' => fake()->paragraph(),
            'target_amount' => fake()->numberBetween(100, 10000),
            'collected_amount' => fake()->numberBetween(0, 5000),
            'status' => 1, // Active
        ];
    }
}