<?php

namespace App\Services;

use App\Models\RolePermissionAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class RolePermissionAuditLogger
{
    public const FIELD_LABELS = [
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'phone_country_code_name' => 'Phone Country',
        'user_name' => 'Username',
        'lion_roaring_id' => 'Lion Roaring ID',
        'roar_id' => 'Roar ID',
        'user_type' => 'User Type',
        'role' => 'Profile Role',
        'permissions' => 'Permissions',
        'ecclesia' => 'House of Ecclesia',
        'is_ecclesia_admin' => 'Ecclesia Admin',
        'manage_ecclesia' => 'Manage Ecclesia',
        'country' => 'Country',
        'state' => 'State',
        'city' => 'City',
        'zip' => 'Zip',
        'address' => 'Address',
        'address2' => 'Address 2',
        'membership_excluded' => 'Membership Excluded',
        'membership_tier' => 'Membership Tier',
        'password' => 'Password',
    ];

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

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $new
     * @return array<int, array{field: string, label: string, old: mixed, new: mixed}>
     */
    public function buildFieldChanges(array $old, array $new): array
    {
        $keys = array_values(array_unique(array_merge(array_keys($old), array_keys($new))));
        $changes = [];

        foreach ($keys as $key) {
            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;

            if ($key === 'password') {
                if (!empty($newVal)) {
                    $changes[] = [
                        'field' => 'password',
                        'label' => self::FIELD_LABELS['password'],
                        'old' => null,
                        'new' => '(changed)',
                    ];
                }
                continue;
            }

            if ($key === 'permissions') {
                $oldPerms = $this->normalizePermissions(is_array($oldVal) ? $oldVal : []);
                $newPerms = $this->normalizePermissions(is_array($newVal) ? $newVal : []);
                if ($oldPerms === $newPerms) {
                    continue;
                }
                $changes[] = [
                    'field' => 'permissions',
                    'label' => self::FIELD_LABELS['permissions'],
                    'old' => $oldPerms,
                    'new' => $newPerms,
                ];
                continue;
            }

            $oldCmp = $this->normalizeComparable($oldVal);
            $newCmp = $this->normalizeComparable($newVal);
            if ($oldCmp === $newCmp) {
                continue;
            }

            $changes[] = [
                'field' => $key,
                'label' => self::FIELD_LABELS[$key] ?? ucwords(str_replace('_', ' ', $key)),
                'old' => $this->displayValue($oldVal),
                'new' => $this->displayValue($newVal),
            ];
        }

        return $changes;
    }

    public function hasMeaningfulChange(array $payload): bool
    {
        if (!empty($payload['field_changes']) && is_array($payload['field_changes'])) {
            return true;
        }

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

            if (isset($payload['field_changes']) && is_array($payload['field_changes'])) {
                $payload['field_changes'] = array_values($payload['field_changes']);
            }

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
                'exception' => $e,
            ]);

            return null;
        }
    }

    private function normalizeComparable(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_array($value)) {
            $normalized = array_map(fn ($v) => trim((string) $v), $value);
            sort($normalized);

            return implode('|', $normalized);
        }
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    private function displayValue(mixed $value): mixed
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        if (is_array($value)) {
            return array_values($value);
        }
        if ($value === null || $value === '') {
            return null;
        }

        return is_scalar($value) ? (string) $value : $value;
    }
}
