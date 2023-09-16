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
        Schema::create('activities', function (Blueprint $table) {

            // $table->ulid('id')->primary();
            // $table->foreignUlid('user_id')->constrained();
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->ulidmorphs('activityable');
            $table->string('activity_type');
            $table->text('activity_details')->nullable();        
            $table->timestamps();  
            
            

            // $table->string('action')->nullable();
            // $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
