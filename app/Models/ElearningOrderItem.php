<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElearningOrderItem extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'elearning_order_id',
        'elearning_product_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(ElearningOrder::class, 'elearning_order_id');
    }

    public function product()
    {
        return $this->belongsTo(ElearningProduct::class, 'elearning_product_id');
    }
}
