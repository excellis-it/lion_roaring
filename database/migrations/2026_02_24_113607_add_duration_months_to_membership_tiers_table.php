<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->integer('duration_months')->default(12)->after('cost');
        });
    }

    public function down(): void
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->dropColumn('duration_months');
        });
    }
};
