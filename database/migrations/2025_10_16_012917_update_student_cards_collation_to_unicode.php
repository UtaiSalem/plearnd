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
        // เปลี่ยน collation ของตาราง student_cards ให้เป็น utf8mb3_unicode_ci
        // เหมือนกับตาราง students
        DB::statement('ALTER TABLE student_cards CONVERT TO CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // เปลี่ยนกลับเป็น collation เดิม
        DB::statement('ALTER TABLE student_cards CONVERT TO CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci');
    }
};
