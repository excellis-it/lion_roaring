<?php

namespace Tests\Feature;

use App\Models\ChangeLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\Support\CreatesApiUsers;
use Tests\TestCase;

class ChangeLogManageTest extends TestCase
{
    use CreatesApiUsers;
    use DatabaseTransactions;

    private function manager(): User
    {
        Permission::findOrCreate('Manage Change Logs');

        $user = $this->createApiUser([
            'time_zone' => 'Asia/Kolkata',
            'membership_excluded' => true,
        ]);
        $user->givePermissionTo('Manage Change Logs');

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

    public function test_store_publishes_immediately_when_publish_date_blank(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-28 07:30:00', 'UTC'));

        $user = $this->manager();

        $response = $this->asManager($user)->post(route('change-logs.store'), [
            'platform' => 'web',
            'version' => 'v9.9.9',
            'title' => 'Immediate publish',
            'type' => 'bugfix',
            'description' => '<ul><li>Bullet one</li><li>Bullet two</li></ul>',
            'published_at' => '',
        ]);

        $response->assertRedirect(route('change-logs.index', ['platform' => 'web']));

        $log = ChangeLog::where('title', 'Immediate publish')->first();
        $this->assertNotNull($log);
        $this->assertNotNull($log->published_at);
        $this->assertTrue($log->published_at->lte(now()));
        $this->assertStringContainsString('<ul>', $log->description);
        $this->assertStringContainsString('<li>Bullet one</li>', $log->description);

        Carbon::setTestNow();
    }

    public function test_store_treats_user_local_past_datetime_as_published(): void
    {
        // 13:00 IST is 07:30 UTC — past relative to "now" below.
        Carbon::setTestNow(Carbon::parse('2026-07-28 08:00:00', 'UTC'));

        $user = $this->manager();

        $response = $this->asManager($user)->post(route('change-logs.store'), [
            'platform' => 'mobile',
            'version' => 'v1.0.99',
            'title' => 'Past local publish',
            'type' => 'improvement',
            'description' => '<p>Release notes</p>',
            'published_at' => '2026-07-28T13:00',
        ]);

        $response->assertRedirect(route('change-logs.index', ['platform' => 'mobile']));

        $log = ChangeLog::where('title', 'Past local publish')->first();
        $this->assertNotNull($log);
        $this->assertTrue(
            $log->published_at->lte(now()),
            'Past local datetime should be published (not marked unpublished)'
        );
        $this->assertSame(
            '2026-07-28 07:30:00',
            $log->published_at->timezone('UTC')->format('Y-m-d H:i:s')
        );

        Carbon::setTestNow();
    }

    public function test_store_rejects_future_publish_date(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-28 07:30:00', 'UTC'));

        $user = $this->manager();

        $response = $this->asManager($user)->from(route('change-logs.create'))->post(route('change-logs.store'), [
            'platform' => 'web',
            'version' => 'v9.9.8',
            'title' => 'Future schedule',
            'type' => 'feature',
            'description' => '<p>Should not schedule</p>',
            // 20:00 IST = 14:30 UTC — future vs test now
            'published_at' => '2026-07-28T20:00',
        ]);

        $response->assertRedirect(route('change-logs.create'));
        $response->assertSessionHasErrors('published_at');
        $this->assertNull(ChangeLog::where('title', 'Future schedule')->first());

        Carbon::setTestNow();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        User::query()->where('email', 'like', '%@api-contract.test')->forceDelete();
        parent::tearDown();
    }
}
