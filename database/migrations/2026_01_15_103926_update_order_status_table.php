<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrderStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            if (!Schema::hasColumn('order_statuses', 'is_pickup')) {
                $table->boolean('is_pickup')->default(false)->after('slug');
            }

            if (Schema::hasColumn('order_statuses', 'pickup_name')) {
                $table->dropColumn('pickup_name');
            }

            if (Schema::hasColumn('order_statuses', 'color')) {
                $table->dropColumn('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('order_statuses', function (Blueprint $table) {
            if (!Schema::hasColumn('order_statuses', 'pickup_name')) {
                $table->string('pickup_name')->nullable()->after('name');
            }

            if (!Schema::hasColumn('order_statuses', 'color')) {
                $table->string('color')->nullable()->after('slug');
            }

            if (Schema::hasColumn('order_statuses', 'is_pickup')) {
                $table->dropColumn('is_pickup');
            }
        });
    }
}
