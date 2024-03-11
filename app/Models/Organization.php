<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'banner_title',
        'banner_description',
        'banner_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];


    public function images()
    {
        return $this->hasMany(OrganizationImage::class);
    }

    public function projects()
    {
        return $this->hasMany(OrganizationProject::class);
    }
}
