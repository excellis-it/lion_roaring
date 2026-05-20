<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Country;
use Illuminate\Http\Request;

trait AppliesPmaCountryFromRequest
{
    protected function requiresPmaCountryFromRequest(): bool
    {
        $user = auth()->user();

        return $user->hasNewRole('SUPER ADMIN') || $user->user_type === 'Global';
    }

    protected function resolvePmaCountryId(Request $request): int
    {
        $user = auth()->user();

        if ($user->hasNewRole('SUPER ADMIN') || $user->user_type === 'Global') {
            return (int) $request->input('country_id');
        }

        $currentCountry = Country::findByCurrentRequest();
        $isOnGlobalServer = $currentCountry && $currentCountry->is_global;

        if ($user->user_type === 'G_R' && $isOnGlobalServer) {
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
