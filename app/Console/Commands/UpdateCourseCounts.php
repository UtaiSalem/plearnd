<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Course;

class UpdateCourseCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:update-counts {--verify : Show verification after update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update courses table with counts from related tables (groups, quizzes, enrolled_students)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating course counts...');
        
        // Update groups count
        $this->info('Updating groups count...');
        DB::statement('
            UPDATE courses c
            SET c.groups = (
                SELECT COUNT(*)
                FROM course_groups cg
                WHERE cg.course_id = c.id
            )
        ');
        
        // Update quizzes count
        $this->info('Updating quizzes count...');
        DB::statement('
            UPDATE courses c
            SET c.quizzes = (
                SELECT COUNT(*)
                FROM course_quizzes cq
                WHERE cq.course_id = c.id
            )
        ');
        
        // Update enrolled_students count
        $this->info('Updating enrolled students count...');
        DB::statement('
            UPDATE courses c
            SET c.enrolled_students = (
                SELECT COUNT(*)
                FROM course_members cm
                WHERE cm.course_id = c.id
                AND cm.course_member_status = 1
                AND (cm.role = 1 OR cm.role = 2)
            )
        ');
        
        $this->info('✓ Course counts updated successfully!');
        
        // Show verification if requested
        if ($this->option('verify')) {
            $this->info('\nVerification:');
            $results = DB::select('
                SELECT
                    c.id,
                    c.name,
                    c.groups,
                    c.quizzes,
                    c.enrolled_students,
                    (SELECT COUNT(*) FROM course_groups cg WHERE cg.course_id = c.id) as actual_groups,
                    (SELECT COUNT(*) FROM course_quizzes cq WHERE cq.course_id = c.id) as actual_quizzes,
                    (SELECT COUNT(*) FROM course_members cm WHERE cm.course_id = c.id AND cm.course_member_status = 1 AND (cm.role = 1 OR cm.role = 2)) as actual_enrolled_students
                FROM courses c
                ORDER BY c.id
                LIMIT 10
            ');
            
            $tableData = [];
            foreach ($results as $row) {
                $tableData[] = [
                    $row->id,
                    $row->name,
                    $row->groups,
                    $row->quizzes,
                    $row->enrolled_students,
                    $row->actual_groups,
                    $row->actual_quizzes,
                    $row->actual_enrolled_students
                ];
            }
            
            $this->table(
                ['ID', 'Name', 'Groups', 'Quizzes', 'Enrolled', 'Actual Groups', 'Actual Quizzes', 'Actual Enrolled'],
                $tableData
            );
        }
        
        return Command::SUCCESS;
    }
}