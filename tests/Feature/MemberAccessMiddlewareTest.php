<?php

namespace Tests\Feature;

use App\Http\Middleware\MemberAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class MemberAccessMiddlewareTest extends TestCase
{
    public function test_excluded_user_passes_through_without_redirect(): void
    {
        $user = new class extends User {
            public function hasNewRole($roles): bool
            {
                return false;
            }
        };
        $user->membership_excluded = true;

        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($user);

        $middleware = new MemberAccess();
        $request = Request::create('/user/dashboard', 'GET');

        $response = $middleware->handle($request, fn () => response('ok', 200));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('ok', $response->getContent());
    }
}
