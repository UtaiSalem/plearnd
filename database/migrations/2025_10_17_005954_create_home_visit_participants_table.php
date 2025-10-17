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
        Schema::create('home_visit_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_visit_id')->constrained('student_home_visits')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable()->comment('ถ้าเป็น user ในระบบ');
            $table->string('participant_name')->comment('ชื่อผู้เข้าร่วม');
            $table->string('participant_position')->nullable()->comment('ตำแหน่ง');
            $table->enum('participant_type', ['teacher', 'admin', 'counselor', 'external'])->default('teacher');
            $table->enum('role', ['primary', 'observer', 'assistant'])->default('observer')->comment('primary = ครูหลัก, observer = สังเกตการณ์, assistant = ผู้ช่วย');
            $table->text('notes')->nullable()->comment('บันทึกเพิ่มเติมของผู้เข้าร่วม');
            $table->timestamps();
            
            $table->index(['home_visit_id', 'role']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_visit_participants');
    }
};
