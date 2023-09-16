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
        Schema::create('post_comments', function (Blueprint $table) {
            // $table->ulid()->primary();
            // $table->foreignUlid('post_id')->constrained();
            // $table->foreignUlid('user_id')->constrained();
            // $table->foreignUlid('parent_post_comment_id')->constrained()->nullable();
            $table->id();
            $table->foreignId('post_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('parent_post_comment_id')->nullable();
            $table->text('content');
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->integer('replies')->default(0);
            $table->string('sentiment')->nullable();
            $table->string('privacy_settings')->default('public');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};
