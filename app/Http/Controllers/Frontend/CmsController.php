<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\ContactUsCms;
use App\Models\EcclesiaAssociation;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\HomeCms;
use App\Models\Organization;
use App\Models\OrganizationCenter;
use App\Models\OurGovernance;
use App\Models\OurOrganization;
use App\Models\PrincipalAndBusiness;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function index()
    {
        $home = HomeCms::orderBy('id', 'desc')->first();
        $galleries = Gallery::orderBy('id', 'desc')->get();
        $testimonials = Testimonial::orderBy('id', 'desc')->get();
        $our_organizations = OurOrganization::orderBy('id', 'desc')->get();
        $our_governances = OurGovernance::orderBy('id', 'desc')->get();
        return view('frontend.home')->with(compact('galleries','testimonials','our_organizations','our_governances','home'));
    }

    public function gallery()
    {
        $galleries = Gallery::orderBy('id', 'desc')->get();
        return view('frontend.gallery')->with('galleries', $galleries);
    }

    public function contactUs()
    {
        $contact = ContactUsCms::first();
        return view('frontend.contact-us')->with('contact', $contact);
    }

    public function faq()
    {
        $faqs = Faq::orderBy('id', 'desc')->get();
        return view('frontend.faq')->with('faqs', $faqs);
    }

    public function principleAndBusiness()
    {
        $principleAndBusiness = PrincipalAndBusiness::orderBy('id', 'desc')->first();
        return view('frontend.principle-and-business')->with('principleAndBusiness', $principleAndBusiness);
    }

    public function ecclesiaAssociations()
    {
        $ecclesiaAssociations = EcclesiaAssociation::orderBy('id', 'desc')->first();
        return view('frontend.ecclesia-associations')->with('ecclesiaAssociations', $ecclesiaAssociations);
    }


    public function organization()
    {
        $organization = Organization::orderBy('id', 'desc')->first();
        return view('frontend.organizations')->with('organization', $organization);
    }

    public function service($slug)
    {
        $service = OurOrganization::where('slug', $slug)->first();
        return view('frontend.service')->with('service', $service);
    }

    public function ourOrganization($slug)
    {
        $our_organization = OurOrganization::where('slug', $slug)->first();
        $organization_centers = OrganizationCenter::where('our_organization_id', $our_organization->id)->orderBy('id', 'desc')->get();
        return view('frontend.our-organization')->with(compact('our_organization', 'organization_centers'));
    }

    public function features($slug)
    {
        $organization_center = OrganizationCenter::where('slug', $slug)->first();
        return view('frontend.features')->with('organization_center', $organization_center);
    }

    public function ourGovernance($slug)
    {
        $our_governance = OurGovernance::where('slug', $slug)->first();
        return view('frontend.our-governance')->with('our_governance', $our_governance);
    }

    public function aboutUs()
    {
        $about_us = AboutUs::orderBy('id', 'desc')->first();
        return view('frontend.about-us')->with('about_us', $about_us);
    }
}
