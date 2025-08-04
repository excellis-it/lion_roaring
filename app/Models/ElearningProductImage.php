<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElearningProductImage extends Model
{
    use HasFactory;

    // table name
    protected $table = 'elearning_product_images';

    // fillable fields
    protected $fillable = [
        'product_id',
        'image',
        'featured_image',
    ];
}
