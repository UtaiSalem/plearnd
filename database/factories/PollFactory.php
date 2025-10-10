<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Poll>
 */
class PollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $user = User::inRandomOrder()->first();
        return [
            'user_id'            => User::inRandomOrder()->first()->id,
            'title'              => $this->faker->sentence,
            'description'        => $this->faker->paragraph,
            'start_date'         => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_date'           => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'is_active'          => $this->faker->boolean(70),
            'is_public'          => $this->faker->boolean(80),
            'max_votes'          => $this->faker->numberBetween(1, 10),
            'is_multiple_choice' => $this->faker->boolean(50),
            'total_votes'        => $this->faker->numberBetween(0, 1000),
            'image_url'          => $this->faker->imageUrl(),
        ];
    }
}
