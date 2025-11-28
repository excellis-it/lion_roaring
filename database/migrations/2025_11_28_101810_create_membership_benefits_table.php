<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipBenefitsTable extends Migration
{
    public function up()
    {
        Schema::create('membership_benefits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tier_id');
            $table->string('benefit');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->foreign('tier_id')->references('id')->on('membership_tiers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_benefits');
    }
}
