<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentAcademicInfo;

class SeparateProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to separate province from school address...');

        // อัพเดทข้อมูลสำหรับโรงเรียนจริยธรรมศึกษามูลนิธิ
        $schoolName = 'โรงเรียนจริยธรรมศึกษามูลนิธิ';
        $newAddress = 'หมู่ 8 ตำบลสะกอม อำเภอจะนะ'; // ที่อยู่ใหม่โดยไม่มีจังหวัด
        $province = 'สงขลา'; // จังหวัดแยกต่างหาก

        // อัพเดทข้อมูลที่มีอยู่
        $updatedCount = StudentAcademicInfo::where('school_name', $schoolName)
            ->where('school_address', 'หมู่ 8 ตำบลสะกอม อำเภอจะนะ จังหวัดสงขลา')
            ->update([
                'school_address' => $newAddress,
                'province' => $province,
                'updated_at' => now(),
            ]);

        $this->command->info("Updated {$updatedCount} records with separated province information.");

        // ตรวจสอบและอัพเดท records อื่นๆ ที่อาจมีจังหวัดในที่อยู่
        $recordsWithProvince = StudentAcademicInfo::where('school_address', 'like', '%จังหวัด%')
            ->whereNull('province')
            ->get();

        $manualUpdateCount = 0;
        foreach ($recordsWithProvince as $record) {
            // แยกจังหวัดออกจากที่อยู่
            if (preg_match('/จังหวัด(.+)$/', $record->school_address, $matches)) {
                $extractedProvince = trim($matches[1]);
                $addressWithoutProvince = preg_replace('/\s*จังหวัด.+$/', '', $record->school_address);
                
                $record->update([
                    'school_address' => trim($addressWithoutProvince),
                    'province' => $extractedProvince,
                ]);
                $manualUpdateCount++;
            }
        }

        $this->command->info("Extracted province from {$manualUpdateCount} additional records.");

        // แสดงสถิติสุดท้าย
        $totalRecords = StudentAcademicInfo::count();
        $recordsWithProvince = StudentAcademicInfo::whereNotNull('province')->count();
        $recordsWithUpdatedAddress = StudentAcademicInfo::where('school_address', $newAddress)->count();

        $this->command->info('=== Province Separation Summary ===');
        $this->command->info("Total academic records: {$totalRecords}");
        $this->command->info("Records with province field: {$recordsWithProvince}");
        $this->command->info("Records with updated address (no province): {$recordsWithUpdatedAddress}");
        $this->command->info('Province separation completed successfully!');
    }
}