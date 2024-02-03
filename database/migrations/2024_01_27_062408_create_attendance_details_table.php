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
        Schema::create('attendance_details', function (Blueprint $table) {
            $table->id();
            $table->morphs('attendanceable');
            $table->foreignId('course_attendance_id')->constrained();
            $table->foreignId('course_id')->constrained();
            $table->foreignId('group_id')->constrained();
            $table->foreignId('course_member_id')->constrained();
            $table->tinyInteger('status'); // 0=>'absent', 1 => 'present', 2=>'late', 3=>'excused'
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->text('comments')->nullable();
            $table->unsignedInteger('late_threshold')->nullable();
            $table->string('excused_absence_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_details');
    }
};
