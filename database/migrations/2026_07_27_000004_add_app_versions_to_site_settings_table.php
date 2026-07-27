<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('WEB_APP_VERSION')->nullable()->after('STRIPE_SECRET');
            $table->string('MOBILE_APP_VERSION')->nullable()->after('WEB_APP_VERSION');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['WEB_APP_VERSION', 'MOBILE_APP_VERSION']);
        });
    }
};
