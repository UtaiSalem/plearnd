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
        Schema::create('student_home_visits', function (Blueprint $table) {
            $table->id();
            
            // Student Information
            $table->string('student_name');
            $table->string('class', 10);
            $table->date('birth_date');
            $table->enum('gender', ['ชาย', 'หญิง']);
            $table->string('id_card', 17);
            $table->string('phone', 15);
            
            // Address Information
            $table->string('house_number', 20);
            $table->string('village_number', 10)->nullable();
            $table->string('village_name', 100)->nullable();
            $table->string('province', 100);
            $table->string('district', 100);
            $table->string('subdistrict', 100);
            $table->string('postal_code', 5);
            
            // Family Information
            $table->string('father_name')->nullable();
            $table->integer('father_age')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name')->nullable();
            $table->integer('mother_age')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship', 100)->nullable();
            $table->integer('siblings_count')->nullable();
            $table->integer('sibling_position')->nullable();
            
            // Home Environment
            $table->string('house_type', 100)->nullable();
            $table->string('ownership_status', 100)->nullable();
            $table->json('utilities')->nullable();
            $table->string('study_space', 100)->nullable();
            $table->boolean('internet_access')->nullable();
            
            // Student Behavior and Learning
            $table->text('learning_behavior')->nullable();
            $table->text('home_responsibilities')->nullable();
            $table->text('extracurricular')->nullable();
            $table->text('academic_support')->nullable();
            $table->text('challenges')->nullable();
            $table->text('family_expectations')->nullable();
            
            // Visit Information
            $table->date('visit_date');
            $table->string('visit_time');
            $table->string('visitor_name');
            $table->string('visitor_position');
            
            // Recommendations
            $table->text('recommendations')->nullable();
            $table->text('follow_up')->nullable();
            $table->date('next_visit')->nullable();
            
            // Photos and signatures
            $table->json('photos')->nullable();
            $table->text('parent_signature')->nullable();
            $table->text('visitor_signature')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['student_name', 'class']);
            $table->index('visit_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_home_visits');
    }
};
