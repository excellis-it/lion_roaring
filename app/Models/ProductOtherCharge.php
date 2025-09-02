<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOtherCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'charge_name',
        'charge_amount',
    ];
}
