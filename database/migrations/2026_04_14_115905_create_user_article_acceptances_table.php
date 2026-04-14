<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserArticleAcceptancesTable extends Migration
{
    public function up()
    {
        Schema::create('user_article_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->string('country_code')->nullable();
            $table->text('checkbox_text_snapshot')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'article_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_article_acceptances');
    }
}
