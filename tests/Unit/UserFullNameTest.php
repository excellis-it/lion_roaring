<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserFullNameTest extends TestCase
{
    public function test_full_name_joins_parts_with_single_spaces_when_middle_name_missing(): void
    {
        $user = new User([
            'first_name' => 'John',
            'middle_name' => null,
            'last_name' => 'Carter',
        ]);

        $this->assertSame('John Carter', $user->full_name);
    }

    public function test_full_name_includes_middle_name_when_present(): void
    {
        $user = new User([
            'first_name' => 'John',
            'middle_name' => 'Q',
            'last_name' => 'Carter',
        ]);

        $this->assertSame('John Q Carter', $user->full_name);
    }

    public function test_full_name_collapses_blank_middle_name_string(): void
    {
        $user = new User([
            'first_name' => 'John',
            'middle_name' => '   ',
            'last_name' => 'Carter',
        ]);

        $this->assertSame('John Carter', $user->full_name);
    }
}
