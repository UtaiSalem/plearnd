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
        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('guardian_type', ['father', 'mother', 'guardian', 'other']);
            
            $table->string('citizen_id', 13)->nullable()->comment('เลขประจำตัวประชาชน');
            $table->string('title_prefix', 20)->nullable()->comment('คำนำหน้าชื่อ');
            $table->string('first_name', 100)->comment('ชื่อ');
            $table->string('last_name', 100)->comment('นามสกุล');
            
            $table->string('occupation', 100)->nullable()->comment('อาชีพ');
            $table->string('workplace', 200)->nullable()->comment('สถานที่ทำงาน');
            $table->decimal('monthly_income', 10, 2)->nullable()->comment('รายได้ต่อเดือน');
            
            $table->string('relationship', 50)->nullable()->comment('ความสัมพันธ์');
            $table->enum('status', ['alive', 'deceased', 'unknown'])->default('alive');
            $table->string('nationality', 50)->default('ไทย');
            
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('is_emergency_contact')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index('guardian_type');
            $table->index('is_primary_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_guardians');
    }
};
