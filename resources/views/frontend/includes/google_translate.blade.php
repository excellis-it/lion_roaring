@php
    // Kept at this path so the four layout @includes (frontend / user / ecom /
    // e-learning) keep working. The Google Translate *widget* is gone — this now
    // boots LrTranslate, which talks to our own key-protected endpoint.
    $translationSurface = 'website';
    $requestPath = request()->path();
    if (str_starts_with($requestPath, 'user')) {
        $translationSurface = 'user_pma';
    } elseif (str_starts_with($requestPath, 'e-store')) {
        $translationSurface = 'ecom';
    } elseif (str_starts_with($requestPath, 'e-learning')) {
        $translationSurface = 'elearning';
    }

    $translateVersion = (string) config('services.google.translate.cache_version', 'v1');
@endphp

<style>
    /* Brief dim while the first uncached batch resolves. Cached visits never see it. */
    html.lr-translating body {
        opacity: .97;
        transition: opacity .15s ease;
    }

    .lr-translate-progress {
        display: inline-block;
        vertical-align: middle;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 4px;
        background: #c62828;
        color: #fff;
        white-space: nowrap;
        line-height: 1.3;
    }

    .lr-translate-progress[hidden] {
        display: none !important;
    }

    .lr-translate-progress.is-active {
        animation: lr-translate-blink 1s linear infinite;
    }

    @keyframes lr-translate-blink {
        0%, 100% { opacity: 1; background: #c62828; box-shadow: 0 0 0 0 rgba(198, 40, 40, .55); }
        50% { opacity: .55; background: #ef6c00; box-shadow: 0 0 0 4px rgba(239, 108, 0, .25); }
    }

    .lr-translate-progress--fixed {
        position: fixed;
        top: 12px;
        right: 12px;
        z-index: 10050;
    }
</style>

<script>
    window.LR_TRANSLATE_CONFIG = {
        endpoint: @json(route('translate.batch')),
        csrf: @json(csrf_token()),
        cacheVersion: @json($translateVersion),
        surface: @json($translationSurface),
        debug: @json(config('app.debug') === true)
    };

    // Languages offered to this visitor (country-scoped). Consumed by the switcher.
    window.sessionLanguages = @json(\App\Helpers\Helper::getVisitorCountryLanguages());

    window.lrTranslationDiagnostics = {
        logUrl: @json(route('translation-client-log')),
        csrfToken: @json(csrf_token()),
        surface: @json($translationSurface),
    };
</script>

<script src="{{ asset('frontend_assets/js/translation-diagnostics.js') }}?v={{ $translateVersion }}"></script>
<script src="{{ asset('frontend_assets/js/protect-names-from-translate.js') }}?v={{ $translateVersion }}"></script>
<script src="{{ asset('frontend_assets/js/lr-translate.js') }}?v={{ $translateVersion }}"></script>
