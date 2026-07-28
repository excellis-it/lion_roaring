<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TranslationClientLogController extends Controller
{
    private const ALLOWED_SURFACES = ['website', 'user_pma', 'ecom', 'elearning'];

    private const ALLOWED_REASONS = [
        'page_not_translated_after_reload',
        'google_translate_script_blocked',
        'google_translate_widget_missing',
        'google_translate_widget_timeout',
        'translation_not_detected_after_select',
        'googtrans_cookie_mismatch',
    ];

    /**
     * Receive anonymized browser translation failure diagnostics from the web UI.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:100',
            'expected_lang' => 'nullable|string|max:20',
            'surface' => 'nullable|string|max:30',
            'diagnostics' => 'nullable|array',
            'diagnostics.url' => 'nullable|string|max:2000',
            'diagnostics.path' => 'nullable|string|max:500',
            'diagnostics.userAgent' => 'nullable|string|max:500',
            'diagnostics.selectedLang' => 'nullable|string|max:30',
            'diagnostics.cookies' => 'nullable|array|max:20',
            'diagnostics.cookies.*' => 'nullable|string|max:500',
            'diagnostics.googTeCombo' => 'nullable|boolean',
            'diagnostics.googTeValue' => 'nullable|string|max:100',
            'diagnostics.htmlClasses' => 'nullable|string|max:500',
            'diagnostics.htmlLang' => 'nullable|string|max:30',
            'diagnostics.gtScriptLoaded' => 'nullable|boolean',
            'diagnostics.translatedDetected' => 'nullable|boolean',
            'diagnostics.activeTranslateLang' => 'nullable|string|max:30',
            'diagnostics.sessionLanguagesCount' => 'nullable|integer|min:0|max:500',
            'extra' => 'nullable|array',
        ]);

        $reason = (string) $validated['reason'];
        if (! in_array($reason, self::ALLOWED_REASONS, true)) {
            $reason = Str::limit($reason, 100, '');
        }

        $surface = $validated['surface'] ?? 'website';
        if (! in_array($surface, self::ALLOWED_SURFACES, true)) {
            $surface = 'website';
        }

        Log::warning('Translation client failure', [
            'reason' => $reason,
            'expected_lang' => $validated['expected_lang'] ?? null,
            'surface' => $surface,
            'user_id' => optional($request->user())->id,
            'ip' => $request->ip(),
            'diagnostics' => $validated['diagnostics'] ?? [],
            'extra' => $validated['extra'] ?? [],
        ]);

        return response()->json(['status' => true]);
    }
}
