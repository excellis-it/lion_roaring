<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('home_cms', function (Blueprint $table) {
            $table->boolean('show_banner_image')->default(true)->after('banner_image');
        });

        // Preserve previous hardcoded behavior: hide banner image for Global (GL).
        DB::table('home_cms')->where('country_code', 'GL')->update(['show_banner_image' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_cms', function (Blueprint $table) {
            $table->dropColumn('show_banner_image');
        });
    }
};
