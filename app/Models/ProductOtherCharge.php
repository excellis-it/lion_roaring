<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOtherCharge extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'charge_name',
        'charge_amount',
        'charge_type',
        'display_at',
    ];

    public function scopeListing($query)
    {
        return $query->where('display_at', 'listing');
    }

    public function scopeCheckout($query)
    {
        return $query->where('display_at', 'checkout');
    }
}
