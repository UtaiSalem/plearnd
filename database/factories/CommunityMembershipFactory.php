<?php

namespace Database\Factories;

use App\Models\CommunityMembership;
use App\Models\Community;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommunityMembership>
 */
class CommunityMembershipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CommunityMembership::class;
 
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'community_id'  => Community::inRandomOrder()->first(),
            'user_id'       => User::inRandomOrder()->first(),
            'role'          => 'member',
            'join_date'     => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status'        => 'active',
            'last_active'   => $this->faker->dateTimeBetween('-1 month', 'now'),
            'metadata'      => null,
        ];
    }
}
