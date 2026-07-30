<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_cache', function (Blueprint $table) {
            $table->id();
            // sha1 of the normalized source text
            $table->char('source_hash', 40);
            $table->string('target_lang', 12);
            $table->mediumText('source_text');
            $table->mediumText('translated_text');
            $table->unsignedInteger('char_count')->default(0);
            $table->unsignedInteger('hit_count')->default(0);
            $table->timestamps();

            $table->unique(['source_hash', 'target_lang'], 'translation_cache_hash_lang_unique');
            $table->index('target_lang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_cache');
    }
};
