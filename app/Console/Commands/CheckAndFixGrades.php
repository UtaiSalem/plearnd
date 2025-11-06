<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAndFixGrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grades:check-fix {--fix : Execute the fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix incorrect grades in course_members table based on Thai education grading system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=================================================');
        $this->info('ตรวจสอบและแก้ไขเกรดในตาราง course_members');
        $this->info('=================================================');
        $this->newLine();

        // ค้นหาเรคคอร์ดที่มีปัญหา
        $incorrectRecords = $this->findIncorrectGrades();

        if ($incorrectRecords->isEmpty()) {
            $this->info('✓ ไม่พบเรคคอร์ดที่มีการคำนวณเกรดผิดพลาด');
            return Command::SUCCESS;
        }

        // แสดงสรุปภาพรวม
        $this->displaySummary($incorrectRecords);

        // แสดงรายละเอียด
        $this->displayDetails($incorrectRecords);

        // ถามว่าต้องการแก้ไขหรือไม่
        if ($this->option('fix')) {
            $this->fixGrades($incorrectRecords);
        } else {
            $this->warn('⚠ ใช้ option --fix เพื่อดำเนินการแก้ไข');
            $this->info('คำสั่ง: php artisan grades:check-fix --fix');
        }

        return Command::SUCCESS;
    }

    /**
     * Calculate Thai grade from percentage
     */
    private function calculateThaiGrade(?float $percentage): ?int
    {
        if ($percentage === null) {
            return null;
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

    /**
     * Get grade name from numeric grade
     */
    private function getGradeName(int $grade): string
    {
        $gradeNames = [
            4 => 'A',
            3 => 'B+/B',
            2 => 'C+/C',
            1 => 'D+/D',
            0 => 'F'
        ];

        return $gradeNames[$grade] ?? 'N/A';
    }

    /**
     * Find all records with incorrect grades
     */
    private function findIncorrectGrades()
    {
        return DB::table('course_members as cm')
            ->join('courses as c', 'cm.course_id', '=', 'c.id')
            ->join('users as u', 'cm.user_id', '=', 'u.id')
            ->select(
                'cm.id',
                'cm.user_id',
                'u.name as student_name',
                'cm.course_id',
                'c.name as course_title',
                'cm.achieved_score',
                'cm.bonus_points',
                'c.total_score',
                'cm.grade_progress as current_grade'
            )
            ->whereNotNull('cm.achieved_score')
            ->where('c.total_score', '>', 0)
            ->get()
            ->map(function ($record) {
                // Calculate total score including bonus points
                $totalAchievedScore = $record->achieved_score + ($record->bonus_points ?? 0);
                
                // Calculate percentage and cap at 100
                $percentage = ($totalAchievedScore / $record->total_score) * 100;
                $percentage = min(100, max(0, $percentage));
                
                $correctGrade = $this->calculateThaiGrade($percentage);
                
                $record->total_achieved_score = $totalAchievedScore;
                $record->percentage = round($percentage, 2);
                $record->correct_grade = $correctGrade;
                $record->is_incorrect = $correctGrade != $record->current_grade;
                
                return $record;
            })
            ->filter(function ($record) {
                return $record->is_incorrect;
            });
    }

    /**
     * Display summary of incorrect records
     */
    private function displaySummary($records)
    {
        $this->info('📊 สรุปภาพรวม');
        $this->info('─────────────────────────────────────────');
        $this->table(
            ['รายการ', 'จำนวน'],
            [
                ['เรคคอร์ดที่ไม่ถูกต้อง', $records->count()],
                ['รายวิชาที่ได้รับผลกระทบ', $records->pluck('course_id')->unique()->count()],
                ['นักเรียนที่ได้รับผลกระทบ', $records->pluck('user_id')->unique()->count()],
            ]
        );
        $this->newLine();
    }

    /**
     * Display details of incorrect records
     */
    private function displayDetails($records)
    {
        $this->info('📋 รายละเอียดเรคคอร์ดที่ต้องแก้ไข (แสดง 20 รายการแรก)');
        $this->info('─────────────────────────────────────────');
        
        $displayRecords = $records->take(20)->map(function ($record) {
            $bonusText = $record->bonus_points > 0 ? " +{$record->bonus_points}" : "";
            $totalScoreText = "{$record->achieved_score}{$bonusText}/{$record->total_score}";
            
            return [
                'ID' => $record->id,
                'นักเรียน' => mb_substr($record->student_name, 0, 20),
                'รายวิชา' => mb_substr($record->course_title, 0, 25),
                'คะแนน' => $totalScoreText,
                'เปอร์เซ็นต์' => $record->percentage . '%',
                'เกรดเดิม' => $this->getGradeName($record->current_grade),
                'เกรดที่ถูกต้อง' => $this->getGradeName($record->correct_grade),
            ];
        });

        $this->table(
            ['ID', 'นักเรียน', 'รายวิชา', 'คะแนน', 'เปอร์เซ็นต์', 'เกรดเดิม', 'เกรดที่ถูกต้อง'],
            $displayRecords->toArray()
        );

        if ($records->count() > 20) {
            $this->warn("... และอีก " . ($records->count() - 20) . " รายการ");
        }
        
        $this->newLine();
    }

    /**
     * Fix the grades in database
     */
    private function fixGrades($records)
    {
        if (!$this->confirm('⚠ ต้องการดำเนินการแก้ไขเกรดทั้งหมด ' . $records->count() . ' รายการใช่หรือไม่?', false)) {
            $this->info('ยกเลิกการแก้ไข');
            return;
        }

        $this->info('🔄 กำลังแก้ไขข้อมูล...');
        
        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        $fixedCount = 0;
        $failedCount = 0;
        $fixedRecords = [];

        DB::beginTransaction();
        
        try {
            foreach ($records as $record) {
                try {
                    // บันทึกเกรดเดิมไปยัง edited_grade ก่อนแก้ไข
                    DB::table('course_members')
                        ->where('id', $record->id)
                        ->update([
                            'edited_grade' => $record->current_grade,
                            'grade_progress' => $record->correct_grade,
                            'updated_at' => now(),
                        ]);
                    
                    $fixedRecords[] = [
                        'id' => $record->id,
                        'student_name' => $record->student_name,
                        'course_title' => $record->course_title,
                        'old_grade' => $this->getGradeName($record->current_grade),
                        'new_grade' => $this->getGradeName($record->correct_grade),
                        'percentage' => $record->percentage,
                    ];
                    
                    $fixedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $this->error("\nError fixing record ID {$record->id}: " . $e->getMessage());
                }
                
                $bar->advance();
            }

            DB::commit();
            $bar->finish();
            
            $this->newLine(2);
            $this->info("✓ แก้ไขเสร็จสิ้น!");
            $this->info("  - สำเร็จ: {$fixedCount} รายการ");
            
            if ($failedCount > 0) {
                $this->error("  - ล้มเหลว: {$failedCount} รายการ");
            }
            
            // สร้างรายงาน
            $this->generateReport($fixedRecords);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('เกิดข้อผิดพลาด: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Generate fix report
     */
    private function generateReport($fixedRecords)
    {
        $this->newLine();
        $this->info('📄 รายงานการแก้ไข');
        $this->info('─────────────────────────────────────────');
        
        $reportData = collect($fixedRecords)->take(20)->map(function ($record) {
            return [
                'ID' => $record['id'],
                'นักเรียน' => mb_substr($record['student_name'], 0, 20),
                'รายวิชา' => mb_substr($record['course_title'], 0, 25),
                'เปอร์เซ็นต์' => $record['percentage'] . '%',
                'เกรดเดิม' => $record['old_grade'],
                'เกรดใหม่' => $record['new_grade'],
            ];
        });

        $this->table(
            ['ID', 'นักเรียน', 'รายวิชา', 'เปอร์เซ็นต์', 'เกรดเดิม', 'เกรดใหม่'],
            $reportData->toArray()
        );

        if (count($fixedRecords) > 20) {
            $this->warn("... และอีก " . (count($fixedRecords) - 20) . " รายการ");
        }

        // บันทึกรายงานเป็นไฟล์
        $reportPath = storage_path('logs/grade_fix_report_' . date('Y_m_d_His') . '.txt');
        $reportContent = "รายงานการแก้ไขเกรด - " . date('Y-m-d H:i:s') . "\n";
        $reportContent .= str_repeat('=', 80) . "\n\n";
        $reportContent .= "จำนวนที่แก้ไข: " . count($fixedRecords) . " รายการ\n\n";
        $reportContent .= "หมายเหตุ: การคำนวณเกรดรวมถึงคะแนนโบนัส (bonus_points) และจำกัดค่าร้อยละสูงสุดที่ 100%\n\n";
        
        foreach ($fixedRecords as $record) {
            $reportContent .= "ID: {$record['id']}\n";
            $reportContent .= "นักเรียน: {$record['student_name']}\n";
            $reportContent .= "รายวิชา: {$record['course_title']}\n";
            $reportContent .= "เปอร์เซ็นต์: {$record['percentage']}%\n";
            $reportContent .= "เกรดเดิม: {$record['old_grade']} → เกรดใหม่: {$record['new_grade']}\n";
            $reportContent .= str_repeat('-', 80) . "\n";
        }

        file_put_contents($reportPath, $reportContent);
        
        $this->newLine();
        $this->info("💾 บันทึกรายงานแล้วที่: {$reportPath}");
    }
}
