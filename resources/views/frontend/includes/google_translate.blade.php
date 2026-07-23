<div id="google_translate_element_mount" class="google-translate-mount" aria-hidden="true"></div>


<script>
    // Use Helper::getVisitorCountryLanguages() which returns:
    // - All active languages when no country is selected (MAIN_URL, first visit)
    // - Country-specific languages when a country is selected
    // - US languages when on LION_ROARING_USA
    window.sessionLanguages = @json(\App\Helpers\Helper::getVisitorCountryLanguages());
</script>

<!-- Google Translate initialization + robust allowed-language logic -->
<script type="text/javascript">
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
     * clearGoogleTranslateCookies()
     * Clears all googtrans cookies across all domain/path variants
     */
    function clearGoogleTranslateCookies() {
        var domain = window.location.hostname;
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + domain + ";";
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=." + domain + ";";
    }
    window.clearGoogleTranslateCookies = clearGoogleTranslateCookies;

    /**
     * content_lang marks an explicit language choice for UGC (bulletins).
     * Absent on first load so posts stay in the author's original language.
     */
    function setContentLangCookie(lang) {
        var domain = window.location.hostname;
        if (!lang || lang === '__original__') {
            document.cookie = "content_lang=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "content_lang=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + domain + ";";
            if (domain.includes('.')) {
                document.cookie = "content_lang=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=." + domain + ";";
            }
            return;
        }
        document.cookie = "content_lang=" + lang + "; path=/";
        document.cookie = "content_lang=" + lang + "; path=/; domain=" + domain;
        if (domain.includes('.')) {
            document.cookie = "content_lang=" + lang + "; path=/; domain=." + domain;
        }
    }
    window.setContentLangCookie = setContentLangCookie;

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
            clearGoogleTranslateCookies();
            setContentLangCookie(null);
            if (document.getElementById('show-bulletin') && typeof window.reloadBulletinBoardOriginal === 'function') {
                window.reloadBulletinBoardOriginal();
                return;
            }
            window.location.reload();
            return;
        }

        setContentLangCookie(lang);

        // English UI = clear googtrans; content_lang still drives bulletin translation
        if (lang === 'en') {
            var hadGoogtrans = /(?:^|;\s*)googtrans=/.test(document.cookie);
            clearGoogleTranslateCookies();
            // Fast path: already on English UI — only refresh bulletin texts (no full reload)
            if (!hadGoogtrans && document.getElementById('show-bulletin') && typeof window.applyBulletinBoardTranslations === 'function') {
                window.applyBulletinBoardTranslations();
                return;
            }
            window.location.reload();
            return;
        }

        // Other languages: set googtrans then reload so server UGC translation + GT widget both apply
        const domain = window.location.hostname;
        document.cookie = "googtrans=/auto/" + lang + "; path=/";
        document.cookie = "googtrans=/auto/" + lang + "; path=/; domain=" + domain;
        if (domain.includes('.')) {
            document.cookie = "googtrans=/auto/" + lang + "; path=/; domain=." + domain;
        }
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
            clearGoogleTranslateCookies();
            setContentLangCookie(null);
            window.location.reload();
            return;
        }

        setContentLangCookie(value);

        // English UI = clear googtrans; content_lang still drives bulletin translation
        if (value === 'en') {
            clearGoogleTranslateCookies();
            window.location.reload();
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
                    window.location.reload();
                }
            }, 1000);
        } else {
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
            if (!selectEl) return;
            selectEl.addEventListener('change', function(e) {
                var selectedValue = selectEl.value;
                if (selectedValue === 'en' || selectedValue === '' || selectedValue === 'en|en') {
                    e.stopPropagation();
                    setContentLangCookie('en');
                    clearGoogleTranslateCookies();
                    setTimeout(function() { window.location.reload(); }, 100);
                }
            }, true);
            initLanguageSwitcher(selectEl);
        }, 5000);
    }

    /**
     * Active language from googtrans / content_lang, or Original when unset.
     */
    function getActiveTranslateLang() {
        const match = document.cookie.match(/(?:^|;\s*)googtrans=\/auto\/([^;]+)/);
        if (match && match[1]) {
            return match[1];
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
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
</script>
