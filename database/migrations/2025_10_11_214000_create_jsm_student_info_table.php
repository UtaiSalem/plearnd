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
        Schema::create('jsm_student_info', function (Blueprint $table) {
            // 1. ลำดับ
            $table->id();
            
            // 2-3. ข้อมูลประจำตัว
            $table->string('citizen_id', 13)->nullable()->comment('เลขประจำตัวประชาชน');
            $table->string('student_id', 20)->nullable()->comment('เลขประจำตัวนักเรียน');
            
            // 4. ข้อมูลชั้นเรียน
            $table->string('classroom', 20)->nullable()->comment('ชั้นเรียน');
            
            // 5-8. ชื่อ-สกุลภาษาไทย
            $table->string('title_prefix', 20)->nullable()->comment('คำนำหน้าชื่อ');
            $table->string('first_name', 100)->nullable()->comment('ชื่อ');
            $table->string('last_name', 100)->nullable()->comment('นามสกุล');
            $table->string('middle_name', 100)->nullable()->comment('ชื่อกลาง');
            
            // 9-12. ชื่อ-สกุลภาษาอังกฤษ
            $table->string('en_title_prefix', 20)->nullable()->comment('คำนำหน้าชื่อ (ภาษาอังกฤษ)');
            $table->string('en_first_name', 100)->nullable()->comment('ชื่อภาษาอังกฤษ');
            $table->string('en_last_name', 100)->nullable()->comment('นามสกุลภาษาอังกฤษ');
            $table->string('en_middle_name', 100)->nullable()->comment('ชื่อกลางภาษาอังกฤษ');
            
            // 13-17. ข้อมูลส่วนตัว
            $table->date('date_of_birth')->nullable()->comment('วันเดือนปีเกิด');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('เพศ');
            $table->string('nationality', 50)->nullable()->comment('สัญชาติ');
            $table->string('religion', 50)->nullable()->comment('ศาสนา');
            $table->string('student_status', 50)->nullable()->comment('สถานะนักเรียน');
            
            // 18-19. ข้อมูลเพิ่มเติม
            $table->date('record_date')->nullable()->comment('วันที่บันทึก');
            $table->string('disability_type', 100)->nullable()->comment('ประเภทความพิการ');
            
            // 20-28. ที่อยู่
            $table->string('house_code', 20)->nullable()->comment('รหัสประจำบ้าน');
            $table->string('house_number', 50)->nullable()->comment('บ้านเลขที่');
            $table->string('village_number', 10)->nullable()->comment('หมู่ที่');
            $table->string('alley', 100)->nullable()->comment('ซอย');
            $table->string('road', 100)->nullable()->comment('ถนน');
            $table->string('subdistrict', 100)->nullable()->comment('ตำบล/แขวง');
            $table->string('district', 100)->nullable()->comment('อำเภอ/เขต');
            $table->string('province', 100)->nullable()->comment('จังหวัด');
            $table->string('postal_code', 10)->nullable()->comment('รหัสไปรษณีย์');
            
            // 29-30. ติดต่อและการศึกษา
            $table->string('phone_number', 20)->nullable()->comment('เบอร์โทรศัพท์');
            $table->date('enrollment_date')->nullable()->comment('วันที่เข้าเรียน');
            
            // 31-36. ข้อมูลบิดา
            $table->string('father_citizen_id', 13)->nullable()->comment('เลขประจำตัวประชาชน (บิดา)');
            $table->string('father_title_prefix', 20)->nullable()->comment('คำนำหน้าชื่อ (บิดา)');
            $table->string('father_first_name', 100)->nullable()->comment('ชื่อ (บิดา)');
            $table->string('father_last_name', 100)->nullable()->comment('นามสกุล (บิดา)');
            $table->string('father_status', 50)->nullable()->comment('สถานภาพของบิดา');
            $table->string('father_nationality', 50)->nullable()->comment('สัญชาติ (บิดา)');
            
            // 37-42. ข้อมูลมารดา
            $table->string('mother_citizen_id', 13)->nullable()->comment('เลขประจำตัวประชาชน (มารดา)');
            $table->string('mother_title_prefix', 20)->nullable()->comment('คำนำหน้าชื่อ (มารดา)');
            $table->string('mother_first_name', 100)->nullable()->comment('ชื่อ (มารดา)');
            $table->string('mother_last_name', 100)->nullable()->comment('นามสกุล (มารดา)');
            $table->string('mother_status', 50)->nullable()->comment('สถานภาพของมารดา');
            $table->string('mother_nationality', 50)->nullable()->comment('สัญชาติ (มารดา)');
            
            // 43-48. ข้อมูลผู้ปกครอง
            $table->string('guardian_citizen_id', 13)->nullable()->comment('เลขประจำตัวประชาชน (ผู้ปกครอง)');
            $table->string('guardian_title_prefix', 20)->nullable()->comment('คำนำหน้าชื่อ (ผู้ปกครอง)');
            $table->string('guardian_full_name', 200)->nullable()->comment('ชื่อ - นามสกุล (ผู้ปกครอง)');
            $table->string('guardian_occupation', 100)->nullable()->comment('อาชีพของผู้ปกครอง');
            $table->string('guardian_phone_number', 20)->nullable()->comment('เบอร์โทรศัพท์ (ผู้ปกครอง)');
            $table->string('relationship', 50)->nullable()->comment('ความสัมพันธ์');
            
            // 49-50. ข้อมูลร่างกาย
            $table->decimal('height_cm', 5, 2)->nullable()->comment('ความสูง (ซม.)');
            $table->decimal('weight_kg', 5, 2)->nullable()->comment('น้ำหนัก (กก.)');
            
            // 51-53. ข้อมูลการศึกษาเดิม
            $table->string('previous_school_name', 200)->nullable()->comment('ชื่อโรงเรียนเดิม');
            $table->string('previous_school_province', 100)->nullable()->comment('จังหวัดโรงเรียนเดิม');
            $table->string('previous_grade_level', 20)->nullable()->comment('ชั้นเรียน (เดิม)');
            
            // Laravel timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('citizen_id');
            $table->index('student_id');
            $table->index('classroom');
            $table->index(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jsm_student_info');
    }
};