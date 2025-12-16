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
        Schema::table('suppliers', function (Blueprint $table) {
            // Check if columns exist before adding them to avoid duplicate column errors
            if (!Schema::hasColumn('suppliers', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('suppliers', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'tax_number')) {
                $table->string('tax_number')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'business_name')) {
                $table->string('business_name')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'balance')) {
                $table->decimal('balance', 10, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
             $table->dropColumn(['name', 'email', 'phone', 'address', 'tax_number', 'business_name', 'balance']);
        });
    }
};
