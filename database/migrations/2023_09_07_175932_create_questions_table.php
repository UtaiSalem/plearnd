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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('course_id')->constrained();
            $table->morphs('questionable');
            $table->text('text');
            $table->string('type')->nullable();
            $table->integer('correct_answers')->nullable();
            $table->integer('correct_option_id')->nullable();
            $table->text('explanation')->nullable();
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->nullable();
            $table->integer('time_limit')->nullable();
            $table->integer('points');
            $table->integer('position')->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
