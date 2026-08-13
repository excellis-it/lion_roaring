<?php

namespace Tests\Unit;

use App\Helpers\Helper;
use Tests\TestCase;

class FormatBadgeCountTest extends TestCase
{
    public function test_counts_over_ninety_nine_display_as_ninety_nine_plus(): void
    {
        $this->assertSame('99+', Helper::formatBadgeCount(100));
        $this->assertSame('99+', Helper::formatBadgeCount(106));
        $this->assertSame('99+', Helper::formatBadgeCount(1000));
    }

    public function test_counts_up_to_ninety_nine_display_as_the_number(): void
    {
        $this->assertSame('1', Helper::formatBadgeCount(1));
        $this->assertSame('99', Helper::formatBadgeCount(99));
    }

    public function test_non_positive_counts_display_as_zero(): void
    {
        $this->assertSame('0', Helper::formatBadgeCount(0));
        $this->assertSame('0', Helper::formatBadgeCount(-3));
    }
}
