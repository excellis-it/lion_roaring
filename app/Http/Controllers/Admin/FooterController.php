<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    use ImageTrait;

    public function index()
    {
        $footer = Footer::orderBy('id', 'desc')->first();
        return view('admin.footer.update')->with(compact('footer'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'footer_title' => 'required',
            'footer_newsletter_title' => 'required',
            'footer_playstore_link' => 'nullable',
            'footer_appstore_link' => 'nullable',
        ]);

        if ($request->id != '') {
            $footer = Footer::find($request->id);
        } else {
            $footer = new Footer();
        }

        $footer->footer_title = $request->footer_title;
        $footer->footer_newsletter_title = $request->footer_newsletter_title;
        $footer->footer_playstore_link = $request->footer_playstore_link;
        $footer->footer_appstore_link = $request->footer_appstore_link;
        if ($request->hasFile('footer_logo')) {
            $footer->footer_logo = $this->imageUpload($request->file('footer_logo'), 'footer');
        }
        $footer->save();
        return redirect()->back()->with('message', 'Footer updated successfully');
    }
}
