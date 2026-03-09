<div id="google_translate_element" style="position: absolute; opacity: 0; width: 0; height: 0; overflow: hidden;"></div>

<script>
    // Use Helper::getVisitorCountryLanguages() — returns all active languages when no country selected
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
     */
    function clearGoogleTranslateCookies() {
        var domain = window.location.hostname;
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + domain + ";";
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=." + domain + ";";
    }
    window.clearGoogleTranslateCookies = clearGoogleTranslateCookies;

    /**
     * changeGoogleTranslateLanguage(lang)
     */
    window.changeGoogleTranslateLanguage = function(lang) {
        const langMap = { 'cn': 'zh-CN', 'us': 'en', 'uk': 'en' };
        if (langMap[lang]) lang = langMap[lang];

        if (lang === 'en') {
            clearGoogleTranslateCookies();
            window.location.reload();
            return;
        }

        const domain = window.location.hostname;
        document.cookie = "googtrans=/auto/" + lang + "; path=/";
        document.cookie = "googtrans=/auto/" + lang + "; path=/; domain=" + domain;

        let attempts = 0;
        const checkAndSet = setInterval(function() {
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                clearInterval(checkAndSet);
                if (window.forceSelectValue) {
                    window.forceSelectValue(select, lang);
                } else {
                    select.value = lang;
                    select.dispatchEvent(new Event('change'));
                }
            } else {
                attempts++;
                if (attempts >= 50) {
                    clearInterval(checkAndSet);
                }
            }
        }, 100);
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
     */
    function forceSelectValue(selectEl, value) {
        if (!selectEl) return;

        if (value === 'en') {
            clearGoogleTranslateCookies();
            window.location.reload();
            return;
        }

        let found = Array.from(selectEl.options).find(opt =>
            opt.value === value ||
            opt.value.startsWith(value + '|') ||
            (opt.text.toLowerCase().includes(value.toLowerCase()) && value !== 'en')
        );

        if (found) {
            selectEl.value = found.value;
            const evt = document.createEvent('HTMLEvents');
            evt.initEvent('change', true, true);
            selectEl.dispatchEvent(evt);
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
        }, 'google_translate_element');

        // Watch for the dropdown and intercept English selection
        waitForTranslateSelect(function(selectEl) {
            if (!selectEl) return;
            selectEl.addEventListener('change', function(e) {
                var selectedValue = selectEl.value;
                if (selectedValue === 'en' || selectedValue === '' || selectedValue === 'en|en') {
                    e.stopPropagation();
                    clearGoogleTranslateCookies();
                    setTimeout(function() { window.location.reload(); }, 100);
                }
            }, true);
        }, 5000);
    }
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
</script>
