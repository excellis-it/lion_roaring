<?php

namespace Tests\Unit;

use App\Http\Controllers\Concerns\SendsUsernameRecoveryEmails;
use Tests\TestCase;

class SendsUsernameRecoveryEmailsTest extends TestCase
{
    public function test_mask_email_for_display_distinguishes_similar_local_parts(): void
    {
        $harness = new class {
            use SendsUsernameRecoveryEmails {
                maskEmailForDisplay as public;
            }
        };

        $yahooOne = $harness->maskEmailForDisplay('ssubowo1@yahoo.com');
        $yahooTwelve = $harness->maskEmailForDisplay('ssubowo12@yahoo.com');
        $proton = $harness->maskEmailForDisplay('ssubowo@proton.me');

        $this->assertNotSame($yahooOne, $yahooTwelve);
        $this->assertNotSame($yahooOne, $proton);
        $this->assertStringContainsString('yahoo.com', $yahooOne);
        $this->assertStringContainsString('proton.me', $proton);
    }
}
