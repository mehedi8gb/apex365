<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing transactions table if it exists
        Schema::dropIfExists('transactions');

        // Recreate the transactions table with optimized structure
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transactionId')->unique(); // Indexed column for fast search
            $table->timestamps();

            $table->index('transactionId');
        });
    }

    public function down(): void
    {
        // In case of rollback, drop the table (BE CAREFUL!)
        Schema::dropIfExists('transactions');
    }
};
