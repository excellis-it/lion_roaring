<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Detected source language per unique string.
     *
     * Google bills language detection at the same rate as translation, so it must
     * be paid for exactly once per unique string — never per page view. This table
     * is that memory: once a bulletin written in Hindi has been detected, every
     * future visitor in every language reuses the answer for free.
     */
    public function up(): void
    {
        Schema::create('translation_source', function (Blueprint $table) {
            $table->id();
            $table->char('source_hash', 40)->unique();
            $table->string('detected_lang', 12);
            $table->float('confidence')->nullable();
            $table->unsignedInteger('char_count')->default(0);
            $table->timestamps();

            $table->index('detected_lang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_source');
    }
};
