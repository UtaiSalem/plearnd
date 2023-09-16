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
            // $table->ulid('id')->primary();
            $table->id();
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('full_name')->nullable();
            $table->unsignedBigInteger('pp')->nullable()->default(0); //personal point
            $table->unsignedBigInteger('wallet')->nullable()->default(0); //personal point
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken()->nullable();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('profile_cover_path', 2048)->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender')->nullable();
            $table->string('privacy_settings')->default('public');
            $table->unsignedBigInteger('followers')->default(0);
            $table->unsignedBigInteger('following')->default(0);
            $table->unsignedBigInteger('friends')->default(0);
            $table->timestamp('join_date')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->boolean('verified')->default(false);
            $table->string('account_status')->default('active');
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
