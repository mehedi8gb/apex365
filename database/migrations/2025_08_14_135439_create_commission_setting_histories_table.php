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
        Schema::create('commission_setting_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_setting_id')->constrained('commission_settings')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->json('old_levels'); // previous JSON value
            $table->json('new_levels'); // new JSON value
            $table->timestamps(); // when the change occurred
            $table->softDeletes();

            $table->index(['admin_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_setting_histories');
    }
};
