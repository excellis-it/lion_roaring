<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcomWishList extends Model
{
    use HasFactory;

    protected $table = 'ecom_wish_lists';

    protected $fillable = [
        'user_id',
        'product_id',
    ];
}
