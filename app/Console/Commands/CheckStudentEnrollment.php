<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;

class CheckStudentEnrollment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:check-enrollment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check student enrollment date data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== ตรวจสอบข้อมูล enrollment_date ในตาราง students ===');
        
        $totalStudents = Student::count();
        $withEnrollmentDate = Student::whereNotNull('enrollment_date')->count();
        
        $this->line("จำนวนนักเรียนทั้งหมด: {$totalStudents}");
        $this->line("จำนวนที่มี enrollment_date: {$withEnrollmentDate}");
        
        if ($withEnrollmentDate > 0) {
            $this->info('=== ตัวอย่างข้อมูล enrollment_date ===');
            $samples = Student::whereNotNull('enrollment_date')
                ->limit(5)
                ->get(['id', 'first_name_th', 'last_name_th', 'enrollment_date']);
                
            foreach ($samples as $student) {
                $this->line("ID: {$student->id}, ชื่อ: {$student->first_name_th} {$student->last_name_th}, วันที่เข้าเรียน: {$student->enrollment_date}");
            }
            
            // อัปเดต enrollment_date ในตาราง student_academic_info
            $this->info('=== อัปเดต enrollment_date ในตาราง student_academic_info ===');
            
            $updated = \DB::table('student_academic_info')
                ->join('students', 'students.id', '=', 'student_academic_info.student_id')
                ->whereNull('student_academic_info.enrollment_date')
                ->whereNotNull('students.enrollment_date')
                ->update(['student_academic_info.enrollment_date' => \DB::raw('students.enrollment_date')]);
                
            $this->info("อัปเดตข้อมูล enrollment_date สำเร็จ: {$updated} รายการ");
            
            // ตรวจสอบผลลัพธ์
            $academicWithEnrollmentDate = \DB::table('student_academic_info')->whereNotNull('enrollment_date')->count();
            $this->line("ตาราง student_academic_info ที่มี enrollment_date: {$academicWithEnrollmentDate} รายการ");
            
        } else {
            $this->warn('ไม่มีข้อมูล enrollment_date ในตาราง students');
        }
    }
}
