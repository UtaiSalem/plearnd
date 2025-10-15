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
        // First, rename the table from student_cards to jsm_students_info
        Schema::rename('student_cards', 'jsm_students_info');
        
        // Then rename columns in the jsm_students_info table
        Schema::table('jsm_students_info', function (Blueprint $table) {
            // Rename columns to more standardized names
            $table->renameColumn('student_fullname_th', 'full_name_thai');
            $table->renameColumn('student_class', 'class_level');
            $table->renameColumn('student_section', 'class_section');
            $table->renameColumn('id_card_no', 'national_id');
            $table->renameColumn('student_id', 'student_number');
            $table->renameColumn('level_room', 'level_and_room');
            $table->renameColumn('prefix_name', 'title_name');
            $table->renameColumn('student_name_th', 'first_name_thai');
            $table->renameColumn('last_name_th', 'last_name_thai');
            $table->renameColumn('student_name_en', 'first_name_english');
            $table->renameColumn('date_of_birth', 'birth_date');
            $table->renameColumn('date_of_birth_str', 'birth_date_string');
            $table->renameColumn('issue_date', 'card_issue_date');
            $table->renameColumn('expiry_date', 'card_expiry_date');
            $table->renameColumn('status', 'student_status');
            $table->renameColumn('student_image', 'profile_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the column renames first
        Schema::table('jsm_students_info', function (Blueprint $table) {
            $table->renameColumn('full_name_thai', 'student_fullname_th');
            $table->renameColumn('class_level', 'student_class');
            $table->renameColumn('class_section', 'student_section');
            $table->renameColumn('national_id', 'id_card_no');
            $table->renameColumn('student_number', 'student_id');
            $table->renameColumn('level_and_room', 'level_room');
            $table->renameColumn('title_name', 'prefix_name');
            $table->renameColumn('first_name_thai', 'student_name_th');
            $table->renameColumn('last_name_thai', 'last_name_th');
            $table->renameColumn('first_name_english', 'student_name_en');
            $table->renameColumn('birth_date', 'date_of_birth');
            $table->renameColumn('birth_date_string', 'date_of_birth_str');
            $table->renameColumn('card_issue_date', 'issue_date');
            $table->renameColumn('card_expiry_date', 'expiry_date');
            $table->renameColumn('student_status', 'status');
            $table->renameColumn('profile_image', 'student_image');
        });
        
        // Then rename the table back to student_cards
        Schema::rename('jsm_students_info', 'student_cards');
    }
};