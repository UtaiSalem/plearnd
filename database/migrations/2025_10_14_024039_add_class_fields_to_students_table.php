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
        Schema::table('students', function (Blueprint $table) {
            // เพิ่มฟิลด์ห้องเรียนและชั้นเรียน
            $table->string('class_level', 10)->nullable()->after('enrollment_date')->comment('ระดับชั้น เช่น ม.1, ม.2');
            $table->string('class_section', 10)->nullable()->after('class_level')->comment('ห้องเรียน เช่น 1, 2, 3');
            
            // เพิ่ม index สำหรับการค้นหา
            $table->index(['class_level', 'class_section']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['class_level', 'class_section']);
            $table->dropColumn(['class_level', 'class_section']);
        });
    }
};
