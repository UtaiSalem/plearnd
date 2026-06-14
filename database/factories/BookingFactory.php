<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startAt = fake()->dateTimeBetween('now', '+1 month');
        $endAt = (clone $startAt)->modify('+'.fake()->numberBetween(1, 4).' hours');

        return [
            'room_id' => Room::factory(),
            'user_id' => User::factory(),
            'requester_name' => fn (array $attributes) => User::find($attributes['user_id'])->name,
            'requester_type' => fn (array $attributes) => User::find($attributes['user_id'])->user_type ?? 'student',
            'requester_identifier' => fn (array $attributes) => User::find($attributes['user_id'])->identifier(),
            'faculty' => 'คณะวิทยาศาสตร์และเทคโนโลยี',
            'department' => fn (array $attributes) => User::find($attributes['user_id'])->department,
            'phone' => fn (array $attributes) => User::find($attributes['user_id'])->phone,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'attendees' => fake()->numberBetween(5, 50),
            'purpose' => fake()->sentence(),
            'status' => 'pending',
            'staff_status' => 'scheduled',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => User::factory()->staff(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_reason' => fake()->sentence(),
            'reviewed_at' => now(),
            'reviewed_by' => User::factory()->staff(),
        ]);
    }
}
