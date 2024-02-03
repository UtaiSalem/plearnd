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
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->unsignedBigInteger('supporter')->nullable();
            $table->integer('amounts');
            $table->integer('duration');
            $table->integer('total_views');
            $table->integer('remaining_views');
            $table->string('slip');
            $table->string('media_image');
            $table->string('media_link');
            $table->timestamp('transfer_date');
            $table->string('transfer_time');
            $table->enum('status', [0,1])->default(0); //0=pending, 1=approved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supports');
    }
};
