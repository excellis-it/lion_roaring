<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    //
    public function edit()
    {
        if(!auth()->user()->can('Manage Site Settings')){
            abort(403, 'You do not have permission to access this page.');
        }
        $settings = SiteSetting::first();
        return view('user.admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        if(!auth()->user()->can('Manage Site Settings')){
            abort(403, 'You do not have permission to access this page.');
        }
        $request->validate([
            'SITE_NAME' => 'required|string|max:255',
            'SITE_LOGO' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',  // Validate logo file
            'SITE_CONTACT_EMAIL' => 'required|email|max:255',
            'SITE_CONTACT_PHONE' => 'required|string',
            'DONATE_TEXT' => 'nullable|string',  // Validate donate text
            'DONATE_BANK_TRANSFER_DETAILS' => 'nullable|string',  // Validate bank transfer details
        ]);

        $settings = SiteSetting::first();

        // Handle file upload for SITE_LOGO
        if ($request->hasFile('SITE_LOGO')) {
            $logo = $request->file('SITE_LOGO');

            // If you want to remove the old logo before uploading a new one:
            if (File::exists(public_path('storage/' . $settings->SITE_LOGO))) {
                File::delete(public_path('storage/' . $settings->SITE_LOGO));
            }

            // Define the path to store the file
            $destinationPath = public_path('user_assets/images');

            // Move the uploaded logo to the desired location
            $logo->move($destinationPath, 'logo.png');

            // Update the path in the database
            $settings->SITE_LOGO = 'user_assets/images/logo.png';
        }

        // Update other settings values
        $settings->SITE_NAME = $request->SITE_NAME;
        $settings->SITE_CONTACT_EMAIL = $request->SITE_CONTACT_EMAIL;
        $settings->SITE_CONTACT_PHONE = $request->SITE_CONTACT_PHONE;
        $settings->DONATE_TEXT = $request->DONATE_TEXT;  // Update donate text
        $settings->DONATE_BANK_TRANSFER_DETAILS = $request->DONATE_BANK_TRANSFER_DETAILS;  // Update bank transfer details

        $settings->save();

        return redirect()->route('admin.settings.edit')->with('success', 'Settings updated successfully');
    }
}
