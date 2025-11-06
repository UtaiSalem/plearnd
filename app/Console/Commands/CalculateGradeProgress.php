<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseMember;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateGradeProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-grade-progress {--course-id= : Specific course ID to process} {--member-id= : Specific member ID to process} {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and update grade_progress for course members based on achieved_score';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $courseId = $this->option('course-id');
        $memberId = $this->option('member-id');
        $isDryRun = $this->option('dry-run');

        $this->info('Starting grade progress calculation...');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE: No changes will be made to the database.');
        }

        // Build query
        $query = CourseMember::with(['course', 'user']);
        
        if ($courseId) {
            $query->where('course_id', $courseId);
            $this->info("Processing course ID: {$courseId}");
        }
        
        if ($memberId) {
            $query->where('id', $memberId);
            $this->info("Processing member ID: {$memberId}");
        }

        $members = $query->get();
        $totalMembers = $members->count();
        
        if ($totalMembers === 0) {
            $this->error('No course members found matching the criteria.');
            return 1;
        }

        $this->info("Found {$totalMembers} course members to process.");
        
        $processed = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($totalMembers);
        $bar->start();

        foreach ($members as $member) {
            try {
                $bar->advance();
                $processed++;

                // Get course total score and scores
                $courseTotalScore = $member->course->total_score ?? 0;
                $achievedScore = $member->achieved_score ?? 0;
                $bonusPoints = $member->bonus_points ?? 0;
                
                // Skip if course has no total score
                if ($courseTotalScore <= 0) {
                    $skipped++;
                    if ($this->getOutput()->isVerbose()) {
                        $this->line("Member ID {$member->id}: Skipped - Course has no total score");
                    }
                    continue;
                }

                // Calculate total score including bonus points
                $totalAchievedScore = $achievedScore + $bonusPoints;
                
                // Calculate percentage and cap at 100
                $percentage = 0;
                if ($courseTotalScore > 0) {
                    $percentage = round(($totalAchievedScore / $courseTotalScore) * 100);
                    $percentage = max(0, min(100, $percentage));
                }

                // Convert percentage to Thai grade system
                $thaiGrade = $this->calculateThaiGrade($percentage, $achievedScore, $courseTotalScore);

                $currentGradeProgress = $member->grade_progress ?? 0;
                
                // Enhanced debug: Show calculation details for all records with achieved_score > 0
                if ($achievedScore > 0 && $this->getOutput()->isVerbose()) {
                    $bonusText = $bonusPoints > 0 ? " +{$bonusPoints}" : "";
                    $this->line("DEBUG: Member ID {$member->id} - Achieved: {$achievedScore}{$bonusText}/{$courseTotalScore} = {$percentage}% -> Grade: {$thaiGrade}");
                }
                
                // Debug: Show high-percentage students getting wrong grades
                if ($percentage >= 80 && $thaiGrade != 4) {
                    $bonusText = $bonusPoints > 0 ? " +{$bonusPoints}" : "";
                    $this->error("ERROR: Member ID {$member->id} - Score: {$achievedScore}{$bonusText}/{$courseTotalScore} = {$percentage}% -> Grade: {$thaiGrade} (Expected: 4)");
                }
                
                // Debug: Check if course total score is greater than 100
                if ($courseTotalScore > 100) {
                    $bonusText = $bonusPoints > 0 ? " +{$bonusPoints}" : "";
                    $this->warn("DEBUG: Member ID {$member->id} - Course total > 100: {$courseTotalScore} points, Achieved: {$achievedScore}{$bonusText} = {$percentage}%");
                }
                
                // Check if update is needed
                if ($currentGradeProgress != $thaiGrade) {
                    if (!$isDryRun) {
                        $member->grade_progress = $thaiGrade;
                        $member->save();
                    }
                    
                    $updated++;
                    
                    if ($this->getOutput()->isVerbose()) {
                        $bonusText = $bonusPoints > 0 ? " +{$bonusPoints}" : "";
                        $this->line("Member ID {$member->id}: Updated grade from {$currentGradeProgress} to {$thaiGrade} (Score: {$achievedScore}{$bonusText}/{$courseTotalScore}, {$percentage}%)");
                    }
                } else {
                    if ($this->getOutput()->isVerbose()) {
                        $this->line("Member ID {$member->id}: No change needed (Current: {$currentGradeProgress}, Calculated: {$thaiGrade})");
                    }
                }

            } catch (\Exception $e) {
                $errors++;
                $this->error("Error processing member ID {$member->id}: " . $e->getMessage());
                Log::error('Grade calculation error', [
                    'member_id' => $member->id,
                    'course_id' => $member->course_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $bar->finish();
        $this->line('');

        // Summary
        $this->info('=== PROCESSING SUMMARY ===');
        $this->info("Total members processed: {$processed}");
        $this->info("Members updated: {$updated}");
        $this->info("Members skipped: {$skipped}");
        $this->info("Errors encountered: {$errors}");
        
        if ($isDryRun) {
            $this->warn('DRY RUN completed. No changes were made to the database.');
            $this->info("To apply changes, run the command without --dry-run option.");
        } else {
            $this->info('Grade progress calculation completed successfully.');
        }

        return $errors > 0 ? 1 : 0;
    }

    /**
     * Calculate Thai grade based on percentage score
     * 
     * Thai Grading System (Integer-based):
     * - 80-100% = 4 (A)
     * - 75-79% = 3 (B+/B)
     * - 70-74% = 3 (B)
     * - 65-69% = 2 (C+/C)
     * - 60-64% = 2 (C)
     * - 55-59% = 1 (D+/D)
     * - 50-54% = 1 (D)
     * - 0-49% = 0 (F)
     * - Incomplete/No score = null
     * 
     * @param int $percentage The percentage score (0-100)
     * @param int $achievedScore The achieved score
     * @param int $courseTotalScore The total possible score
     * @return int|null The Thai grade
     */
    private function calculateThaiGrade($percentage, $achievedScore, $courseTotalScore)
    {
        // Check for incomplete/no score
        if ($achievedScore === 0 && $courseTotalScore > 0) {
            return null; // No score yet
        }

        if ($percentage >= 80) {
            return 4; // A (80-100)
        } elseif ($percentage >= 75) {
            return 3; // B+ (75-79)
        } elseif ($percentage >= 70) {
            return 3; // B (70-74)
        } elseif ($percentage >= 65) {
            return 2; // C+ (65-69)
        } elseif ($percentage >= 60) {
            return 2; // C (60-64)
        } elseif ($percentage >= 55) {
            return 1; // D+ (55-59)
        } elseif ($percentage >= 50) {
            return 1; // D (50-54)
        } else {
            return 0; // F (0-49)
        }
    }
}
