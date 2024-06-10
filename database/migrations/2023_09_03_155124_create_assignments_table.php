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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->morphs('assignmentable');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('points')->default(0)->nullable();
            $table->integer('increase_points')->default(0)->nullable();
            $table->integer('decrease_points')->default(0)->nullable();
            $table->string('assignment_type')->nullable();
            $table->string('submission_method')->nullable();
            $table->integer('max_file_size')->nullable();
            $table->boolean('is_group_assignment')->default(false);
            $table->json('target_groups')->nullable();
            // $table->enum('status', ['not_started', 'in_progress', 'submitted', 'graded'])->nullable();
            $table->tinyInteger('status')->nullable(); //, [0=>not_started, 1=> 'published', 2=> 'in_progress', 3=> 'submitted', 4=> 'graded]
            $table->text('grading_rubric')->nullable()->nullable();
            $table->integer('graded_score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
