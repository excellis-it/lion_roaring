<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\VerifyOTP;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Support\AssertsApiContract;
use Tests\Support\CreatesApiUsers;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use AssertsApiContract;
    use CreatesApiUsers;
    use DatabaseTransactions;

    public function test_login_sends_otp_for_active_user(): void
    {
        $user = $this->createApiUser();

        $response = $this->postJson('/api/v3/login', [
            'user_name' => $user->email,
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'message',
                'user' => ['id', 'email', 'user_name'],
                'otp',
            ]);

        $this->assertDatabaseHas('verify_o_t_p_s', [
            'user_id' => $user->id,
            'otp' => $response->json('otp'),
        ]);
    }

    public function test_verify_otp_returns_passport_token(): void
    {
        $user = $this->createApiUser();

        $login = $this->postJson('/api/v3/login', [
            'user_name' => $user->email,
            'password' => 'Password@123',
        ]);

        $otp = $login->json('otp');

        $response = $this->postJson('/api/v3/verify-otp', [
            'id' => $user->id,
            'otp' => $otp,
            'fcm_token' => 'test-fcm-token',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure(['status', 'message', 'token']);

        $this->assertIsString($response->json('token'));
        $this->assertNotEmpty($response->json('token'));

        $user->refresh();
        $this->assertSame('test-fcm-token', $user->fcm_token);
    }

    public function test_protected_profile_with_bearer_token(): void
    {
        $user = $this->createApiUser();
        $token = $this->bearerTokenFor($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v3/user/profile');

        $this->assertApiEnvelope($response);
        $response->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'message',
                'in_app_membership',
                'data' => ['id', 'email', 'user_name', 'created_at', 'updated_at'],
            ]);

        $this->assertIso8601Dates($response->json('data'), ['created_at', 'updated_at']);
    }

    public function test_protected_route_rejects_missing_token(): void
    {
        $response = $this->postJson('/api/v3/user/profile');

        $response->assertStatus(401);
    }

    protected function tearDown(): void
    {
        VerifyOTP::query()->where('email', 'like', '%@api-contract.test')->delete();
        User::query()->where('email', 'like', '%@api-contract.test')->forceDelete();

        parent::tearDown();
    }
}
