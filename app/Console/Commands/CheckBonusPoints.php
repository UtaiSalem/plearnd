<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckBonusPoints extends Command
{
    protected $signature = 'grades:check-bonus';
    protected $description = 'Check bonus points statistics in course_members table';

    public function handle()
    {
        $this->info('=================================================');
        $this->info('ตรวจสอบสถิติคะแนนโบนัสในตาราง course_members');
        $this->info('=================================================');
        $this->newLine();

        // Get statistics
        $stats = DB::table('course_members')
            ->selectRaw('
                COUNT(*) as total_records,
                SUM(CASE WHEN bonus_points > 0 THEN 1 ELSE 0 END) as records_with_bonus,
                MAX(bonus_points) as max_bonus,
                AVG(bonus_points) as avg_bonus,
                SUM(bonus_points) as total_bonus
            ')
            ->first();

        $this->table(
            ['รายการ', 'ค่า'],
            [
                ['จำนวนเรคคอร์ดทั้งหมด', number_format($stats->total_records)],
                ['เรคคอร์ดที่มีคะแนนโบนัส', number_format($stats->records_with_bonus)],
                ['คะแนนโบนัสสูงสุด', number_format($stats->max_bonus)],
                ['คะแนนโบนัสเฉลี่ย', number_format($stats->avg_bonus, 2)],
                ['คะแนนโบนัสรวม', number_format($stats->total_bonus)],
            ]
        );

        $this->newLine();

        // Show some examples with bonus points
        $this->info('📋 ตัวอย่างเรคคอร์ดที่มีคะแนนโบนัส (แสดง 10 รายการแรก)');
        $this->info('─────────────────────────────────────────');
        
        $examples = DB::table('course_members as cm')
            ->join('courses as c', 'cm.course_id', '=', 'c.id')
            ->join('users as u', 'cm.user_id', '=', 'u.id')
            ->select(
                'cm.id',
                'u.name as student_name',
                'c.name as course_name',
                'cm.achieved_score',
                'cm.bonus_points',
                'c.total_score',
                'cm.grade_progress'
            )
            ->where('cm.bonus_points', '>', 0)
            ->limit(10)
            ->get();

        if ($examples->isEmpty()) {
            $this->warn('ไม่พบเรคคอร์ดที่มีคะแนนโบนัส');
        } else {
            $displayData = $examples->map(function ($record) {
                $totalScore = $record->achieved_score + $record->bonus_points;
                $percentage = min(100, ($totalScore / $record->total_score) * 100);
                
                return [
                    'ID' => $record->id,
                    'นักเรียน' => mb_substr($record->student_name, 0, 20),
                    'คะแนน' => $record->achieved_score,
                    'โบนัส' => $record->bonus_points,
                    'รวม' => $totalScore,
                    'เต็ม' => $record->total_score,
                    '%' => round($percentage, 2),
                    'เกรด' => $record->grade_progress,
                ];
            });

            $this->table(
                ['ID', 'นักเรียน', 'คะแนน', 'โบนัส', 'รวม', 'เต็ม', '%', 'เกรด'],
                $displayData->toArray()
            );
        }

        return Command::SUCCESS;
    }
}
