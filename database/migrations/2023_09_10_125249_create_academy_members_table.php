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
            $table->tinyInteger('numbers')->nullable(); //รหัสนักเรียน
            $table->enum('status', [1, 2, 3, 4])->default(1); //[1=>'pending', 2=>'accepted', 3=>'blocked', 4=>'rejected']
            $table->unsignedBigInteger('action_user_id')->nullable();
            $table->timestamp('action_date')->nullable();
            $table->boolean('favorite')->default(false);
            $table->boolean('close_friends')->default(false);
            $table->text('note_comment')->nullable();
            $table->boolean('visibility_settings')->default(true);
            $table->boolean('blocked')->default(false);
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
