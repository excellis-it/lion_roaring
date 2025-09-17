<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateStatusEnumInEstorePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('estore_payments', function (Blueprint $table) {
             DB::statement("ALTER TABLE estore_payments MODIFY COLUMN status
            ENUM('pending', 'processing', 'succeeded', 'failed', 'cancelled', 'refunded')
            NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estore_payments', function (Blueprint $table) {
              DB::statement("ALTER TABLE estore_payments MODIFY COLUMN status
            ENUM('pending', 'processing', 'succeeded', 'failed', 'cancelled')
            NOT NULL DEFAULT 'pending'");
        });
    }
}
