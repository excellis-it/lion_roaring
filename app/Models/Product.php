<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'product_type',
        'name',
        'background_image',
        'description',
        'short_description',
        'slug',
        'feature_product',
        'is_new_product',
        'status',
        'is_free',
        'is_deleted',

        // only for simple product type
        'sku',
        'specification',
        'price',
        'sale_price',
        'quantity',
        'market_grams',
        'market_unit',
        'is_market_priced',
    ];

    protected $appends = [
        'main_image',
        'average_rating',
        'review_count',
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

    public function files()
    {
        return $this->hasMany(ProductFile::class);
    }
    // review count of approved reviews
    public function getReviewCountAttribute()
    {
        return $this->reviews()->approved()->count();
    }

    // average rating of approved reviews
    public function getAverageRatingAttribute()
    {
        $avg_rating = $this->reviews()->approved()->avg('rating');
        // round to one decimal place and return 0 if null
        return $avg_rating ? round($avg_rating, 1) : 0;
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

    // variation colors
    public function variationColors()
    {
        return $this->hasManyThrough(Color::class, ProductVariation::class, 'product_id', 'id', 'id', 'color_id');
    }

    // variation unique colors
    public function variationUniqueColors()
    {
        return $this->variationColors()->distinct();
    }


    // color images
    public function colorImages()
    {
        return $this->hasMany(ProductColorImage::class, 'product_id');
    }

    /**
     * Determine if the product is free.
     */
    public function getIsFreeAttribute($value)
    {
        return (bool) $value;
    }

    /**
     * Convenience helper.
     */
    public function isFree(): bool
    {
        return (bool) $this->is_free;
    }

    // unique color first image with color detail from ProductColorImage (only one image for each color)
    public function getVariationUniqueColorFirstImagesAttribute()
    {

        // get all images for this product with color, then pick first image per color
        $images = $this->colorImages()
            ->with('color')
            ->get()
            ->unique('color_id')
            ->values();

        return $images;
    }

    // check if this product purchased by the user anytime
    public function isPurchasedByUser($userId)
    {
        return EstoreOrderItem::where('product_id', $this->id)
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('payment_status', 'paid');
            })
            ->exists();
    }

    // check if user already reviewed this product
    public function isReviewedByUser($userId)
    {
        return Review::where('product_id', $this->id)
            ->where('user_id', $userId)
            ->exists();
    }


    // getProductFirstImage get first image from colorImages if exists if provide color_id otherwise get from images
    // getProductFirstImage use like $product->getProductFirstImage($color_id)
    public function getProductFirstImage($color_id = null)
    {
        if ($color_id) {

            return $this->colorImages->where('color_id', $color_id)->first()->image_path ?? $this->images->first()->image;
        }

        return $this->images->first()->image;
    }

    // in products map main image, to get in api response


}
