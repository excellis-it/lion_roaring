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
        'role_id'
    ];

    public function benefits()
    {
        return $this->hasMany(MembershipBenefit::class, 'tier_id')->orderBy('sort_order');
    }
}
