<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueFromSlugInEcomCmsPagesTable extends Migration
{
    public function up(): void
    {
        Schema::table('ecom_cms_pages', function (Blueprint $table) {
            // Drop the unique index on slug
            $table->dropUnique('ecom_cms_pages_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('ecom_cms_pages', function (Blueprint $table) {
            // Re-add the unique index if you ever roll back
            $table->unique('slug');
        });
    }
}
