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
            $table->integer('course_member_status')->nullable();
            $table->integer('group_member_status')->nullable();
            $table->timestamp('enrollment_date')->useCurrent();
            $table->enum('status', [0, 1, 2, 3, 4, 5])->default(1); 
            //[0=>'pending' ,1=>'enrolled', 2=>'completed', 3=>'in_progress', 4=>'dropped', 5=>'archived/completed']
            //[0=>'waiting', 1=>'enrolled', 2='in_progress', 3=>'blocked', 4=>'dropped', 5=>'archived/completed']
            $table->enum('role', ['student', 'student_leader'])->default('student');
            $table->integer('order_number')->nullable();
            $table->integer('grade_progress')->nullable();
            $table->integer('achieved_score')->nullable();
            $table->enum('member_status', [0, 1, 2, 3, 4, 5])->nullable(); 
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
