<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_variation_id',
        'image_path',
    ];

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    
}
