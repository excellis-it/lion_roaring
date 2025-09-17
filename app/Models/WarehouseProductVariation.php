<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseProductVariation extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_variation_id',
        'product_id',
        'warehouse_id',
        'warehouse_quantity',
    ];

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    
}
