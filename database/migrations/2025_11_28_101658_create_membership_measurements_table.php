<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMembershipMeasurementsTable extends Migration
{
    public function up()
    {
        Schema::create('membership_measurements', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('yearly_dues')->nullable();
            $table->timestamps();
        });

        // seed a default measurement record
        DB::table('membership_measurements')->insert([
            'label' => 'Life Force Energy',
            'description' => 'Custom membership energy measure used as dues',
            'yearly_dues' => '30',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('membership_measurements');
    }
}
