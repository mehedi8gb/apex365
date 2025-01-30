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
        Schema::create('referral_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Referral code
            $table->enum('type', ['admin', 'user'])->default('user'); // "admin" for initial codes
            $table->unsignedBigInteger('user_id')->nullable(); // Null if it's an admin code
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_codes');
    }
};
