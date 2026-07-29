/**
 * Web translation diagnostics: server logging + user popup when Google Translate fails.
 * Loaded by frontend.includes.google_translate (website, User PMA, e-store, e-learning).
 */
(function (window) {
    'use strict';

    var STORAGE_KEY = 'lr_expected_translate_lang';
    var STORAGE_VERIFY_AT = 'lr_translate_verify_at';
    var reportedKeys = {};
    var popupShownForLang = {};

    function getConfig() {
        return window.lrTranslationDiagnostics || {};
    }

    function readGoogtransLang() {
        var match = document.cookie.match(/(?:^|;\s*)googtrans=([^;]+)/);
        if (!match || !match[1]) {
            return null;
        }

        var raw = decodeURIComponent(match[1]);
        if (raw === '/en/en' || raw === 'en|en') {
            return null;
        }

        var auto = raw.match(/^\/auto\/(.+)$/);
        if (auto && auto[1] && auto[1] !== 'en') {
            return auto[1];
        }

        var pair = raw.match(/^\/([^/]+)\/(.+)$/);
        if (pair && pair[2] && pair[1] !== pair[2] && pair[2] !== 'en') {
            return pair[2];
        }

        return null;
    }

    function collectDiagnostics() {
        var cookies = document.cookie.split(';').map(function (s) {
            return s.trim();
        }).filter(function (s) {
            return s.indexOf('googtrans=') === 0 || s.indexOf('content_lang=') === 0;
        });

        var switcher = document.getElementById('languageSwitcher');
        var googTe = document.querySelector('.goog-te-combo');
        var html = document.documentElement;

        return {
            url: location.href,
            path: location.pathname,
            userAgent: navigator.userAgent,
            selectedLang: switcher ? switcher.value : null,
            cookies: cookies,
            googTeCombo: !!googTe,
            googTeValue: googTe ? googTe.value : null,
            htmlClasses: html.className,
            htmlLang: html.lang || null,
            gtScriptLoaded: typeof google !== 'undefined' && !!(google.translate),
            translatedDetected: html.classList.contains('translated-ltr') ||
                html.classList.contains('translated-rtl'),
            activeTranslateLang: typeof window.getActiveTranslateLang === 'function'
                ? window.getActiveTranslateLang()
                : null,
            sessionLanguagesCount: Array.isArray(window.sessionLanguages)
                ? window.sessionLanguages.length
                : null,
        };
    }

    function isPageTranslated() {
        var html = document.documentElement;
        return html.classList.contains('translated-ltr') ||
            html.classList.contains('translated-rtl');
    }

    function expectedLangRequiresGt(lang) {
        return !!lang && lang !== '__original__' && lang !== 'en';
    }

    function markPendingVerification(lang) {
        if (!expectedLangRequiresGt(lang)) {
            return;
        }

        try {
            sessionStorage.setItem(STORAGE_KEY, lang);
            sessionStorage.setItem(STORAGE_VERIFY_AT, String(Date.now()));
        } catch (e) {
            // Private browsing may block sessionStorage.
        }
    }

    function clearPendingVerification() {
        try {
            sessionStorage.removeItem(STORAGE_KEY);
            sessionStorage.removeItem(STORAGE_VERIFY_AT);
        } catch (e) {
            // ignore
        }
    }

    function getPendingVerification() {
        try {
            var lang = sessionStorage.getItem(STORAGE_KEY);
            if (!lang) {
                return null;
            }

            return {
                lang: lang,
                at: parseInt(sessionStorage.getItem(STORAGE_VERIFY_AT) || '0', 10),
            };
        } catch (e) {
            return null;
        }
    }

    function reportFailure(reason, extra) {
        extra = extra || {};
        var expectedLang = extra.expectedLang || null;
        var dedupeKey = reason + '|' + (expectedLang || '') + '|' + location.pathname;

        if (reportedKeys[dedupeKey]) {
            return;
        }
        reportedKeys[dedupeKey] = true;

        var config = getConfig();
        var payload = {
            reason: reason,
            expected_lang: expectedLang,
            surface: config.surface || 'website',
            diagnostics: collectDiagnostics(),
            extra: extra,
        };

        if (!config.logUrl) {
            console.warn('[TranslationDiagnostics]', reason, payload);
            return;
        }

        var headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        };

        if (config.csrfToken) {
            headers['X-CSRF-TOKEN'] = config.csrfToken;
        }

        fetch(config.logUrl, {
            method: 'POST',
            headers: headers,
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        }).catch(function () {
            // Logging must never break the page.
        });
    }

    function showFailurePopup(lang) {
        var langLabel = lang || 'your selected language';
        var message = 'We could not translate this page to ' + langLabel + '. ' +
            'This can happen when a browser extension blocks Google Translate, or when cookies are restricted or when google translate is limited to access. ' +
            'Try disabling ad blockers for this site, allowing cookies, or using a different browser.';

        if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
            Swal.fire({
                icon: 'warning',
                title: 'Translation unavailable',
                text: message,
                confirmButtonText: 'OK',
            });
            return;
        }

        if (typeof swal === 'function') {
            swal('Translation unavailable', message, 'warning');
            return;
        }

        window.alert('Translation unavailable\n\n' + message);
    }

    function handleTranslationFailure(reason, expectedLang, extra) {
        var payload = extra || {};
        payload.expectedLang = expectedLang || null;
        reportFailure(reason, payload);

        if (!expectedLangRequiresGt(expectedLang)) {
            return;
        }

        var popupKey = String(expectedLang) + '|' + location.pathname;
        if (popupShownForLang[popupKey]) {
            return;
        }
        popupShownForLang[popupKey] = true;
        showFailurePopup(expectedLang);
    }

    function resolveExpectedLang() {
        var pending = getPendingVerification();
        if (pending && pending.lang) {
            return pending.lang;
        }

        return readGoogtransLang();
    }

    function verifyAfterLoad() {
        var expectedLang = resolveExpectedLang();

        if (!expectedLangRequiresGt(expectedLang)) {
            clearPendingVerification();
            return;
        }

        // First check after GT has time to rewrite the DOM.
        window.setTimeout(function () {
            if (isPageTranslated()) {
                clearPendingVerification();
                return;
            }

            // Second check — slow networks / heavy pages.
            window.setTimeout(function () {
                if (isPageTranslated()) {
                    clearPendingVerification();
                    return;
                }

                var cookieLang = readGoogtransLang();
                if (!cookieLang) {
                    handleTranslationFailure('googtrans_cookie_mismatch', expectedLang);
                } else {
                    handleTranslationFailure('page_not_translated_after_reload', expectedLang);
                }

                clearPendingVerification();
            }, 2500);
        }, 2500);
    }

    function watchGtScriptLoad() {
        window.setTimeout(function () {
            var expectedLang = readGoogtransLang();
            if (!expectedLangRequiresGt(expectedLang)) {
                return;
            }

            if (typeof google === 'undefined' || !google.translate) {
                handleTranslationFailure('google_translate_script_blocked', expectedLang);
            }
        }, 8000);
    }

    function watchGtWidgetMissing() {
        window.setTimeout(function () {
            var expectedLang = readGoogtransLang();
            if (!expectedLangRequiresGt(expectedLang)) {
                return;
            }

            if (!document.querySelector('.goog-te-combo')) {
                handleTranslationFailure('google_translate_widget_missing', expectedLang);
            }
        }, 6000);
    }

    function init() {
        verifyAfterLoad();
        watchGtScriptLoad();
        watchGtWidgetMissing();
    }

    window.LrTranslationDiagnostics = {
        markPendingVerification: markPendingVerification,
        clearPendingVerification: clearPendingVerification,
        reportFailure: handleTranslationFailure,
        collectDiagnostics: collectDiagnostics,
        verifyAfterLoad: verifyAfterLoad,
        init: init,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window);
