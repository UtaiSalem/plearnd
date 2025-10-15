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
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('document_type', [
                'profile_image', 'birth_certificate', 'house_registration', 
                'parent_id', 'transcript', 'medical_certificate', 'other'
            ]);
            
            $table->string('original_name', 255);
            $table->string('stored_name', 255);
            $table->string('file_path', 500);
            $table->bigInteger('file_size')->unsigned()->nullable();
            $table->string('mime_type', 100)->nullable();
            
            $table->text('description')->nullable()->comment('คำอธิบาย');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->comment('ผู้อัปโหลด');
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->comment('ผู้ตรวจสอบ');
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('document_type');
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
