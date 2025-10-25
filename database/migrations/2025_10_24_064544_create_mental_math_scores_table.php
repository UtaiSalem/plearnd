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
        Schema::create('mental_math_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('player_name');
            $table->integer('score');
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->integer('combo')->default(0);
            $table->integer('questions_answered')->default(0);
            $table->float('accuracy')->default(0);
            $table->integer('time_spent')->default(0); // in seconds
            $table->boolean('is_practice_mode')->default(false);
            $table->boolean('is_daily_challenge')->default(false);
            $table->date('date_played');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mental_math_scores');
    }
};
