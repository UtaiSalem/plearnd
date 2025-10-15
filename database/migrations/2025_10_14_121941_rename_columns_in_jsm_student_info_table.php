<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jsm_student_info', function (Blueprint $table) {
            // เปลี่ยนชื่อคอลัมน์จาก COL 1-53 เป็นชื่อใหม่ตามภาษาอังกฤษ
            
            // คอลัมน์ 1-8: ข้อมูลพื้นฐานนักเรียน
            $table->renameColumn('COL 1', 'id');
            $table->renameColumn('COL 2', 'citizen_id');
            $table->renameColumn('COL 3', 'student_id');
            $table->renameColumn('COL 4', 'classroom');
            $table->renameColumn('COL 5', 'title_prefix');
            $table->renameColumn('COL 6', 'first_name');
            $table->renameColumn('COL 7', 'last_name');
            $table->renameColumn('COL 8', 'middle_name');
            
            // คอลัมน์ 9-12: ชื่อภาษาอังกฤษ
            $table->renameColumn('COL 9', 'en_title_prefix');
            $table->renameColumn('COL 10', 'en_first_name');
            $table->renameColumn('COL 11', 'en_last_name');
            $table->renameColumn('COL 12', 'en_middle_name');
            
            // คอลัมน์ 13-18: ข้อมูลส่วนตัว
            $table->renameColumn('COL 13', 'date_of_birth');
            $table->renameColumn('COL 14', 'gender');
            $table->renameColumn('COL 15', 'nationality');
            $table->renameColumn('COL 16', 'religion');
            $table->renameColumn('COL 17', 'student_status');
            $table->renameColumn('COL 18', 'record_date');
            
            // คอลัมน์ 19-30: ข้อมูลที่อยู่และติดต่อ
            $table->renameColumn('COL 19', 'disability_type');
            $table->renameColumn('COL 20', 'house_code');
            $table->renameColumn('COL 21', 'house_number');
            $table->renameColumn('COL 22', 'village_number');
            $table->renameColumn('COL 23', 'alley');
            $table->renameColumn('COL 24', 'road');
            $table->renameColumn('COL 25', 'subdistrict');
            $table->renameColumn('COL 26', 'district');
            $table->renameColumn('COL 27', 'province');
            $table->renameColumn('COL 28', 'postal_code');
            $table->renameColumn('COL 29', 'phone_number');
            $table->renameColumn('COL 30', 'enrollment_date');
            
            // คอลัมน์ 31-36: ข้อมูลบิดา
            $table->renameColumn('COL 31', 'father_citizen_id');
            $table->renameColumn('COL 32', 'father_title_prefix');
            $table->renameColumn('COL 33', 'father_first_name');
            $table->renameColumn('COL 34', 'father_last_name');
            $table->renameColumn('COL 35', 'father_status');
            $table->renameColumn('COL 36', 'father_nationality');
            
            // คอลัมน์ 37-42: ข้อมูลมารดา
            $table->renameColumn('COL 37', 'mother_citizen_id');
            $table->renameColumn('COL 38', 'mother_title_prefix');
            $table->renameColumn('COL 39', 'mother_first_name');
            $table->renameColumn('COL 40', 'mother_last_name');
            $table->renameColumn('COL 41', 'mother_status');
            $table->renameColumn('COL 42', 'mother_nationality');
            
            // คอลัมน์ 43-48: ข้อมูลผู้ปกครอง
            $table->renameColumn('COL 43', 'guardian_citizen_id');
            $table->renameColumn('COL 44', 'guardian_title_prefix');
            $table->renameColumn('COL 45', 'guardian_full_name');
            $table->renameColumn('COL 46', 'guardian_occupation');
            $table->renameColumn('COL 47', 'guardian_phone_number');
            $table->renameColumn('COL 48', 'relationship');
            
            // คอลัมน์ 49-53: ข้อมูลสุขภาพและการศึกษา
            $table->renameColumn('COL 49', 'height_cm');
            $table->renameColumn('COL 50', 'weight_kg');
            $table->renameColumn('COL 51', 'previous_school_name');
            $table->renameColumn('COL 52', 'previous_school_province');
            $table->renameColumn('COL 53', 'previous_grade_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jsm_student_info', function (Blueprint $table) {
            // เปลี่ยนชื่อคอลัมน์กลับเป็น COL 1-53
            
            // คอลัมน์ 1-8: ข้อมูลพื้นฐานนักเรียน
            $table->renameColumn('id', 'COL 1');
            $table->renameColumn('citizen_id', 'COL 2');
            $table->renameColumn('student_id', 'COL 3');
            $table->renameColumn('classroom', 'COL 4');
            $table->renameColumn('title_prefix', 'COL 5');
            $table->renameColumn('first_name', 'COL 6');
            $table->renameColumn('last_name', 'COL 7');
            $table->renameColumn('middle_name', 'COL 8');
            
            // คอลัมน์ 9-12: ชื่อภาษาอังกฤษ
            $table->renameColumn('en_title_prefix', 'COL 9');
            $table->renameColumn('en_first_name', 'COL 10');
            $table->renameColumn('en_last_name', 'COL 11');
            $table->renameColumn('en_middle_name', 'COL 12');
            
            // คอลัมน์ 13-18: ข้อมูลส่วนตัว
            $table->renameColumn('date_of_birth', 'COL 13');
            $table->renameColumn('gender', 'COL 14');
            $table->renameColumn('nationality', 'COL 15');
            $table->renameColumn('religion', 'COL 16');
            $table->renameColumn('student_status', 'COL 17');
            $table->renameColumn('record_date', 'COL 18');
            
            // คอลัมน์ 19-30: ข้อมูลที่อยู่และติดต่อ
            $table->renameColumn('disability_type', 'COL 19');
            $table->renameColumn('house_code', 'COL 20');
            $table->renameColumn('house_number', 'COL 21');
            $table->renameColumn('village_number', 'COL 22');
            $table->renameColumn('alley', 'COL 23');
            $table->renameColumn('road', 'COL 24');
            $table->renameColumn('subdistrict', 'COL 25');
            $table->renameColumn('district', 'COL 26');
            $table->renameColumn('province', 'COL 27');
            $table->renameColumn('postal_code', 'COL 28');
            $table->renameColumn('phone_number', 'COL 29');
            $table->renameColumn('enrollment_date', 'COL 30');
            
            // คอลัมน์ 31-36: ข้อมูลบิดา
            $table->renameColumn('father_citizen_id', 'COL 31');
            $table->renameColumn('father_title_prefix', 'COL 32');
            $table->renameColumn('father_first_name', 'COL 33');
            $table->renameColumn('father_last_name', 'COL 34');
            $table->renameColumn('father_status', 'COL 35');
            $table->renameColumn('father_nationality', 'COL 36');
            
            // คอลัมน์ 37-42: ข้อมูลมารดา
            $table->renameColumn('mother_citizen_id', 'COL 37');
            $table->renameColumn('mother_title_prefix', 'COL 38');
            $table->renameColumn('mother_first_name', 'COL 39');
            $table->renameColumn('mother_last_name', 'COL 40');
            $table->renameColumn('mother_status', 'COL 41');
            $table->renameColumn('mother_nationality', 'COL 42');
            
            // คอลัมน์ 43-48: ข้อมูลผู้ปกครอง
            $table->renameColumn('guardian_citizen_id', 'COL 43');
            $table->renameColumn('guardian_title_prefix', 'COL 44');
            $table->renameColumn('guardian_full_name', 'COL 45');
            $table->renameColumn('guardian_occupation', 'COL 46');
            $table->renameColumn('guardian_phone_number', 'COL 47');
            $table->renameColumn('relationship', 'COL 48');
            
            // คอลัมน์ 49-53: ข้อมูลสุขภาพและการศึกษา
            $table->renameColumn('height_cm', 'COL 49');
            $table->renameColumn('weight_kg', 'COL 50');
            $table->renameColumn('previous_school_name', 'COL 51');
            $table->renameColumn('previous_school_province', 'COL 52');
            $table->renameColumn('previous_grade_level', 'COL 53');
        });
    }
};
