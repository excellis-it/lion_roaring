<?php

namespace App\Helpers;
use App\Models\Article;
use App\Models\Chat;
use App\Models\Country;
use App\Models\Footer;
use App\Models\Organization;
use App\Models\OurOrganization;
use App\Models\PmaTerm;
use App\Models\Review;
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

    public static function getFooter()
    {
        $footer = Footer::orderBy('id', 'desc')->first();
        return $footer;
    }

    public static function expireTo($date)
    {
        // how many day left to expire
        $now = time();
        $your_date = strtotime($date);
        $datediff = $your_date - $now;
        $days = floor($datediff / (60 * 60 * 24));
        return $days;
    }

    public static function showTheLastChat($sender_id, $reciver_id)
    {
        $chats = Chat::where(function ($query) use ($sender_id, $reciver_id) {
            $query->where('sender_id', $sender_id)
                ->where('reciver_id', $reciver_id);
        })
            ->orWhere(function ($query) use ($sender_id, $reciver_id) {
                $query->where('sender_id', $reciver_id)
                    ->where('reciver_id', $sender_id);
            })
            ->orderBy('created_at', 'desc')
            ->first();
        return $chats;
    }

    public static function getPmaTerm()
    {
        $term = PmaTerm::orderBy('id', 'desc')->first();
        return $term;
    }

    public static function getTotalProductRating($product_id)
    {
        $total_rating = Review::where('product_id', $product_id)->sum('rating');
        $total_review = Review::where('product_id', $product_id)->count();
        if ($total_review > 0) {
            $avg_rating = $total_rating / $total_review;
        } else {
            $avg_rating = 0;
        }
        // showing 1 decimal point
        return $avg_rating = number_format((float)$avg_rating, 1, '.', '');
    }

    public static function getRatingCount($product_id)
    {
        $rating_count = Review::where('product_id', $product_id)->count();
        return $rating_count;
    }
}
