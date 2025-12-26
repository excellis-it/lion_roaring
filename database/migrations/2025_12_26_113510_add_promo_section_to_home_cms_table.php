<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('home_cms', function (Blueprint $table) {
            $table->string('section_6_title')->nullable()->after('section_5_title');
            $table->string('section_6_subtitle')->nullable()->after('section_6_title');
            $table->string('section_6_button_text')->nullable()->after('section_6_subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_cms', function (Blueprint $table) {
            $table->dropColumn([
                'section_6_title',
                'section_6_subtitle',
                'section_6_button_text',
            ]);
        });
    }
};
