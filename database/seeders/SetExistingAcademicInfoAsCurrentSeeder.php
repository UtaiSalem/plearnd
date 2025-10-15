<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentAcademicInfo;

class SetExistingAcademicInfoAsCurrentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // อัปเดตข้อมูลเก่าทั้งหมดให้เป็น current 
        // เนื่องจากระบบเก่าใช้ HasOne relationship แต่ตอนนี้เปลี่ยนเป็น HasMany
        StudentAcademicInfo::whereNull('is_current')
            ->orWhere('is_current', false)
            ->update(['is_current' => true]);
            
        $this->command->info('Updated ' . StudentAcademicInfo::where('is_current', true)->count() . ' academic records to current status');
    }
}
