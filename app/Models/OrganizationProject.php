<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationProject extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $attributes = [
        'section' => 1,
    ];

    protected $casts = [
        'section' => 'integer',
    ];
}
