<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElearningSubCategory extends BaseModel
{
    use HasFactory;

    protected $fillable = ['elearning_category_id', 'name', 'slug', 'status', 'image', 'meta_title', 'meta_description'];

    public function category()
    {
        return $this->belongsTo(ElearningCategory::class, 'elearning_category_id');
    }
}
