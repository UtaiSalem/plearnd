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
            $table->integer('status')->default(1); 
            $table->integer('member_status')->nullable();
            //[0=>'pending' ,1=>'enrolled', 2=>'in_progress',3=>'blocked', 4=>'dropped', 5=>'archived/completed']
            //[0=>'waiting', 1=>'enrolled', 2='in_progress', 3=>'blocked', 4=>'dropped', 5=>'archived/completed']
            $table->integer('role')->default(1);
            $table->integer('order_number')->nullable();
            $table->integer('grade_progress')->nullable();
            $table->integer('achieved_score')->nullable();
            $table->integer('bonus_points')->nullable();
            $table->integer('last_accessed_tab')->nullable();
            $table->integer('last_accessed_group_tab')->nullable();
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
