<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'country_code',
    //     'banner_title',
    //     'banner_description',
    //     'banner_image',
    //     'meta_title',
    //     'meta_description',
    //     'meta_keywords',
    // ];


    protected $guarded = [];


    public function images()
    {
        return $this->hasMany(OrganizationImage::class);
    }

    public function projects()
    {
        return $this->hasMany(OrganizationProject::class)->where('section', 1);
    }

    public function projectsTwo()
    {
        return $this->hasMany(OrganizationProject::class)->where('section', 2);
    }
}
