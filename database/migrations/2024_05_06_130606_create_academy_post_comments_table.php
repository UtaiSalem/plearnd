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
        Schema::create('academy_post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_post_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->text('content');
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->integer('replies')->default(0);
            $table->foreignId('parent_comment_id')->nullable()->constrained('academy_post_comments');
            $table->string('sentiment')->nullable();
            $table->json('hashtags')->nullable();
            // $table->json('privacy_settings')->nullable();
            // $table->json('mentions')->nullable();
            // $table->json('images')->nullable();
            // $table->json('videos')->nullable();
            // $table->json('audios')->nullable();
            // $table->json('files')->nullable();
            // $table->json('links')->nullable();
            // $table->json('polls')->nullable();
            // $table->json('events')->nullable();
            // $table->json('products')->nullable();
            // $table->json('services')->nullable();
            // $table->json('places')->nullable();
            // $table->json('people')->nullable();
            // $table->json('organizations')->nullable();
            // $table->json('communities')->nullable();
            // $table->json('groups')->nullable();
            // $table->json('pages')->nullable();
            // $table->json('posts')->nullable();
            // $table->json('comments')->nullable();   
            // $table->json('reactions')->nullable();
            // $table->json('shares')->nullable();
            // $table->json('views')->nullable();
            // $table->json('reports')->nullable();
            // $table->json('notifications')->nullable();
            // $table->json('activities')->nullable();
            // $table->json('logs')->nullable();
            // $table->json('settings')->nullable();
            // $table->json('metadata')->nullable();
            // $table->json('relationships')->nullable();
            // $table->json('permissions')->nullable();
            // $table->json('policies')->nullable();
            // $table->json('rules')->nullable();
            // $table->json('guidelines')->nullable();
            // $table->json('standards')->nullable();
            // $table->json('requirements')->nullable();
            // $table->json('protocols')->nullable();
            // $table->json('mechanisms')->nullable();
            // $table->json('systems')->nullable();
            // $table->json('technologies')->nullable();
            // $table->json('tools')->nullable();
            // $table->json('methods')->nullable();
            // $table->json('processes')->nullable();
            // $table->json('procedures')->nullable();
            // $table->json('policies')->nullable();
            // $table->json('principles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academy_post_comments');
    }
};
