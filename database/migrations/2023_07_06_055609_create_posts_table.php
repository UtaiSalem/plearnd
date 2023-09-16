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
        Schema::create('posts', function (Blueprint $table) {
            // $table->ulid('id')->primary();
            // $table->foreignUlid('user_id')->constrained();
            $table->id();
            $table->foreignId('user_id')->constrained();
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
            $table->string('post_type')->nullable();
            $table->string('source_platform')->nullable();
            $table->ulid('parent_post_id')->nullable();
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
        Schema::dropIfExists('posts');
    }
};
