<?php

namespace Database\Factories;

use App\Models\Play\Activity;
use App\Models\Play\Post;
use App\Models\Play\Poll;
use App\Models\Learn\Academy\AcademyPost;
use App\Models\Learn\Course\Post\CoursePost;
use App\Models\Earn\Donate;
use App\Models\Earn\Support;
use App\Models\Earn\DonateRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Play\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1, // Assuming user with ID 1 exists
            'activity_type' => fake()->randomElement(['created', 'updated', 'liked', 'commented']),
            'activity_details' => fake()->sentence(),
            'activityable_type' => fake()->randomElement([
                'App\Models\Play\Post',
                'App\Models\Learn\Academy\AcademyPost',
                'App\Models\Learn\Course\Post\CoursePost',
                'App\Models\Earn\Donate',
                'App\Models\Earn\Support',
                'App\Models\Earn\DonateRecipient',
                'App\Models\Play\Poll'
            ]),
            'activityable_id' => fake()->numberBetween(1, 10),
            'action' => fake()->randomElement(['create', 'update', 'delete']),
        ];
    }

    /**
     * Create an activity for a post
     */
    public function forPost(): static
    {
        return $this->state(fn (array $attributes) => [
            'activityable_type' => 'App\Models\Play\Post',
            'activityable_id' => Post::factory(),
        ]);
    }

    /**
     * Create an activity for a poll
     */
    public function forPoll(): static
    {
        return $this->state(fn (array $attributes) => [
            'activityable_type' => 'App\Models\Play\Poll',
            'activityable_id' => Poll::factory(),
        ]);
    }

    /**
     * Create an activity for an academy post
     */
    public function forAcademyPost(): static
    {
        return $this->state(fn (array $attributes) => [
            'activityable_type' => 'App\Models\Learn\Academy\AcademyPost',
            'activityable_id' => AcademyPost::factory(),
        ]);
    }

    /**
     * Create an activity for a course post
     */
    public function forCoursePost(): static
    {
        return $this->state(fn (array $attributes) => [
            'activityable_type' => 'App\Models\Learn\Course\Post\CoursePost',
            'activityable_id' => CoursePost::factory(),
        ]);
    }
}