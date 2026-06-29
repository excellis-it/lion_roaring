<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Support\AssertsApiContract;
use Tests\Support\CreatesApiUsers;
use Tests\TestCase;

class ProtectedEndpointsTest extends TestCase
{
    use AssertsApiContract;
    use CreatesApiUsers;
    use DatabaseTransactions;

    public function test_user_update_fcm_token(): void
    {
        $user = $this->createApiUser();
        $token = $this->bearerTokenFor($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v3/user/update-fcm-token', [
                'fcm_token' => 'contract-test-fcm',
            ]);

        $this->assertApiEnvelope($response);
        $response->assertJsonPath('status', true);

        $user->refresh();
        $this->assertSame('contract-test-fcm', $user->fcm_token);
    }

    public function test_fcm_update_token_route(): void
    {
        $user = $this->createApiUser();
        $token = $this->bearerTokenFor($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v3/user/fcm/update-token', [
                'fcm_token' => 'contract-test-fcm-route',
            ]);

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('status', $json);
        $this->assertTrue($json['status']);

        $user->refresh();
        $this->assertSame('contract-test-fcm-route', $user->fcm_token);
    }

    protected function tearDown(): void
    {
        User::query()->where('email', 'like', '%@api-contract.test')->forceDelete();

        parent::tearDown();
    }
}
