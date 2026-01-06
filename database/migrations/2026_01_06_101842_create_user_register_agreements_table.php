<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRegisterAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_register_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('country_code', 5)->default('US')->index();
            $table->string('signer_name');
            $table->string('signer_initials', 10)->nullable();
            $table->string('pdf_path');
            $table->string('agreement_title_snapshot')->nullable();
            $table->longText('agreement_description_snapshot')->nullable();
            $table->string('checkbox_text_snapshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_register_agreements');
    }
}
