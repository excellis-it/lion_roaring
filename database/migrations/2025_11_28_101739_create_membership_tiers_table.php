<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMembershipTiersTable extends Migration
{
    public function up()
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cost')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->timestamps();
        });

        // seed default tiers
        DB::table('membership_tiers')->insert([
            ['name' => 'Standard', 'slug' => 'standard', 'description' => 'Standard membership', 'cost' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gold', 'slug' => 'gold', 'description' => 'Gold membership with extra benefits', 'cost' => '30', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Platinum', 'slug' => 'platinum', 'description' => 'Platinum membership with premium benefits', 'cost' => '60', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // seed to create roles if not exist 'MEMBERSHIP_STANDARD', 'MEMBERSHIP_GOLD', 'MEMBERSHIP_PLATINUM'
        $roles = [
            ['name' => 'MEMBERSHIP_STANDARD', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MEMBERSHIP_GOLD', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MEMBERSHIP_PLATINUM', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('roles')->insertOrIgnore($roles);
    }

    public function down()
    {
        Schema::dropIfExists('membership_tiers');
    }
}
