<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Models\ContactUs;
use App\Models\ContactUsCms;
use App\Models\Detail;
use App\Models\EcclesiaAssociation;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\HomeCms;
use App\Models\Newsletter;
use App\Models\Organization;
use App\Models\OrganizationCenter;
use App\Models\OurGovernance;
use App\Models\OurOrganization;
use App\Models\PrincipalAndBusiness;
use App\Models\PrincipleBusinessImage;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Mail\NewsletterSubscription;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUsForm;
use App\Mail\ContactUsUserConfirmation;
use App\Models\PrivacyPolicy;
use App\Models\SiteSetting;
use App\Models\TermsAndCondition;

class CmsController extends Controller
{
    public function index()
    {
        $home = HomeCms::orderBy('id', 'desc')->first();
        $galleries = Gallery::orderBy('id', 'desc')->get();
        $testimonials = Testimonial::orderBy('id', 'desc')->get();
        $our_organizations = OurOrganization::orderBy('id', 'desc')->get();
        $our_governances = OurGovernance::orderBy('id', 'desc')->get();
        return view('frontend.home')->with(compact('galleries', 'testimonials', 'our_organizations', 'our_governances', 'home'));
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
        $principle_images = PrincipleBusinessImage::get();
        return view('frontend.principle-and-business')->with(compact('principleAndBusiness', 'principle_images'));
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
        $our_organization = OurOrganization::where('slug', $slug)->first();
        $services  = $our_organization->services;
        return view('frontend.service')->with(compact('our_organization', 'services'));
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

    public function details()
    {
        $details = Detail::orderBy('id', 'asc')->get();
        return view('frontend.details')->with('details', $details);
    }

    public function newsletter(Request $request)
    {
        $request->validate([
            'newsletter_name' => 'required',
            'newsletter_email' => 'required|email',
            'newsletter_message' => 'required',
        ]);

        if ($request->ajax()) {
            $newsletter = new Newsletter();
            $newsletter->full_name = $request->newsletter_name;
            $newsletter->email = $request->newsletter_email;
            $newsletter->message = $request->newsletter_message;
            $newsletter->save();

            $adminEmail = SiteSetting::first()->SITE_CONTACT_EMAIL;
            $mailData = [
                'name' => $newsletter->full_name,
                'email' => $newsletter->email,
                'message' => $newsletter->message,
            ];

            // Send mail using Mailable
            Mail::to($adminEmail)->send(new NewsletterSubscription($mailData));


            return response()->json(['message' => 'Thank you for subscribing to our newsletter', 'status' => true]);
        }
    }

    public function contactUsForm(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required',
        ]);

        if ($request->ajax()) {
            $contact = new ContactUs();
            $contact->first_name = $request->first_name;
            $contact->last_name = $request->last_name;
            $contact->email = $request->email;
            $contact->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone : $request->phone;
            $contact->message = $request->message;
            $contact->save();

            $contactData = [
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'message' => $contact->message,
            ];

            // Send email to admin
            $adminEmail = SiteSetting::first()->SITE_CONTACT_EMAIL;
            try {
                Mail::to($adminEmail)->send(new ContactUsForm($contactData));

                // Send confirmation email to the user
                Mail::to($contact->email)->send(new ContactUsUserConfirmation($contactData));
            } catch (\Throwable $th) {
                session()->flash('success', 'Thank you for contacting us');
            return response()->json(['message' => 'Thank you for contacting us', 'status' => true]);
            }


            session()->flash('success', 'Thank you for contacting us');
            return response()->json(['message' => 'Thank you for contacting us', 'status' => true]);
        }
    }


    public function session(Request $request)
    {
        // return $request->all();
        if ($request->is_checked) {
            Session::put('agree', 'true');
            session()->flash('message', 'You have agreed to the terms and conditions');
            return redirect()->back();
        } else {
            session()->flash('error', 'Please agree to the terms and conditions');
            return redirect()->back();
        }
    }


    public function privacy_policy()
    {
        $privacy_policy = PrivacyPolicy::orderBy('id', 'desc')->first();
        return view('frontend.privacy-policy')->with('privacy_policy', $privacy_policy);
    }


    public function terms()
    {
        $term = TermsAndCondition::orderBy('id', 'desc')->first();
        return view('frontend.terms-and-condition')->with('term', $term);
    }
}
