<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipTier extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cost',
        'monthly_cost',
        'yearly_cost',
        'duration_months',
        'pricing_type',
        'life_force_energy_tokens',
        'agree_description',
        'permissions',
    ];

    protected $casts = [
        'monthly_cost' => 'decimal:2',
        'yearly_cost' => 'decimal:2',
    ];

    public function benefits()
    {
        return $this->hasMany(MembershipBenefit::class, 'tier_id')->orderBy('sort_order');
    }
}
