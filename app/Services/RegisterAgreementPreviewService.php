<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\RegisterAgreement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDF;

/**
 * Builds register-agreement PDF previews and stores pending metadata for mobile/API clients.
 */
class RegisterAgreementPreviewService
{
    public const CACHE_PREFIX = 'pending_login_agreement_';

    public const CACHE_PREFIX_GUEST = 'pending_register_agreement_guest_';

    public const CACHE_TTL_MINUTES = 120;

    /**
     * @return array{token: string, tmp_path: string, country_code: string, signer_name: string, signer_initials: ?string, agreement_title_snapshot: string, agreement_description_snapshot: string, checkbox_text_snapshot: ?string}
     */
    public function buildAndCacheForUser(int $userId, string $signerName, ?string $countryCode = null): array
    {
        $signerName = trim($signerName);
        $signerInitials = $this->makeInitials($signerName);

        $countryCode = strtoupper($countryCode ?: Helper::getVisitorCountryCode() ?: 'US');
        $template = RegisterAgreement::where('country_code', $countryCode)->orderByDesc('id')->first();
        if (!$template) {
            $template = RegisterAgreement::where('country_code', 'US')->orderByDesc('id')->first();
        }

        $title = $template->agreement_title ?? 'Lion Roaring PMA (Private Members Association) Agreement';
        $html = $template->agreement_description ?? 'This is the agreement for Lion Roaring PMA (Private Members Association)';
        $checkboxText = $template->checkbox_text ?? null;

        $html = $this->applyPlaceholders($html, $signerName, $signerInitials, $template);
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

        $pending = [
            'token' => $token,
            'tmp_path' => $tmpPath,
            'country_code' => $countryCode,
            'signer_name' => $signerName,
            'signer_initials' => $signerInitials,
            'agreement_title_snapshot' => $title,
            'agreement_description_snapshot' => $html,
            'checkbox_text_snapshot' => $checkboxText,
        ];

        Cache::put(self::CACHE_PREFIX . $userId, $pending, now()->addMinutes(self::CACHE_TTL_MINUTES));

        return $pending;
    }

    public function getPendingForUser(int $userId): ?array
    {
        $pending = Cache::get(self::CACHE_PREFIX . $userId);

        return is_array($pending) ? $pending : null;
    }

    public function forgetPendingForUser(int $userId): void
    {
        Cache::forget(self::CACHE_PREFIX . $userId);
    }

    /**
     * @return array{token: string, guest_token: string, tmp_path: string, country_code: string, signer_name: string, signer_initials: ?string, agreement_title_snapshot: string, agreement_description_snapshot: string, checkbox_text_snapshot: ?string}
     */
    public function buildAndCacheForGuest(string $guestToken, string $signerName, ?string $countryCode = null, ?string $signerInitials = null): array
    {
        $signerName = trim($signerName);
        $signerInitials = $signerInitials ?? $this->makeInitials($signerName);

        $countryCode = strtoupper($countryCode ?: Helper::getVisitorCountryCode() ?: 'US');
        $template = RegisterAgreement::where('country_code', $countryCode)->orderByDesc('id')->first();
        if (!$template) {
            $template = RegisterAgreement::where('country_code', 'US')->orderByDesc('id')->first();
        }

        $title = $template->agreement_title ?? 'Lion Roaring PMA (Private Members Association) Agreement';
        $html = $template->agreement_description ?? 'This is the agreement for Lion Roaring PMA (Private Members Association)';
        $checkboxText = $template->checkbox_text ?? null;

        $html = $this->applyPlaceholders($html, $signerName, $signerInitials, $template);
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

        $pending = [
            'token' => $token,
            'guest_token' => $guestToken,
            'tmp_path' => $tmpPath,
            'country_code' => $countryCode,
            'signer_name' => $signerName,
            'signer_initials' => $signerInitials,
            'agreement_title_snapshot' => $title,
            'agreement_description_snapshot' => $html,
            'checkbox_text_snapshot' => $checkboxText,
        ];

        Cache::put(self::CACHE_PREFIX_GUEST . $guestToken, $pending, now()->addMinutes(self::CACHE_TTL_MINUTES));

        return $pending;
    }

    public function getPendingForGuest(string $guestToken): ?array
    {
        $pending = Cache::get(self::CACHE_PREFIX_GUEST . $guestToken);

        return is_array($pending) ? $pending : null;
    }

    public function forgetPendingForGuest(string $guestToken): void
    {
        Cache::forget(self::CACHE_PREFIX_GUEST . $guestToken);
    }

    public function absolutePdfUrl(string $storageRelativePath): string
    {
        $path = Storage::url($storageRelativePath);
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
    }

    private function makeInitials(string $name): ?string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $parts = array_values(array_filter($parts, fn ($p) => $p !== ''));
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

    private function applyPlaceholders(string $html, string $signerName, ?string $initials, ?RegisterAgreement $template = null): string
    {
        $sealImageHtml = '';
        if ($template && $template->seal_image) {
            $sealPath = storage_path('app/public/' . $template->seal_image);
            if (file_exists($sealPath)) {
                $sealData = base64_encode(file_get_contents($sealPath));
                $sealMime = mime_content_type($sealPath);
                $sealImageHtml = '<img src="data:' . $sealMime . ';base64,' . $sealData . '" alt="Seal" style="max-height:100px;">';
            }
        }

        $currentDate = date('d/m/Y');
        $stewardMember1 = ($template && $template->steward_member_1) ? e($template->steward_member_1) : '';
        $stewardMember2 = ($template && $template->steward_member_2) ? e($template->steward_member_2) : '';

        $replacements = [
            '/\{\{\s*user_name\s*\}\}/i' => e($signerName),
            '/\{\{\s*i_user_name\s*\}\}/i' => 'I, ' . e($signerName),
            '/\{\{\s*user_initial\s*\}\}/i' => e($initials ?? ''),
            '/\{\{\s*seal_image\s*\}\}/i' => $sealImageHtml,
            '/\{\{\s*current_date\s*\}\}/i' => $currentDate,
            '/\{\{\s*steward_member_1\s*\}\}/i' => $stewardMember1,
            '/\{\{\s*steward_member_2\s*\}\}/i' => $stewardMember2,
            '/\[\[\s*user_name\s*\]\]/i' => e($signerName),
            '/\[\[\s*i_user_name\s*\]\]/i' => 'I, ' . e($signerName),
            '/\[\[\s*user_initial\s*\]\]/i' => e($initials ?? ''),
            '/\[\[\s*seal_image\s*\]\]/i' => $sealImageHtml,
            '/\[\[\s*current_date\s*\]\]/i' => $currentDate,
            '/\[\[\s*steward_member_1\s*\]\]/i' => $stewardMember1,
            '/\[\[\s*steward_member_2\s*\]\]/i' => $stewardMember2,
        ];

        foreach ($replacements as $pattern => $value) {
            $html = preg_replace($pattern, $value, $html) ?? $html;
        }

        return $html;
    }
}
