<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_usage', function (Blueprint $table) {
            $table->id();
            $table->date('usage_date');
            $table->string('target_lang', 12);
            // Characters actually sent to Google (cache hits cost nothing and are not counted)
            $table->unsignedBigInteger('billed_chars')->default(0);
            $table->unsignedInteger('api_requests')->default(0);
            $table->unsignedInteger('cache_hits')->default(0);
            $table->timestamps();

            $table->unique(['usage_date', 'target_lang'], 'translation_usage_date_lang_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_usage');
    }
};
