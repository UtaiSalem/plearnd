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
        // แปลงข้อมูลเดิมให้เป็นมาตรฐาน (ตั้งค่าเริ่มต้นเป็น 0 สำหรับกรณีที่ไม่รู้)
        DB::statement("UPDATE students SET gender = CASE 
            WHEN gender IN ('หญิง', 'female', 'f', 'F') THEN '0'
            WHEN gender IN ('ชาย', 'male', 'm', 'M') THEN '1'  
            WHEN gender = '0' THEN '0'
            WHEN gender = '1' THEN '1'
            ELSE '0'
        END");
        
        // เปลี่ยน column type เป็น tinyint
        Schema::table('students', function (Blueprint $table) {
            $table->tinyInteger('gender')->nullable()->change()->comment('เพศ: 0=หญิง, 1=ชาย');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // เปลี่ยน column type กลับเป็น string
        Schema::table('students', function (Blueprint $table) {
            $table->string('gender', 10)->nullable()->change()->comment('เพศ');
        });
        
        // แปลงข้อมูลกลับเป็นข้อความ
        DB::statement("UPDATE students SET gender = CASE 
            WHEN gender = 0 THEN 'หญิง'
            WHEN gender = 1 THEN 'ชาย'
            ELSE NULL
        END");
    }
};
