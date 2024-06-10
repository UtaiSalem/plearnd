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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('phone_number')->unique()->nullable();
            $table->string('google_id')->unique()->nullable();
            $table->string('facebook_id')->unique()->nullable();
            $table->string('twitter_id')->unique()->nullable();
            $table->string('linkedin_id')->unique()->nullable();
            $table->string('github_id')->unique()->nullable();
            $table->string('suggester_code')->default('99999999'); //suggester_code = personal_code of the suggester 

            $table->string('personal_code')->unique();
            $table->string('reference_code')->unique();
            $table->Integer('no_of_ref')->default(0); //number of referals this user has
            $table->unsignedBigInteger('pp')->default(0); //personal point
            $table->unsignedBigInteger('wallet')->default(0); //personal point
            
            $table->boolean('verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
