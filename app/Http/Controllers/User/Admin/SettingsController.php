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
        if (!auth()->user()->can('Manage Site Settings')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $settings = SiteSetting::first();
        return view('user.admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!auth()->user()->can('Manage Site Settings')) {
            abort(403, 'You do not have permission to access this page.');
        }
        $request->validate([
            'SITE_NAME' => 'required|string|max:255',
            'SITE_LOGO' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',  // Validate logo file
            'PANEL_WATERMARK_LOGO' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',  // Validate watermark logo
            'PMA_PANEL_LOGO' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',  // Validate PMA panel logo
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

        // Handle file upload for PANEL_WATERMARK_LOGO
        if ($request->hasFile('PANEL_WATERMARK_LOGO')) {
            $watermarkLogo = $request->file('PANEL_WATERMARK_LOGO');

            // If you want to remove the old watermark logo before uploading a new one:
            if ($settings->PANEL_WATERMARK_LOGO && File::exists(public_path($settings->PANEL_WATERMARK_LOGO))) {
                File::delete(public_path($settings->PANEL_WATERMARK_LOGO));
            }

            // Define the path to store the file
            $destinationPath = public_path('user_assets/images');

            // Move the uploaded watermark logo to the desired location
            $watermarkLogo->move($destinationPath, 'watermark_logo.png');

            // Update the path in the database
            $settings->PANEL_WATERMARK_LOGO = 'user_assets/images/watermark_logo.png';
        }

        // Handle file upload for PMA_PANEL_LOGO
        if ($request->hasFile('PMA_PANEL_LOGO')) {
            $pmaPanelLogo = $request->file('PMA_PANEL_LOGO');

            // If you want to remove the old PMA panel logo before uploading a new one:
            if ($settings->PMA_PANEL_LOGO && File::exists(public_path($settings->PMA_PANEL_LOGO))) {
                File::delete(public_path($settings->PMA_PANEL_LOGO));
            }

            // Define the path to store the file
            $destinationPath = public_path('user_assets/images');

            // Move the uploaded PMA panel logo to the desired location
            $pmaPanelLogo->move($destinationPath, 'pma_panel_logo.png');

            // Update the path in the database
            $settings->PMA_PANEL_LOGO = 'user_assets/images/pma_panel_logo.png';
        }

        // Update other settings values
        $settings->SITE_NAME = $request->SITE_NAME;
        $settings->SITE_CONTACT_EMAIL = $request->SITE_CONTACT_EMAIL;
        $settings->SITE_CONTACT_PHONE = $request->SITE_CONTACT_PHONE;
        $settings->DONATE_TEXT = $request->DONATE_TEXT;  // Update donate text
        $settings->DONATE_BANK_TRANSFER_DETAILS = $request->DONATE_BANK_TRANSFER_DETAILS;  // Update bank transfer details

        $settings->save();

        return redirect()->route('user.admin.settings.edit')->with('message', 'Settings updated successfully');
    }
}
