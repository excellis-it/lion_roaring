<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->string('stripe_charge_id')->nullable()->index()->after('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->dropIndex(['stripe_charge_id']);
            $table->dropColumn('stripe_charge_id');
        });
    }
};
