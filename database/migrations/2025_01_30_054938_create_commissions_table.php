<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('from_user_id')->constrained('users');
            $table->decimal('amount', 10, 2);
            $table->unsignedTinyInteger('level');
            $table->timestamps();

            // Critical indexes
            $table->index(['user_id', 'level']);
            $table->index('from_user_id');
        });

        // Partition by month for 100M+ rows
//        DB::statement('ALTER TABLE commissions PARTITION BY RANGE (YEAR(created_at)*100 + MONTH(created_at))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
