<?php

namespace Database\Factories;

use App\Models\PostComment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class PostCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
    */
    protected $model = PostComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::inRandomOrder()->first(),
            'user_id' => User::inRandomOrder()->first(),
            'parent_post_comment_id' => null,
            'content' => $this->faker->sentence(),
            'likes' => $this->faker->numberBetween(0, 100),
            'replies' => $this->faker->numberBetween(0, 20),
            'sentiment' => $this->faker->randomElement(['positive', 'negative', 'neutral']),
            'privacy_settings' => $this->faker->randomElement(['public', 'private']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
