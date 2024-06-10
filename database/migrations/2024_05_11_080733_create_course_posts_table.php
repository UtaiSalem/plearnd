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
        Schema::create('course_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('academy_id')->nullable()->constrained();
            $table->foreignId('course_id')->nullable()->constrained();
            $table->foreignId('group_id')->nullable()->constrained();
            $table->text('content');
            $table->string('status')->default(0); //0 =>pending, 1 = published, 2 => blocked
            $table->unsignedBigInteger('approved_by')->nullable()->constrained('users');
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('views')->default(0);
            $table->json('hashtags')->nullable();
            $table->string('privacy_settings')->default(3);
            $table->string('location')->nullable();
            $table->json('tags')->nullable();
            $table->string('sentiment')->nullable();
            $table->float('engagement_rate')->default(0);
            $table->string('post_type')->nullable();
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
        Schema::dropIfExists('course_posts');
    }
};
