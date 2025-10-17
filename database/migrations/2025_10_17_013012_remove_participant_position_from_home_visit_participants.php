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
            $table->dropColumn('participant_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_visit_participants', function (Blueprint $table) {
            $table->string('participant_position')->nullable()->after('participant_name');
        });
    }
};
