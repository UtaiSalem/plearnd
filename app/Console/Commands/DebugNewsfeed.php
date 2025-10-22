<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Play\Post;
use App\Models\Learn\AcademyPost;
use App\Models\Learn\CoursePost;
use App\Models\Earn\Donate;
use App\Models\Earn\Support;
use App\Models\Earn\DonateRecipient;
use App\Models\Play\Activity;

class DebugNewsfeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug-newsfeed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Posts count: ' . Post::count());
        $this->info('AcademyPosts count: ' . AcademyPost::count());
        $this->info('CoursePosts count: ' . CoursePost::count());
        $this->info('Donates count: ' . Donate::count());
        $this->info('Supports count: ' . Support::count());
        $this->info('DonateRecipients count: ' . DonateRecipient::count());
        
        $this->info('Total activities: ' . Activity::count());
        // Check both old and new namespace formats
        $this->info('Activities with Post: ' . Activity::where('activityable_type', 'App\Models\Play\Post')->count());
        $this->info('Activities with old Post: ' . Activity::where('activityable_type', 'App\Models\Post')->count());
        
        $this->info('Activities with AcademyPost: ' . Activity::where('activityable_type', 'App\Models\Learn\AcademyPost')->count());
        $this->info('Activities with old AcademyPost: ' . Activity::where('activityable_type', 'App\Models\AcademyPost')->count());
        
        $this->info('Activities with CoursePost: ' . Activity::where('activityable_type', 'App\Models\Learn\CoursePost')->count());
        $this->info('Activities with old CoursePost: ' . Activity::where('activityable_type', 'App\Models\CoursePost')->count());
        
        $this->info('Activities with Donate: ' . Activity::where('activityable_type', 'App\Models\Earn\Donate')->count());
        $this->info('Activities with old Donate: ' . Activity::where('activityable_type', 'App\Models\Donate')->count());
        
        $this->info('Activities with Support: ' . Activity::where('activityable_type', 'App\Models\Earn\Support')->count());
        $this->info('Activities with old Support: ' . Activity::where('activityable_type', 'App\Models\Support')->count());
        
        $this->info('Activities with DonateRecipient: ' . Activity::where('activityable_type', 'App\Models\Earn\DonateRecipient')->count());
        $this->info('Activities with old DonateRecipient: ' . Activity::where('activityable_type', 'App\Models\DonateRecipient')->count());
        
        // Get a sample activity
        $activity = Activity::first();
        if ($activity) {
            $this->info('Sample activity:');
            $this->info('  ID: ' . $activity->id);
            $this->info('  User ID: ' . $activity->user_id);
            $this->info('  Activityable Type: ' . $activity->activityable_type);
            $this->info('  Activityable ID: ' . $activity->activityable_id);
        }
    }
}
