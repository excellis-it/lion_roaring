<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserMembershipExclusionTest extends TestCase
{
    public function test_is_membership_excluded_returns_false_by_default(): void
    {
        $user = new User();

        $this->assertFalse($user->isMembershipExcluded());
    }

    public function test_is_membership_excluded_returns_true_when_flag_set(): void
    {
        $user = new User(['membership_excluded' => true]);

        $this->assertTrue($user->isMembershipExcluded());
    }

    public function test_membership_panel_not_applicable_when_excluded(): void
    {
        $user = new class extends User {
            public function hasNewRole($roles): bool
            {
                return false;
            }
        };
        $user->membership_excluded = true;

        $this->assertFalse($user->membershipPanelApplicable());
    }

    public function test_membership_app_not_applicable_when_excluded(): void
    {
        config(['lion_roaring.in_app_membership' => true]);

        $user = new class extends User {
            public function hasNewRole($roles): bool
            {
                return false;
            }
        };
        $user->membership_excluded = true;

        $this->assertFalse($user->membershipAppApplicable());
    }

    public function test_super_admin_still_not_applicable_even_when_not_excluded(): void
    {
        $user = new class extends User {
            public function hasNewRole($roles): bool
            {
                return $roles === 'SUPER ADMIN';
            }
        };

        $this->assertFalse($user->membershipPanelApplicable());
        $this->assertFalse($user->membershipAppApplicable());
    }
}
