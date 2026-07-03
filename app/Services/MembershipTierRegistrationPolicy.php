<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MembershipTierRegistrationPolicy
{
    public const LOCKED_MESSAGE = 'This plan is not available on this domain.';

    /**
     * Web registration always enforces tier lock.
     * API clients opt in via X-App-Build or X-Lion-Client-Features (backward compatible).
     */
    public function shouldEnforceTierLock(?Request $request = null): bool
    {
        $request = $request ?? request();

        if (! $request->is('api/*')) {
            return true;
        }

        $minBuild = (int) config('lion_roaring.membership_tier_lock_min_app_build', 39);
        $clientBuild = (int) $request->header('X-App-Build', 0);

        if ($clientBuild > 0 && $clientBuild >= $minBuild) {
            return true;
        }

        $feature = (string) config('lion_roaring.membership_tier_lock_feature', 'membership_tier_lock');
        $features = strtolower((string) $request->header('X-Lion-Client-Features', ''));

        return $feature !== '' && str_contains($features, strtolower($feature));
    }

    public function isGlobalRegistrationContext(?Request $request = null): bool
    {
        $domainCountry = Helper::getCountryByDomain();

        return $domainCountry && $domainCountry->is_global;
    }

    /**
     * @param  Collection<int, MembershipTier>|null  $tiers
     */
    public function lowestTierCost(?Collection $tiers = null): float
    {
        $tiers = $tiers ?? MembershipTier::all();

        if ($tiers->isEmpty()) {
            return 0.0;
        }

        return (float) $tiers->min(fn (MembershipTier $tier) => (float) ($tier->cost ?? 0));
    }

    public function isTierLockedForRegistration(
        MembershipTier $tier,
        ?bool $globalContext = null,
        ?float $lowestCost = null,
        ?Request $request = null
    ): bool {
        if (! $this->shouldEnforceTierLock($request)) {
            return false;
        }

        $globalContext = $globalContext ?? $this->isGlobalRegistrationContext($request);

        if (! $globalContext) {
            return false;
        }

        $lowestCost = $lowestCost ?? $this->lowestTierCost();

        if ($lowestCost <= 0) {
            return false;
        }

        return (float) ($tier->cost ?? 0) <= $lowestCost;
    }

    public function validateTierSelectable(int $tierId, ?Request $request = null): ?string
    {
        if (! $this->shouldEnforceTierLock($request)) {
            return null;
        }

        $tier = MembershipTier::find($tierId);

        if (! $tier) {
            return null;
        }

        if ($this->isTierLockedForRegistration($tier, request: $request)) {
            return self::LOCKED_MESSAGE;
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeTierForRegistration(
        MembershipTier $tier,
        bool $globalContext,
        float $lowestCost,
        ?Request $request = null
    ): array {
        $enforce = $this->shouldEnforceTierLock($request);

        return [
            'id' => $tier->id,
            'name' => $tier->name,
            'description' => $tier->description,
            'cost' => $tier->cost,
            'duration_months' => $tier->duration_months,
            'pricing_type' => $tier->pricing_type ?? 'amount',
            'life_force_energy_tokens' => $tier->life_force_energy_tokens,
            'agree_description' => $tier->agree_description,
            'benefits' => $tier->relationLoaded('benefits')
                ? $tier->benefits->map(fn ($benefit) => $benefit->benefit)->values()
                : [],
            'is_locked' => $enforce && $this->isTierLockedForRegistration(
                $tier,
                $globalContext,
                $lowestCost,
                $request
            ),
        ];
    }
}
