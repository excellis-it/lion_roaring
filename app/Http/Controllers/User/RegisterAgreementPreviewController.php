<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\RegisterAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDF;

class RegisterAgreementPreviewController extends Controller
{
    public function generate(Request $request)
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
        // Always satisfy the requested "I, user name" line.
        $html = '<p><strong>I, ' . e($signerName) . '</strong></p>' . $html;

        $token = (string) Str::uuid();
        $tmpPath = "register-agreements/tmp/{$token}.pdf";

        $pdf = PDF::loadView('pdf.register_agreement', [
            'title' => $title,
            'html' => $html,
            'signerName' => $signerName,
            'signerInitials' => $signerInitials,
        ]);

        Storage::disk('public')->put($tmpPath, $pdf->output());

        $request->session()->put('pending_register_agreement', [
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
            // Blade-ish
            '/{{\s*user_name\s*}}/i' => e($signerName),
            '/{{\s*i_user_name\s*}}/i' => 'I, ' . e($signerName),
            '/{{\s*user_initial\s*}}/i' => e($initials ?? ''),
            // Bracket style
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
