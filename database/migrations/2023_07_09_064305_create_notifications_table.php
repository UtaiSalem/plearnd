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
        Schema::create('notifications', function (Blueprint $table) {
            // $table->ulid('id')->primary();
            // $table->foreignUlid('user_id')->constrained();
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->text('content');
            $table->boolean('read_status')->default(false);
            $table->string('action_url')->nullable();
            $table->string('type');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
