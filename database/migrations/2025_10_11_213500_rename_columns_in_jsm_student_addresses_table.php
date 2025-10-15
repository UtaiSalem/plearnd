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
        Schema::table('jsm_student_addresses', function (Blueprint $table) {
            // Rename columns from English to Thai or more descriptive names
            $table->renameColumn('address_line1', 'house_number');
            $table->renameColumn('address_line2', 'village_name');
            $table->renameColumn('city', 'district');
            $table->renameColumn('state', 'province');
            $table->renameColumn('postal_code', 'zip_code');
            $table->renameColumn('country', 'nation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jsm_student_addresses', function (Blueprint $table) {
            // Reverse the column renames
            $table->renameColumn('house_number', 'address_line1');
            $table->renameColumn('village_name', 'address_line2');
            $table->renameColumn('district', 'city');
            $table->renameColumn('province', 'state');
            $table->renameColumn('zip_code', 'postal_code');
            $table->renameColumn('nation', 'country');
        });
    }
};