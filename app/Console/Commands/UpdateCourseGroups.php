<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\CourseGroup;
use Illuminate\Support\Facades\DB;

class UpdateCourseGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:update-groups {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update course group counts based on actual course_groups data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting course group count update process...');
        
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('📋 DRY RUN MODE - No changes will be made');
        }
        
        // Get all courses with their current group counts
        $courses = Course::select('id', 'name', 'groups')->get();
        
        $this->info("\n📊 Found {$courses->count()} courses to check");
        
        $updatesNeeded = 0;
        $totalDiscrepancy = 0;
        
        foreach ($courses as $course) {
            // Count actual groups from course_groups table
            $actualCount = CourseGroup::where('course_id', $course->id)->count();
            $currentCount = $course->groups;
            
            if ($actualCount != $currentCount) {
                $updatesNeeded++;
                $discrepancy = $actualCount - $currentCount;
                $totalDiscrepancy += abs($discrepancy);
                
                $this->line("\n📚 Course: {$course->name} (ID: {$course->id})");
                $this->line("   Current groups: {$currentCount}");
                $this->line("   Actual groups: {$actualCount}");
                $this->line("   Discrepancy: " . ($discrepancy > 0 ? '+' : '') . $discrepancy);
                
                if (!$isDryRun) {
                    // Update the group count
                    $course->groups = $actualCount;
                    $course->save();
                    $this->info("   ✅ Updated group count to {$actualCount}");
                } else {
                    $this->warn("   ⚠️  Would update group count to {$actualCount}");
                }
            }
        }
        
        $this->info("\n📈 Summary:");
        $this->line("   Total courses checked: {$courses->count()}");
        $this->line("   Courses needing updates: {$updatesNeeded}");
        $this->line("   Total discrepancy resolved: {$totalDiscrepancy}");
        
        if ($isDryRun) {
            $this->warn("\n💡 Run without --dry-run flag to apply these changes");
        } else {
            $this->info("\n✅ Course group count update completed successfully!");
        }
        
        return Command::SUCCESS;
    }
}