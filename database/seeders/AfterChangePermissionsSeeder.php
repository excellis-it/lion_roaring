<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\UserType;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AfterChangePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Ensure SUPER ADMIN UserType exists
        $superAdminType = UserType::where('name', 'SUPER ADMIN')->first();

        // 2. Define Chatbot Permissions
        $permissions = [
             // Signup rules
            ["name" => "Manage Signup Rules", 'guard_name' => 'web', 'type' => 2],
            ["name" => "Create Signup Rules", 'guard_name' => 'web', 'type' => 2],
            ["name" => "Edit Signup Rules", 'guard_name' => 'web', 'type' => 2],
            ["name" => "Delete Signup Rules", 'guard_name' => 'web', 'type' => 2],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']],
                ['type' => $permission['type']]
            );
        }

        // 3. Ensure SUPER ADMIN Role exists (Spatie)
       $superadmin_users = User::where('user_type_id', $superAdminType->id)->get();

       foreach ($superadmin_users as $superadmin_user) {
         $role = $superadmin_user->roles()->first();
         $role->givePermissionTo(Permission::all());
       }
    }
}
