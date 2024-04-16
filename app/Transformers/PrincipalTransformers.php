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
            'banner_image_url' => Storage::url($principalAndBusiness->banner_image), // 'http://localhost:8000/storage/imagename.jpg
            'banner_title' => $principalAndBusiness->banner_title,
            'image_url' =>  Storage::url($principalAndBusiness->image),
            'title' => $principalAndBusiness->banner_title,
            'content' => $principalAndBusiness->description,
            'content1' => $principalAndBusiness->description1 ?? null,
            'content2' => $principalAndBusiness->description2 ?? null,
            'content3' => $principalAndBusiness->description3 ?? null,
            'content4' => $principalAndBusiness->description4 ?? null,
        ];
    }
}
