<?php

namespace Tests\Feature\Api;

use App\Models\SupportReport;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Support\CreatesApiUsers;
use Tests\TestCase;

class SupportReportApiTest extends TestCase
{
    use CreatesApiUsers;
    use DatabaseTransactions;

    public function test_guest_cannot_list_support_reports(): void
    {
        $this->getJson('/api/v3/user/support-reports')->assertStatus(401);
    }

    public function test_member_lists_only_own_reports(): void
    {
        $user = $this->createApiUser();
        $other = $this->createApiUser();
        SupportReport::create([
            'user_id' => $user->id,
            'subject' => 'Mine',
            'message' => 'Body',
            'status' => 'open',
        ]);
        SupportReport::create([
            'user_id' => $other->id,
            'subject' => 'Theirs',
            'message' => 'Body',
            'status' => 'open',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->bearerTokenFor($user))
            ->getJson('/api/v3/user/support-reports');

        $response->assertOk()->assertJsonPath('status', true);
        $subjects = collect($response->json('data.data'))->pluck('subject');
        $this->assertTrue($subjects->contains('Mine'));
        $this->assertFalse($subjects->contains('Theirs'));
    }

    public function test_member_can_create_report_with_attachment(): void
    {
        Mail::fake();
        Storage::fake('public');
        $user = $this->createApiUser();
        $file = UploadedFile::fake()->create('note.pdf', 100, 'application/pdf');

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->bearerTokenFor($user))
            ->post('/api/v3/user/support-reports', [
                'subject' => 'Help',
                'message' => 'Details here',
                'attachment' => $file,
            ]);

        $response->assertCreated()->assertJsonPath('status', true);
        $this->assertDatabaseHas('support_reports', [
            'user_id' => $user->id,
            'subject' => 'Help',
            'status' => 'open',
        ]);
    }

    public function test_member_cannot_view_others_report(): void
    {
        $user = $this->createApiUser();
        $other = $this->createApiUser();
        $report = SupportReport::create([
            'user_id' => $other->id,
            'subject' => 'Secret',
            'message' => 'Body',
            'status' => 'open',
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $this->bearerTokenFor($user))
            ->getJson('/api/v3/user/support-reports/' . $report->id)
            ->assertStatus(403);
    }

    public function test_show_own_report_returns_serialized_fields(): void
    {
        $user = $this->createApiUser();
        $report = SupportReport::create([
            'user_id' => $user->id,
            'subject' => 'Own',
            'message' => 'Body text',
            'status' => 'open',
            'admin_notes' => 'We are looking into it',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->bearerTokenFor($user))
            ->getJson('/api/v3/user/support-reports/' . $report->id);

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.subject', 'Own')
            ->assertJsonPath('data.admin_notes', 'We are looking into it')
            ->assertJsonPath('data.message', 'Body text');
    }

    protected function tearDown(): void
    {
        User::query()->where('email', 'like', '%@api-contract.test')->forceDelete();
        parent::tearDown();
    }
}
