<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Footer;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;


class FooterController extends Controller
{
    use ImageTrait;

    public function index(Request $request)
    {
        if (auth()->user()->can('Manage Footer')) {
            $countryCode = $request->get('content_country_code', 'US');
            $loaded = Helper::loadCmsRowForEdit(Footer::class, $countryCode);

            // BUG-058: social links UI removed (not rendered on website footer)
            return view('admin.footer.update', [
                'footer' => $loaded['row'],
                'isUsPrefill' => $loaded['isUsPrefill'],
                'cmsEditCountryCode' => $loaded['countryCode'],
                'prefillCountryName' => Helper::cmsPrefillCountryName($loaded['countryCode']),
            ]);
        } else {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function update(Request $request)
    {
        // return $request;
        $request->validate([
            'footer_title' => 'required',
            'footer_address_title' => 'required',
            'footer_address' => 'required',
            'footer_phone_number' => 'required',
            'footer_email' => 'required',
            'footer_copywrite_text' => 'required',
            'footer_newsletter_title' => 'required',
        ]);

        $country = $request->content_country_code ?? 'US';

        $footer = null;
        if ($request->filled('id')) {
            $footer = Footer::query()
                ->where('id', $request->id)
                ->where('country_code', $country)
                ->first();
        }
        if (!$footer) {
            $footer = new Footer();
        }

        $footer->footer_title = $request->footer_title;
        $footer->footer_newsletter_title = $request->footer_newsletter_title;
        $footer->footer_address_title = $request->footer_address_title;
        $footer->footer_address = $request->footer_address;
        $footer->footer_phone_number = $request->footer_phone_number;
        $footer->footer_email = $request->footer_email;
        $footer->footer_copywrite_text = $request->footer_copywrite_text;
        // BUG-058: do not overwrite play/app store fields — admin UI for those is hidden
        if ($request->hasFile('footer_logo')) {
            $request->validate([
                'footer_logo' => 'mimes:jpeg,jpg,png,gif,webp|required',
            ]);
            $footer->footer_logo = $this->imageUpload($request->file('footer_logo'), 'footer');
        }

        if ($request->hasFile('footer_flag')) {
            $request->validate([
                'footer_flag' => 'mimes:jpeg,jpg,png,gif|required',
            ]);
            $footer->footer_flag = $this->imageUpload($request->file('footer_flag'), 'footer');
        }

        $attrs = $footer->getAttributes();
        unset($attrs['id']);
        $attrs = Helper::applyUsCmsMediaDefaults(Footer::class, $country, $attrs, ['footer_logo', 'footer_flag']);
        $footer = Footer::updateOrCreate(['country_code' => $country], array_merge($attrs, ['country_code' => $country]));

        return redirect()->back()->with('message', 'Footer updated successfully');
    }
}
