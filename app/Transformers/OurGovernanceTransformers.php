<?php

namespace App\Transformers;

use App\Models\OurGovernance;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class OurGovernanceTransformers extends TransformerAbstract
{

    public function transform(OurGovernance $ourGovernance)
    {
        return [
            'banner_title' => $ourGovernance->name, // 'banner_title' => 'Our Governance
            'banner_image' => env('APP_URL') . Storage::url($ourGovernance->banner_image), // 'banner_image' => env('APP_URL') . Storage::url($ourGovernance->banner_image),
            'name' => $ourGovernance->name,
            'description' => $ourGovernance->description,
            'image_url' => env('APP_URL') . Storage::url($ourGovernance->image),
        ];
    }
}
