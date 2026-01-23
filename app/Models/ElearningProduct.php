<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ElearningTopic;

class ElearningProduct extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'elearning_topic_id',
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
        return $this->belongsTo(ElearningCategory::class);
    }

    public function images()
    {
        return $this->hasMany(ElearningProductImage::class, 'product_id');
    }

    public function withOutMainImage()
    {
        return $this->hasMany(ElearningProductImage::class, 'product_id')->where('featured_image', 0);
    }

    public function getMainImageAttribute()
    {
        return $this->images->where('featured_image', 1)->pluck('image')->first();
    }

    public function image()
    {
        return $this->hasOne(ElearningProductImage::class, 'product_id')->where('featured_image', 1);
    }

    public function reviews()
    {
        return $this->hasMany(ElearningReview::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function elearningTopic()
    {
        return $this->belongsTo(ElearningTopic::class, 'elearning_topic_id');
    }

    public function topic()
    {
        return $this->belongsTo(ElearningTopic::class, 'elearning_topic_id');
    }
}
