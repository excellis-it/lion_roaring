<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoreOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'warehouse_product_id',
        'warehouse_id',
        'product_name',
        'product_image',
        'price',
        'quantity',
        'size_id',
        'color_id',
        'other_charges',
        'total',
        'size',
        'color',
        'warehouse_name',
        'warehouse_address',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(EstoreOrder::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'warehouse_id');
    }

    // warehouse product
    public function warehouseProduct()
    {
        return $this->belongsTo(WarehouseProduct::class, 'warehouse_product_id');
    }
}
