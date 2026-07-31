@php
    // never let branding lookups turn an error page into a second error
    try {
        $settings = \App\Helpers\Helper::getSettings();
    } catch (\Throwable $e) {
        $settings = null;
    }
    $logo = $settings->PMA_PANEL_LOGO ?? $settings->SITE_LOGO ?? null;
    $siteName = $settings->SITE_NAME ?? config('app.name');
    try {
        $isLoggedIn = auth()->check();
    } catch (\Throwable $e) {
        $isLoggedIn = false;
    }
    $backRoute =
        $isLoggedIn && Route::has('user.profile')
            ? route('user.profile')
            : (Route::has('home')
                ? route('home')
                : url('/'));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') | {{ $siteName }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Roboto:wght@300;400;500;700&display=swap">

    <style>
        :root {
            --brand: #643271;
            --brand-dark: #4c2556;
            --brand-soft: #f3ecf5;
            --ink: #212529;
            --muted: #6c757d;
            --line: #e7e2ea;
            --surface: #ffffff;
            --canvas: #fafafa;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background: var(--canvas);
            background-image:
                radial-gradient(circle at 12% 18%, rgba(100, 50, 113, .10), transparent 42%),
                radial-gradient(circle at 88% 82%, rgba(100, 50, 113, .07), transparent 45%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }

        .card {
            width: 100%;
            max-width: 560px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 48px 40px 40px;
            text-align: center;
            box-shadow: 0 1px 2px rgba(33, 37, 41, .04), 0 18px 44px -12px rgba(100, 50, 113, .18);
            animation: rise .45s cubic-bezier(.22, .8, .3, 1) both;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
        }

        .logo {
            max-height: 46px;
            max-width: 190px;
            width: auto;
            margin-bottom: 30px;
        }

        .badge {
            width: 84px;
            height: 84px;
            margin: 0 auto 24px;
            border-radius: 50%;
            background: var(--brand-soft);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge svg {
            width: 38px;
            height: 38px;
            stroke: var(--brand);
        }

        h1 {
            font-family: "Playfair Display", Georgia, serif;
            font-size: 30px;
            font-weight: 700;
            line-height: 1.25;
            margin: 0 0 14px;
        }

        p {
            font-size: 15.5px;
            line-height: 1.65;
            color: var(--muted);
            margin: 0 auto 8px;
            max-width: 42ch;
        }

        .hint {
            font-size: 13.5px;
            color: var(--muted);
            opacity: .85;
            margin-top: 18px;
        }

        .ref {
            display: inline-block;
            margin-top: 18px;
            padding: 6px 14px;
            border-radius: 8px;
            background: var(--canvas);
            border: 1px solid var(--line);
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12.5px;
            color: var(--muted);
            word-break: break-all;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin-top: 32px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 26px;
            border-radius: 999px;
            font-size: 14.5px;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background-color .18s ease, color .18s ease, border-color .18s ease, transform .18s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--brand);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--brand-dark);
        }

        .btn-ghost {
            background: transparent;
            color: var(--brand);
            border-color: var(--line);
            font-family: inherit;
        }

        .btn-ghost:hover {
            background: var(--brand-soft);
            border-color: var(--brand);
        }

        .btn svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
        }

        @media (max-width: 520px) {
            .card {
                padding: 36px 22px 30px;
                border-radius: 14px;
            }

            h1 {
                font-size: 24px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .card,
            .btn {
                animation: none;
                transition: none;
            }

            .btn:hover {
                transform: none;
            }
        }
    </style>
</head>

<body>
    <main class="card" role="alert">
        @if ($logo)
            <img class="logo" src="{{ asset($logo) }}" alt="{{ $siteName }}">
        @endif

        <div class="badge" aria-hidden="true">
            @yield('icon')
        </div>

        <h1>@yield('heading')</h1>
        <p>@yield('message')</p>

        @hasSection('hint')
            <p class="hint">@yield('hint')</p>
        @endif

        @yield('extra')

        <div class="actions">
            <a class="btn btn-primary" href="{{ $backRoute }}">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M3 11l9-8 9 8" />
                    <path d="M5 10v10h14V10" />
                </svg>
                {{ $isLoggedIn ? 'Back to dashboard' : 'Go to homepage' }}
            </a>
            <button class="btn btn-ghost" type="button" onclick="history.back()">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
                Previous page
            </button>
        </div>
    </main>
</body>

</html>
