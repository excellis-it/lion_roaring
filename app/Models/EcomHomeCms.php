<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcomHomeCms extends Model
{
    use HasFactory;

    protected $fillable = [
        'banner_image',
        'banner_image_small',
        'banner_title',
        'banner_subtitle',
        'product_category_image',
        'product_category_title',
        'product_category_subtitle',
        'featured_product_image',
        'featured_product_title',
        'featured_product_subtitle',
        'new_arrival_image',
        'new_arrival_title',
        'new_arrival_subtitle',
        'new_product_image',
        'new_product_title',
        'new_product_subtitle',
    ];
}
