<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'name' => $this->faker->name(),
            // 'email' => $this->faker->unique()->safeEmail(),
            // 'email_verified_at' => now(),
            // 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            // 'two_factor_secret' => null,
            // 'two_factor_recovery_codes' => null,
            // 'remember_token' => Str::random(10),
            // 'profile_photo_path' => null,
            // 'current_team_id' => null,
            
            'name'              => $name = $this->faker->name(),
            'username'          => $name,
            'pp'                => $this->faker->numberBetween(0, 10000),
            'wallet'            => $this->faker->numberBetween(0, 10000),
            'email'             => $this->faker->unique()->safeEmail(),
            'password'          => bcrypt('password'), // Replace 'password' with your desired default password
            'full_name'         => $name,
            'profile_photo_path'=> $this->faker->imageUrl(200, 200, 'people'),
            'profile_cover_path'=> $this->faker->imageUrl(200, 200, 'nature'),
            'bio'               => $this->faker->sentence,
            'location'          => $this->faker->city,
            'website'           => $this->faker->url,
            'birthdate'         => $this->faker->date,
            'gender'            => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'privacy_settings'  => $this->faker->randomElement(['public', 'private', 'friends-only']),
            'followers'         => $this->faker->numberBetween(0, 1000),
            'following'         => $this->faker->numberBetween(0, 1000),
            'friends'           => $this->faker->numberBetween(0, 1000),
            'join_date'         => $this->faker->dateTimeBetween('-5 years', 'now'),
            'last_login'        => $this->faker->dateTimeBetween('-1 year', 'now'),
            'verified'          => $this->faker->boolean(80), // 80% chance of being true
            'email_verified_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'account_status'    => $this->faker->randomElement(['active', 'suspended', 'deactivated']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(callable $callback = null): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name.'\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }
}
