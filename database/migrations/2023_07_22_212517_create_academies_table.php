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
        Schema::create('academies', function (Blueprint $table) {
            $table->id();
            // $table->ulid('id')->primary();            
            $table->foreignId('user_id')->constrained();
            $table->string('name')->unique();
            $table->string('slogan')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('director')->nullable();
            $table->unsignedSmallInteger('established_year')->nullable();
            $table->string('type')->nullable();
            $table->string('accreditation')->nullable();
            $table->string('accreditation_body')->nullable();
            $table->unsignedInteger('total_students')->default(0);
            $table->unsignedInteger('total_teachers')->default(0);
            $table->unsignedInteger('membership_fees_points')->default(0);
            $table->unsignedInteger('courses_offered')->nullable();
            $table->text('facilities')->nullable();
            $table->text('academy_timings')->nullable();
            $table->text('holidays')->nullable();
            $table->string('social_media_links')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academies');
    }
};
