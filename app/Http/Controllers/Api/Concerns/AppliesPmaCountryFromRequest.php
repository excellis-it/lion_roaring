<?php

namespace App\Http\Controllers\Api\Concerns;

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
        if ($this->requiresPmaCountryFromRequest()) {
            return (int) $request->input('country_id');
        }

        return (int) auth()->user()->country;
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
