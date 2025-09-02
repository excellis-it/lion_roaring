<?php

namespace App\Transformers;

use App\Models\Footer;
use App\Models\EcclesiaAssociation;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class EcclesiaAssociationTransformers extends TransformerAbstract
{

    public function transform(EcclesiaAssociation $ecclesiaAssociations)
    {
        return [
            'banner_image_url' => Storage::url($ecclesiaAssociations->banner_image), // 'http://localhost:8000/storage/imagename.jpg
            'banner_title' => $ecclesiaAssociations->banner_title,
            'image_url' =>  Storage::url($ecclesiaAssociations->image),
            'title' => $ecclesiaAssociations->banner_title,
            'content_1' => $ecclesiaAssociations->description,
            'content_2' => $ecclesiaAssociations->description1,
        ];
    }
}
