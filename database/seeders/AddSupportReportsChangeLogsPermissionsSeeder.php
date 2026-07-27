<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class AddSupportReportsChangeLogsPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'Manage Support Reports',
            'Manage Change Logs',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Ensure Super Admin users get these permissions via their Spatie role
        $superAdmins = User::whereHas('userRole', function ($q) {
            $q->where('name', 'SUPER ADMIN');
        })->orWhereHas('roles', function ($q) {
            $q->where('name', 'SUPER ADMIN');
        })->get();

        foreach ($superAdmins as $superAdmin) {
            $role = $superAdmin->roles()->first();
            if ($role) {
                $role->givePermissionTo($permissions);
            } else {
                $superAdmin->givePermissionTo($permissions);
            }
        }

        $sidebarMenus = [
            ['key' => 'support_reports', 'default_name' => 'Support Reports'],
            ['key' => 'change_logs', 'default_name' => 'Change Logs'],
        ];

        foreach ($sidebarMenus as $menu) {
            $existing = DB::table('menu_items')->where('key', $menu['key'])->first();

            if ($existing) {
                DB::table('menu_items')
                    ->where('key', $menu['key'])
                    ->update([
                        'default_name' => $menu['default_name'],
                        'name' => $existing->name ?: $menu['default_name'],
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('menu_items')->insert([
                    'key' => $menu['key'],
                    'default_name' => $menu['default_name'],
                    'name' => $menu['default_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
