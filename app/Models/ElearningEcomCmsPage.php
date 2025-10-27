<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElearningEcomCmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'page_name',
        'page_title',
        'page_content',
        'page_banner_image',
        'slug',
    ];
}
