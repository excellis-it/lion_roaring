<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->nullable();
            $table->string('email')->nullable();
            $table->string('user_roles')->nullable();
            $table->string('ecclesia_name')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('country_name', 100)->nullable();
            $table->string('device_mac')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('url')->nullable();
            $table->string('permission_access')->nullable();
            $table->string('activity_type')->nullable();
            $table->text('activity_description')->nullable();
            $table->timestamp('activity_date')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_activities');
    }
}
