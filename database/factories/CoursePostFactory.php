<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Course;
use App\Models\Academy;
use App\Models\CoursePost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoursePost>
 */
class CoursePostFactory extends Factory
{
    protected $model = CoursePost::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $course = Course::inRandomOrder()->first();
        // $course_post = CoursePost::create([
        //     // 'user_id' => User::inRandomOrder()->pluck('id')->first(),
        //     'user_id' => User::query()->inRandomOrder()->first()?->id,
        //     'academy_id' => Academy::inRandomOrder()->first()->id,
        //     'course_id' => $course->id,
        //     'group_id' => $course->courseGroups()->inRandomOrder()->pluck('id')->first(),
        //     'content' => $this->faker->text,
        //     'status' => 1,
        //     'likes' => $this->faker->numberBetween(0, 100),
        //     'dislikes' => $this->faker->numberBetween(0, 100),
        //     'comments' => $this->faker->numberBetween(0, 100),
        //     'shares' => $this->faker->numberBetween(0, 100),
        //     'views' => $this->faker->numberBetween(0, 100),
        //     'hashtags' => $this->faker->words,
        //     'privacy_settings' => 3,
        //     'location' => $this->faker->city,
        //     'tags' => $this->faker->words,
        //     'sentiment' => $this->faker->word,
        //     'engagement_rate' => $this->faker->randomFloat(2, 0, 100),
        //     'post_type' => $this->faker->word,
        //     'source_platform' => $this->faker->word,
        //     'interacted_at' => $this->faker->dateTime,
        //     'meta' => $this->faker->words,
        // ]);

        // $course_activity = $course_post->activity()->create([
        //     'user_id' => $course_post->user_id,
        //     'activity_type' => 'createpost',
        //     'action' => 'createpost',
        //     'activity_details' => 'created a post',
        // ]);

        // return $course_post->toArray();
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'academy_id' => Academy::inRandomOrder()->first()->id,
            'course_id' => $course->id,
            'group_id' => $course->courseGroups()->inRandomOrder()->pluck('id')->first(),
            'content' => $this->faker->text,
            'status' => 1,
            'likes' => $this->faker->numberBetween(0, 100),
            'dislikes' => $this->faker->numberBetween(0, 100),
            'comments' => $this->faker->numberBetween(0, 100),
            'shares' => $this->faker->numberBetween(0, 100),
            'views' => $this->faker->numberBetween(0, 100),
            'hashtags' => $this->faker->words,
            'privacy_settings' => 3,
            'location' => $this->faker->city,
            'tags' => $this->faker->words,
            'sentiment' => $this->faker->word,
            'engagement_rate' => $this->faker->randomFloat(2, 0, 100),
            'post_type' => $this->faker->word,
            'source_platform' => $this->faker->word,
            'interacted_at' => $this->faker->dateTime,
            'meta' => $this->faker->words,
        ];
    }
}
