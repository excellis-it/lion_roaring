<?php

namespace App\Services;

use App\Models\RolePermissionAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class RolePermissionAuditLogger
{
    public function normalizePermissions(iterable $names): array
    {
        return collect($names)
            ->map(fn ($n) => trim((string) $n))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function diffPermissions(array $old, array $new): array
    {
        $old = $this->normalizePermissions($old);
        $new = $this->normalizePermissions($new);

        return [
            'added' => array_values(array_diff($new, $old)),
            'removed' => array_values(array_diff($old, $new)),
        ];
    }

    public function hasMeaningfulChange(array $payload): bool
    {
        if (($payload['old_role_name'] ?? null) !== ($payload['new_role_name'] ?? null)) {
            return true;
        }
        if (($payload['old_user_type'] ?? null) !== ($payload['new_user_type'] ?? null)) {
            return true;
        }
        if (($payload['old_membership_tier_id'] ?? null) != ($payload['new_membership_tier_id'] ?? null)) {
            return true;
        }
        if (($payload['role_template_name'] ?? null) && in_array($payload['action'] ?? '', [
            'role_template_created', 'role_template_deleted',
        ], true)) {
            return true;
        }

        if (($payload['action'] ?? '') === 'role_template_updated') {
            $meta = $payload['meta'] ?? [];
            if (($meta['old_is_admin'] ?? null) !== ($meta['new_is_admin'] ?? null)) {
                return true;
            }
            if (($meta['old_is_ecclesia'] ?? null) !== ($meta['new_is_ecclesia'] ?? null)) {
                return true;
            }
        }

        $old = $this->normalizePermissions($payload['old_permissions'] ?? []);
        $new = $this->normalizePermissions($payload['new_permissions'] ?? []);

        return $old !== $new;
    }

    public function log(array $payload): ?RolePermissionAuditLog
    {
        try {
            if (empty($payload['action']) || empty($payload['source'])) {
                return null;
            }

            $oldPerms = $this->normalizePermissions($payload['old_permissions'] ?? []);
            $newPerms = $this->normalizePermissions($payload['new_permissions'] ?? []);
            $payload['old_permissions'] = $oldPerms;
            $payload['new_permissions'] = $newPerms;

            $diff = $this->diffPermissions($oldPerms, $newPerms);
            $payload['permissions_added'] = $payload['permissions_added'] ?? $diff['added'];
            $payload['permissions_removed'] = $payload['permissions_removed'] ?? $diff['removed'];

            if (!$this->hasMeaningfulChange($payload)) {
                return null;
            }

            /** @var User|null $actor */
            $actor = auth()->user();
            if (!isset($payload['actor_id']) && $actor) {
                $payload['actor_id'] = $actor->id;
                $payload['actor_name'] = trim(($actor->first_name ?? '') . ' ' . ($actor->last_name ?? ''));
                $payload['actor_email'] = $actor->email;
            }

            if (!isset($payload['ip'])) {
                $payload['ip'] = request()->ip();
            }
            if (!isset($payload['user_agent'])) {
                $payload['user_agent'] = request()->userAgent();
            }

            $payload['created_at'] = $payload['created_at'] ?? now();

            return RolePermissionAuditLog::create($payload);
        } catch (Throwable $e) {
            Log::error('RolePermissionAuditLogger failed: ' . $e->getMessage(), [
                'action' => $payload['action'] ?? null,
            ]);

            return null;
        }
    }
}
