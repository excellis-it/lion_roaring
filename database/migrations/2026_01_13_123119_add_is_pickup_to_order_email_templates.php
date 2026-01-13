<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPickupToOrderEmailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('order_email_templates', function (Blueprint $table) {
            $table->boolean('is_pickup')->default(false)->after('order_status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('order_email_templates', function (Blueprint $table) {
            $table->dropColumn('is_pickup');
        });
    }
}
