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
     * Cookie paths for path-based demos (/lion-roaring-org) and host-based production (/).
     * Google Translate often writes googtrans with the subdirectory path; clearing only
     * path=/ leaves that cookie and language switching appears stuck.
     */
    function getTranslateCookiePaths() {
        const paths = new Set(['/']);
        const base = String(window.appBasePath || '').replace(/\/$/, '');
        if (base) {
            paths.add(base);
            paths.add(base + '/');
        }
        const pathname = window.location.pathname || '/';
        const parts = pathname.split('/').filter(Boolean);
        let acc = '';
        for (let i = 0; i < parts.length; i++) {
            acc += '/' + parts[i];
            paths.add(acc);
            paths.add(acc + '/');
        }
        return Array.from(paths);
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

    /**
     * clearGoogleTranslateCookies()
     * Clears googtrans across domain + path + Secure/SameSite variants.
     */
    function clearGoogleTranslateCookies() {
        const paths = getTranslateCookiePaths();
        const domains = cookieDomainVariants();
        paths.forEach(function (path) {
            domains.forEach(function (domain) {
                expireNamedCookie('googtrans', path, domain);
            });
        });
    }
    window.clearGoogleTranslateCookies = clearGoogleTranslateCookies;

    /**
     * Overwrite leftovers with /en/en (source=target) so GT stops translating.
     * Deleting alone often fails when Google set a Secure path-scoped cookie.
     */
    function neutralizeGoogtransCookie() {
        const paths = getTranslateCookiePaths();
        const domains = cookieDomainVariants();
        paths.forEach(function (path) {
            domains.forEach(function (domain) {
                writeNamedCookie('googtrans', '/en/en', path, domain);
            });
        });
    }

    /**
     * content_lang marks an explicit language choice for UGC (bulletins).
     * Absent on first load so posts stay in the author's original language.
     */
    function setContentLangCookie(lang) {
        const paths = getTranslateCookiePaths();
        const domains = cookieDomainVariants();
        if (!lang || lang === '__original__') {
            paths.forEach(function (path) {
                domains.forEach(function (domain) {
                    expireNamedCookie('content_lang', path, domain);
                });
            });
            return;
        }
        paths.forEach(function (path) {
            domains.forEach(function (domain) {
                writeNamedCookie('content_lang', lang, path, domain);
            });
        });
    }
    window.setContentLangCookie = setContentLangCookie;

    function setGoogtransCookie(lang) {
        const value = '/auto/' + lang;
        const paths = getTranslateCookiePaths();
        const domains = cookieDomainVariants();
        // Clear first so Secure leftovers cannot win over a new language
        clearGoogleTranslateCookies();
        paths.forEach(function (path) {
            domains.forEach(function (domain) {
                writeNamedCookie('googtrans', value, path, domain);
            });
        });
    }

    function resetGoogleTranslateUi(nextContentLang) {
        if (window.LrTranslationDiagnostics) {
            window.LrTranslationDiagnostics.clearPendingVerification();
        }
        clearGoogleTranslateCookies();
        neutralizeGoogtransCookie();
        if (nextContentLang) {
            setContentLangCookie(nextContentLang);
        } else {
            setContentLangCookie(null);
        }
        window.location.reload();
    }

    /**
     * changeGoogleTranslateLanguage(lang)
     * - For Original: clear translation cookies and reload (UGC stays original)
     * - For English: clear googtrans, set content_lang=en, reload (UGC → English)
     * - For other languages: set cookies and updates the Google Translate widget
     */
    window.changeGoogleTranslateLanguage = function(lang) {
        const langMap = { 'cn': 'zh-CN', 'us': 'en', 'uk': 'en' };
        if (langMap[lang]) lang = langMap[lang];

        if (lang === '__original__') {
            // Must full-reload: Google Translate rewrites the whole DOM (menus, headers).
            resetGoogleTranslateUi(null);
            return;
        }

        // English UI = neutralize googtrans; content_lang drives bulletin translation
        if (lang === 'en') {
            var hadGoogtrans = /(?:^|;\s*)googtrans=/.test(document.cookie);
            var pageIsTranslated = document.documentElement.classList.contains('translated-ltr') ||
                document.documentElement.classList.contains('translated-rtl');
            var googtransIsNeutral = /(?:^|;\s*)googtrans=\/en\/en(?:;|$)/.test(document.cookie);
            // Fast path only when UI was never machine-translated
            if (!hadGoogtrans && !pageIsTranslated && document.getElementById('show-bulletin') && typeof window.applyBulletinBoardTranslations === 'function') {
                setContentLangCookie('en');
                window.applyBulletinBoardTranslations();
                return;
            }
            if (googtransIsNeutral && !pageIsTranslated && document.getElementById('show-bulletin') && typeof window.applyBulletinBoardTranslations === 'function') {
                setContentLangCookie('en');
                window.applyBulletinBoardTranslations();
                return;
            }
            resetGoogleTranslateUi('en');
            return;
        }

        setContentLangCookie(lang);
        if (window.LrTranslationDiagnostics) {
            window.LrTranslationDiagnostics.markPendingVerification(lang);
        }
        // Other languages: set googtrans then reload so server UGC translation + GT widget both apply
        setGoogtransCookie(lang);
        window.location.reload();
    }

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
     * - For English: clears cookies and reloads (only reliable way to restore original content)
     * - For other languages: sets the dropdown and triggers change
     */
    function forceSelectValue(selectEl, value) {
        if (!selectEl) return;

        if (value === '__original__') {
            resetGoogleTranslateUi(null);
            return;
        }

        setContentLangCookie(value);

        // English UI = neutralize googtrans; content_lang still drives bulletin translation
        if (value === 'en') {
            resetGoogleTranslateUi('en');
            return;
        }

        // For non-English: find matching option by exact value or value prefix
        let found = Array.from(selectEl.options).find(opt =>
            opt.value === value ||
            opt.value.startsWith(value + '|')
        );

        if (found) {
            selectEl.value = found.value;
            const evt = document.createEvent('HTMLEvents');
            evt.initEvent('change', true, true);
            selectEl.dispatchEvent(evt);
            
            // Fallback for E-store and E-learning where dynamic translation might stall
            setTimeout(() => {
                const htmlEl = document.documentElement;
                const isTranslated = htmlEl.classList.contains('translated-ltr') || 
                                   htmlEl.classList.contains('translated-rtl') ||
                                   htmlEl.lang === value;
                
                if (!isTranslated) {
                    console.log("Translation not detected, forcing reload...");
                    if (window.LrTranslationDiagnostics) {
                        window.LrTranslationDiagnostics.markPendingVerification(value);
                    }
                    window.location.reload();
                }
            }, 1000);
        } else {
            if (window.LrTranslationDiagnostics) {
                window.LrTranslationDiagnostics.reportFailure(
                    'translation_not_detected_after_select',
                    value,
                    { optionFound: false }
                );
            }
            window.location.reload();
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
        const match = document.cookie.match(/(?:^|;\s*)googtrans=([^;]+)/);
        if (match && match[1]) {
            const raw = decodeURIComponent(match[1]);
            // /en/en = GT neutralized (English source=target); treat as no machine language
            if (raw === '/en/en' || raw === 'en|en') {
                // fall through to content_lang / Original
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
        const contentMatch = document.cookie.match(/(?:^|;\s*)content_lang=([^;]+)/);
        if (contentMatch && contentMatch[1]) {
            return decodeURIComponent(contentMatch[1]);
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
</script>
<script type="text/javascript"
    src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"
    onerror="if (window.LrTranslationDiagnostics) { window.LrTranslationDiagnostics.reportFailure('google_translate_script_blocked', readGoogtransLangFromCookie(), { phase: 'script_onerror' }); }">
</script>
