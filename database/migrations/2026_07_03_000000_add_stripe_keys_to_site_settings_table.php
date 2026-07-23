<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->text('STRIPE_KEY')->nullable()->after('DONATE_BANK_TRANSFER_DETAILS');
            $table->text('STRIPE_SECRET')->nullable()->after('STRIPE_KEY');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['STRIPE_KEY', 'STRIPE_SECRET']);
        });
    }
};
