<?php

namespace Tests\Feature\Api;

use App\Models\ChangeLog;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Support\CreatesApiUsers;
use Tests\TestCase;

class ChangeLogApiTest extends TestCase
{
    use CreatesApiUsers;
    use DatabaseTransactions;

    public function test_lists_only_published_for_platform(): void
    {
        $user = $this->createApiUser();
        ChangeLog::create([
            'created_by' => $user->id,
            'version' => 'v1',
            'title' => 'Published Web',
            'description' => '<p>Hi</p>',
            'type' => 'feature',
            'platform' => 'web',
            'published_at' => now()->subDay(),
        ]);
        ChangeLog::create([
            'created_by' => $user->id,
            'version' => 'v2',
            'title' => 'Draft',
            'description' => '<p>No</p>',
            'type' => 'bugfix',
            'platform' => 'web',
            'published_at' => null,
        ]);
        ChangeLog::create([
            'created_by' => $user->id,
            'version' => 'v3',
            'title' => 'Mobile Only',
            'description' => '<p>App</p>',
            'type' => 'improvement',
            'platform' => 'mobile',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->bearerTokenFor($user))
            ->getJson('/api/v3/user/change-logs?platform=web');

        $response->assertOk()->assertJsonPath('status', true)->assertJsonPath('platform', 'web');
        $titles = collect($response->json('data.data'))->pluck('title');
        $this->assertTrue($titles->contains('Published Web'));
        $this->assertFalse($titles->contains('Draft'));
        $this->assertFalse($titles->contains('Mobile Only'));
    }

    public function test_invalid_platform_defaults_to_web(): void
    {
        $user = $this->createApiUser();
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->bearerTokenFor($user))
            ->getJson('/api/v3/user/change-logs?platform=tv');
        $response->assertOk()->assertJsonPath('platform', 'web');
    }

    protected function tearDown(): void
    {
        User::query()->where('email', 'like', '%@api-contract.test')->forceDelete();
        parent::tearDown();
    }
}
