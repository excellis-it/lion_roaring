<div id="google_translate_element" style="position: absolute; opacity: 0; width: 0; height: 0; overflow: hidden;"></div>


<script>
    // example: [{"id":202,"code":"es","name":"Spanish",...}, {"id":249,"code":"en","name":"English",...}]
    window.sessionLanguages = @json(session('visitor_country_languages') ?? []);
</script>

<!-- Google Translate initialization + robust allowed-language logic -->
<script type="text/javascript">
    /**
     * parseLanguages(data)
     * - Accepts either:
     *   1) flat array of language objects: [{code:'es', name:'Spanish'}, ...]
     *   2) nested weird session shape used earlier
     * - Returns a Set of language codes (strings), ensures 'en' is present.
     */
    function parseLanguages(data) {
        const codes = new Set();

        if (!data) {
            codes.add('en');
            return codes;
        }

        // If it's a flat array of objects like [{code:'es'}, {code:'en'}]
        if (Array.isArray(data) && data.length > 0 && typeof data[0] === 'object' && data[0] !== null && 'code' in data[
                0]) {
            data.forEach(lang => {
                if (lang && lang.code) codes.add(String(lang.code));
            });
            codes.add('en');
            return codes;
        }

        // Fallback: attempt to safely traverse nested structure (previous attempts)
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

        codes.add('en'); // always include English
        return codes;
    }

    /**
     * buildIncludedLanguagesString(sessionData)
     * - Returns the comma-separated string expected by Google Translate's includedLanguages option.
     */
    function buildIncludedLanguagesString(sessionData) {
        const codes = parseLanguages(sessionData);
        // convert to comma-separated string (Google expects e.g., 'en,es,fr')
        return Array.from(codes).join(',');
    }

    /**
     * changeGoogleTranslateLanguage(lang)
     * - Helper to programmatically change language (used by chatbot)
     */
    window.changeGoogleTranslateLanguage = function(lang) {
        // 1. Map common codes
        const langMap = {
            'cn': 'zh-CN',
            'us': 'en',
            'uk': 'en'
        };
        if (langMap[lang]) lang = langMap[lang];

        // 2. Set Cookies (Persistence)
        const domain = window.location.hostname;
        document.cookie = "googtrans=/auto/" + lang + "; path=/";
        document.cookie = "googtrans=/auto/" + lang + "; path=/; domain=" + domain;

        // 3. Polling mechanism to wait for Google Translate widget
        let attempts = 0;
        const maxAttempts = 50; // Try for 5 seconds

        const checkAndSet = setInterval(function() {
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                clearInterval(checkAndSet);
                // Use forceSelectValue if available to handle 'en' -> '' mapping
                if (window.forceSelectValue) {
                    window.forceSelectValue(select, lang);
                } else {
                    select.value = lang;
                    select.dispatchEvent(new Event('change'));
                    select.dispatchEvent(new Event('click'));
                }
                console.log('Language changed to: ' + lang);
            } else {
                attempts++;
                if (attempts >= maxAttempts) {
                    clearInterval(checkAndSet);
                    console.warn('Google Translate widget not found. Cookie set for next page load.');
                }
            }
        }, 100);
    }

    /**
     * waitForTranslateSelect(callback)
     * - Uses MutationObserver to wait for the Google Translate select (.goog-te-combo)
     * - Calls callback(selectElement) when it appears.
     */
    function waitForTranslateSelect(callback, timeout = 4000) {
        // If already exists, call immediately
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

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // safety timeout to disconnect and call callback with null if not found
        setTimeout(() => {
            try {
                observer.disconnect();
            } catch (e) {}
            const el = document.querySelector('.goog-te-combo');
            callback(el);
        }, timeout);
    }

    /**
     * forceSelectValue(selectEl, value)
     * - Safely forces the .goog-te-combo select to a value (language code) if option exists.
     */
    function forceSelectValue(selectEl, value) {
        if (!selectEl) return;


        // Try to find the option by value, prefix, or text
        let found = Array.from(selectEl.options).find(opt =>
            opt.value === value ||
            opt.value.startsWith(value + '|') ||
            (value === 'en' && opt.value === '') || // Google often uses empty value for original
            opt.text.toLowerCase().includes(value.toLowerCase())
        );

        // Fallback for English: first option is usually the original language
        if (!found && value === 'en') {
            found = selectEl.options[0];
            // console.log('english not found');
            // console.log(selectEl.options[0]);

        }

        if (found) {
            selectEl.value = found.value;
            // Trigger change event so Google Translate applies the language
            const evt = document.createEvent('HTMLEvents');
            evt.initEvent('change', true, true);
            selectEl.dispatchEvent(evt);
            console.log('english found');
            // console.log(selectEl.options[0]);
            // If switching back to English, clear the Google Translate cookies to ensure a full reset
            // if (value === 'en') {
            //     const domain = window.location.hostname;
            //     const path = "/";
            //     document.cookie = `googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=${path};`;
            //     document.cookie = `googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=${path}; domain=${domain};`;
            //     document.cookie = `googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=${path}; domain=.${domain};`;
            // }
        }
    }
    window.forceSelectValue = forceSelectValue;

    /**
     * googleTranslateElementInit
     * - Called by Google's script callback
     * - Uses includedLanguages built from session languages
     */
    function googleTranslateElementInit() {
        const includedLanguages = buildIncludedLanguagesString(window.sessionLanguages || []);
        new google.translate.TranslateElement({
            // pageLanguage: 'en',
            includedLanguages: includedLanguages,
            //   layout: google.translate.TranslateElement.InlineLayout.SIMPLE
        }, 'google_translate_element');

        // Removed the automatic English reset on load to allow persistent translations
        /*
        waitForTranslateSelect(function(selectEl) {
            if (!selectEl) return;
            forceSelectValue(selectEl, 'en');
        }, 5000);
        */
    }
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
</script>
