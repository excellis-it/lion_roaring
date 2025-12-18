<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->string('subscription_method')->default('amount')->after('plan_id'); // amount|token
            $table->string('life_force_energy_tokens')->nullable()->after('subscription_price');
            $table->timestamp('agree_accepted_at')->nullable()->after('life_force_energy_tokens');
            $table->text('agree_description_snapshot')->nullable()->after('agree_accepted_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_method',
                'life_force_energy_tokens',
                'agree_accepted_at',
                'agree_description_snapshot',
            ]);
        });
    }
};
