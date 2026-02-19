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

        // ensure SUPER ADMIN user type exists (create if missing)
        $user_type = UserType::firstOrCreate(
            ['name' => 'SUPER ADMIN'],
            ['guard_name' => 'web', 'type' => '1', 'is_ecclesia' => 0]
        );

        $users = User::where('user_type_id', $user_type->id)
            ->orWhereHas('roles', function ($q) {
                $q->where('name', 'SUPER ADMIN');
            })->get();

        foreach ($users as $user) {
            $role = $user->roles->first();
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }

        $this->command->info('Promo code permissions created successfully!');
    }
}
