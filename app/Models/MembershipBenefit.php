<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipBenefit extends Model
{
    use HasFactory;

    protected $fillable = ['tier_id', 'benefit', 'sort_order'];

    public function tier()
    {
        return $this->belongsTo(MembershipTier::class, 'tier_id');
    }
}
