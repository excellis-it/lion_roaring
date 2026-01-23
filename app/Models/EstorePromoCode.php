<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstorePromoCode extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'code',
        'is_percentage',
        'discount_amount',
        'start_date',
        'end_date',
        'status',
        'scope_type',
        'user_ids',
        'product_ids',
    ];

    protected $casts = [
        'is_percentage' => 'boolean',
        'status' => 'boolean',
        'user_ids' => 'array',
        'product_ids' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function scopeSummary(): string
    {
        return match ($this->scope_type) {
            'all_users' => 'All users',
            'selected_users' => 'Selected users',
            'all_products' => 'All products',
            'selected_products' => 'Selected products',
            default => 'All orders',
        };
    }
}
