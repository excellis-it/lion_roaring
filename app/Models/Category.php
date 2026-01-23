<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'status', 'image', 'meta_title', 'meta_description', 'main'];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getBreadcrumbAttribute()
    {
        $category = $this;
        $breadcrumbs = [];

        while ($category->parent) {
            array_unshift($breadcrumbs, $category->parent->name); // prepend parent name
            $category = $category->parent;
        }

        return implode(' > ', $breadcrumbs);
    }

    public function getParentTreeAttribute()
    {
        $parent = $this->parent;
        $tree = [];

        while ($parent) {
            $tree[] = $parent->name;
            $parent = $parent->parent;
        }

        return implode(' → ', array_reverse($tree));
    }
}
