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
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('user.documentation.attachment'));
    }

    public function test_documentation_index_uses_standard_pma_page_shell(): void
    {
        $index = file_get_contents(resource_path('views/user/documentation/index.blade.php'));
        $show = file_get_contents(resource_path('views/user/documentation/show.blade.php'));
        $css = file_get_contents(public_path('user_assets/css/documentation.css'));

        foreach ([$index, $show] as $blade) {
            $this->assertStringContainsString('container-fluid', $blade);
            $this->assertStringContainsString('bg_white_border', $blade);
            $this->assertStringNotContainsString('pma-docs-shell', $blade);
        }

        $this->assertStringContainsString('<h3>Project Documentation</h3>', $index);
        $this->assertStringContainsString('pma-docs-lead', $index);
        $this->assertStringContainsString('pma-docs-hub-card', $index);
        $this->assertStringContainsString('class="card pma-docs-hub-card', $index);
        $this->assertStringContainsString('class="card pma-docs-panel"', $show);
        $this->assertStringContainsString('<div class="pma-docs-lead">', $index);
        $this->assertStringContainsString('all: unset', $css);
        $this->assertStringContainsString('line-height: 1.75', $css);
        $this->assertStringContainsString('padding: 25px 40px', $css);
        $this->assertStringNotContainsString('max-width: 42rem', $css);
        $this->assertStringNotContainsString('padding: 0.5rem 0 2.5rem !important', $css);
    }
}
