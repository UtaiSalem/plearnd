<?php

namespace Database\Seeders;

use App\Models\Play\Activity;
use App\Models\Play\Post;
use App\Models\Play\Poll;
use App\Models\Learn\Academy\AcademyPost;
use App\Models\Learn\Course\Post\CoursePost;
use App\Models\Earn\Donate;
use App\Models\Earn\Support;
use App\Models\Earn\DonateRecipient;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample posts first
        $posts = \App\Models\Play\Post::factory(5)->create();
        
        // Create some sample polls
        $polls = \App\Models\Play\Poll::factory(3)->create();
        
        // Create some sample academy posts
        $academyPosts = \App\Models\Learn\Academy\AcademyPost::factory(3)->create();
        
        // Create some sample course posts
        $coursePosts = \App\Models\Learn\Course\Post\CoursePost::factory(3)->create();
        
        // Create some sample donations
        $donates = \App\Models\Earn\Donate::factory(2)->create();
        
        // Create some sample supports
        $supports = \App\Models\Earn\Support::factory(2)->create();
        
        // Create some sample donate recipients
        $donateRecipients = \App\Models\Earn\DonateRecipient::factory(2)->create();
        
        // Create activities for posts
        foreach ($posts as $post) {
            Activity::factory()->create([
                'activityable_type' => 'App\Models\Play\Post',
                'activityable_id' => $post->id,
                'activity_type' => 'created',
                'activity_details' => 'Created a new post',
            ]);
        }
        
        // Create activities for polls
        foreach ($polls as $poll) {
            Activity::factory()->create([
                'activityable_type' => 'App\Models\Play\Poll',
                'activityable_id' => $poll->id,
                'activity_type' => 'created',
                'activity_details' => 'Created a new poll',
            ]);
        }
        
        // Create activities for academy posts
        foreach ($academyPosts as $academyPost) {
            Activity::factory()->create([
                'activityable_type' => 'App\Models\Learn\Academy\AcademyPost',
                'activityable_id' => $academyPost->id,
                'activity_type' => 'created',
                'activity_details' => 'Created a new academy post',
            ]);
        }
        
        // Create activities for course posts
        foreach ($coursePosts as $coursePost) {
            Activity::factory()->create([
                'activityable_type' => 'App\Models\Learn\Course\Post\CoursePost',
                'activityable_id' => $coursePost->id,
                'activity_type' => 'created',
                'activity_details' => 'Created a new course post',
            ]);
        }
        
        // Create activities for donations
        foreach ($donates as $donate) {
            Activity::factory()->create([
                'activityable_type' => 'App\Models\Earn\Donate',
                'activityable_id' => $donate->id,
                'activity_type' => 'created',
                'activity_details' => 'Created a new donation',
            ]);
        }
        
        // Create activities for supports
        foreach ($supports as $support) {
            Activity::factory()->create([
                'activityable_type' => 'App\Models\Earn\Support',
                'activityable_id' => $support->id,
                'activity_type' => 'created',
                'activity_details' => 'Created a new support',
            ]);
        }
        
        // Create activities for donate recipients
        foreach ($donateRecipients as $donateRecipient) {
            Activity::factory()->create([
                'activityable_type' => 'App\Models\Earn\DonateRecipient',
                'activityable_id' => $donateRecipient->id,
                'activity_type' => 'created',
                'activity_details' => 'Added a new donate recipient',
            ]);
        }
    }
}