<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElearningCart extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'elearning_product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(ElearningProduct::class, 'elearning_product_id');
    }
}
