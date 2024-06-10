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
        Schema::create('academy_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academy_id');
            $table->foreignId('user_id');
            $table->unsignedInteger('member_code')->nullable(); //รหัสสมาชิก
            $table->tinyInteger('status')->default(0); //[0=>'pending', 1=>'accepted', 2=>'blocked', 3=>'rejected']
                        
            $table->string('role')->nullable();  // Optional: Role within the academy
            $table->date('enrollment_date')->default(now());  // Optional: Enrollment date
            $table->date('graduation_date')->nullable();  // Optional: Graduation date
            $table->enum('graduation_reason', ['completed', 'expelled', 'dropped_out', 'transferred', 'other'])->nullable();
            
            $table->text('note_comment')->nullable();
            $table->text('additional_info')->nullable();  // Optional: Additional information

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academy_member');
    }
};
