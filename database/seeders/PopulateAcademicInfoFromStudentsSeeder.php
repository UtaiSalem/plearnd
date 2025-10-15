<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\StudentAcademicInfo;
use Illuminate\Support\Facades\DB;

class PopulateAcademicInfoFromStudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('เริ่มเติมข้อมูลประวัติการศึกษาจากตาราง students...');
        
        // ดึงนักเรียนที่มี class_level และ class_section
        $students = Student::whereNotNull('class_level')
                          ->whereNotNull('class_section')
                          ->get();
                          
        $this->command->info("พบนักเรียน {$students->count()} คน ที่มีข้อมูลระดับชั้น");
        
        $created = 0;
        $updated = 0;
        $skipped = 0;
        
        foreach ($students as $student) {
            try {
                // ตรวจสอบว่ามี academic info อยู่แล้วหรือไม่
                $academicInfo = $student->academicInfos()->where('is_current', true)->first();
                
                if ($academicInfo) {
                    // มีข้อมูลแล้ว - อัปเดตถ้าข้อมูลเป็น null
                    $needsUpdate = false;
                    $updateData = [];
                    
                    if (empty($academicInfo->current_grade) && !empty($student->class_level)) {
                        $updateData['current_grade'] = $student->class_level;
                        $needsUpdate = true;
                    }
                    
                    if (empty($academicInfo->current_class) && !empty($student->class_section)) {
                        $updateData['current_class'] = $student->class_section;
                        $needsUpdate = true;
                    }
                    
                    if (empty($academicInfo->classroom_full) && !empty($student->class_level) && !empty($student->class_section)) {
                        $updateData['classroom_full'] = $student->class_level . '/' . $student->class_section;
                        $needsUpdate = true;
                    }
                    
                    if ($needsUpdate) {
                        $academicInfo->update($updateData);
                        $updated++;
                        $this->command->line("  อัปเดต: {$student->first_name_th} {$student->last_name_th} ({$student->class_level}/{$student->class_section})");
                    } else {
                        $skipped++;
                    }
                } else {
                    // ไม่มีข้อมูล - สร้างใหม่
                    $academicData = [
                        'student_id' => $student->id,
                        'current_grade' => $student->class_level,
                        'current_class' => $student->class_section,
                        'classroom_full' => $student->class_level . '/' . $student->class_section,
                        'academic_year' => $this->getCurrentAcademicYear(),
                        'semester' => $this->getCurrentSemester(),
                        'school_name' => 'โรงเรียนจริยธรรมศึกษามูลนิธิ',
                        'enrollment_date' => $student->enrollment_date,
                        'study_status' => 'studying',
                        'is_current' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    StudentAcademicInfo::create($academicData);
                    $created++;
                    $this->command->line("  สร้าง: {$student->first_name_th} {$student->last_name_th} ({$student->class_level}/{$student->class_section})");
                }
                
            } catch (\Exception $e) {
                $this->command->error("  ข้อผิดพลาด {$student->id}: " . $e->getMessage());
            }
        }
        
        $this->command->info("เสร็จสิ้น!");
        $this->command->info("- สร้างใหม่: {$created} รายการ");
        $this->command->info("- อัปเดต: {$updated} รายการ");
        $this->command->info("- ข้าม: {$skipped} รายการ (มีข้อมูลครบแล้ว)");
    }
    
    /**
     * ดึงปีการศึกษาปัจจุบัน
     */
    private function getCurrentAcademicYear(): int
    {
        $now = now();
        $buddhistYear = $now->year + 543;
        
        // ปีการศึกษาเริ่ม พ.ค. ของปีก่อนหน้า
        if ($now->month < 5) { // มกราคม-เมษายน = ภาคเรียนที่ 2
            return $buddhistYear - 1;
        }
        return $buddhistYear; // พฤษภาคม-ธันวาคม = ภาคเรียนที่ 1 ปีใหม่
    }
    
    /**
     * ดึงภาคเรียนปัจจุบัน
     */
    private function getCurrentSemester(): int
    {
        $now = now();
        
        // ภาคเรียนที่ 1: พฤษภาคม-กันยายน
        // ภาคเรียนที่ 2: ตุลาคม-เมษายน
        if ($now->month >= 5 && $now->month <= 9) {
            return 1;
        }
        return 2;
    }
}
