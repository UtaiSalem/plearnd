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
        Schema::create('course_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained();
            $table->foreignId('group_id')->constrained();
            // instructor_id is the user_id of the instructor
            $table->foreignId('instuctor_id')->constrained();
            $table->date('date');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_attendances');
    }
};
