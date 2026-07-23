<?php

namespace App\Services;

use App\Models\MembershipTier;
use App\Models\User;
use App\Models\UserType;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class MembershipPrivilegeService
{
    public const CHANGE_NEW = 'new';
    public const CHANGE_SAME = 'same';
    public const CHANGE_UPGRADE = 'upgrade';
    public const CHANGE_DOWNGRADE = 'downgrade';

    /**
     * Apply privilege rules after a membership renew / upgrade / downgrade / first subscribe.
     */
    public function applyAfterTierChange(User $user, MembershipTier $newTier, ?int $previousPlanId): void
    {
        $user->loadMissing(['roles', 'userRole']);

        $change = $this->resolveChangeType($newTier, $previousPlanId);

        if ($change === self::CHANGE_SAME) {
            $currentTier = $previousPlanId
                ? MembershipTier::find($previousPlanId)
                : $newTier;

            if ($this->hasAssignedPrivilege($user, $currentTier)) {
                return;
            }

            return;
        }

        if ($change === self::CHANGE_DOWNGRADE) {
            $this->applyDowngradeReset($user, $newTier);

            return;
        }

        // upgrade or first subscribe
        $this->syncTierPermissions($user, $newTier);
    }

    public function resolveChangeType(MembershipTier $newTier, ?int $previousPlanId): string
    {
        if ($previousPlanId === null) {
            return self::CHANGE_NEW;
        }

        if ((int) $previousPlanId === (int) $newTier->id) {
            return self::CHANGE_SAME;
        }

        $oldTier = MembershipTier::withTrashed()->find($previousPlanId);
        if (!$oldTier) {
            return self::CHANGE_NEW;
        }

        // Soft-deleted previous tiers still need a stable rank; include them so indices don't shift wrongly.
        $oldRank = $this->tierRank($oldTier, true);
        $newRank = $this->tierRank($newTier, true);

        if ($newRank > $oldRank) {
            return self::CHANGE_UPGRADE;
        }

        if ($newRank < $oldRank) {
            return self::CHANGE_DOWNGRADE;
        }

        // Same rank index (unlikely different ids) — treat as same-tier privilege rules
        return self::CHANGE_SAME;
    }

    /**
     * Index in MembershipTier ordered by id (Partner UI: first = Tier 1, second = Tier 2).
     *
     * @param  bool  $includeTrashed  When comparing against a soft-deleted previous plan, include trashed tiers.
     */
    public function tierRank(MembershipTier $tier, bool $includeTrashed = false): int
    {
        $query = MembershipTier::query()->orderBy('id');
        if ($includeTrashed) {
            $query->withTrashed();
        }

        $ids = $query->pluck('id')->map(fn ($id) => (int) $id)->all();
        $index = array_search((int) $tier->id, $ids, true);

        return $index === false ? 0 : (int) $index;
    }

    /**
     * True when the user has an assigned / Ecclesia role, or custom permissions beyond the tier.
     */
    public function hasAssignedPrivilege(User $user, ?MembershipTier $currentTier): bool
    {
        if (!$user->isMemberSovereign()) {
            return true;
        }

        if ((int) ($user->is_ecclesia_admin ?? 0) === 1) {
            return true;
        }

        $userType = $user->relationLoaded('userRole') ? $user->userRole : $user->userRole()->first();
        if ($userType && (int) ($userType->is_ecclesia ?? 0) === 1) {
            return true;
        }

        if (!$currentTier) {
            return false;
        }

        $tierPermissions = $this->tierPermissionNames($currentTier);
        $role = $this->resolveCustomRole($user);

        if (!$role) {
            $userPermissions = $user->getDirectPermissions()->pluck('name')->map(fn ($n) => trim((string) $n))->filter()->sort()->values()->all();

            return $userPermissions !== $tierPermissions;
        }

        $rolePermissions = $role->permissions->pluck('name')->map(fn ($n) => trim((string) $n))->filter()->sort()->values()->all();

        return $rolePermissions !== $tierPermissions;
    }

    public function resolveCustomRole(User $user): ?Role
    {
        $baseRoleNames = UserType::pluck('name')->toArray();

        foreach ($user->roles as $role) {
            if (!in_array($role->name, $baseRoleNames, true)) {
                return $role;
            }
        }

        return null;
    }

    public function syncTierPermissions(User $user, MembershipTier $tier): void
    {
        $user->loadMissing('roles');
        $userRole = $this->resolveCustomRole($user);

        if (!empty($tier->permissions)) {
            $permissions = $this->tierPermissionNames($tier);

            if ($userRole) {
                $userRole->syncPermissions($permissions);
            } else {
                $user->syncPermissions($permissions);
            }

            $directPerms = $user->getDirectPermissions()->pluck('name')->toArray();
            if (!empty($directPerms) && $userRole) {
                $user->revokePermissionTo($directPerms);
            }
        } else {
            // Clear role and direct permissions when the target tier has none.
            if ($userRole) {
                $userRole->syncPermissions([]);
            }
            $directPerms = $user->getDirectPermissions()->pluck('name')->toArray();
            if (!empty($directPerms)) {
                $user->revokePermissionTo($directPerms);
            }
        }

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
        $user->forgetCachedPermissions();
    }

    public function applyDowngradeReset(User $user, MembershipTier $newTier): void
    {
        $user->loadMissing(['roles', 'userRole']);

        $wasEcclesiaAdmin = (int) ($user->is_ecclesia_admin ?? 0) === 1
            || (int) (optional($user->userRole)->is_ecclesia ?? 0) === 1;

        $manageIds = $this->parseManageEcclesiaIds($user->manage_ecclesia ?? null);

        $memberType = UserType::where('name', 'MEMBER_SOVEREIGN')->first();
        if (!$memberType) {
            $this->syncTierPermissions($user, $newTier);

            return;
        }

        $user->user_type = 'Regional';
        $user->user_type_id = $memberType->id;
        $user->is_ecclesia_admin = 0;
        $user->manage_ecclesia = null;

        if ($wasEcclesiaAdmin) {
            if (count($manageIds) === 1) {
                $user->ecclesia_id = $manageIds[0];
            } elseif (count($manageIds) > 1) {
                // UI/export shows null ecclesia_id as "NO NAME"
                $user->ecclesia_id = null;
            }
            // empty manage list: keep existing ecclesia_id
        }
        // Non-ecclesia (e.g. Global + assigned role): leave ecclesia_id unchanged

        $user->save();

        $customRole = $this->resolveCustomRole($user);
        if ($customRole) {
            $customRole->type = $memberType->type ?? 2;
            $customRole->is_ecclesia = 0;
            $customRole->save();
            $user->syncRoles([$customRole->name]);
        }

        $user->unsetRelation('roles');
        $user->unsetRelation('userRole');
        $user->load(['roles', 'userRole']);

        $this->syncTierPermissions($user, $newTier);
    }

    /**
     * @return list<string>
     */
    public function tierPermissionNames(MembershipTier $tier): array
    {
        if (empty($tier->permissions)) {
            return [];
        }

        return collect(explode(',', $tier->permissions))
            ->map(fn ($n) => trim((string) $n))
            ->filter()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return list<int>
     */
    private function parseManageEcclesiaIds($manageEcclesia): array
    {
        if (is_array($manageEcclesia)) {
            return array_values(array_filter(array_map('intval', $manageEcclesia)));
        }

        if ($manageEcclesia === null || $manageEcclesia === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            'intval',
            array_map('trim', explode(',', (string) $manageEcclesia))
        )));
    }
}
