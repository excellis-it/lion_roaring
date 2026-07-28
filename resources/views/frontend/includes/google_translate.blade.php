@php
    $translationSurface = 'website';
    $requestPath = request()->path();
    if (str_starts_with($requestPath, 'user')) {
        $translationSurface = 'user_pma';
    } elseif (str_starts_with($requestPath, 'e-store')) {
        $translationSurface = 'ecom';
    } elseif (str_starts_with($requestPath, 'e-learning')) {
        $translationSurface = 'elearning';
    }
@endphp

<div id="google_translate_element_mount" class="google-translate-mount" aria-hidden="true"></div>

<script src="{{ asset('frontend_assets/js/protect-names-from-translate.js') }}"></script>

<script>
    // Use Helper::getVisitorCountryLanguages() which returns:
    // - All active languages when no country is selected (MAIN_URL, first visit)
    // - Country-specific languages when a country is selected
    // - US languages when on LION_ROARING_USA
    window.sessionLanguages = @json(\App\Helpers\Helper::getVisitorCountryLanguages());
    // App URL path prefix (empty on production hosts; e.g. /lion-roaring-org on path-based demos)
    window.appBasePath = @json(rtrim((string) (parse_url(url('/'), PHP_URL_PATH) ?: ''), '/') ?: '');
    window.lrTranslationDiagnostics = {
        logUrl: @json(route('translation-client-log')),
        csrfToken: @json(csrf_token()),
        surface: @json($translationSurface),
    };
</script>
<script src="{{ asset('frontend_assets/js/translation-diagnostics.js') }}"></script>

<script>
    /**
     * parseLanguages(data)
     */
    function parseLanguages(data) {
        const codes = new Set();

        if (!data) {
            codes.add('en');
            return codes;
        }

        if (Array.isArray(data) && data.length > 0 && typeof data[0] === 'object' && data[0] !== null && 'code' in data[0]) {
            data.forEach(lang => {
                if (lang && lang.code) codes.add(String(lang.code));
            });
            codes.add('en');
            return codes;
        }

        try {
            const arr = Array.isArray(data) ? data : [data];
            arr.forEach(item => {
                const innerArray = Array.isArray(item) ? item : [item];
                innerArray.forEach(inner => {
                    if (!inner || typeof inner !== 'object') return;
                    Object.values(inner).forEach(val => {
                        const list = Array.isArray(val) ? val : [val];
                        list.forEach(lang => {
                            if (lang && typeof lang === 'object' && lang.code) {
                                codes.add(String(lang.code));
                            }
                        });
                    });
                });
            });
        } catch (e) {
            console.error('parseLanguages fallback error', e);
        }

        codes.add('en');
        return codes;
    }

    /**
     * buildIncludedLanguagesString(sessionData)
     */
    function buildIncludedLanguagesString(sessionData) {
        const codes = parseLanguages(sessionData);
        return Array.from(codes).join(',');
    }

    /**
     * WRITE cookies only at site root (and app base path). Writing per-page path
     * segments caused Hindi on /user/a and Spanish on /user/b (multiple googtrans).
     */
    function getCanonicalWritePaths() {
        const paths = new Set(['/']);
        const base = String(window.appBasePath || '').replace(/\/$/, '');
        if (base) {
            paths.add(base);
            paths.add(base + '/');
        }
        return Array.from(paths);
    }

    /**
     * CLEAR cookies on current path ancestors + common app prefixes + any paths
     * we previously visited (sessionStorage), so path-scoped leftovers die.
     */
    function getClearCookiePaths() {
        const paths = new Set(getCanonicalWritePaths());
        const pathname = window.location.pathname || '/';
        const parts = pathname.split('/').filter(Boolean);
        let acc = '';
        for (let i = 0; i < parts.length; i++) {
            acc += '/' + parts[i];
            paths.add(acc);
            paths.add(acc + '/');
        }
        // Common surface prefixes (GT often re-writes googtrans here)
        [
            '/user', '/e-learning', '/e-store', '/admin', '/chatbot',
            '/frontend', '/login', '/register'
        ].forEach(function (p) {
            paths.add(p);
            paths.add(p + '/');
        });
        try {
            var stored = JSON.parse(sessionStorage.getItem('lr_gt_clear_paths') || '[]');
            if (Array.isArray(stored)) {
                stored.forEach(function (p) {
                    if (typeof p === 'string' && p) {
                        paths.add(p);
                    }
                });
            }
            var merged = Array.from(paths);
            if (merged.length > 80) {
                merged = merged.slice(-80);
            }
            sessionStorage.setItem('lr_gt_clear_paths', JSON.stringify(merged));
        } catch (e) {}
        return Array.from(paths);
    }

    // Back-compat alias used by older call sites
    function getTranslateCookiePaths() {
        return getClearCookiePaths();
    }

    function cookieDomainVariants() {
        const domain = window.location.hostname;
        const domains = [null, domain];
        if (domain.indexOf('.') !== -1) {
            domains.push('.' + domain);
        }
        return domains;
    }

    function cookieAttrVariants() {
        // HTTPS googtrans from Google Translate is Secure; expire/set without Secure leaves it stuck.
        if (window.location.protocol === 'https:') {
            return [
                '',
                '; Secure',
                '; SameSite=Lax',
                '; SameSite=Lax; Secure',
                '; SameSite=None; Secure',
                '; SameSite=Strict',
                '; SameSite=Strict; Secure'
            ];
        }
        return ['', '; SameSite=Lax', '; SameSite=Strict'];
    }

    function expireNamedCookie(name, path, domain) {
        cookieAttrVariants().forEach(function (extra) {
            let cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; Max-Age=0; path=' + path + extra;
            if (domain) {
                cookie += '; domain=' + domain;
            }
            document.cookie = cookie;
        });
    }

    function writeNamedCookie(name, value, path, domain) {
        let cookie = name + '=' + value + '; path=' + path + '; Max-Age=31536000';
        if (domain) {
            cookie += '; domain=' + domain;
        }
        if (window.location.protocol === 'https:') {
            cookie += '; Secure; SameSite=Lax';
        }
        document.cookie = cookie;
    }

    function writeCanonicalCookie(name, value) {
        const paths = getCanonicalWritePaths();
        const domains = cookieDomainVariants();
        paths.forEach(function (path) {
            domains.forEach(function (domain) {
                writeNamedCookie(name, value, path, domain);
            });
        });
    }

    /**
     * Write the same value on every clear-path we know about so a deep leftover
     * (e.g. content_lang=en on /user/bulletin-board) cannot beat path=/.
     */
    function writeCookieEverywhere(name, value) {
        const paths = getClearCookiePaths();
        const domains = cookieDomainVariants();
        paths.forEach(function (path) {
            domains.forEach(function (domain) {
                writeNamedCookie(name, value, path, domain);
            });
        });
    }

    function expireNamedCookieEverywhere(name) {
        const paths = getClearCookiePaths();
        const domains = cookieDomainVariants();
        paths.forEach(function (path) {
            domains.forEach(function (domain) {
                expireNamedCookie(name, path, domain);
            });
        });
    }

    /**
     * clearGoogleTranslateCookies()
     * Clears googtrans across domain + path + Secure/SameSite variants.
     */
    function clearGoogleTranslateCookies() {
        expireNamedCookieEverywhere('googtrans');
    }
    window.clearGoogleTranslateCookies = clearGoogleTranslateCookies;

    /**
     * Overwrite leftovers with /en/en (source=target) so GT stops translating.
     */
    function neutralizeGoogtransCookie() {
        writeCookieEverywhere('googtrans', '/en/en');
    }

    function setDocumentTranslateEnabled(enabled) {
        var html = document.documentElement;
        if (!html) {
            return;
        }
        if (enabled) {
            html.removeAttribute('translate');
            html.classList.remove('notranslate');
        } else {
            // Original: block Google Translate from "fixing" non-English text into English
            html.setAttribute('translate', 'no');
            html.classList.add('notranslate');
        }
    }

    function readContentLangFromCookie() {
        // If duplicates exist, prefer the last value (most recently written).
        var values = [];
        document.cookie.split(';').forEach(function (part) {
            var p = part.trim();
            if (p.indexOf('content_lang=') === 0) {
                try {
                    values.push(decodeURIComponent(p.substring('content_lang='.length)));
                } catch (e) {
                    values.push(p.substring('content_lang='.length));
                }
            }
        });
        if (!values.length) {
            return null;
        }
        return values[values.length - 1];
    }

    var LR_LANG_KEY = 'lr_content_lang';
    var LR_LANG_PENDING = 'lr_lang_pending';

    function persistLanguageIntent(lang) {
        var value = (!lang || lang === '__original__') ? '__original__' : String(lang);
        try {
            localStorage.setItem(LR_LANG_KEY, value);
            sessionStorage.setItem(LR_LANG_KEY, value);
            sessionStorage.setItem(LR_LANG_PENDING, value);
        } catch (e) {}
        return value;
    }

    /**
     * Site-wide language intent. Pending (set on switch) wins over storage/cookies
     * so a stale content_lang cookie cannot undo a fresh Hindi/Spanish selection.
     */
    function readLanguageIntent() {
        try {
            var pending = sessionStorage.getItem(LR_LANG_PENDING);
            if (pending) {
                sessionStorage.removeItem(LR_LANG_PENDING);
                try {
                    localStorage.setItem(LR_LANG_KEY, pending);
                    sessionStorage.setItem(LR_LANG_KEY, pending);
                } catch (e2) {}
                return pending;
            }
        } catch (e) {}
        try {
            var stored = localStorage.getItem(LR_LANG_KEY) || sessionStorage.getItem(LR_LANG_KEY);
            if (stored) {
                return stored;
            }
        } catch (e) {}
        var fromCookie = readContentLangFromCookie();
        if (fromCookie) {
            try {
                localStorage.setItem(LR_LANG_KEY, fromCookie);
                sessionStorage.setItem(LR_LANG_KEY, fromCookie);
            } catch (e) {}
            return fromCookie;
        }
        return '__original__';
    }

    /**
     * Wipe leftovers and write content_lang + googtrans from intent everywhere we can,
     * so deep path leftovers (content_lang=en) cannot make "Original" serve English.
     */
    function applyLanguageIntent(intent) {
        var value = (!intent || intent === '__original__') ? '__original__' : String(intent);
        expireNamedCookieEverywhere('content_lang');
        clearGoogleTranslateCookies();
        // Overwrite on all known paths (same value) — expire alone is not enough
        writeCookieEverywhere('content_lang', value);
        if (value === '__original__') {
            neutralizeGoogtransCookie();
            setDocumentTranslateEnabled(false);
        } else if (value === 'en') {
            neutralizeGoogtransCookie();
            setDocumentTranslateEnabled(true);
        } else {
            writeCookieEverywhere('googtrans', '/auto/' + value);
            setDocumentTranslateEnabled(true);
        }
        return value;
    }

    /**
     * content_lang cookie helper (also updates localStorage intent).
     */
    function setContentLangCookie(lang) {
        var value = persistLanguageIntent(lang);
        expireNamedCookieEverywhere('content_lang');
        writeCookieEverywhere('content_lang', value);
    }
    window.setContentLangCookie = setContentLangCookie;

    function setGoogtransCookie(lang) {
        clearGoogleTranslateCookies();
        writeCookieEverywhere('googtrans', '/auto/' + lang);
    }

    /**
     * On every page load: apply stored language intent (not a possibly-stale cookie).
     */
    function reconcileLanguageCookies() {
        applyLanguageIntent(readLanguageIntent());
    }
    // Run immediately (before Google Translate script applies a stale path cookie)
    reconcileLanguageCookies();

    /**
     * Full document navigation with cache-buster so the new language always loads.
     * Same-URL replace() can restore bfcache / skip applying new cookies.
     */
    function hardNavigateForLanguageChange() {
        var url = window.location.pathname + window.location.search;
        try {
            var parsed = new URL(window.location.href);
            parsed.searchParams.delete('_lr');
            url = parsed.pathname + (parsed.search ? parsed.search : '');
        } catch (e) {}
        var sep = url.indexOf('?') >= 0 ? '&' : '?';
        window.location.replace(url + sep + '_lr=' + Date.now());
    }
    window.hardNavigateForLanguageChange = hardNavigateForLanguageChange;

    // Strip cache-buster from the address bar after load
    (function stripLangNavParam() {
        try {
            var parsed = new URL(window.location.href);
            if (parsed.searchParams.has('_lr')) {
                parsed.searchParams.delete('_lr');
                var clean = parsed.pathname + (parsed.search ? parsed.search : '') + parsed.hash;
                window.history.replaceState(null, '', clean);
            }
        } catch (e) {}
    })();

    function resetGoogleTranslateUi(nextContentLang) {
        if (window.LrTranslationDiagnostics) {
            window.LrTranslationDiagnostics.clearPendingVerification();
        }
        var intent = persistLanguageIntent(nextContentLang || '__original__');
        applyLanguageIntent(intent);
        hardNavigateForLanguageChange();
    }

    /**
     * changeGoogleTranslateLanguage(lang)
     * Persist intent first, sync cookies, then hard-navigate so the next paint matches.
     */
    window.changeGoogleTranslateLanguage = function(lang) {
        const langMap = { 'cn': 'zh-CN', 'us': 'en', 'uk': 'en' };
        if (langMap[lang]) lang = langMap[lang];

        var intent = persistLanguageIntent(lang === '__original__' ? '__original__' : lang);

        if (intent !== '__original__' && intent !== 'en' && window.LrTranslationDiagnostics) {
            window.LrTranslationDiagnostics.markPendingVerification(intent);
        }

        applyLanguageIntent(intent);
        hardNavigateForLanguageChange();
    };

    /**
     * waitForTranslateSelect(callback)
     */
    function waitForTranslateSelect(callback, timeout = 4000) {
        const existing = document.querySelector('.goog-te-combo');
        if (existing) {
            callback(existing);
            return;
        }

        const observer = new MutationObserver((mutations, obs) => {
            const el = document.querySelector('.goog-te-combo');
            if (el) {
                obs.disconnect();
                callback(el);
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });

        setTimeout(() => {
            try { observer.disconnect(); } catch (e) {}
            const el = document.querySelector('.goog-te-combo');
            callback(el);
        }, timeout);
    }

    /**
     * forceSelectValue(selectEl, value)
     * Delegates to the same site-wide language change path (storage + cookies + navigate).
     */
    function forceSelectValue(selectEl, value) {
        if (!value) return;
        if (window.changeGoogleTranslateLanguage) {
            window.changeGoogleTranslateLanguage(value);
        }
    }
    window.forceSelectValue = forceSelectValue;

    /**
     * googleTranslateElementInit
     */
    function googleTranslateElementInit() {
        const includedLanguages = buildIncludedLanguagesString(window.sessionLanguages || []);
        new google.translate.TranslateElement({
            // pageLanguage: 'en',
            includedLanguages: includedLanguages,
        }, 'google_translate_element_mount');

        // Watch for the dropdown and intercept English selection
        waitForTranslateSelect(function(selectEl) {
            if (!selectEl) {
                const expectedLang = readGoogtransLangFromCookie();
                if (expectedLang && window.LrTranslationDiagnostics) {
                    window.LrTranslationDiagnostics.reportFailure(
                        'google_translate_widget_timeout',
                        expectedLang,
                        { phase: 'googleTranslateElementInit' }
                    );
                }
                return;
            }
            selectEl.addEventListener('change', function(e) {
                var selectedValue = selectEl.value;
                if (selectedValue === 'en' || selectedValue === '' || selectedValue === 'en|en') {
                    e.stopPropagation();
                    resetGoogleTranslateUi('en');
                }
            }, true);
            initLanguageSwitcher(selectEl);
        }, 5000);
    }

    function getActiveTranslateLang() {
        // Prefer persisted intent (localStorage) — cookies can still hold stale duplicates
        try {
            var stored = localStorage.getItem(LR_LANG_KEY) || sessionStorage.getItem(LR_LANG_KEY);
            if (stored) {
                return stored;
            }
        } catch (e) {}

        const contentMatch = document.cookie.match(/(?:^|;\s*)content_lang=([^;]+)/);
        if (contentMatch && contentMatch[1]) {
            const contentLang = decodeURIComponent(contentMatch[1]);
            if (contentLang && contentLang !== '__original__') {
                return contentLang;
            }
            if (contentLang === '__original__') {
                return '__original__';
            }
        }
        const match = document.cookie.match(/(?:^|;\s*)googtrans=([^;]+)/);
        if (match && match[1]) {
            const raw = decodeURIComponent(match[1]);
            if (raw === '/en/en' || raw === 'en|en') {
                // fall through
            } else {
                const auto = raw.match(/^\/auto\/(.+)$/);
                if (auto && auto[1] && auto[1] !== 'en') {
                    return auto[1];
                }
                const pair = raw.match(/^\/([^/]+)\/(.+)$/);
                if (pair && pair[2] && pair[1] !== pair[2]) {
                    return pair[2];
                }
            }
        }
        const html = document.documentElement;
        if (
            html.classList.contains('translated-ltr') ||
            html.classList.contains('translated-rtl')
        ) {
            return html.getAttribute('lang') || '__original__';
        }
        return '__original__';
    }
    window.getActiveTranslateLang = getActiveTranslateLang;
    window.readGoogtransLangFromCookie = readGoogtransLangFromCookie;

    function readGoogtransLangFromCookie() {
        const match = document.cookie.match(/(?:^|;\s*)googtrans=([^;]+)/);
        if (!match || !match[1]) {
            return null;
        }
        const raw = decodeURIComponent(match[1]);
        if (raw === '/en/en' || raw === 'en|en') {
            return null;
        }
        const auto = raw.match(/^\/auto\/(.+)$/);
        if (auto && auto[1] && auto[1] !== 'en') {
            return auto[1];
        }
        const pair = raw.match(/^\/([^/]+)\/(.+)$/);
        if (pair && pair[2] && pair[1] !== pair[2] && pair[2] !== 'en') {
            return pair[2];
        }
        return null;
    }

    /**
     * Custom header language UI (opens downward on Safari; native .goog-te-combo is hidden).
     */
    function initLanguageSwitcher(googTeSelect) {
        const customSelect = document.getElementById('languageSwitcher');
        if (!customSelect || customSelect.dataset.translateBound === '1') {
            return;
        }
        customSelect.dataset.translateBound = '1';

        const active = getActiveTranslateLang();
        const matched = Array.from(customSelect.options).find(function (opt) {
            return opt.value === active || opt.value.startsWith(active + '|');
        });
        if (matched && customSelect.value !== matched.value) {
            customSelect.value = matched.value;
            const wrapper = customSelect.closest('.cst-select-wrapper');
            const display = wrapper && wrapper.querySelector('.cst-select-content');
            if (display) {
                display.textContent = matched.text;
            }
        }

        customSelect.addEventListener('change', function () {
            const lang = customSelect.value;
            if (!lang) {
                return;
            }
            if (window.changeGoogleTranslateLanguage) {
                window.changeGoogleTranslateLanguage(lang);
            }
        });

        if (googTeSelect) {
            googTeSelect.setAttribute('tabindex', '-1');
            googTeSelect.setAttribute('aria-hidden', 'true');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const customSelect = document.getElementById('languageSwitcher');
        if (customSelect && customSelect.dataset.translateBound !== '1') {
            initLanguageSwitcher(document.querySelector('.goog-te-combo'));
        }
    });

    /**
     * If bfcache restores a Google-Translate-mutated DOM while Original is selected,
     * clear cookies again and hard-navigate once (guarded to avoid loops).
     */
    window.addEventListener('pageshow', function (event) {
        var active = typeof window.getActiveTranslateLang === 'function'
            ? window.getActiveTranslateLang()
            : '__original__';
        var html = document.documentElement;
        var isTranslated = html.classList.contains('translated-ltr') ||
            html.classList.contains('translated-rtl');
        var guardKey = 'lr_orig_restore_guard';

        if (active !== '__original__') {
            try { sessionStorage.removeItem(guardKey); } catch (e) {}
            return;
        }

        if (!isTranslated) {
            try { sessionStorage.removeItem(guardKey); } catch (e) {}
            return;
        }

        // Original selected but page still machine-translated
        try {
            if (sessionStorage.getItem(guardKey) === '1') {
                sessionStorage.removeItem(guardKey);
                return;
            }
            sessionStorage.setItem(guardKey, '1');
        } catch (e) {}

        clearGoogleTranslateCookies();
        persistLanguageIntent('__original__');
        applyLanguageIntent('__original__');
        hardNavigateForLanguageChange();
    });
</script>
<script type="text/javascript"
    src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"
    onerror="if (window.LrTranslationDiagnostics) { window.LrTranslationDiagnostics.reportFailure('google_translate_script_blocked', readGoogtransLangFromCookie(), { phase: 'script_onerror' }); }">
</script>
