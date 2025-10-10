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
        Schema::create('assignment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('assignment_id')->constrained();
            $table->text('content')->nullable();
            $table->integer('points')->nullable();
            $table->dateTime('submission_date')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['submitted', 'in_review', 'graded'])->default('submitted');
            $table->boolean('late_submission')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_answers');
    }
};
