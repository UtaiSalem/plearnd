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
        Schema::table('student_academic_info', function (Blueprint $table) {
            // School information
            $table->string('school_name', 200)->nullable()->after('classroom_full')->comment('ชื่อสถาบันการศึกษา');
            $table->text('school_address')->nullable()->after('school_name')->comment('ที่อยู่โรงเรียน');
            $table->string('school_province', 100)->nullable()->after('school_address')->comment('จังหวัดของโรงเรียน');
            
            // Enrollment and graduation information
            $table->date('enrollment_date')->nullable()->after('semester')->comment('วันที่เข้าเรียน');
            $table->date('graduation_date')->nullable()->after('enrollment_date')->comment('วันที่จบการศึกษา');
            
            // Status and current indicator
            $table->enum('study_status', ['studying', 'graduated', 'transferred', 'dropped', 'suspended'])
                  ->default('studying')->after('graduation_date')->comment('สถานะการศึกษา');
            $table->boolean('is_current')->default(false)->after('study_status')->comment('เป็นประวัติปัจจุบันหรือไม่');
            
            // Additional information
            $table->string('transfer_reason', 500)->nullable()->after('is_current')->comment('เหตุผลการย้าย/ออก');
            $table->text('notes')->nullable()->after('transfer_reason')->comment('หมายเหตุเพิ่มเติม');
            
            // Add index for better performance
            $table->index(['student_id', 'is_current'], 'idx_student_current');
            $table->index(['student_id', 'academic_year'], 'idx_student_year');
            $table->index(['study_status'], 'idx_study_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_academic_info', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_student_current');
            $table->dropIndex('idx_student_year');
            $table->dropIndex('idx_study_status');
            
            // Drop columns
            $table->dropColumn([
                'school_name',
                'school_address', 
                'school_province',
                'enrollment_date',
                'graduation_date',
                'study_status',
                'is_current',
                'transfer_reason',
                'notes'
            ]);
        });
    }
};
