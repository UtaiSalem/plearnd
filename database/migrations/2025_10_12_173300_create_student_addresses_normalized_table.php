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
        Schema::create('student_addresses_normalized', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('address_type', ['current', 'permanent', 'contact'])->default('current');
            
            $table->string('house_number', 50)->nullable()->comment('บ้านเลขที่');
            $table->string('village_number', 10)->nullable()->comment('หมู่');
            $table->string('village_name', 100)->nullable()->comment('ชื่อหมู่บ้าน');
            $table->string('alley', 100)->nullable()->comment('ซอย');
            $table->string('road', 100)->nullable()->comment('ถนน');
            $table->string('subdistrict', 100)->nullable()->comment('ตำบล/แขวง');
            $table->string('district', 100)->nullable()->comment('อำเภอ/เขต');
            $table->string('province', 100)->nullable()->comment('จังหวัด');
            $table->string('postal_code', 10)->nullable()->comment('รหัสไปรษณีย์');
            $table->string('country', 50)->default('ประเทศไทย');
            
            $table->boolean('is_primary')->default(true)->comment('ที่อยู่หลัก');
            
            $table->timestamps();
            
            // Indexes
            $table->index('address_type');
            $table->index(['province', 'district']);
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_addresses_normalized');
    }
};
