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
            // เพิ่มฟิลด์ระดับการศึกษา
            $table->enum('education_level', ['kindergarten', 'primary', 'secondary'])
                  ->nullable()
                  ->after('current_grade')
                  ->comment('ระดับการศึกษา: kindergarten=อนุบาล, primary=ประถม, secondary=มัธยม');
            
            // เพิ่ม index
            $table->index('education_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_academic_info', function (Blueprint $table) {
            $table->dropIndex(['education_level']);
            $table->dropColumn('education_level');
        });
    }
};
