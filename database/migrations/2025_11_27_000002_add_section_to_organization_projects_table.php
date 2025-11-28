<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectionToOrganizationProjectsTable extends Migration
{
    public function up()
    {
        Schema::table('organization_projects', function (Blueprint $table) {
            $table->unsignedTinyInteger('section')->default(1)->after('organization_id');
        });
    }

    public function down()
    {
        Schema::table('organization_projects', function (Blueprint $table) {
            $table->dropColumn('section');
        });
    }
}
