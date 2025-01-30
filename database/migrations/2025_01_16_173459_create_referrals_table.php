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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users');
            $table->foreignId('referrer_id')->constrained('users');
            $table->foreignId('referral_code_id')->constrained();
            $table->unsignedTinyInteger('level');
            $table->timestamps();

            // Optimized indexes
            $table->index(['referrer_id', 'level']);
            $table->index('referral_code_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
