<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends BaseModel
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'before_sale_price',
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
        return $this->hasMany(ProductColorImage::class, 'product_id', 'product_id')
            ->where('color_id', $this->color_id);
    }


    // color images
    public function colorImages()
    {
        return $this->hasMany(ProductColorImage::class, 'product_id', 'product_id')
            ->where('color_id', $this->color_id);
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
