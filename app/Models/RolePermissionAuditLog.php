<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermissionAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'action', 'source',
        'actor_id', 'actor_name', 'actor_email',
        'target_user_id', 'target_user_name', 'target_user_email', 'target_country_id',
        'role_template_id', 'role_template_name',
        'old_role_name', 'new_role_name', 'old_user_type', 'new_user_type',
        'old_permissions', 'new_permissions', 'permissions_added', 'permissions_removed',
        'old_membership_tier_id', 'old_membership_tier_name',
        'new_membership_tier_id', 'new_membership_tier_name',
        'ip', 'user_agent', 'country_code', 'meta', 'created_at',
    ];

    protected $casts = [
        'old_permissions' => 'array',
        'new_permissions' => 'array',
        'permissions_added' => 'array',
        'permissions_removed' => 'array',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
