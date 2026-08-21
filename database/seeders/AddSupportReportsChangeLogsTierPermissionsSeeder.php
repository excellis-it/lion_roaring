<?php

namespace Database\Seeders;

use App\Models\MembershipTier;
use App\Models\User;
use App\Services\MembershipPrivilegeService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Client testing 2026-08, item US #17: Support Reports & Change Logs were gated only by the
 * role permission checkboxes, with no Member Tier privilege behind them — inconsistent with
 * every other menu. This gives both menus a full View/Create/Edit/Delete privilege set and
 * seeds the tier defaults: Tier 1 & 2 get View, Tier 3 gets the full set.
 */
class AddSupportReportsChangeLogsTierPermissionsSeeder extends Seeder
{
    private const VIEW_ONLY = [
        'View Support Reports',
        'View Change Logs',
    ];

    private const FULL = [
        'View Support Reports',
        'Create Support Reports',
        'Edit Support Reports',
        'Delete Support Reports',
        'View Change Logs',
        'Create Change Logs',
        'Edit Change Logs',
        'Delete Change Logs',
    ];

    public function run(): void
    {
        foreach (self::FULL as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Super Admins keep everything.
        $superAdmins = User::whereHas('userRole', function ($q) {
            $q->where('name', 'SUPER ADMIN');
        })->orWhereHas('roles', function ($q) {
            $q->where('name', 'SUPER ADMIN');
        })->get();

        foreach ($superAdmins as $superAdmin) {
            $role = $superAdmin->roles()->first();
            if ($role) {
                $role->givePermissionTo(self::FULL);
            } else {
                $superAdmin->givePermissionTo(self::FULL);
            }
        }

        // Tier defaults. Tier 3 is matched by name/slug; everything else gets View only.
        $tierGrants = [];

        foreach (MembershipTier::all() as $tier) {
            $isTierThree = str_contains(strtolower((string) $tier->name), 'tier iii')
                || str_contains(strtolower((string) $tier->name), 'tier 3')
                || str_contains(strtolower((string) $tier->slug), 'tier iii')
                || str_contains(strtolower((string) $tier->slug), 'tier 3');

            $grant = $isTierThree ? self::FULL : self::VIEW_ONLY;

            $current = collect(explode(',', (string) $tier->permissions))
                ->map(fn ($n) => trim((string) $n))
                ->filter();

            $tier->permissions = $current->merge($grant)->unique()->sort()->values()->implode(',');
            $tier->save();

            $this->command?->info(sprintf(
                'Tier "%s" (#%d): granted %s',
                $tier->name,
                $tier->id,
                $isTierThree ? 'View/Create/Edit/Delete' : 'View only'
            ));

            $tierGrants[$tier->id] = $grant;
        }

        $this->backfillExistingMembers($tierGrants);
    }

    /**
     * Tier privileges are only pushed onto a member at tier-change time
     * (MembershipPrivilegeService::applyAfterTierChange). Without this backfill, existing
     * members would lose the Support Reports / Change Logs menus until their next renewal.
     *
     * @param  array<int, list<string>>  $tierGrants
     */
    private function backfillExistingMembers(array $tierGrants): void
    {
        $service = app(MembershipPrivilegeService::class);
        $granted = 0;

        User::with(['roles', 'userLastSubscription'])
            ->whereHas('userLastSubscription')
            ->chunk(200, function ($users) use ($tierGrants, $service, &$granted) {
                foreach ($users as $user) {
                    $planId = $user->userLastSubscription->plan_id ?? null;
                    if (!$planId || !isset($tierGrants[$planId])) {
                        continue;
                    }

                    $role = $service->resolveCustomRole($user);
                    if ($role) {
                        $role->givePermissionTo($tierGrants[$planId]);
                    } else {
                        $user->givePermissionTo($tierGrants[$planId]);
                    }

                    $granted++;
                }
            });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info("Backfilled {$granted} existing member(s) from their current tier.");
    }
}
