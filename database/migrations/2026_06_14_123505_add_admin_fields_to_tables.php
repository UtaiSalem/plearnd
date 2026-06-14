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
        if (!Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('role');
            });
        }
        if (!Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_login_at')->nullable()->after('updated_at');
            });
        }

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bookings', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->after('cancelled_at');
            }
            if (!Schema::hasColumn('bookings', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->after('cancelled_by');
            }
            if (!Schema::hasColumn('bookings', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('rejection_reason');
            }
            
            // We'll skip index creation in this safe-migration if columns already exist 
            // to avoid "Index already exists" errors, or use a try-catch if needed.
            // Since I saw them already in db:table, I'll only add them if cancelled_at was just added.
        });

        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'is_bookable')) {
                $table->boolean('is_bookable')->default(true)->after('summary');
            }
            if (!Schema::hasColumn('rooms', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_bookable');
            }
            if (!Schema::hasColumn('rooms', 'color')) {
                $table->string('color')->nullable()->after('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['is_bookable', 'sort_order', 'color']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['cancelled_at', 'cancelled_by', 'updated_by', 'admin_note']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'last_login_at']);
        });
    }
};
