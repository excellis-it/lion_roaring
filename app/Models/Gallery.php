<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'image',
    ];

    // country relationship
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
}
