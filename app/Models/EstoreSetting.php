<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_cost',
        'delivery_cost',
        'tax_percentage',
        'is_pickup_available',
        'credit_card_percentage',
        'refund_max_days',
    ];
}
