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
            // ย้ายข้อมูลจาก province ไปยัง school_province ก่อนลบ
            \DB::statement("UPDATE student_academic_info SET school_province = province WHERE province IS NOT NULL AND (school_province IS NULL OR school_province = '')");
            
            // ลบ index และ column
            $table->dropIndex(['province']);
            $table->dropColumn('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_academic_info', function (Blueprint $table) {
            // เพิ่ม province column กลับมา
            $table->string('province')->nullable()->after('school_address')->comment('จังหวัดของโรงเรียน');
            $table->index('province');
        });
    }
};
