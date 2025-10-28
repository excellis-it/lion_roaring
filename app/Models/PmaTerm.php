<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PmaTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'title',
        'description',
    ];
}
