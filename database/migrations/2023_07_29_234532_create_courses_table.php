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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('academy_id')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('tuition_fee', 10, 2)->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('category')->nullable();
            $table->string('instructor')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('enrolled_students')->default(0);
            $table->text('class_schedule')->nullable();
            $table->text('prerequisites')->nullable();
            $table->text('course_materials')->nullable();
            $table->string('status')->default('Open');
            $table->string('location')->nullable();
            $table->string('accreditation')->nullable();
            $table->string('accreditation_body')->nullable();
            $table->string('level')->nullable();
            $table->float('rating', 2, 1)->nullable();
            $table->string('logo')->nullable();
            $table->string('cover')->nullable();
            $table->text('syllabus')->nullable();
            $table->boolean('certificate')->default(false);
            $table->timestamps();

            $table->foreign('instructor_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
