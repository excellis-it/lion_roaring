<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElearningReview extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'review',
        'rating',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(ElearningProduct::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
