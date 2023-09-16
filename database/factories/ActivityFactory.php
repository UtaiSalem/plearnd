<?php

namespace Database\Factories;

use App\Models\Poll;
use App\Models\Post;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
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
        $user = User::inRandomOrder()->first();
        $post = Post::inRandomOrder()->first();
        $poll = Poll::inRandomOrder()->first();
        $collection = collect(['like','unlike', 'comment', 'share']);
        $model = collect([$post,$poll]);

        $activity = new Activity();
        $activity->user_id = $user->id;
        $activity->activity_type = $collection->random();
        $activity->activityable()->associate($model->random());
        $activity->save();
        
        return [
            'user_id'           => $activity->user_id,
            'activity_type'     => $activity->activity_type,
            'activity_details'  => $this->faker->sentence(),
            'activityable_id'    => $activity->activityable_id,
            'activityable_type'  => $activity->activityable_type,
            'created_at'        => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
