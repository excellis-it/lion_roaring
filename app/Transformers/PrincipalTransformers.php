<?php

namespace App\Transformers;

use App\Models\PrincipalAndBusiness;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class PrincipalTransformers extends TransformerAbstract
{

    public function transform(PrincipalAndBusiness $principalAndBusiness)
    {
        return [
            'banner_image_url' => env('APP_URL') . Storage::url($principalAndBusiness->banner_image), // 'http://localhost:8000/storage/imagename.jpg
            'banner_title' => $principalAndBusiness->banner_title,
            'image_url' =>  env('APP_URL') . Storage::url($principalAndBusiness->image),
            'title' => $principalAndBusiness->banner_title,
            'content' => $principalAndBusiness->description,
        ];
    }
}
