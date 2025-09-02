<?php

namespace App\Transformers;

use App\Models\OrganizationCenter;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class OrganizationCenterTransformers extends TransformerAbstract
{

    public function transform(OrganizationCenter $organization_center)
    {
        return [
            'banner_title' => $organization_center->name, // 'banner_title' => 'Our Governance
            'banner_image' => Storage::url($organization_center->banner_image), // 'banner_image' => Storage::url($ourGovernance->banner_image),
            'name' => $organization_center->name,
            'description' => $organization_center->description,
            'image_url' => Storage::url($organization_center->image),
        ];
    }
}
