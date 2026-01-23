<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoreRefund extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'payment_intent',
        'amount',
        'order_id',
        'user_id',
        'is_approved'
    ];
}
