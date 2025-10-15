<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_academic_info', function (Blueprint $table) {
            // เพิ่มคอลัมน์ใหม่เป็น tinyint
            $table->tinyInteger('education_level_int')
                  ->nullable()
                  ->after('education_level')
                  ->comment('ระดับการศึกษา: 0=อนุบาล, 1=ประถม, 2=มัธยม');
        });

        // แปลงข้อมูลจาก string เป็น integer
        DB::statement("UPDATE student_academic_info SET education_level_int = CASE 
            WHEN education_level = 'kindergarten' THEN 0 
            WHEN education_level = 'primary' THEN 1 
            WHEN education_level = 'secondary' THEN 2 
            ELSE NULL 
        END");

        Schema::table('student_academic_info', function (Blueprint $table) {
            // ลบคอลัมน์เดิมและ rename คอลัมน์ใหม่
            $table->dropIndex(['education_level']);
            $table->dropColumn('education_level');
        });

        Schema::table('student_academic_info', function (Blueprint $table) {
            // เปลี่ยนชื่อคอลัมน์และเพิ่ม index
            $table->renameColumn('education_level_int', 'education_level');
            $table->index('education_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_academic_info', function (Blueprint $table) {
            // เพิ่มคอลัมน์ enum กลับมา
            $table->enum('education_level_enum', ['kindergarten', 'primary', 'secondary'])
                  ->nullable()
                  ->after('education_level')
                  ->comment('ระดับการศึกษา: kindergarten=อนุบาล, primary=ประถม, secondary=มัธยม');
        });

        // แปลงข้อมูลจาก integer เป็น string
        DB::statement("UPDATE student_academic_info SET education_level_enum = CASE 
            WHEN education_level = 0 THEN 'kindergarten'
            WHEN education_level = 1 THEN 'primary'
            WHEN education_level = 2 THEN 'secondary'
            ELSE NULL 
        END");

        Schema::table('student_academic_info', function (Blueprint $table) {
            // ลบคอลัมน์เดิมและ rename คอลัมน์ใหม่
            $table->dropIndex(['education_level']);
            $table->dropColumn('education_level');
        });

        Schema::table('student_academic_info', function (Blueprint $table) {
            // เปลี่ยนชื่อคอลัมน์และเพิ่ม index
            $table->renameColumn('education_level_enum', 'education_level');
            $table->index('education_level');
        });
    }
};
