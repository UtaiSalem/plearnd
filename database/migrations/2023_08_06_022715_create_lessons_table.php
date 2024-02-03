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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description');
            $table->text('content')->nullable();
            $table->text('video_url')->nullable();
            $table->text('youtube_url')->nullable();
            $table->integer('duration')->nullable();
            $table->enum('status', [0, 1])->default(1); //[0=>'draft', 2=>'publish',]
            $table->unsignedInteger('order')->nullable();
            $table->unsignedInteger('point_tuition_fee')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
