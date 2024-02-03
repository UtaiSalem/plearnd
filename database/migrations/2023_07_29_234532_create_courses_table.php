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
            $table->decimal('tuition_fees', 10, 2)->nullable(); // Personal point paid to be a course member
            $table->decimal('price', 8, 2)->nullable(); //Price for sell ($)
            $table->string('category')->nullable();
            $table->integer('credit_units')->default(0);
            $table->integer('hours_per_week')->default(1); //Hours per week, Weekly hours,Number of Class Hours per Week
            // $table->integer('total_study_time')->default(0);
            // $table->string('instructor')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('enrolled_students')->default(0);
            $table->text('class_schedule')->nullable();
            $table->text('prerequisites')->nullable();
            $table->text('course_materials')->nullable();
            $table->integer('status')->default(0); //0=>inactive, 1 active;
            $table->boolean('salable')->default(false)->nullable();
            // $table->string('location')->nullable();
            $table->string('accreditation')->nullable();
            $table->string('accreditation_body')->nullable();
            $table->string('level')->nullable();
            $table->float('rating', 2, 1)->nullable();
            // $table->string('logo')->nullable();
            $table->string('cover')->nullable();
            $table->text('syllabus')->nullable();
            $table->integer('total_score')->default(0)->nullable();
            $table->boolean('certificate')->default(false);
            $table->timestamps();

            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cassade');

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
