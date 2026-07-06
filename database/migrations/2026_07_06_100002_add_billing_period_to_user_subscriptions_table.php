<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->string('billing_period', 10)->nullable()->after('subscription_price');
        });

        DB::table('user_subscriptions')->orderBy('id')->each(function ($sub) {
            $period = ((int) ($sub->subscription_validity ?? 12)) >= 12 ? 'yearly' : 'monthly';
            DB::table('user_subscriptions')->where('id', $sub->id)->update([
                'billing_period' => $period,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn('billing_period');
        });
    }
};
