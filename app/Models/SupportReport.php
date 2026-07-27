<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportReport extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'attachment',
        'status',
        'admin_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'sr-badge sr-badge-open',
            'in_progress' => 'sr-badge sr-badge-in-progress',
            'resolved' => 'sr-badge sr-badge-resolved',
            'closed' => 'sr-badge sr-badge-closed',
            default => 'sr-badge sr-badge-closed',
        };
    }

    public function statusLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }
}
