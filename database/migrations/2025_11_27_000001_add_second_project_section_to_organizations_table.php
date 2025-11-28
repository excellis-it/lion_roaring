<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecondProjectSectionToOrganizationsTable extends Migration
{
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('project_section_two_title')->nullable();
            $table->string('project_section_two_sub_title')->nullable();
            $table->longText('project_section_two_description')->nullable();
        });
    }

    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['project_section_two_title', 'project_section_two_sub_title', 'project_section_two_description']);
        });
    }
}
