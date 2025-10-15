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
        Schema::table('student_guardians', function (Blueprint $table) {
            $table->string('student_code', 20)->nullable()->after('student_id')->comment('รหัสนักเรียน');
            $table->index('student_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_guardians', function (Blueprint $table) {
            $table->dropIndex(['student_code']);
            $table->dropColumn('student_code');
        });
    }
};
