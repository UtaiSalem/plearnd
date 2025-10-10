<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
        /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $sentence = $this->faker->sentence($nbWords = 6, $variableNbWords = true);
        return [
            // 'content'       => $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true),
            // 'user_id'       => $user->id,
            // 'public'        => 1,
            // 'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            
            'user_id'           => $user->id,
            'title'             => $sentence,
            'content'           => $this->faker->paragraphs(3, true),
            'status'            => $this->faker->randomElement(['published', 'muted', 'draft', 'pending', 'deleted']),
            'likes'             => $this->faker->numberBetween(0, 1000),
            'comments'          => $this->faker->numberBetween(0, 500),
            'shares'            => $this->faker->numberBetween(0, 200),
            'views'             => $this->faker->numberBetween(0, 10000),
            'hashtags'          => $this->faker->words(3, true),
            'privacy_settings'  => $this->faker->randomElement(['public', 'private']),
            'location'          => $this->faker->address,
            'url'               => $this->faker->url,
            'tags'              => $this->faker->userName,
            'sentiment'         => $this->faker->randomElement(['positive', 'negative', 'neutral']),
            'engagement_rate'   => $this->faker->randomFloat(2, 0, 1),
            'post_type'         => $this->faker->randomElement(['text', 'photo', 'video', 'poll']),
            'source_platform'   => $this->faker->randomElement(['Facebook', 'Twitter', 'Instagram']),
            'parent_post_id'    => null,
            'meta'              => json_encode([
                'title'         => $sentence,
                'author'        => $user
            ]),
        ];
    }
}
