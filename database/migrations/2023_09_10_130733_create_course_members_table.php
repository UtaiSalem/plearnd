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
        Schema::create('course_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('course_id')->constrained();
            $table->foreignId('group_id')->constrained()->nullable();
            $table->timestamp('enrollment_date')->useCurrent();
            $table->enum('status', [1, 2, 3, 4, 5])->default(1); //[1=>'enrolled', 2=>'completed', 3=>'in_progress', 4=>'dropped', 5=>'archived']
            $table->string('role')->default('student');
            $table->integer('grade_progress')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->timestamp('access_expiry_date')->nullable();
            $table->text('notes_comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_members');
    }
};
