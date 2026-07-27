<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeLog extends Model
{
    protected $fillable = [
        'created_by',
        'version',
        'title',
        'description',
        'type',
        'platform',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function typeBadgeClass(): string
    {
        return match ($this->type) {
            'feature' => 'badge bg-primary',
            'improvement' => 'badge bg-info text-dark',
            'bugfix' => 'badge bg-warning text-dark',
            'security' => 'badge bg-danger',
            default => 'badge bg-secondary',
        };
    }

    public function platformLabel(): string
    {
        return match ($this->platform) {
            'mobile' => 'Mobile App',
            default => 'Web',
        };
    }

    public function platformBadgeClass(): string
    {
        return match ($this->platform) {
            'mobile' => 'badge bg-success',
            default => 'badge bg-dark',
        };
    }
}
