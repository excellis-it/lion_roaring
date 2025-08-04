<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'short_description',
        'sku',
        'specification',
        'price',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function withOutMainImage()
    {
        return $this->hasMany(ProductImage::class)->where('featured_image', 0);
    }

    public function getMainImageAttribute()
    {
        return $this->images->where('featured_image', 1)->pluck('image')->first();
    }

    public function image()
    {
        return $this->hasOne(ProductImage::class)->where('featured_image', 1);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    public function isInWishlist()
    {
        if (auth()->check()) {
            return EcomWishList::where('product_id', $this->id)
                ->where('user_id', auth()->id())
                ->exists();
        }
        return false;
    }
}
