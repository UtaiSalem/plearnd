<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentAcademicInfo;

class UpdateEducationLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academic:update-education-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update education levels for existing academic records based on current grade';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== ตรวจสอบข้อมูลปัจจุบัน ===');
        $this->line('จำนวนข้อมูลทั้งหมด: ' . StudentAcademicInfo::count());
        $this->line('จำนวนข้อมูลที่มี education_level: ' . StudentAcademicInfo::whereNotNull('education_level')->count());

        $this->info('=== ตัวอย่างค่า current_grade ===');
        $grades = StudentAcademicInfo::select('current_grade')
            ->distinct()
            ->whereNotNull('current_grade')
            ->limit(20)
            ->get()
            ->pluck('current_grade')
            ->toArray();

        foreach($grades as $grade) {
            $this->line("- " . $grade);
        }

        if (!$this->confirm('ต้องการดำเนินการกำหนดค่าเริ่มต้นหรือไม่?')) {
            $this->info('ยกเลิกการดำเนินการ');
            return;
        }

        $this->info('=== เริ่มกำหนดค่าเริ่มต้น ===');

        // กำหนดค่าเริ่มต้นตามระดับชั้น - ตามที่ผู้ใช้ระบุว่านักเรียนส่วนใหญ่เป็นมัธยม
        $totalUpdated = 0;
        
        // กำหนดค่าเริ่มต้นเป็น secondary (มัธยม) สำหรับทุกรายการที่ยังไม่มีการกำหนด
        $allUpdated = StudentAcademicInfo::whereNull('education_level')
            ->whereNotNull('current_grade')
            ->where('current_grade', '!=', '')
            ->update(['education_level' => 'secondary']);

        if($allUpdated > 0) {
            $this->line("Updated {$allUpdated} records to 'secondary' (มัธยมศึกษา) as default");
            $totalUpdated += $allUpdated;
        }



        $this->info('=== สรุปผลลัพธ์ ===');
        $this->line('อนุบาล: ' . StudentAcademicInfo::where('education_level', 'kindergarten')->count() . ' records');
        $this->line('ประถม: ' . StudentAcademicInfo::where('education_level', 'primary')->count() . ' records');
        $this->line('มัธยม: ' . StudentAcademicInfo::where('education_level', 'secondary')->count() . ' records');
        $this->line('ยังไม่กำหนด: ' . StudentAcademicInfo::whereNull('education_level')->count() . ' records');
        
        $this->info("อัปเดตเสร็จสิ้น! รวมทั้งหมด {$totalUpdated} รายการ");
    }
}
