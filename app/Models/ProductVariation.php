<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock_quantity',
        'color_id',
        'size_id',
        'additional_info',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function colorDetail()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function sizeDetail()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function images()
    {
        return $this->hasMany(ProductVariationImage::class, 'product_variation_id');
    }

    public function warehouseProductVariations()
    {
        return $this->hasMany(WarehouseProductVariation::class, 'product_variation_id');
    }

    // get only stock_quantity column value
    public function getAvailableQuantityAttribute()
    {
        return $this->stock_quantity;
    }
}
