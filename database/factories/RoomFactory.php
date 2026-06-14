<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('LAB-###'),
            'name' => 'ห้องปฏิบัติการ ' . fake()->word(),
            'building' => fake()->randomElement(['อาคาร 1', 'อาคาร 2', 'อาคาร 3']),
            'floor' => fake()->numberBetween(1, 5),
            'category' => fake()->randomElement(['Computer', 'Science', 'Language']),
            'capacity' => fake()->numberBetween(20, 50),
            'manager_user_id' => User::factory()->staff(),
            'manager_name' => fn (array $attributes) => User::find($attributes['manager_user_id'])->name,
            'contact' => fake()->phoneNumber(),
            'status' => 'available',
            'open_hours' => '08:30 - 16:30',
            'summary' => fake()->paragraph(),
            'is_bookable' => true,
            'color' => fake()->safeHexColor(),
        ];
    }
}
