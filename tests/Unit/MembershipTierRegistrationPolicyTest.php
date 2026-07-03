<?php

namespace Tests\Unit;

use App\Models\MembershipTier;
use App\Services\MembershipTierRegistrationPolicy;
use Illuminate\Http\Request;
use Tests\TestCase;

class MembershipTierRegistrationPolicyTest extends TestCase
{
    public function test_cheapest_tier_is_locked_only_in_global_context_when_enforced(): void
    {
        $policy = new MembershipTierRegistrationPolicy();

        $gold = new MembershipTier(['name' => 'Gold', 'cost' => '30']);
        $platinum = new MembershipTier(['name' => 'Platinum', 'cost' => '60']);

        $lowest = $policy->lowestTierCost(collect([$gold, $platinum]));
        $this->assertSame(30.0, $lowest);

        $request = Request::create('/api/v3/register', 'POST');

        $this->assertFalse($policy->shouldEnforceTierLock($request));
        $this->assertFalse($policy->isTierLockedForRegistration($gold, true, $lowest, $request));

        $newAppRequest = Request::create('/api/v3/register', 'POST', server: [
            'HTTP_X_APP_BUILD' => '39',
        ]);

        $this->assertTrue($policy->shouldEnforceTierLock($newAppRequest));
        $this->assertTrue($policy->isTierLockedForRegistration($gold, true, $lowest, $newAppRequest));
        $this->assertFalse($policy->isTierLockedForRegistration($platinum, true, $lowest, $newAppRequest));
    }

    public function test_web_requests_always_enforce_tier_lock(): void
    {
        $policy = new MembershipTierRegistrationPolicy();

        $request = Request::create('/register', 'POST');

        $this->assertTrue($policy->shouldEnforceTierLock($request));
    }

    public function test_no_lock_when_a_free_tier_exists(): void
    {
        $policy = new MembershipTierRegistrationPolicy();

        $standard = new MembershipTier(['name' => 'Standard', 'cost' => '0']);
        $gold = new MembershipTier(['name' => 'Gold', 'cost' => '30']);
        $lowest = $policy->lowestTierCost(collect([$standard, $gold]));

        $request = Request::create('/api/v3/register', 'POST', server: [
            'HTTP_X_LION_CLIENT_FEATURES' => 'membership_tier_lock',
        ]);

        $this->assertSame(0.0, $lowest);
        $this->assertFalse($policy->isTierLockedForRegistration($gold, true, $lowest, $request));
    }
}
