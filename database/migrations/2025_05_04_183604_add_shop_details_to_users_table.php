<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('business_name')->nullable();
        $table->string('business_type')->nullable();
        $table->string('shop_email')->nullable();
        $table->string('shop_phone')->nullable();
        $table->string('country')->nullable();
        $table->string('shop_address')->nullable();
        $table->string('topbar_announcement')->nullable();
        $table->string('shop_qr_code')->nullable(); // Optional: If you have QR code saved as a path
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'business_name',
            'business_type',
            'shop_email',
            'shop_phone',
            'country',
            'shop_address',
            'topbar_announcement',
            'shop_qr_code',
        ]);
    });
}

};
