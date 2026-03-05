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
        Schema::create('admin_rank_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Pro Platinum, Platinum, etc
            $table->unsignedInteger('threshold'); // e.g., 10
            $table->decimal('coins', 12, 2);     // e.g., 30000.00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_rank_settings');
    }
};
