<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'product_type',
        'name',
        'description',
        'short_description',
        'slug',
        'feature_product',
        'status',
        'is_deleted',

        // only for simple product type
        'sku',
        'specification',
        'price',
        'quantity',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class, 'product_id');
    }

    // sizes with size details
    public function sizesWithDetails()
    {
        return $this->sizes()->with('size')->get();
    }

    // sizeIds
    public function sizeIds()
    {
        return $this->sizes->pluck('size_id');
    }

    public function colors()
    {
        return $this->hasMany(ProductColor::class, 'product_id');
    }

    // colors with color details
    public function colorsWithDetails()
    {
        return $this->colors()->with('color')->get();
    }

    // colorIds
    public function colorIds()
    {
        return $this->colors->pluck('color_id');
    }

    public function otherCharges()
    {
        return $this->hasMany(ProductOtherCharge::class, 'product_id');
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

    public function warehouseProducts()
    {
        return $this->hasMany(WarehouseProduct::class, 'product_id');
    }

    public function warehouses()
    {
        return $this->belongsToMany(WareHouse::class, 'warehouse_products', 'product_id', 'warehouse_id');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }
}
