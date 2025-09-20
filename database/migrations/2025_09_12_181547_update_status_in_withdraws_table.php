<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdraws', function (Blueprint $table) {
            // Drop old column
            $table->dropColumn('status');
        });

        Schema::table('withdraws', function (Blueprint $table) {
            // Add new column with new enum values
            $table->enum('status', ['pending', 'approved', 'suspended'])
                  ->default('pending')
                  ->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('withdraws', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('withdraws', function (Blueprint $table) {
            $table->enum('status', ['due', 'paid'])
                  ->default('due')
                  ->after('payment_method');
        });
    }
};
