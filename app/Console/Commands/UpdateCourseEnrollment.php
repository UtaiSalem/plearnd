<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\CourseMember;
use Illuminate\Support\Facades\DB;

class UpdateCourseEnrollment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:update-enrollment {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update course enrollment counts based on actual course_members data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting course enrollment update process...');
        
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('📋 DRY RUN MODE - No changes will be made');
        }
        
        // Get all courses with their current enrollment counts
        $courses = Course::select('id', 'name', 'enrolled_students')->get();
        
        $this->info("\n📊 Found {$courses->count()} courses to check");
        
        $updatesNeeded = 0;
        $totalDiscrepancy = 0;
        
        foreach ($courses as $course) {
            // Count actual members from course_members table
            $actualCount = CourseMember::where('course_id', $course->id)->count();
            $currentCount = $course->enrolled_students;
            
            if ($actualCount != $currentCount) {
                $updatesNeeded++;
                $discrepancy = $actualCount - $currentCount;
                $totalDiscrepancy += abs($discrepancy);
                
                $this->line("\n📚 Course: {$course->name} (ID: {$course->id})");
                $this->line("   Current enrollment: {$currentCount}");
                $this->line("   Actual members: {$actualCount}");
                $this->line("   Discrepancy: " . ($discrepancy > 0 ? '+' : '') . $discrepancy);
                
                if (!$isDryRun) {
                    // Update the enrollment count
                    $course->enrolled_students = $actualCount;
                    $course->save();
                    $this->info("   ✅ Updated enrollment count to {$actualCount}");
                } else {
                    $this->warn("   ⚠️  Would update enrollment count to {$actualCount}");
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
            $this->info("\n✅ Course enrollment update completed successfully!");
        }
        
        return Command::SUCCESS;
    }
}