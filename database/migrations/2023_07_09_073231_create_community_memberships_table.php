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
        Schema::create('community_memberships', function (Blueprint $table) {
            // $table->ulid('id')->primary();
            // $table->foreignUlid('community_id')->constrained();
            // $table->foreignUlid('user_id')->constrained();
            $table->id();
            $table->foreignId('community_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('role')->default('member');
            $table->timestamp('join_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_active')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_memberships');
    }
};
