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
        Schema::table('home_visit_participants', function (Blueprint $table) {
            $table->dropColumn('participant_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_visit_participants', function (Blueprint $table) {
            $table->enum('participant_type', ['teacher', 'admin', 'counselor', 'external'])->default('teacher')->after('participant_name');
        });
    }
};
