<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->First(),
            'content' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'read_status' => $this->faker->boolean(50), // 50% chance of being true
            'action_url' => $this->faker->url,
            'type' => $this->faker->randomElement(['message', 'like', 'comment', 'system']),
            'sender_id' => User::inRandomOrder()->First(),
            'related_id' => null,
            'metadata' => null,
        ];
    }
}
