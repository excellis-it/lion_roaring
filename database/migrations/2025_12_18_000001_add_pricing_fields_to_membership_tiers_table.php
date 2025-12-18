<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->string('pricing_type')->default('amount')->after('cost'); // amount|token
            $table->string('life_force_energy_tokens')->nullable()->after('pricing_type');
            $table->text('agree_description')->nullable()->after('life_force_energy_tokens');
        });
    }

    public function down(): void
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->dropColumn(['pricing_type', 'life_force_energy_tokens', 'agree_description']);
        });
    }
};
