<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class OurGovernance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_image',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'country_code',
        'order_no'
    ];

    protected $casts = [
        'order_no' => 'integer',
    ];
}
