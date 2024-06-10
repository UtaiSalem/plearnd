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
        Schema::create('donates', function (Blueprint $table) {
            $table->id();
            $table->integer('donor_id')->nullable();
            $table->string('donor_name')->nullable();
            $table->decimal('amounts', 10, 2); // Assuming a decimal data type for amount
            $table->string('slip');
            $table->date('transfer_date');
            $table->time('transfer_time')->nullable();
            $table->string('donor_email')->nullable();
            $table->string('donation_purpose')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->integer('remaining_points')->nullable(); // Assuming a big integer data type for balance
            $table->tinyInteger('status')->default(0); // Assuming a tiny integer data type for status , [0=>'pending', 1=>'approved', 2=>'rejected']
            $table->tinyInteger('approved_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donates');
    }
};
