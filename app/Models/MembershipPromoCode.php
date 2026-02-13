<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class MembershipPromoCode extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'is_percentage',
        'discount_amount',
        'start_date',
        'end_date',
        'status',
        'scope_type',
        'tier_ids',
        'user_ids',
        'usage_limit',
        'usage_count',
        'per_user_limit',
    ];

    protected $casts = [
        'is_percentage' => 'boolean',
        'status' => 'boolean',
        'tier_ids' => 'array',
        'user_ids' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function usages()
    {
        return $this->hasMany(MembershipPromoUsage::class, 'promo_code_id');
    }

    public function scopeSummary(): string
    {
        return match ($this->scope_type) {
            'all_tiers' => 'All membership tiers',
            'selected_tiers' => 'Selected membership tiers',
            'all_users' => 'All users',
            'selected_users' => 'Selected users',
            default => 'All memberships',
        };
    }

    /**
     * Check if the promo code is valid
     */
    public function isValid(): bool
    {
        if (!$this->status) {
            return false;
        }

        $now = Carbon::now()->startOfDay();
        $startDate = Carbon::parse($this->start_date)->startOfDay();
        $endDate = Carbon::parse($this->end_date)->endOfDay();

        if ($now->lt($startDate) || $now->gt($endDate)) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can use this promo code
     */
    public function canBeUsedByUser($userId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check user scope
        if ($this->scope_type === 'selected_users') {
            if (!in_array($userId, $this->user_ids ?? [])) {
                return false;
            }
        }

        // Check per-user limit
        if ($this->per_user_limit) {
            $userUsageCount = $this->usages()->where('user_id', $userId)->count();
            if ($userUsageCount >= $this->per_user_limit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if promo code can be applied to a tier
     */
    public function canBeAppliedToTier($tierId): bool
    {
        if ($this->scope_type === 'selected_tiers') {
            return in_array($tierId, $this->tier_ids ?? []);
        }

        return true; // For all_tiers, all_users, and selected_users
    }

    /**
     * Calculate discount for a given price
     */
    public function calculateDiscount($price): float
    {
        if ($this->is_percentage) {
            return round(($price * $this->discount_amount) / 100, 2);
        }

        return min($this->discount_amount, $price); // Discount can't exceed price
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
