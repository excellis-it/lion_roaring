<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WareHouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location_lat',
        'location_lng',
        'address',
        'country_id',
        'service_range',
        'is_active',
    ];
}
