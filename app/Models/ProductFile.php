<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'file_location',
    ];

    /**
     * Relationship: ProductFile belongs to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
