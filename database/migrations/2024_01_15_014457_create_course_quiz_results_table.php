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
        Schema::create('course_quiz_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->constrained();
            $table->unsignedBigInteger('course_id')->constrained();
            $table->unsignedBigInteger('quiz_id')->constrained();
            $table->integer('score')->nullable();
            $table->float('percentage');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('attempted_questions')->nullable();
            $table->integer('correct_answers')->nullable();
            $table->integer('incorrect_answers')->nullable();
            $table->integer('skipped_questions')->nullable();
            $table->integer('status')->nullable(); //0=>pending, 1=>blocked, 2=>passed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_quiz_results');
    }
};
