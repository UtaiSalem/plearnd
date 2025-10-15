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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id', 20)->unique()->comment('รหัสนักเรียน');
            $table->string('citizen_id', 13)->unique()->nullable()->comment('เลขประจำตัวประชาชน');
            
            // ชื่อภาษาไทย
            $table->string('title_prefix_th', 20)->nullable()->comment('คำนำหน้าชื่อ');
            $table->string('first_name_th', 100)->comment('ชื่อ');
            $table->string('last_name_th', 100)->comment('นามสกุล');
            $table->string('middle_name_th', 100)->nullable()->comment('ชื่อกลาง');
            
            // ชื่อภาษาอังกฤษ
            $table->string('title_prefix_en', 20)->nullable()->comment('Title (EN)');
            $table->string('first_name_en', 100)->nullable()->comment('First Name (EN)');
            $table->string('last_name_en', 100)->nullable()->comment('Last Name (EN)');
            $table->string('middle_name_en', 100)->nullable()->comment('Middle Name (EN)');
            
            // ข้อมูลพื้นฐาน
            $table->date('date_of_birth')->nullable()->comment('วันเดือนปีเกิด');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('เพศ');
            $table->string('nationality', 50)->default('ไทย')->comment('สัญชาติ');
            $table->string('religion', 50)->nullable()->comment('ศาสนา');
            
            // สถานะ
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred'])->default('active');
            $table->date('enrollment_date')->nullable()->comment('วันที่เข้าเรียน');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['first_name_th', 'last_name_th']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
