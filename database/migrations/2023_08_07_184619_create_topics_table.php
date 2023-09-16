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
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->unsignedBigInteger('academy_id')->nullable();
            $table->foreignId('course_id')->constrained()->nullable();
            $table->foreignId('lesson_id')->constrained()->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('status')->default('published');
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('views')->default(0);
            $table->string('hashtags')->nullable();
            $table->string('privacy_settings')->default('public');
            $table->string('location')->nullable();
            $table->string('url')->nullable();
            $table->string('tags')->nullable();
            $table->string('sentiment')->nullable();
            $table->float('engagement_rate')->default(0);
            $table->string('source_platform')->nullable();
            $table->timestamp('interacted_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
