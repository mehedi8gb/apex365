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
        Schema::create('referral_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade');  // Referrer user
            $table->foreignId('referral_code_id')->constrained('referral_codes')->onDelete('cascade');
            $table->integer('level');  // Level in the referral chain (1, 2, 3)
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_users');
    }
};
