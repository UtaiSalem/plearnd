<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Community>
 */
class CommunityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Community::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'creator_id' => User::inRandomOrder()->first(),
            'creation_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'privacy_settings' => $this->faker->randomElement(['public', 'private', 'invite-only']),
            'category' => $this->faker->word,
            'member_count' => $this->faker->numberBetween(0, 1000),
            'rules' => $this->faker->paragraph,
            'community_picture' => $this->faker->imageUrl(200, 200, 'people'),
            'metadata' => null,
        ];
    }
}
