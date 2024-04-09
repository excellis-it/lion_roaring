<?php

namespace App\Transformers;

use App\Models\Organization;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class OrganizationTransformers extends TransformerAbstract
{

    public function transform(Organization $organization)
    {
        $banner_section = [
            'banner_image_url' => Storage::url($organization->banner_image), // 'http://localhost:8000/storage/imagename.jpg
            'banner_title' => $organization->banner_title,
            'banner_description' => $organization->banner_description,
        ];

        $about_section = [];
        foreach ($organization->images as $key => $value) {
            $about_section['about_section_image_' . $key+1] = Storage::url($value->image);
        }
        $project_section = [
            'project_section_title' => $organization->project_section_title,
            'project_section_sub_title' => $organization->project_section_sub_title,
            'project_section_description' => $organization->project_section_description,
         ];

        $project_section_details = [];
        foreach ($organization->projects as $key => $value) {
            $project_section_details[] = [
                'title' => $value->title,
                'details' => $value->description,
            ];
        }

        return [
            'banner_section' => $banner_section,
            'about_section' => $about_section,
            'project_section' => $project_section,
            'project_section_details' => $project_section_details,
        ];
    }
}
