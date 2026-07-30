/**
 * LrTranslate — site-wide translation engine backed by Google Cloud Translation.
 *
 * Replaces the free Google Website Translator widget. Differences that matter:
 *   - The API key never reaches the browser; everything goes through Laravel.
 *   - Switching language does NOT reload the page.
 *   - "Original" is instant and free: originals are kept in a WeakMap, so
 *     restoring them makes zero network calls.
 *   - Exclusion rules (icons, person names) are ours, applied before anything is
 *     sent, so protected text is never even transmitted — let alone billed.
 *
 * Cost model: each unique normalized string is paid for once, server-side,
 * forever. localStorage means a returning visitor usually spends nothing at all.
 */
(function (window, document) {
    'use strict';

    if (window.LrTranslate) {
        return;
    }

    // ── configuration ───────────────────────────────────────────────────────

    var CFG = window.LR_TRANSLATE_CONFIG || {};
    var ENDPOINT = CFG.endpoint || '/translate/batch';
    var CSRF = CFG.csrf || '';
    var CACHE_VERSION = CFG.cacheVersion || 'v1';
    var DEBUG = !!CFG.debug;

    var LANG_KEY = 'lr_content_lang';
    var ORIGINAL = '__original__';

    var MAX_ITEMS_PER_REQUEST = 100;
    var MAX_CHARS_PER_REQUEST = 12000;
    var MAX_PARALLEL_REQUESTS = 3;
    var LS_MAX_ENTRIES_PER_LANG = 4000;
    var OBSERVER_DEBOUNCE_MS = 200;

    // ── skip rules (single source of truth) ─────────────────────────────────

    var SKIP_TAGS = {
        SCRIPT: 1, STYLE: 1, NOSCRIPT: 1, TEXTAREA: 1, CODE: 1, PRE: 1, KBD: 1,
        SAMP: 1, VAR: 1, SVG: 1, CANVAS: 1, IFRAME: 1, OBJECT: 1, EMBED: 1,
        MATH: 1, TEMPLATE: 1, I: 1, TITLE: 1, HEAD: 1, META: 1, LINK: 1
    };

    /** Icon fonts render glyphs as text ligatures — translating them destroys the icon. */
    var ICON_SELECTOR = [
        '.material-icons', '.material-icons-outlined', '.material-icons-round',
        '.material-icons-sharp', '.material-icons-two-tone',
        '.material-symbols-outlined', '.material-symbols-rounded', '.material-symbols-sharp',
        '.fa', '.fas', '.far', '.fab', '.fal', '.fad', '.fa-solid', '.fa-regular', '.fa-brands',
        '.bi', '.glyphicon', '.icon', '.iconify', '.feather', '.ti', '.bx',
        '[class*="fa-"]', '[class*="icon-"]', '[class^="icon"]', '[class*="-icon"]'
    ].join(',');

    /** Person names must never be translated, anywhere. */
    var NAME_SELECTOR = [
        '.notranslate', '[translate="no"]', '[data-nt]',
        '.GroupName', '.namemember', '.name_bull', '.person-name', '.profile-name',
        '.user-name', '.username', '.member-name', '.author-name', '.full-name',
        '[data-person-name]', '[data-user-name]'
    ].join(',');

    var SKIP_SELECTOR = ICON_SELECTOR + ',' + NAME_SELECTOR;

    /** Attributes worth translating, and the elements they may appear on. */
    var TRANSLATABLE_ATTRS = ['placeholder', 'title', 'aria-label', 'alt', 'data-original-title'];

    var NAME_INPUT_SELECTOR = [
        '[name="first_name"]', '[name="last_name"]', '[name="middle_name"]',
        '[name="user_name"]', '[name="full_name"]', '[name="username"]',
        '[name="edit_user_name"]', '[name="edit_first_name"]', '[name="edit_last_name"]',
        '[id="first_name"]', '[id="last_name"]', '[id="middle_name"]', '[id="user_name"]',
        '[id="register-first-name"]', '[id="register-last-name"]'
    ].join(',');

    // ── state ───────────────────────────────────────────────────────────────

    var currentLang = ORIGINAL;
    var textOriginals = new WeakMap();  // textNode  -> original nodeValue
    var attrOriginals = new WeakMap();  // element   -> { attr: originalValue }
    var skipCache = new WeakMap();      // element   -> bool
    var memCache = Object.create(null); // "lang|hash" -> translated
    var lsDirty = false;
    var lsTimer = null;
    var applying = false;               // guards our own DOM writes from the observer
    var observer = null;
    var pendingPass = null;
    var inFlight = 0;
    var titleOriginal = null;
    var initialized = false;
    var translatedOnce = false;

    // ── small utilities ─────────────────────────────────────────────────────

    function log() {
        if (DEBUG && window.console) {
            console.log.apply(console, ['[LrTranslate]'].concat([].slice.call(arguments)));
        }
    }

    /** Must mirror GoogleTranslateService::normalize() so client and server agree. */
    function normalize(text) {
        return text
            .replace(/[\u00a0\u2007\u202f]/g, ' ')
            .replace(/\r\n?/g, '\n')
            .replace(/[ \t]+/g, ' ')
            .replace(/\n{3,}/g, '\n\n')
            .replace(/^\s+|\s+$/g, '');
    }

    function padLike(original, translated) {
        var lead = /^\s*/.exec(original)[0];
        var tail = /\s*$/.exec(original)[0];
        return lead + translated + tail;
    }

    /** FNV-1a, two rounds, hex — collision risk is negligible at our volumes. */
    function hash(str) {
        var h1 = 0x811c9dc5, h2 = 0x01000193;
        for (var i = 0; i < str.length; i++) {
            var c = str.charCodeAt(i);
            h1 ^= c; h1 = (h1 * 0x01000193) >>> 0;
            h2 = ((h2 ^ c) * 0x85ebca6b) >>> 0;
        }
        return h1.toString(36) + h2.toString(36) + str.length.toString(36);
    }

    /**
     * Content with no translatable language. Filtering here means these strings
     * are never transmitted and never billed.
     */
    var HAS_LETTER = (function () {
        try { return new RegExp('\\p{L}', 'u'); } catch (e) { return /[A-Za-z]/; }
    })();

    function isTranslatable(text) {
        if (!text || text.length < 2) return false;
        if (text.length > 5000) return false;
        if (!HAS_LETTER.test(text)) return false;                             // no letters at all
        if (/^[^\d]?\s?[\d.,\s]+\s?[A-Za-z%]{0,4}$/.test(text)) return false; // numbers/money/units
        if (/^\S+@\S+\.\S+$/.test(text)) return false;                        // email
        if (/^(https?:\/\/|www\.|\/)\S*$/i.test(text)) return false;          // url/path
        if (text.indexOf(' ') === -1 && /^[\w\-.:#]+$/.test(text) && /[\d_\-]/.test(text)) return false;
        return true;
    }

    function isSkipped(el) {
        if (!el || el.nodeType !== 1) return false;

        var cached = skipCache.get(el);
        if (cached !== undefined) return cached;

        var result = false;
        var node = el;
        while (node && node.nodeType === 1) {
            var known = skipCache.get(node);
            if (known === true) { result = true; break; }

            if (SKIP_TAGS[node.tagName]) { result = true; break; }
            if (node.classList && node.classList.contains('notranslate')) { result = true; break; }
            if (node.getAttribute && node.getAttribute('translate') === 'no') { result = true; break; }
            if (node.hasAttribute && node.hasAttribute('data-nt')) { result = true; break; }
            if (node.matches) {
                try {
                    if (node.matches(SKIP_SELECTOR)) { result = true; break; }
                } catch (e) { /* selector unsupported in this browser */ }
            }
            node = node.parentElement;
        }

        skipCache.set(el, result);
        return result;
    }

    // ── cache (memory + localStorage) ───────────────────────────────────────

    function lsKey(lang) {
        return 'lrtr:' + CACHE_VERSION + ':' + lang;
    }

    function loadLangCache(lang) {
        try {
            var raw = window.localStorage.getItem(lsKey(lang));
            if (!raw) return;
            var obj = JSON.parse(raw);
            for (var k in obj) {
                if (Object.prototype.hasOwnProperty.call(obj, k)) {
                    memCache[lang + '|' + k] = obj[k];
                }
            }
            log('cache loaded', lang, Object.keys(obj).length);
        } catch (e) { /* quota/parse issues are non-fatal */ }
    }

    function flushLangCache(lang) {
        lsDirty = false;
        try {
            var prefix = lang + '|';
            var obj = {};
            var count = 0;
            for (var k in memCache) {
                if (k.indexOf(prefix) === 0) {
                    if (count++ >= LS_MAX_ENTRIES_PER_LANG) break;
                    obj[k.slice(prefix.length)] = memCache[k];
                }
            }
            window.localStorage.setItem(lsKey(lang), JSON.stringify(obj));
        } catch (e) {
            // Over quota: drop this language's cache rather than break the page.
            try { window.localStorage.removeItem(lsKey(lang)); } catch (e2) {}
        }
    }

    function scheduleFlush(lang) {
        lsDirty = true;
        if (lsTimer) clearTimeout(lsTimer);
        lsTimer = setTimeout(function () {
            if (lsDirty) flushLangCache(lang);
        }, 1500);
    }

    function cacheGet(lang, h) {
        return memCache[lang + '|' + h];
    }

    function cacheSet(lang, h, value) {
        memCache[lang + '|' + h] = value;
    }

    // ── language intent ─────────────────────────────────────────────────────

    function readStoredLang() {
        try {
            var v = window.localStorage.getItem(LANG_KEY);
            if (v) return v;
        } catch (e) {}
        var m = document.cookie.match(/(?:^|;\s*)content_lang=([^;]+)/);
        if (m && m[1]) {
            try { return decodeURIComponent(m[1]); } catch (e) { return m[1]; }
        }
        return ORIGINAL;
    }

    function writeStoredLang(lang) {
        try { window.localStorage.setItem(LANG_KEY, lang); } catch (e) {}
        // The cookie is what the server reads when it renders UGC.
        var secure = window.location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = 'content_lang=' + encodeURIComponent(lang) +
            '; path=/; Max-Age=31536000; SameSite=Lax' + secure;
    }

    /** One-time cleanup of cookies left behind by the old Google Translate widget. */
    function purgeLegacyWidgetCookies() {
        var host = window.location.hostname;
        var domains = [null, host, '.' + host];
        var paths = ['/', '/user', '/user/', '/e-store', '/e-store/', '/e-learning', '/e-learning/', '/admin', '/admin/'];
        var secure = window.location.protocol === 'https:' ? '; Secure' : '';
        for (var d = 0; d < domains.length; d++) {
            for (var p = 0; p < paths.length; p++) {
                var c = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 GMT; Max-Age=0; path=' + paths[p] + secure;
                if (domains[d]) c += '; domain=' + domains[d];
                document.cookie = c;
            }
        }
        try {
            window.localStorage.removeItem('lr_lang_pending');
            window.sessionStorage.removeItem('lr_gt_clear_paths');
            window.sessionStorage.removeItem('lr_orig_restore_guard');
        } catch (e) {}
    }

    // ── DOM collection ──────────────────────────────────────────────────────

    function collectTextNodes(root, out) {
        var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
            acceptNode: function (node) {
                if (!node.nodeValue || !node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
                if (isSkipped(node.parentElement)) return NodeFilter.FILTER_REJECT;
                return NodeFilter.FILTER_ACCEPT;
            }
        });
        var n;
        while ((n = walker.nextNode())) out.push(n);
    }

    var ATTR_QUERY = '[placeholder],[title],[aria-label],[alt],[data-original-title],' +
        'input[type="submit"],input[type="button"],input[type="reset"]';

    function collectAttrTargets(root, out) {
        if (!root || !root.querySelectorAll) return;

        var elements = [].slice.call(root.querySelectorAll(ATTR_QUERY));
        // querySelectorAll skips the root itself, which matters for observer subtrees.
        if (root.nodeType === 1 && root.matches) {
            try { if (root.matches(ATTR_QUERY)) elements.unshift(root); } catch (e) {}
        }

        for (var i = 0; i < elements.length; i++) {
            var el = elements[i];
            if (isSkipped(el)) continue;

            var isNameField = false;
            try { isNameField = el.matches(NAME_INPUT_SELECTOR); } catch (e) {}

            for (var a = 0; a < TRANSLATABLE_ATTRS.length; a++) {
                var attr = TRANSLATABLE_ATTRS[a];
                if (el.hasAttribute(attr)) out.push({ el: el, attr: attr });
            }
            // Button captions live in `value`; name-field values are user data.
            if (!isNameField && el.tagName === 'INPUT' && /^(submit|button|reset)$/i.test(el.type || '')) {
                out.push({ el: el, attr: 'value' });
            }
        }
    }

    // ── original snapshot / restore ─────────────────────────────────────────

    function rememberText(node) {
        if (!textOriginals.has(node)) textOriginals.set(node, node.nodeValue);
        return textOriginals.get(node);
    }

    function rememberAttr(el, attr) {
        var store = attrOriginals.get(el);
        if (!store) { store = {}; attrOriginals.set(el, store); }
        if (!(attr in store)) store[attr] = el.getAttribute(attr) || '';
        return store[attr];
    }

    function restoreOriginals() {
        applying = true;
        try {
            var textNodes = [];
            collectTextNodes(document.body, textNodes);
            for (var i = 0; i < textNodes.length; i++) {
                var original = textOriginals.get(textNodes[i]);
                if (original !== undefined && textNodes[i].nodeValue !== original) {
                    textNodes[i].nodeValue = original;
                }
            }

            var attrTargets = [];
            collectAttrTargets(document.body, attrTargets);
            for (var j = 0; j < attrTargets.length; j++) {
                var t = attrTargets[j];
                var store = attrOriginals.get(t.el);
                if (store && t.attr in store) t.el.setAttribute(t.attr, store[t.attr]);
            }

            if (titleOriginal !== null) document.title = titleOriginal;
        } finally {
            applying = false;
        }
    }

    // ── network ─────────────────────────────────────────────────────────────

    function postBatch(lang, items) {
        return fetch(ENDPOINT, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ target: lang, items: items })
        }).then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        });
    }

    function reportFailure(reason, lang, extra) {
        if (window.LrTranslationDiagnostics && window.LrTranslationDiagnostics.reportFailure) {
            try { window.LrTranslationDiagnostics.reportFailure(reason, lang, extra || {}); } catch (e) {}
        }
    }

    // ── translation pass ────────────────────────────────────────────────────

    function buildJobs(root, lang) {
        var jobs = [];       // { apply(translated) , norm, hash }
        var needed = {};     // hash -> normalized text (deduped)

        var textNodes = [];
        collectTextNodes(root, textNodes);
        for (var i = 0; i < textNodes.length; i++) {
            (function (node) {
                var original = rememberText(node);
                var norm = normalize(original);
                if (!isTranslatable(norm)) return;
                var h = hash(norm);
                var cached = cacheGet(lang, h);
                if (cached !== undefined) {
                    var padded = padLike(original, cached);
                    if (node.nodeValue !== padded) node.nodeValue = padded;
                    return;
                }
                needed[h] = norm;
                jobs.push({
                    hash: h,
                    apply: function (translated) {
                        node.nodeValue = padLike(original, translated);
                    }
                });
            })(textNodes[i]);
        }

        var attrTargets = [];
        collectAttrTargets(root, attrTargets);
        for (var j = 0; j < attrTargets.length; j++) {
            (function (target) {
                var original = rememberAttr(target.el, target.attr);
                var norm = normalize(original || '');
                if (!isTranslatable(norm)) return;
                var h = hash(norm);
                var cached = cacheGet(lang, h);
                if (cached !== undefined) {
                    target.el.setAttribute(target.attr, cached);
                    return;
                }
                needed[h] = norm;
                jobs.push({
                    hash: h,
                    apply: function (translated) {
                        target.el.setAttribute(target.attr, translated);
                    }
                });
            })(attrTargets[j]);
        }

        return { jobs: jobs, needed: needed };
    }

    function chunkNeeded(needed) {
        var chunks = [];
        var current = [];
        var chars = 0;
        for (var h in needed) {
            if (!Object.prototype.hasOwnProperty.call(needed, h)) continue;
            var text = needed[h];
            if (current.length && (chars + text.length > MAX_CHARS_PER_REQUEST || current.length >= MAX_ITEMS_PER_REQUEST)) {
                chunks.push(current);
                current = [];
                chars = 0;
            }
            current.push({ h: h, text: text });
            chars += text.length;
        }
        if (current.length) chunks.push(current);
        return chunks;
    }

    function runChunks(lang, chunks, onDone) {
        var index = 0;
        var active = 0;
        var finished = false;

        function done() {
            if (!finished && index >= chunks.length && active === 0) {
                finished = true;
                onDone();
            }
        }

        function next() {
            while (active < MAX_PARALLEL_REQUESTS && index < chunks.length) {
                var chunk = chunks[index++];
                active++;
                (function (c) {
                    postBatch(lang, c.map(function (x) { return x.text; }))
                        .then(function (data) {
                            if (data && Array.isArray(data.translations)) {
                                for (var i = 0; i < c.length; i++) {
                                    var value = data.translations[i];
                                    if (typeof value === 'string' && value.length) {
                                        cacheSet(lang, c[i].h, value);
                                    }
                                }
                                if (data.ok === false && data.reason) {
                                    log('passthrough:', data.reason);
                                    reportFailure('translate_' + data.reason, lang, {});
                                }
                            }
                        })
                        .catch(function (err) {
                            log('batch failed', err);
                            reportFailure('translate_request_failed', lang, { message: String(err && err.message || err) });
                        })
                        .then(function () {
                            active--;
                            inFlight = active;
                            next();
                            done();
                        });
                })(chunk);
            }
            done();
        }

        if (!chunks.length) { onDone(); return; }
        next();
    }

    function translatePass(root, lang, onComplete) {
        if (lang === ORIGINAL || !lang) { if (onComplete) onComplete(); return; }

        applying = true;
        var built;
        try {
            built = buildJobs(root || document.body, lang);
        } finally {
            applying = false;
        }

        var chunks = chunkNeeded(built.needed);
        if (!chunks.length) {
            if (onComplete) onComplete();
            return;
        }

        document.documentElement.classList.add('lr-translating');

        runChunks(lang, chunks, function () {
            applying = true;
            try {
                for (var i = 0; i < built.jobs.length; i++) {
                    var job = built.jobs[i];
                    var value = cacheGet(lang, job.hash);
                    if (value !== undefined) job.apply(value);
                }
            } finally {
                applying = false;
            }
            document.documentElement.classList.remove('lr-translating');
            scheduleFlush(lang);
            if (onComplete) onComplete();
        });
    }

    function translateDocumentTitle(lang) {
        if (lang === ORIGINAL) return;
        if (titleOriginal === null) titleOriginal = document.title;
        var norm = normalize(titleOriginal || '');
        if (!isTranslatable(norm)) return;
        var h = hash(norm);
        var cached = cacheGet(lang, h);
        if (cached !== undefined) { document.title = cached; return; }
        postBatch(lang, [norm]).then(function (data) {
            if (data && Array.isArray(data.translations) && data.translations[0]) {
                cacheSet(lang, h, data.translations[0]);
                document.title = data.translations[0];
                scheduleFlush(lang);
            }
        }).catch(function () {});
    }

    // ── mutation handling ───────────────────────────────────────────────────

    function startObserver() {
        if (observer || typeof MutationObserver === 'undefined') return;

        var queue = [];
        var timer = null;

        function flush() {
            timer = null;
            var roots = queue;
            queue = [];
            if (currentLang === ORIGINAL) return;

            for (var i = 0; i < roots.length; i++) {
                var node = roots[i];
                if (!node || !node.isConnected) continue;
                translatePass(node.nodeType === 1 ? node : node.parentElement, currentLang);
            }
        }

        observer = new MutationObserver(function (mutations) {
            if (applying) return;

            for (var i = 0; i < mutations.length; i++) {
                var m = mutations[i];
                if (m.type === 'childList') {
                    for (var j = 0; j < m.addedNodes.length; j++) {
                        var added = m.addedNodes[j];
                        if (added.nodeType === 1 || added.nodeType === 3) queue.push(added);
                    }
                } else if (m.type === 'characterData') {
                    queue.push(m.target.parentElement);
                } else if (m.type === 'attributes' && m.target) {
                    queue.push(m.target);
                }
            }

            if (queue.length && !timer) {
                timer = setTimeout(flush, OBSERVER_DEBOUNCE_MS);
            }
        });

        observer.observe(document.documentElement, {
            childList: true,
            subtree: true,
            characterData: true,
            attributes: true,
            attributeFilter: TRANSLATABLE_ATTRS
        });
    }

    // ── public API ──────────────────────────────────────────────────────────

    var LrTranslate = {
        ORIGINAL: ORIGINAL,

        getLanguage: function () {
            return currentLang;
        },

        isTranslated: function () {
            return currentLang !== ORIGINAL;
        },

        /**
         * Switch language in place — no page reload, no cookie gymnastics.
         * Going back to Original is instant and costs nothing.
         */
        setLanguage: function (lang) {
            var next = (!lang || lang === ORIGINAL || lang === '') ? ORIGINAL : String(lang);

            // Legacy aliases the old widget accepted.
            var alias = { cn: 'zh-CN', us: 'en', uk: 'en' };
            if (alias[next]) next = alias[next];

            if (next === currentLang) return;

            currentLang = next;
            writeStoredLang(next);
            skipCache = new WeakMap();

            // Always restore first: translating an already-translated DOM would
            // compound errors and pay twice for the same content.
            restoreOriginals();

            document.documentElement.setAttribute('lang', next === ORIGINAL ? 'en' : next);
            document.documentElement.setAttribute('dir', isRtl(next) ? 'rtl' : 'ltr');

            if (next === ORIGINAL) {
                translatedOnce = false;
                document.dispatchEvent(new CustomEvent('lr:translated', { detail: { lang: ORIGINAL } }));
                return;
            }

            translatedOnce = true;
            loadLangCache(next);
            translateDocumentTitle(next);
            translatePass(document.body, next, function () {
                document.dispatchEvent(new CustomEvent('lr:translated', { detail: { lang: next } }));
            });
        },

        /** Re-scan a subtree — call after injecting HTML if you bypass the observer. */
        refresh: function (root) {
            if (currentLang === ORIGINAL) return;
            translatePass(root || document.body, currentLang);
        },

        /** Translate arbitrary strings (e.g. JS-built toast messages). */
        translateStrings: function (texts, callback) {
            if (currentLang === ORIGINAL) { callback(texts); return; }
            postBatch(currentLang, texts)
                .then(function (data) { callback(data && data.translations ? data.translations : texts); })
                .catch(function () { callback(texts); });
        },

        clearCache: function () {
            memCache = Object.create(null);
            try {
                for (var i = window.localStorage.length - 1; i >= 0; i--) {
                    var k = window.localStorage.key(i);
                    if (k && k.indexOf('lrtr:') === 0) window.localStorage.removeItem(k);
                }
            } catch (e) {}
        },

        init: function () {
            // A page script may call setLanguage() before DOMContentLoaded fires.
            // Without this guard init() would repeat the whole pass — and pay for it.
            if (initialized) return;
            initialized = true;

            purgeLegacyWidgetCookies();

            if (titleOriginal === null) titleOriginal = document.title;

            // If a page script already switched language before DOMContentLoaded,
            // that pass stands — redoing it here would translate everything twice.
            if (currentLang === ORIGINAL) currentLang = readStoredLang();

            if (currentLang !== ORIGINAL && !translatedOnce) {
                document.documentElement.setAttribute('lang', currentLang);
                document.documentElement.setAttribute('dir', isRtl(currentLang) ? 'rtl' : 'ltr');
                translatedOnce = true;
                loadLangCache(currentLang);
                translateDocumentTitle(currentLang);
                translatePass(document.body, currentLang, function () {
                    document.dispatchEvent(new CustomEvent('lr:translated', { detail: { lang: currentLang } }));
                });
            }

            startObserver();
            bindSwitcher();
        }
    };

    var RTL_LANGS = { ar: 1, he: 1, iw: 1, fa: 1, ur: 1, ps: 1, sd: 1, ug: 1, yi: 1, dv: 1, ckb: 1, 'fa-AF': 1 };

    function isRtl(lang) {
        if (!lang) return false;
        return !!RTL_LANGS[lang] || !!RTL_LANGS[String(lang).split('-')[0]];
    }

    function bindSwitcher() {
        var select = document.getElementById('languageSwitcher');
        if (!select || select.dataset.lrBound === '1') return;
        select.dataset.lrBound = '1';

        var active = currentLang;
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].value === active) { select.selectedIndex = i; break; }
        }
        // Keep the custom cs-select display label in sync.
        var wrapper = select.closest ? select.closest('.cst-select-wrapper') : null;
        var display = wrapper && wrapper.querySelector('.cst-select-content');
        if (display && select.selectedIndex >= 0) {
            display.textContent = select.options[select.selectedIndex].text;
        }

        select.addEventListener('change', function () {
            LrTranslate.setLanguage(select.value);
        });
    }

    // ── back-compat shims for existing call sites (chatbot, diagnostics) ────

    window.changeGoogleTranslateLanguage = function (lang) { LrTranslate.setLanguage(lang); };
    window.forceSelectValue = function (_el, value) { LrTranslate.setLanguage(value); };
    window.getActiveTranslateLang = function () { return currentLang; };
    window.setContentLangCookie = function (lang) { writeStoredLang(lang || ORIGINAL); };

    window.LrTranslate = LrTranslate;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { LrTranslate.init(); });
    } else {
        LrTranslate.init();
    }
})(window, document);
