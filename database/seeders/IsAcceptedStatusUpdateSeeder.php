<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class IsAcceptedStatusUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()
            ->whereNull('is_accept')
            ->update(['is_accept' => 1]);
    }
}
