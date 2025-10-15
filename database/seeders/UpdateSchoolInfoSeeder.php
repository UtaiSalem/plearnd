<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentAcademicInfo;

class UpdateSchoolInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to update school information for all academic records...');

        // ข้อมูลโรงเรียนจริยธรรมศึกษามูลนิธิ
        $schoolName = 'โรงเรียนจริยธรรมศึกษามูลนิธิ';
        $schoolAddress = 'หมู่ 8 ตำบลสะกอม อำเภอจะนะ จังหวัดสงขลา';

        // อัพเดทข้อมูลโรงเรียนสำหรับ academic records ทั้งหมด
        $updatedCount = StudentAcademicInfo::whereNull('school_name')
            ->orWhere('school_name', '')
            ->orWhere('school_name', 'โรงเรียนจริยธรรมศึกษา')
            ->update([
                'school_name' => $schoolName,
                'school_address' => $schoolAddress,
                'updated_at' => now(),
            ]);

        $this->command->info("Updated school information for {$updatedCount} academic records.");

        // อัพเดทข้อมูลที่อยู่โรงเรียนสำหรับ records ที่มีชื่อโรงเรียนแล้วแต่ไม่มีที่อยู่
        $addressUpdatedCount = StudentAcademicInfo::where('school_name', $schoolName)
            ->where(function($query) {
                $query->whereNull('school_address')
                      ->orWhere('school_address', '');
            })
            ->update([
                'school_address' => $schoolAddress,
                'updated_at' => now(),
            ]);

        $this->command->info("Updated school address for {$addressUpdatedCount} additional records.");

        // แสดงสถิติสุดท้าย
        $totalRecords = StudentAcademicInfo::count();
        $recordsWithSchoolInfo = StudentAcademicInfo::where('school_name', $schoolName)->count();

        $this->command->info('=== Update Summary ===');
        $this->command->info("Total academic records: {$totalRecords}");
        $this->command->info("Records with complete school info: {$recordsWithSchoolInfo}");
        $this->command->info('School information update completed successfully!');
    }
}