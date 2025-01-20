<?php

namespace App\Http\Controllers\Admin;

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
        $settings = SiteSetting::first();
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'SITE_NAME' => 'required|string|max:255',
            'SITE_LOGO' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',  // Validate logo file
            'SITE_CONTACT_EMAIL' => 'required|email|max:255',
            'SITE_CONTACT_PHONE' => 'required|string',
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

        $settings->save();

        return redirect()->route('admin.settings.edit')->with('success', 'Settings updated successfully');
    }
}
