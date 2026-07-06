<?php

namespace App\Services;

use App\Models\MembershipTier;
use InvalidArgumentException;

class MembershipPricing
{
    public const PERIOD_MONTHLY = 'monthly';
    public const PERIOD_YEARLY = 'yearly';

    public static function validatePeriod(?string $period): string
    {
        $period = $period ?: self::PERIOD_YEARLY;

        if (!in_array($period, [self::PERIOD_MONTHLY, self::PERIOD_YEARLY], true)) {
            throw new InvalidArgumentException('Invalid billing period.');
        }

        return $period;
    }

    public static function priceFor(MembershipTier $tier, ?string $period): float
    {
        $period = self::validatePeriod($period);

        if (($tier->pricing_type ?? 'amount') === 'token') {
            return 0.0;
        }

        if ($period === self::PERIOD_MONTHLY) {
            return (float) ($tier->monthly_cost ?? 0);
        }

        return (float) ($tier->yearly_cost ?? 0);
    }

    public static function durationMonthsFor(?string $period): int
    {
        $period = self::validatePeriod($period);

        return $period === self::PERIOD_MONTHLY ? 1 : 12;
    }

    public static function periodLabel(string $period): string
    {
        return $period === self::PERIOD_MONTHLY ? 'Monthly' : 'Yearly';
    }
}
