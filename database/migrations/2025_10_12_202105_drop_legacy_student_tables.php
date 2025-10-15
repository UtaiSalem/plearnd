<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop legacy student tables that are no longer needed after normalization
     */
    public function up(): void
    {
        // Drop legacy tables in reverse dependency order
        Schema::dropIfExists('student_info'); // Monolithic table - replaced by normalized structure
        Schema::dropIfExists('jsm_student_info'); // Temporary table during migration - no longer needed
        Schema::dropIfExists('student_addresses'); // Legacy addresses table - replaced by new normalized student_addresses
        
        // Note: student_cards table is kept as it serves StudentCard system
        // Note: student_home_visits table is kept as it serves HomeVisit system
    }

    /**
     * Reverse the migrations.
     * Recreate the tables if rollback is needed (not recommended)
     */
    public function down(): void
    {
        // This rollback is not recommended as it would require 
        // complex data reconstruction from normalized tables
        // The tables were monolithic and contained redundant data
        
        // If needed, restore from backup files:
        // - backup_student_info.json
        // - backup_student_addresses.json
    }
};
