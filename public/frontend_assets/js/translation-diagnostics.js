/**
 * Translation diagnostics: server logging + a user-facing notice when the
 * translation engine cannot do its job.
 *
 * Rewritten for LrTranslate. The old Google Translate widget watchdogs (polling
 * for `.goog-te-combo`, comparing `googtrans` cookies) are gone — there is no
 * third-party widget left to fail. What can still fail is our own endpoint or
 * the network, and the engine reports those directly.
 */
(function (window) {
    'use strict';

    var reportedKeys = {};
    var popupShownForLang = {};
    var failureCounts = {};

    /** Reasons the visitor should actually be told about. */
    var USER_VISIBLE = {
        translate_request_failed: 'We could not reach the translation service.',
        translate_language_not_allowed: 'That language is not available on this site.'
    };

    /** A single hiccup is noise; repeated failures are worth surfacing. */
    var FAILURES_BEFORE_POPUP = 3;

    function getConfig() {
        return window.lrTranslationDiagnostics || {};
    }

    function activeLang() {
        if (window.LrTranslate && typeof window.LrTranslate.getLanguage === 'function') {
            return window.LrTranslate.getLanguage();
        }
        return null;
    }

    function collectDiagnostics() {
        var switcher = document.getElementById('languageSwitcher');
        var html = document.documentElement;

        var cookies = document.cookie.split(';').map(function (s) {
            return s.trim();
        }).filter(function (s) {
            return s.indexOf('content_lang=') === 0;
        });

        return {
            url: location.href,
            path: location.pathname,
            userAgent: navigator.userAgent,
            selectedLang: switcher ? switcher.value : null,
            cookies: cookies,
            htmlClasses: html.className,
            htmlLang: html.lang || null,
            activeTranslateLang: activeLang(),
            engineLoaded: !!window.LrTranslate,
            sessionLanguagesCount: Array.isArray(window.sessionLanguages)
                ? window.sessionLanguages.length
                : null
        };
    }

    function postLog(reason, expectedLang, extra) {
        var dedupeKey = reason + '|' + (expectedLang || '') + '|' + location.pathname;
        if (reportedKeys[dedupeKey]) {
            return;
        }
        reportedKeys[dedupeKey] = true;

        var config = getConfig();
        var payload = {
            reason: reason,
            expected_lang: expectedLang || null,
            surface: config.surface || 'website',
            diagnostics: collectDiagnostics(),
            extra: extra || {}
        };

        if (!config.logUrl) {
            if (window.console) console.warn('[TranslationDiagnostics]', reason, payload);
            return;
        }

        var headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
        if (config.csrfToken) {
            headers['X-CSRF-TOKEN'] = config.csrfToken;
        }

        fetch(config.logUrl, {
            method: 'POST',
            headers: headers,
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        }).catch(function () {
            // Logging must never break the page.
        });
    }

    function showFailurePopup(reason, lang) {
        var detail = USER_VISIBLE[reason] || 'Translation is unavailable right now.';
        var message = detail + ' The page is still shown in its original language. ' +
            'If this keeps happening, try disabling ad blockers for this site or reloading the page.';

        if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
            Swal.fire({ icon: 'warning', title: 'Translation unavailable', text: message, confirmButtonText: 'OK' });
            return;
        }
        if (typeof toastr !== 'undefined' && typeof toastr.warning === 'function') {
            toastr.warning(message, 'Translation unavailable');
            return;
        }
        if (window.console) console.warn('[TranslationDiagnostics]', message);
    }

    /**
     * @param {string} reason        machine-readable failure code
     * @param {string|null} lang     language the visitor asked for
     * @param {object} [extra]
     */
    function reportFailure(reason, lang, extra) {
        postLog(reason, lang, extra);

        if (!USER_VISIBLE[reason]) {
            return;
        }
        // Passthrough for a language we simply don't translate is not a failure.
        if (!lang || lang === '__original__' || lang === 'en') {
            return;
        }

        failureCounts[reason] = (failureCounts[reason] || 0) + 1;

        var isHardStop = reason !== 'translate_request_failed';
        if (!isHardStop && failureCounts[reason] < FAILURES_BEFORE_POPUP) {
            return;
        }

        var popupKey = reason + '|' + lang;
        if (popupShownForLang[popupKey]) {
            return;
        }
        popupShownForLang[popupKey] = true;
        showFailurePopup(reason, lang);
    }

    window.LrTranslationDiagnostics = {
        reportFailure: reportFailure,
        collectDiagnostics: collectDiagnostics,
        // Retained so older call sites do not throw; the widget they polled is gone.
        markPendingVerification: function () {},
        clearPendingVerification: function () {}
    };
})(window);
