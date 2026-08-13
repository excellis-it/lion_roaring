<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\Support\CreatesApiUsers;
use Tests\TestCase;

class UserActivityExportTest extends TestCase
{
    use CreatesApiUsers;
    use DatabaseTransactions;

    private function manager(): User
    {
        Permission::findOrCreate('Manage User Activity');

        $user = $this->createApiUser([
            'user_type' => 'Global',
            'membership_excluded' => true,
        ]);
        $user->givePermissionTo('Manage User Activity');

        return $user->fresh();
    }

    private function asManager(User $user)
    {
        return $this->actingAs($user)->withoutMiddleware([
            \App\Http\Middleware\CheckAgreementSigned::class,
            \App\Http\Middleware\MemberAccess::class,
            \App\Http\Middleware\PreventBackHistory::class,
            \App\Http\Middleware\UserActivityLogger::class,
            \App\Http\Middleware\EnsureUserIsActive::class,
            \App\Http\Middleware\EnsureCanonicalCountryUrl::class,
            \App\Http\Middleware\EnsureUserInstanceAccess::class,
        ]);
    }

    private function seedActivity(array $overrides = []): UserActivity
    {
        return UserActivity::create(array_merge([
            'user_name' => 'Export Tester',
            'email' => 'export.tester@example.com',
            'user_roles' => 'Member',
            'ecclesia_name' => 'Test Ecclesia',
            'ip' => '127.0.0.1',
            'country_code' => 'US',
            'country_name' => 'United States',
            'device_type' => 'desktop',
            'browser' => 'Chrome',
            'url' => 'https://example.test/user/dashboard',
            'activity_type' => 'LOGIN',
            'activity_description' => 'Logged in',
            'activity_date' => now(),
        ], $overrides));
    }

    public function test_user_without_permission_cannot_start_export(): void
    {
        $user = $this->createApiUser(['membership_excluded' => true]);

        $this->asManager($user)
            ->post(route('user-activity-export-start'))
            ->assertForbidden();
    }

    public function test_list_page_includes_export_progress_modal(): void
    {
        $user = $this->manager();

        $this->asManager($user)
            ->get(route('user-activity-get-list'))
            ->assertOk()
            ->assertSee('Export')
            ->assertSee('export-activities', false)
            ->assertSee('exportProgressModal', false)
            ->assertSee('Cancel');
    }

    public function test_chunked_export_downloads_filtered_csv(): void
    {
        Storage::fake('local');

        $user = $this->manager();
        $this->seedActivity(['email' => 'keep.me@example.com', 'user_name' => 'Keep Me']);
        $this->seedActivity(['email' => 'skip.me@example.com', 'user_name' => 'Skip Me']);

        $start = $this->asManager($user)
            ->post(route('user-activity-export-start'), ['email' => 'keep.me@example.com'])
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->json();

        $this->assertNotEmpty($start['export_id']);

        $chunk = $this->asManager($user)
            ->post(route('user-activity-export-chunk'), ['export_id' => $start['export_id']])
            ->assertOk()
            ->assertJsonPath('done', true)
            ->assertJsonPath('processed', 1)
            ->json();

        $this->assertSame(100, $chunk['percent']);

        $download = $this->asManager($user)
            ->get(route('user-activity-export-download', $start['export_id']));

        $download->assertOk();
        $this->assertStringContainsString('.csv', (string) $download->headers->get('content-disposition'));

        $csv = method_exists($download->baseResponse, 'getFile')
            ? file_get_contents($download->baseResponse->getFile()->getPathname())
            : $download->getContent();

        $this->assertStringContainsString('keep.me@example.com', $csv);
        $this->assertStringNotContainsString('skip.me@example.com', $csv);
    }

    public function test_export_can_be_cancelled(): void
    {
        Storage::fake('local');

        $user = $this->manager();
        $this->seedActivity(['email' => 'cancel.me@example.com']);

        $start = $this->asManager($user)
            ->post(route('user-activity-export-start'), ['email' => 'cancel.me@example.com'])
            ->assertOk()
            ->json();

        $this->asManager($user)
            ->post(route('user-activity-export-cancel'), ['export_id' => $start['export_id']])
            ->assertOk()
            ->assertJsonPath('status', 'cancelled');

        $this->asManager($user)
            ->post(route('user-activity-export-chunk'), ['export_id' => $start['export_id']])
            ->assertStatus(409);

        $this->asManager($user)
            ->get(route('user-activity-export-download', $start['export_id']))
            ->assertNotFound();
    }
}
