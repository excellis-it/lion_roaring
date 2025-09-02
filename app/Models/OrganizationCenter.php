<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_image',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'our_organization_id',
    ];


    public function ourOrganization()
    {
        return $this->belongsTo(OurOrganization::class);
    }
}
