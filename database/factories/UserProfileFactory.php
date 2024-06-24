<?php

namespace Database\Factories;

use App\Models\UserProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'bio' => $this->faker->paragraph,
            'birthdate' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'location' => $this->faker->address,
            'website' => $this->faker->url,
            'interests' => $this->faker->words(3, true),
            'profile_picture' => $this->faker->imageUrl(200, 200, 'people'),
            'cover_image' => $this->faker->imageUrl(1200, 400, 'nature'),
            'social_media_links' => [
                'facebook' => $this->faker->url,
                'twitter' => $this->faker->url,
                'instagram' => $this->faker->url,
            ],
            'privacy_settings' => 'public',
            'metadata' => null,
        ];
    }
}
