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
        Schema::create('friends', function (Blueprint $table) {
            // $table->ulid('id');
            $table->id();
            // $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('friend_id');
            // $table->string('status');
            // $table->unsignedBigInteger('action_user_id');
            // $table->timestamp('created_at')->nullable();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('friend_id')->constrained('users')->onDelete('cascade');
            $table->string('status');
            $table->foreignId('action_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
};
