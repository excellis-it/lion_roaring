<?php

namespace Tests\Feature;

use App\Http\Middleware\SuperAdminMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class DocumentationAccessTest extends TestCase
{
    public function test_super_admin_middleware_blocks_non_super_admin(): void
    {
        $user = new class extends User
        {
            public function hasNewRole($roles): bool
            {
                return false;
            }
        };

        $this->actingAs($user);

        $middleware = new SuperAdminMiddleware();
        $request = Request::create('/user/documentation', 'GET');

        try {
            $middleware->handle($request, fn () => response('ok', 200));
            $this->fail('Expected 403 abort');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_super_admin_middleware_allows_super_admin(): void
    {
        $user = new class extends User
        {
            public function hasNewRole($roles): bool
            {
                return $roles === 'SUPER ADMIN' || (is_array($roles) && in_array('SUPER ADMIN', $roles, true));
            }
        };

        $this->actingAs($user);

        $middleware = new SuperAdminMiddleware();
        $request = Request::create('/user/documentation', 'GET');
        $response = $middleware->handle($request, fn () => response('ok', 200));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('ok', $response->getContent());
    }

    public function test_documentation_routes_are_registered(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('user.documentation.index'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('user.documentation.show'));
    }
}
