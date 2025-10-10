<?php

namespace Database\Factories;

use App\Models\LikedPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LikePost>
 */
class LikedPostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LikedPost::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $post = Post::inRandomOrder()->first();
        // auth()->user()->increment('pp', 1);
        $post->increment('likes', 1);
        return [
            'post_id' => $post->id,
            'user_id' => User::inRandomOrder()->first(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
