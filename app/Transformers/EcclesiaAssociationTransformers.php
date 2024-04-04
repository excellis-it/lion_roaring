<?php

namespace App\Transformers;

use App\Models\EcclesiaAssociation;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class EcclesiaAssociationTransformers extends TransformerAbstract
{

    public function transform(EcclesiaAssociation $ecclesiaAssociation)
    {
        return [
            'banner_image_url' => env('APP_URL') . Storage::url($ecclesiaAssociation->banner_image), // 'http://localhost:8000/storage/imagename.jpg
            'banner_title' => $ecclesiaAssociation->banner_title,
            'title' => $ecclesiaAssociation->banner_title,
            'content' => $ecclesiaAssociation->description,
        ];
    }
}
