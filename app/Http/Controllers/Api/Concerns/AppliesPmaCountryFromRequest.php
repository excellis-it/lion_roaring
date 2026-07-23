<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Country;
use Illuminate\Http\Request;

trait AppliesPmaCountryFromRequest
{
    protected function requiresPmaCountryFromRequest(): bool
    {
        return auth()->user()->hasNewRole('SUPER ADMIN');
    }

    protected function resolvePmaCountryId(Request $request): int
    {
        $user = auth()->user();

        if ($user->hasNewRole('SUPER ADMIN')) {
            return (int) $request->input('country_id');
        }

        $visitorCode = strtoupper(trim((string) \App\Helpers\Helper::resolveVisitorCountryCode($request)));
        $isOnGlobalServer = $visitorCode === 'GL';

        if (! $isOnGlobalServer) {
            $currentCountry = Country::findByCurrentRequest();
            $isOnGlobalServer = $currentCountry && $currentCountry->is_global;
        }

        if ($user->user_type === 'Global' || ($user->user_type === 'G_R' && $isOnGlobalServer)) {
            return (int) Country::where('code', 'GL')->value('id');
        }

        return (int) $user->country;
    }

    /**
     * @return array<string, string>
     */
    protected function pmaCountryValidationRules(): array
    {
        if ($this->requiresPmaCountryFromRequest()) {
            return ['country_id' => 'required|exists:countries,id'];
        }

        return [];
    }
}
