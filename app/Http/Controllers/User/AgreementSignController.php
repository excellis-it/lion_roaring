<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\RegisterAgreement;
use App\Models\UserRegisterAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDF;

class AgreementSignController extends Controller
{
    /**
     * Show the agreement signing page for existing members
     * who are missing signature or register agreement record.
     */
    public function show()
    {
        $user = Auth::user();

        // If user already has both, redirect to profile
        $hasSignature = !empty($user->signature);
        $hasAgreement = UserRegisterAgreement::where('user_id', $user->id)->exists();

        if ($hasSignature && $hasAgreement) {
            return redirect()->route('user.profile');
        }


        $agreement = Helper::getPDFAttribute();

        return view('user.auth.sign-agreement', compact('agreement', 'user'));
    }

    /**
     * Generate a PDF preview of the agreement (AJAX).
     */
    public function preview(Request $request)
    {
        $request->validate([
            'signer_name' => 'required|string|max:255',
        ]);

        $signerName = trim((string) $request->input('signer_name'));
        $signerInitials = $this->makeInitials($signerName);

        $countryCode = strtoupper(Helper::getVisitorCountryCode() ?: 'US');
        $template = RegisterAgreement::where('country_code', $countryCode)->orderBy('id', 'desc')->first();
        if (!$template) {
            $template = RegisterAgreement::where('country_code', 'US')->orderBy('id', 'desc')->first();
        }

        $title = $template->agreement_title ?? 'Lion Roaring PMA (Private Members Association) Agreement';
        $html = $template->agreement_description ?? 'This is the agreement for Lion Roaring PMA (Private Members Association)';
        $checkboxText = $template->checkbox_text ?? null;

        $html = $this->applyPlaceholders($html, $signerName, $signerInitials);
        $html = '<p></p>' . $html;

        $token = (string) Str::uuid();
        $tmpPath = "register-agreements/tmp/{$token}.pdf";

        $pdf = PDF::loadView('pdf.register_agreement', [
            'title' => $title,
            'html' => $html,
            'signerName' => $signerName,
            'signerInitials' => $signerInitials,
        ]);

        Storage::disk('public')->put($tmpPath, $pdf->output());

        $request->session()->put('pending_login_agreement', [
            'token' => $token,
            'tmp_path' => $tmpPath,
            'country_code' => $countryCode,
            'signer_name' => $signerName,
            'signer_initials' => $signerInitials,
            'agreement_title_snapshot' => $title,
            'agreement_description_snapshot' => $html,
            'checkbox_text_snapshot' => $checkboxText,
        ]);

        return response()->json([
            'status' => true,
            'token' => $token,
            'pdf_url' => Storage::url($tmpPath),
        ]);
    }

    /**
     * Process the agreement submission (signature + agreement PDF).
     */
    public function submit(Request $request)
    {
        $request->validate([
            'signature' => 'required|string',
        ], [
            'signature.required' => 'Please provide your signature before submitting.',
        ]);

        $user = Auth::user();

        $pending = $request->session()->get('pending_login_agreement');
        if (!is_array($pending) || empty($pending['tmp_path']) || empty($pending['signer_name'])) {
            return redirect()->back()->withErrors(['agreement' => 'Please review and accept the agreement before submitting.'])->withInput();
        }

        if (!Storage::disk('public')->exists($pending['tmp_path'])) {
            return redirect()->back()->withErrors(['agreement' => 'Agreement preview has expired. Please review the agreement again.'])->withInput();
        }

        // Move PDF from temp to permanent location
        $token = $pending['token'] ?? (string) Str::uuid();
        $finalPath = "register-agreements/users/{$user->id}/agreement-{$token}.pdf";

        Storage::disk('public')->makeDirectory("register-agreements/users/{$user->id}");
        $moved = Storage::disk('public')->move($pending['tmp_path'], $finalPath);
        if (!$moved) {
            $content = Storage::disk('public')->get($pending['tmp_path']);
            Storage::disk('public')->put($finalPath, $content);
            Storage::disk('public')->delete($pending['tmp_path']);
        }

        // Create UserRegisterAgreement record
        UserRegisterAgreement::create([
            'user_id' => $user->id,
            'country_code' => $pending['country_code'] ?? 'US',
            'signer_name' => $pending['signer_name'] ?? ($user->first_name . ' ' . $user->last_name),
            'signer_initials' => $pending['signer_initials'] ?? null,
            'pdf_path' => $finalPath,
            'agreement_title_snapshot' => $pending['agreement_title_snapshot'] ?? null,
            'agreement_description_snapshot' => $pending['agreement_description_snapshot'] ?? null,
            'checkbox_text_snapshot' => $pending['checkbox_text_snapshot'] ?? null,
        ]);

        // Update user signature
        $user->signature = $request->signature;
        $user->save();

        $request->session()->forget('pending_login_agreement');

        return redirect()->route('user.profile')->with('message', 'Agreement signed successfully! Welcome to Lion Roaring PMA.');
    }

    private function makeInitials(string $name): ?string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $parts = array_values(array_filter($parts, fn($p) => $p !== ''));
        if (count($parts) === 0) {
            return null;
        }

        $initials = '';
        foreach ($parts as $part) {
            $initials .= Str::upper(Str::substr($part, 0, 1));
            if (Str::length($initials) >= 4) {
                break;
            }
        }

        return $initials ?: null;
    }

    private function applyPlaceholders(string $html, string $signerName, ?string $initials): string
    {
        $replacements = [
            '/\{\{\s*user_name\s*\}\}/i' => e($signerName),
            '/\{\{\s*i_user_name\s*\}\}/i' => 'I, ' . e($signerName),
            '/\{\{\s*user_initial\s*\}\}/i' => e($initials ?? ''),
            '/\[\[\s*user_name\s*\]\]/i' => e($signerName),
            '/\[\[\s*i_user_name\s*\]\]/i' => 'I, ' . e($signerName),
            '/\[\[\s*user_initial\s*\]\]/i' => e($initials ?? ''),
        ];

        foreach ($replacements as $pattern => $value) {
            $html = preg_replace($pattern, $value, $html) ?? $html;
        }

        return $html;
    }
}
