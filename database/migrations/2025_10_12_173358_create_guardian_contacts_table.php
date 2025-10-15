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
        Schema::create('guardian_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->constrained('student_guardians')->onDelete('cascade');
            $table->enum('contact_type', ['phone', 'mobile', 'email', 'line', 'facebook']);
            $table->string('contact_value', 255);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index('contact_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardian_contacts');
    }
};
