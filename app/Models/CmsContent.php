<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsContent extends Model
{
    use HasFactory;

    protected $fillable = ['page', 'model_name', 'slug', 'country_code', 'content'];

    protected $casts = [
        'content' => 'array', // Laravel will auto convert JSON to array
    ];

    public static function getContent($page, $model_name, $slug = null, $countryCode = 'US')
    {
        // Try to get country-specific content
        $cms = self::where('page', $page)
            ->where('model_name', $model_name)
            ->where('slug', $slug)
            ->where('country_code', $countryCode)
            ->first();

        if (!$cms) {
            // fallback to default US content
            $cms = self::where('page', $page)
                ->where('model_name', $model_name)
                ->where('slug', $slug)
                ->where('country_code', 'US')
                ->first();
        }

        // if also not data found for US then return from base model with id desc first record
        if (!$cms) {
            $data = $model_name::orderBy('id', 'desc')->first();
            if ($data) {
                // Ensure country_code is available
                $data->country_code = 'US';
                return $data;
            } else {
                return null;
            }
        } else {
            // Add content as dynamic properties
            foreach ($cms->content as $key => $value) {
                $cms->$key = $value;
            }

            // Ensure country_code is available
            $cms->country_code = $cms->country_code;

            return $cms;
        }

        // if (!$cms) return null;
        return null;
    }
}
