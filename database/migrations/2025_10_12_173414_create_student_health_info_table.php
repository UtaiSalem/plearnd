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
        Schema::create('student_health_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            $table->decimal('height_cm', 5, 2)->nullable()->comment('ส่วนสูง (ซม.)');
            $table->decimal('weight_kg', 5, 2)->nullable()->comment('น้ำหนัก (กก.)');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable()->comment('หมู่เลือด');
            $table->enum('rh_factor', ['+', '-'])->nullable()->comment('RH');
            
            $table->text('allergies')->nullable()->comment('อาการแพ้');
            $table->text('chronic_diseases')->nullable()->comment('โรคประจำตัว');
            $table->text('medications')->nullable()->comment('ยาที่ต้องรับประทานสม่ำเสมอ');
            
            $table->date('last_checkup_date')->nullable()->comment('วันที่ตรวจสุขภาพล่าสุด');
            
            $table->timestamps();
            
            // Unique constraint - one health record per student
            $table->unique('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_health_info');
    }
};
