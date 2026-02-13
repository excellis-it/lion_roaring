<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AddPromoCodePermissions extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'View Promo Codes',
            'Create Promo Code',
            'Edit Promo Code',
            'Delete Promo Code',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // add all permissions to whose usertype SUPER ADMIN
        $user_type = UserType::where('name', 'SUPER ADMIN')->first();

       $users = User::where('user_type_id', $user_type->id)->get();

       foreach ($users as $user) {
       $user->roles->first()->givePermissionTo($permissions);
       }

        $this->command->info('Promo code permissions created successfully!');
    }
}
