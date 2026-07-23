<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->decimal('monthly_cost', 10, 2)->nullable()->after('cost');
            $table->decimal('yearly_cost', 10, 2)->nullable()->after('monthly_cost');
        });

        DB::table('membership_tiers')->orderBy('id')->each(function ($tier) {
            $cost = (float) ($tier->cost ?? 0);
            DB::table('membership_tiers')->where('id', $tier->id)->update([
                'yearly_cost' => $cost,
                'monthly_cost' => round($cost / 12, 2),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->dropColumn(['monthly_cost', 'yearly_cost']);
        });
    }
};
