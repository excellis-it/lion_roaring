<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseProductImage extends Model
{
    use HasFactory;
    protected $fillable = ['warehouse_product_id', 'image_path'];

    public function warehouseProduct()
    {
        return $this->belongsTo(WarehouseProduct::class);
    }
}
