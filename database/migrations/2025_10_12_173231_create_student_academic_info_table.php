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
        Schema::create('student_academic_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            $table->string('current_grade', 10)->nullable()->comment('ชั้นปีปัจจุบัน');
            $table->string('current_class', 10)->nullable()->comment('ห้องเรียนปัจจุบัน');
            $table->string('classroom_full', 20)->nullable()->comment('ชั้น/ห้อง เต็ม');
            
            // โรงเรียนเดิม
            $table->string('previous_school_name', 200)->nullable()->comment('ชื่อโรงเรียนเดิม');
            $table->string('previous_school_province', 100)->nullable()->comment('จังหวัดโรงเรียนเดิม');
            $table->string('previous_grade_level', 20)->nullable()->comment('ชั้นเรียนเดิม');
            
            // ความพิการ/ความต้องการพิเศษ
            $table->string('disability_type', 100)->nullable()->comment('ประเภทความพิการ');
            $table->text('special_needs')->nullable()->comment('ความต้องการพิเศษ');
            
            $table->string('academic_year', 10)->nullable()->comment('ปีการศึกษา');
            $table->tinyInteger('semester')->nullable()->comment('ภาคเรียน');
            
            $table->timestamps();
            
            // Indexes
            $table->index('academic_year');
            $table->index(['current_grade', 'current_class']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_academic_info');
    }
};
