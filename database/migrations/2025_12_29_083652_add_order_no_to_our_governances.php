<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddOrderNoToOurGovernances extends Migration
{
    public function up(): void
    {
        Schema::table('our_governances', function (Blueprint $table) {
            $table->unsignedInteger('order_no')->default(0)->after('slug')->index();
        });

        // resequence existing rows per country
        if (Schema::hasTable('our_governances')) {
            $countries = DB::table('our_governances')->distinct()->pluck('country_code');
            foreach ($countries as $country) {
                $items = DB::table('our_governances')->where('country_code', $country)->orderBy('id', 'asc')->pluck('id');
                $pos = 1;
                foreach ($items as $id) {
                    DB::table('our_governances')->where('id', $id)->update(['order_no' => $pos]);
                    $pos++;
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('our_governances', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });
    }
}
