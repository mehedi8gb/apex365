<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('id'); // Adding index to 'id' column
            $table->index('phone'); // If filtering by phone frequently
        });

        Schema::table('referral_codes', function (Blueprint $table) {
            $table->index('user_id'); // Index for foreign key
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']); // Composite index for filtering & sorting
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['id']);
            $table->dropIndex(['phone']);
        });

        Schema::table('referral_codes', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
        });
    }
};

