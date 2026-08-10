<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AddMemberAuditLogsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permission = Permission::firstOrCreate([
            'name' => 'View Member Audit Logs',
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdminType = UserType::where('name', 'SUPER ADMIN')->first();
        if (! $superAdminType) {
            return;
        }

        $superAdmins = User::query()
            ->where('user_type_id', $superAdminType->id)
            ->orWhereHas('roles', fn ($q) => $q->where('name', 'SUPER ADMIN'))
            ->get();

        foreach ($superAdmins as $admin) {
            if (! $admin->hasPermissionTo($permission)) {
                $admin->givePermissionTo($permission);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
