<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstorePromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'is_percentage',
        'discount_amount',
        'start_date',
        'end_date',
        'status',
    ];
}
