<?php

namespace Database\Factories;

use App\Models\DislikedPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DislikedPost>
 */
class DislikedPostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DislikedPost::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $post = Post::inRandomOrder()->first();
        // auth()->user()->increment('pp', 1);
        $post->increment('dislikes', 1);
        return [
            'post_id' => $post->id,
            'user_id' => User::inRandomOrder()->pluck('id')->first(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
