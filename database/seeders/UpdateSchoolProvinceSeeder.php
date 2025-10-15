<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentAcademicInfo;

class UpdateSchoolProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to update school_province information...');

        // อัพเดทข้อมูล school_province สำหรับโรงเรียนจริยธรรมศึกษามูลนิธิ
        $schoolName = 'โรงเรียนจริยธรรมศึกษามูลนิธิ';
        $province = 'สงขลา';

        // อัพเดท records ที่ไม่มี school_province หรือมีค่าว่าง
        $updatedCount = StudentAcademicInfo::where('school_name', $schoolName)
            ->where(function($query) {
                $query->whereNull('school_province')
                      ->orWhere('school_province', '');
            })
            ->update([
                'school_province' => $province,
                'updated_at' => now(),
            ]);

        $this->command->info("Updated school_province for {$updatedCount} records.");

        // แสดงสถิติสุดท้าย
        $totalRecords = StudentAcademicInfo::count();
        $recordsWithProvince = StudentAcademicInfo::whereNotNull('school_province')
            ->where('school_province', '!=', '')
            ->count();

        $this->command->info('=== School Province Update Summary ===');
        $this->command->info("Total academic records: {$totalRecords}");
        $this->command->info("Records with school_province: {$recordsWithProvince}");
        $this->command->info("Completion rate: " . round(($recordsWithProvince / $totalRecords) * 100, 2) . "%");
        $this->command->info('School province update completed successfully!');
    }
}