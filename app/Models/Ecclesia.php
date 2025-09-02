<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ecclesia extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
    ];



    public function countryName()
    {
        return $this->belongsTo(Country::class , 'country');
    }
}
