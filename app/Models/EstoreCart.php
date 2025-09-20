<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoreCart extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'warehouse_product_id',
        'product_variation_id',
        'warehouse_id',
        'size_id',
        'color_id',
        'quantity',
        'session_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function warehouseProduct()
    {
        return $this->belongsTo(WarehouseProduct::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
