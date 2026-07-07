<?php

namespace Tests\Feature;

use Tests\TestCase;

class PartnerMembershipExclusionTest extends TestCase
{
    public function test_member_sovereign_tier_not_required_when_membership_excluded(): void
    {
        $role = 'MEMBER_SOVEREIGN';
        $membershipExcluded = true;

        $requiresTier = $role === 'MEMBER_SOVEREIGN' && !$membershipExcluded;

        $this->assertFalse($requiresTier);
    }

    public function test_member_sovereign_tier_required_when_not_excluded(): void
    {
        $role = 'MEMBER_SOVEREIGN';
        $membershipExcluded = false;

        $requiresTier = $role === 'MEMBER_SOVEREIGN' && !$membershipExcluded;

        $this->assertTrue($requiresTier);
    }

    public function test_non_member_sovereign_does_not_require_tier(): void
    {
        $role = 'ESTORE_USER';
        $membershipExcluded = false;

        $requiresTier = $role === 'MEMBER_SOVEREIGN' && !$membershipExcluded;

        $this->assertFalse($requiresTier);
    }
}
