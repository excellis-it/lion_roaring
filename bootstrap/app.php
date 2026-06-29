<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\EnsureUserIsActive::class,
            \App\Http\Middleware\EnsureUserInstanceAccess::class,
            \App\Http\Middleware\UserActivityLogger::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'admin' => \App\Http\Middleware\Admin::class,
            'user' => \App\Http\Middleware\User::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'member.access' => \App\Http\Middleware\MemberAccess::class,
            'api.member.access' => \App\Http\Middleware\ApiMemberAccess::class,
            'member.sovereign' => \App\Http\Middleware\EnsureMemberSovereign::class,
            'preventBackHistory' => \App\Http\Middleware\PreventBackHistory::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'userActivity' => \App\Http\Middleware\UserActivityLogger::class,
            'event.access' => \App\Http\Middleware\CheckEventAccess::class,
            'agreement.signed' => \App\Http\Middleware\CheckAgreementSigned::class,
            'user.instance' => \App\Http\Middleware\EnsureUserInstanceAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function ($schedule) {
        $schedule->command('delete:job')->daily();
        $schedule->command('mails:update-deleted-status')->daily();
        $schedule->command('subscription:send-reminder')->daily();
        $schedule->call(function () {
            \App\Services\MarketRateService::refreshIfStale();
        })->hourly();
    })
    ->create();
