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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referrer_level_1')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('referrer_level_2')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('referrer_level_3')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('purchase_amount', 10, 2);
            $table->decimal('level_1_commission', 10, 2);
            $table->decimal('level_2_commission', 10, 2);
            $table->decimal('level_3_commission', 10, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
