<?php

namespace Database\Factories;

use App\Models\PostImage;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PostImage::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $post = Post::inRandomOrder()->first();
        return [
            'post_id' => $post_id,
            'filename' => $this->faker->word . '.jpg',
            'filepath' => 'images/' . $this->faker->image('public/storage/images', 640, 480, null, false),
            'caption' => $this->faker->sentence(),
            'order' => $this->faker->numberBetween(0, 10),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
