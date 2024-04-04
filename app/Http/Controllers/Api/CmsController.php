<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EcclesiaAssociation;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Organization;
use App\Models\PrincipalAndBusiness;
use App\Transformers\EcclesiaAssociationTransformers;
use App\Transformers\FaqTransformers;
use App\Transformers\GalleryTransformers;
use App\Transformers\OrganizationTransformers;
use App\Transformers\PrincipalTransformers;
use Illuminate\Http\Request;
use Spatie\Fractalistic\Fractal;

/**
 * @group CMS Management
 */
class CmsController extends Controller
{
    protected $successStatus = 200;

    /**
     * Get gallery
     * @response 200{
     * "message": "Gallery",
     * "status": true,
     * "gallery": [
     *     {
     *       "image_url": "http://127.0.0.1:8000/storage/gallery/kg4vGiEpej4HXuc50ZVKQC2Agjvn6wj3rMygAOxu.jpg"
     *     },
     *   ]
     * }
     * @response 201 {
     * "message": "No gallery found",
     * "status": false
     * }
     */

    public function gallery(Request $request)
    {
        try {
            $galleries = Gallery::orderBy('id', 'desc')->get();
            if ($galleries) {
                $galleries = fractal($galleries, new GalleryTransformers())->toArray()['data'];
                return response()->json(['message' => 'Gallery', 'status' => true, 'gallery' => $galleries], $this->successStatus);
            } else {
                return response()->json(['message' => 'No gallery found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get FAQ
     *
     * @response 200{
     * "message": "FAQ",
     * "status": true,
     * "faq": [
     *    {
     *      "title": "Are there any transport facilities available for the staff members?",
     *      "content": "This is the second item's accordion body. It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the .accordion-body, though the transition does limit overflow."
     *   },
     * ]
     * }
     * @response 201 {
     * "message": "No FAQ found",
     * "status": false
     * }
     */

    public function faq(Request $request)
    {
        try {
            $faqs = Faq::orderBy('id', 'desc')->get();
            if ($faqs) {
                $faqs = fractal($faqs, new FaqTransformers())->toArray()['data'];
                return response()->json(['message' => 'FAQ', 'status' => true, 'faq' => $faqs], $this->successStatus);
            } else {
                return response()->json(['message' => 'No FAQ found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get Principal and Business Model
     * @response 200{
     * "message": "Principal and Business",
     * "status": true,
     * "principleAndBusiness": {
     *   "banner_image_url": "http://127.0.0.1:8000/storage/principle-and-business/iJt84RS9xLxfytBLynRBg4huIy9HfhfHWwJXEftc.jpg",
     *   "banner_title": "OUR PRINCIPLE AND BUSINESS MODEL",
     *   "image_url": "http://127.0.0.1:8000/storage/principle-and-business/CzFIynj2rOY7JSYtOVt1KtIKMFxFrP2A38J7Hwpz.png",
     *   "title": "OUR PRINCIPLE AND BUSINESS MODEL",
     *   "content": "<p><strong>These seven primodial spirits in God is the Core principle that Lion Roaring has adopted in order to pursue perfected love in our soul and become like Christ. And the ultimate goal in Lion Roaring is to be able to bring Heaven on Earth.</strong>
     * }
     * }
     * @response 201 {
     * "message": "No Principal and Business found",
     * "status": false
     * }
     */

    public function principleAndBusiness(Request $request)
    {
        try {
            $principleAndBusiness = PrincipalAndBusiness::orderBy('id', 'desc')->first();
            if ($principleAndBusiness) {
                $principleAndBusiness = fractal($principleAndBusiness, new PrincipalTransformers())->toArray()['data'];
                return response()->json(['message' => 'Principal and Business', 'status' => true, 'principleAndBusiness' => $principleAndBusiness], $this->successStatus);
            } else {
                return response()->json(['message' => 'No Principal and Business found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get Ecclesia Associations
     * @response 200{
     * "message": "Ecclesia Associations",
     * "status": true,
     * "ecclesiaAssociations": {
     *   "banner_image_url": "http://127.0.0.1:8000/storage/principle-and-business/iJt84RS9xLxfytBLynRBg4huIy9HfhfHWwJXEftc.jpg",
     *   "banner_title": "OUR ECCLESIA ASSOCIATION",
     *   "title": "OUR ECCLESIA ASSOCIATION",
     *   "content": "<p><strong>These seven primodial spirits in God is the Core principle that Lion Roaring has adopted in order to pursue perfected love in our soul and become like Christ. And the ultimate goal in Lion Roaring is to be able to bring Heaven on Earth.</strong>
     *   }
     * }
     * @response 201 {
     * "message": "No Ecclesia Associations found",
     * "status": false
     * }
     */

    public function ecclesiaAssociations(Request $request)
    {
        try {
            $ecclesiaAssociations = EcclesiaAssociation::orderBy('id', 'desc')->first();
            if ($ecclesiaAssociations) {
                $ecclesiaAssociations = fractal($ecclesiaAssociations, new EcclesiaAssociationTransformers())->toArray()['data'];
                return response()->json(['message' => 'Ecclesia Associations', 'status' => true, 'ecclesiaAssociations' => $ecclesiaAssociations], $this->successStatus);
            } else {
                return response()->json(['message' => 'No Ecclesia Associations found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Get Organization
     * @response 200{
     * "message": "Organization",
     * "status": true,
     * "organization": {
     *"banner_section": {
     *"banner_image_url": "http://127.0.0.1:8000/storage/organization/paSVDc0EoYdGYGzvDpbK3akKyGkpEQII5SXIKt9c.jpg",
     *"banner_title": "OUR ORGANIZATION"
     *},
     *"about_section": {
     *"about_section_image_1": "http://127.0.0.1:8000/storage/organization/ygqFzHisBYM8pAoWIWf4WJuK0QIP6KjGTEHGfpgy.png",
     *"about_section_image_2": "http://127.0.0.1:8000/storage/organization/WqrUEHEAP8nsL4kRC0Qr2zo4tyI9cKVSjcQeWUG5.png",
     *"about_section_image_3": "http://127.0.0.1:8000/storage/organization/gNMs84RgkxLAcYAFlAq06YE3BHexYN0IeOZHADIO.png",
     *"about_section_image_4": "http://127.0.0.1:8000/storage/organization/UvaKMc7pcWKDGxTN20MF1aTK8wmfZPRH2LA1XLbE.png",
     *"about_section_image_5": "http://127.0.0.1:8000/storage/organization/Btj1lC8MDyDrn5oVwoJRfZarYsYQefTin5wIJlyl.png"
     *},
     *"project_section": {
     *"project_section_title": "LION ROARING INNOVATION CENTERS OVERVIEW",
     *"project_section_sub_title": "KINGSHIP AUTHORITY",
     *"project_section_description": "Lion Roaring Innovation Centers"
     *},
     *"project_section_details": [
     *{
     *    "title": "Science & Technology Innovation Center",
     *    "details": "<ul><li>Digital Transformation</li><li>Quantum Technology</li><li>BioTech</li><li>Telecommunication</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Healthcare & Medicine Innovation Center",
     *    "details": "<ul><li>Miracle Healing</li><li>Oil &amp; Herb Medicine</li><li>Natural Nutrition Intake</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Farming, Food & Nutrition Innovation Center",
     *    "details": "<ul><li>Agriculture</li><li>Distribution</li><li>Preserve &amp; Packaging</li><li>Farming</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Lion Roaring Foundation",
     *    "details": "<ul><li>US National Status</li><li>Change our Status</li><li>Training &amp; mentorship</li><li>Restoration of cities &amp; State via Education</li><li>Private Bank, Trust, 1099a, W4, etc.</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Animal Shelter & Training Innovation Center",
     *    "details": "<ul><li>Training</li><li>Rescue/Safari</li><li>Boarding</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Music, Media, Innovation Center",
     *    "details": "<ul><li>Music/Worship &amp; Arts</li><li>Harp &amp; Shofar, others</li><li>Dancing &amp; Painting</li></ul><p><br>&nbsp;</p>"
     *},
     *{
     *    "title": "Entertainment & Arts Innovation Center",
     *    "details": "<ul><li>Video &amp; Photo</li><li>Media &amp; Entertainment</li><li>Marketing &amp; Publisher</li></ul><p><br>&nbsp;</p>"
     *}
     *]
     *}
     * }
     * @response 201 {
     * "message": "No Organization found",
     * "status": false
     * }
     */

    public function ourOrganization(Request $request)
    {
        try {
            $organization = Organization::orderBy('id', 'desc')->first();
            if ($organization) {
                $organization = fractal($organization, new OrganizationTransformers())->toArray()['data'];
                return response()->json(['message' => 'Organization', 'status' => true, 'organization' => $organization], $this->successStatus);
            } else {
                return response()->json(['message' => 'No Organization found', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }
}
