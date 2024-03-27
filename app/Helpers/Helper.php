<?php

namespace App\Helpers;
use App\Models\Article;
use App\Models\Country;
use App\Models\Organization;
use App\Models\OurOrganization;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Constraint\Count;

class Helper
{
    public static function getPDFAttribute()
    {
        $article = Article::orderBy('id', 'desc')->first();
        if ($article) {
            return Storage::url($article->pdf);
        } else {
            return '';
        }
    }

    public static function getOrganzations()
    {
        $organizations = OurOrganization::orderBy('id', 'desc')->get();
        return $organizations;
    }

    public static function getCountries()
    {
        $countries = Country::orderBy('name', 'asc')->get();
        return $countries;
    }
}
