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
        Schema::table('student_addresses', function (Blueprint $table) {
            // เปลี่ยน enum ให้รวม 'temporary'
            $table->enum('address_type', ['current', 'permanent', 'temporary'])->default('current')->change();
            
            // เปลี่ยนชื่อ column จาก is_primary เป็น is_current
            $table->renameColumn('is_primary', 'is_current');
            
            // ลบ country column ถ้าไม่ต้องการใช้
            if (Schema::hasColumn('student_addresses', 'country')) {
                $table->dropColumn('country');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_addresses', function (Blueprint $table) {
            // เปลี่ยนกลับ enum
            $table->enum('address_type', ['current', 'permanent', 'contact'])->default('current')->change();
            
            // เปลี่ยนชื่อกลับ
            $table->renameColumn('is_current', 'is_primary');
            
            // เพิ่ม country column กลับ
            $table->string('country', 50)->default('ประเทศไทย');
        });
    }
};
