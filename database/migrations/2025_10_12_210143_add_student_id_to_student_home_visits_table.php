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
        Schema::table('student_home_visits', function (Blueprint $table) {
            // Add foreign key to students table (normalized database)
            $table->unsignedBigInteger('student_id')->nullable()->after('id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
            
            // Keep existing standalone fields for backup/independence
            // This hybrid approach provides both relational benefits and standalone capability
            $table->index(['student_id', 'visit_date']);
            $table->index('id_card'); // For searching by national ID
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_home_visits', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropIndex(['student_id', 'visit_date']);
            $table->dropIndex(['id_card']);
            $table->dropColumn('student_id');
        });
    }
};
