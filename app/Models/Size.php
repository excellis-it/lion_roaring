<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends BaseModel
{
    use HasFactory;

    protected $fillable = ['size', 'status'];

    public function warehouseProducts()
    {
        return $this->hasMany(WarehouseProduct::class, 'size_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_sizes', 'size_id', 'product_id');
    }

    // product variations
    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class, 'size_id');
    }
}
