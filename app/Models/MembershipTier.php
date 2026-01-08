<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cost',
        'pricing_type',
        'life_force_energy_tokens',
        'agree_description',
        'permissions',
    ];

    public function benefits()
    {
        return $this->hasMany(MembershipBenefit::class, 'tier_id')->orderBy('sort_order');
    }
}
