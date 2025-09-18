<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->foreignId('commission_type_id')
                ->nullable()
                ->after('level')
                ->constrained('commission_settings')
                ->cascadeOnDelete();
        });

    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropForeign(['commission_type_id']);
            $table->dropColumn('commission_type_id');
        });
    }
};
