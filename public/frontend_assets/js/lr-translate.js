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

    // Google's 128-segment / 30k-char limit applies between Laravel and Google,
    // not between the browser and Laravel. Cache hits never reach Google at all,
    // so we send big batches and let the server split only the misses. A page with
    // 3,000 strings becomes ~2 requests instead of ~25.
    var MAX_ITEMS_PER_REQUEST = 1500;
    var MAX_CHARS_PER_REQUEST = 200000;
    var MAX_PARALLEL_REQUESTS = 2;
    var MIN_REQUEST_GAP_MS = 60;
    var MAX_429_RETRIES = 5;
    var LS_MAX_ENTRIES_PER_LANG = 8000;
    var OBSERVER_DEBOUNCE_MS = 400;

    // After a hard failure a string is not re-requested until this cooldown ends.
    // Without it, an unresolved node is re-collected by the next observer pass and
    // re-requested forever — that feedback loop is what produces a 429 storm.
    var FAILURE_COOLDOWN_MS = 60000;
    // Consecutive request failures before the engine stops trying for a while.
    var CIRCUIT_TRIP_FAILURES = 5;
    var CIRCUIT_OPEN_MS = 60000;

    // ── skip rules (single source of truth) ─────────────────────────────────

    var SKIP_TAGS = {
        SCRIPT: 1, STYLE: 1, NOSCRIPT: 1, TEXTAREA: 1, CODE: 1, PRE: 1, KBD: 1,
        SAMP: 1, VAR: 1, SVG: 1, CANVAS: 1, IFRAME: 1, OBJECT: 1, EMBED: 1,
        MATH: 1, TEMPLATE: 1, I: 1, TITLE: 1, HEAD: 1, META: 1, LINK: 1
    };

    /**
     * Icon fonts render glyphs as text ligatures — translating them destroys the icon.
     * These classes are unambiguous, so anything inside them is skipped outright.
     */
    var ICON_SELECTOR = [
        '.material-icons', '.material-icons-outlined', '.material-icons-round',
        '.material-icons-sharp', '.material-icons-two-tone',
        '.material-symbols-outlined', '.material-symbols-rounded', '.material-symbols-sharp',
        '.fa', '.fas', '.far', '.fab', '.fal', '.fad', '.fa-solid', '.fa-regular', '.fa-brands',
        '.bi', '.glyphicon', '.iconify', '.feather', '.bx',
        '[class*="fa-"]'
    ].join(',');

    /**
     * Class names that merely *mention* an icon — `.icon-heading`, `.has-icon`,
     * `.upload-icon-wrap`. Treating these as icons hid real copy: the e-Store contact
     * headings ("Mail us", "Our Address") and every `.form-control.has-icon`
     * placeholder silently refused to translate. So text under these is skipped only
     * when it actually looks like a ligature — one token, no spaces.
     */
    var LOOSE_ICON_SELECTOR = [
        '.icon', '.ti', '[class*="icon-"]', '[class^="icon"]', '[class*="-icon"]', '[class*="_icon"]'
    ].join(',');

    var MAX_LIGATURE_LENGTH = 24;

    function looksLikeLigature(text) {
        var t = text.trim();
        return t.length > 0 && t.length <= MAX_LIGATURE_LENGTH && !/\s/.test(t);
    }

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
    var titleOriginal = null;
    var initialized = false;
    var translatedOnce = false;

    // Progress badge — blinking indicator only (no percentage).
    // MutationObserver refreshes must NOT own the badge.
    var badgeEl = null;
    var badgeHideTimer = null;
    var badgeTracking = false;
    var progressPassId = 0;

    // Global batch queue: serializes all /translate/batch traffic site-wide.
    var batchQueue = [];
    var batchActive = 0;
    var batchLastAt = 0;
    var passBusy = 0; // >0 while a translatePass is collecting/applying/networked

    // Per-hash request state — the guard that makes repeated DOM scans free.
    // "lang|hash" -> true while a request carrying it is in flight
    var hashPending = Object.create(null);
    // "lang|hash" -> timestamp until which we refuse to re-request it
    var hashCooldown = Object.create(null);
    // "lang|hash" -> [job, …] discovered while the hash was already in flight
    var hashWaiters = Object.create(null);

    var consecutiveFailures = 0;
    var circuitOpenUntil = 0;

    // Bumped on every language switch. A response that was already in flight when
    // the user switched belongs to the old selection and must not touch the DOM —
    // otherwise picking Original re-translates the page a moment after restoring it.
    var langEpoch = 0;

    function isCurrentEpoch(epoch) {
        return epoch === langEpoch;
    }

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

    // ── progress badge ──────────────────────────────────────────────────────

    function ensureBadge() {
        if (badgeEl && badgeEl.isConnected) return badgeEl;

        badgeEl = document.getElementById('lr-translate-progress');
        if (!badgeEl) {
            badgeEl = document.createElement('span');
            badgeEl.id = 'lr-translate-progress';
            badgeEl.className = 'lr-translate-progress notranslate';
            badgeEl.setAttribute('translate', 'no');
            badgeEl.setAttribute('data-nt', '');
            badgeEl.setAttribute('aria-live', 'polite');
            badgeEl.hidden = true;

            var slot = document.querySelector('[data-lr-translate-badge]');
            if (slot) {
                slot.appendChild(badgeEl);
            } else {
                badgeEl.classList.add('lr-translate-progress--fixed');
                (document.body || document.documentElement).appendChild(badgeEl);
            }
        }
        return badgeEl;
    }

    function hideBadge() {
        if (badgeHideTimer) {
            clearTimeout(badgeHideTimer);
            badgeHideTimer = null;
        }
        badgeTracking = false;
        var el = ensureBadge();
        el.hidden = true;
        el.classList.remove('is-active');
        el.textContent = '';
    }

    function setBadgeActive(on) {
        if (!badgeTracking && on) return;
        var el = ensureBadge();
        if (!on || currentLang === ORIGINAL) {
            hideBadge();
            return;
        }
        el.hidden = false;
        el.classList.add('is-active');
        el.textContent = 'Translating…';
    }

    function beginBadgeTracking() {
        if (badgeHideTimer) {
            clearTimeout(badgeHideTimer);
            badgeHideTimer = null;
        }
        badgeTracking = true;
        setBadgeActive(true);
    }

    function finishBadgeTracking() {
        if (!badgeTracking) return;
        if (badgeHideTimer) clearTimeout(badgeHideTimer);
        badgeHideTimer = setTimeout(function () {
            hideBadge();
        }, 500);
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

    /** Loose icon container whose text is ligature-shaped — an icon, not a sentence. */
    function isLigatureIcon(el, text) {
        if (!el || el.nodeType !== 1 || !el.matches) return false;
        if (!looksLikeLigature(text)) return false;
        try {
            return el.matches(LOOSE_ICON_SELECTOR);
        } catch (e) {
            return false;
        }
    }

    function collectTextNodes(root, out) {
        var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
            acceptNode: function (node) {
                if (!node.nodeValue || !node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
                if (isSkipped(node.parentElement)) return NodeFilter.FILTER_REJECT;
                // Judge on the ORIGINAL text, never the current value. "Mail us"
                // translates to the single word "Correo"; testing the translation
                // would classify it as a ligature and Original could never restore it.
                var basis = textOriginals.has(node) ? textOriginals.get(node) : node.nodeValue;
                if (isLigatureIcon(node.parentElement, basis)) return NodeFilter.FILTER_REJECT;
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

    // ── network (queued, Google-friendly batching, 429 backoff) ─────────────

    function sleep(ms) {
        return new Promise(function (resolve) { setTimeout(resolve, ms); });
    }

    function postBatchRaw(lang, items) {
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
            if (res.status === 429) {
                var err = new Error('HTTP 429');
                err.status = 429;
                err.retryAfter = parseInt(res.headers.get('Retry-After') || '0', 10) || 0;
                throw err;
            }
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        });
    }

    function postBatchWithRetry(lang, items, attempt) {
        attempt = attempt || 0;
        return postBatchRaw(lang, items).catch(function (err) {
            if (err && err.status === 429 && attempt < MAX_429_RETRIES) {
                var backoff = err.retryAfter
                    ? err.retryAfter * 1000
                    : Math.min(30000, 400 * Math.pow(2, attempt) + Math.floor(Math.random() * 250));
                log('429 backoff', backoff + 'ms', 'attempt', attempt + 1);
                return sleep(backoff).then(function () {
                    return postBatchWithRetry(lang, items, attempt + 1);
                });
            }
            throw err;
        });
    }

    function drainBatchQueue() {
        if (batchActive >= MAX_PARALLEL_REQUESTS || batchQueue.length === 0) return;

        var wait = Math.max(0, MIN_REQUEST_GAP_MS - (Date.now() - batchLastAt));
        setTimeout(function () {
            if (batchActive >= MAX_PARALLEL_REQUESTS || batchQueue.length === 0) return;

            var job = batchQueue.shift();
            batchActive++;
            batchLastAt = Date.now();

            postBatchWithRetry(job.lang, job.items)
                .then(function (data) {
                    job.resolve(data);
                })
                .catch(function (err) {
                    job.reject(err);
                })
                .then(function () {
                    batchActive--;
                    drainBatchQueue();
                });
        }, wait);
    }

    /** Enqueue a batch; all site traffic shares one serial queue. */
    function postBatch(lang, items) {
        return new Promise(function (resolve, reject) {
            batchQueue.push({ lang: lang, items: items, resolve: resolve, reject: reject });
            drainBatchQueue();
        });
    }

    function reportFailure(reason, lang, extra) {
        if (window.LrTranslationDiagnostics && window.LrTranslationDiagnostics.reportFailure) {
            try { window.LrTranslationDiagnostics.reportFailure(reason, lang, extra || {}); } catch (e) {}
        }
    }

    // ── per-hash request state ──────────────────────────────────────────────

    function stateKey(lang, h) { return lang + '|' + h; }

    function circuitOpen() {
        return Date.now() < circuitOpenUntil;
    }

    function noteRequestSuccess() {
        consecutiveFailures = 0;
        circuitOpenUntil = 0;
    }

    function noteRequestFailure(lang) {
        consecutiveFailures++;
        if (consecutiveFailures >= CIRCUIT_TRIP_FAILURES && !circuitOpen()) {
            circuitOpenUntil = Date.now() + CIRCUIT_OPEN_MS;
            log('circuit open for', CIRCUIT_OPEN_MS + 'ms after', consecutiveFailures, 'failures');
            reportFailure('translate_request_failed', lang, { circuitOpen: true, failures: consecutiveFailures });
        }
    }

    /** Can this hash be added to a new request right now? */
    function isRequestable(lang, h) {
        var key = stateKey(lang, h);
        if (hashPending[key]) return false;
        var until = hashCooldown[key];
        if (until && Date.now() < until) return false;
        if (until) delete hashCooldown[key];
        return true;
    }

    function markPending(lang, hashes) {
        for (var i = 0; i < hashes.length; i++) hashPending[stateKey(lang, hashes[i])] = true;
    }

    /** Release in-flight hashes; on failure put them in cooldown so no pass retries them. */
    function releasePending(lang, hashes, failed) {
        var now = Date.now();
        for (var i = 0; i < hashes.length; i++) {
            var key = stateKey(lang, hashes[i]);
            delete hashPending[key];
            if (failed) {
                hashCooldown[key] = now + FAILURE_COOLDOWN_MS;
            }
            delete hashWaiters[key];
        }
    }

    /** A later pass found the same string mid-flight — queue it for the same result. */
    function addWaiter(lang, h, job) {
        var key = stateKey(lang, h);
        if (!hashWaiters[key]) hashWaiters[key] = [];
        hashWaiters[key].push(job);
    }

    function flushWaiters(lang, h, value, epoch) {
        var key = stateKey(lang, h);
        var list = hashWaiters[key];
        if (!list) return;
        delete hashWaiters[key];
        if (!isCurrentEpoch(epoch)) return;
        applying = true;
        try {
            for (var i = 0; i < list.length; i++) list[i].apply(value);
        } finally {
            applying = false;
        }
    }

    // ── translation pass ────────────────────────────────────────────────────

    function buildJobs(root, lang) {
        var jobs = [];       // { apply(translated) , hash }
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
                    if (node.nodeValue !== padded) {
                        applying = true;
                        try { node.nodeValue = padded; } finally { applying = false; }
                    }
                    return;
                }
                var job = {
                    hash: h,
                    apply: function (translated) {
                        node.nodeValue = padLike(original, translated);
                    }
                };
                // Already in flight or cooling down after a failure: never re-request.
                if (!isRequestable(lang, h)) {
                    if (hashPending[stateKey(lang, h)]) addWaiter(lang, h, job);
                    return;
                }
                needed[h] = norm;
                jobs.push(job);
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
                    if (target.el.getAttribute(target.attr) !== cached) {
                        applying = true;
                        try { target.el.setAttribute(target.attr, cached); } finally { applying = false; }
                    }
                    return;
                }
                var job = {
                    hash: h,
                    apply: function (translated) {
                        target.el.setAttribute(target.attr, translated);
                    }
                };
                if (!isRequestable(lang, h)) {
                    if (hashPending[stateKey(lang, h)]) addWaiter(lang, h, job);
                    return;
                }
                needed[h] = norm;
                jobs.push(job);
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

    function runChunks(lang, epoch, chunks, jobsByHash, onDone) {
        var index = 0;
        var active = 0;
        var finished = false;

        function done() {
            if (!finished && index >= chunks.length && active === 0) {
                finished = true;
                onDone();
            }
        }

        function applyHash(h, value) {
            // The result is still cached by the caller — it was paid for — but it is
            // only written to the page while the selection it belongs to is current.
            if (!isCurrentEpoch(epoch)) return;
            var list = jobsByHash[h] || [];
            applying = true;
            try {
                for (var i = 0; i < list.length; i++) {
                    list[i].apply(value);
                }
            } finally {
                applying = false;
            }
        }

        function next() {
            while (active < MAX_PARALLEL_REQUESTS && index < chunks.length) {
                var chunk = chunks[index++];
                active++;
                (function (c) {
                    var hashes = c.map(function (x) { return x.h; });
                    markPending(lang, hashes);

                    // Circuit open: fail fast into cooldown instead of piling on.
                    if (circuitOpen()) {
                        releasePending(lang, hashes, true);
                        active--;
                        next();
                        done();
                        return;
                    }

                    var succeeded = false;
                    var unresolved = [];

                    postBatch(lang, c.map(function (x) { return x.text; }))
                        .then(function (data) {
                            if (!data || !Array.isArray(data.translations)) return;

                            var ok = data.ok !== false;
                            if (!ok && data.reason) {
                                log('passthrough:', data.reason);
                                reportFailure('translate_' + data.reason, lang, {});
                                // A refusal (unsupported language, disabled, too large)
                                // will not change on retry — cool these down.
                                return;
                            }

                            if (data.degraded) log('degraded:', data.degraded);

                            succeeded = true;
                            noteRequestSuccess();
                            for (var i = 0; i < c.length; i++) {
                                var value = data.translations[i];
                                // null = the server could not translate it. Caching the
                                // source text here would permanently "translate" the
                                // string to itself in this browser.
                                if (typeof value === 'string' && value.length) {
                                    cacheSet(lang, c[i].h, value);
                                    applyHash(c[i].h, value);
                                    flushWaiters(lang, c[i].h, value, epoch);
                                } else {
                                    unresolved.push(c[i].h);
                                }
                            }
                        })
                        .catch(function (err) {
                            log('batch failed', err);
                            noteRequestFailure(lang);
                            reportFailure('translate_request_failed', lang, { message: String(err && err.message || err) });
                        })
                        .then(function () {
                            releasePending(lang, hashes, !succeeded);
                            // Items the server declined stay in cooldown too, so a
                            // permanently untranslatable string is not re-sent on
                            // every observer pass.
                            if (succeeded && unresolved.length) {
                                releasePending(lang, unresolved, true);
                            }
                            active--;
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

    /**
     * @param {Element} root
     * @param {string} lang
     * @param {Function} [onComplete]
     * @param {{ trackBadge?: boolean }} [options]
     */
    function translatePass(root, lang, onComplete, options) {
        options = options || {};
        var trackBadge = !!options.trackBadge;

        if (lang === ORIGINAL || !lang) {
            if (trackBadge) hideBadge();
            if (onComplete) onComplete();
            return;
        }

        passBusy++;
        var epoch = langEpoch;
        applying = true;
        var built;
        try {
            built = buildJobs(root || document.body, lang);
        } finally {
            applying = false;
        }

        var passId = trackBadge ? ++progressPassId : progressPassId;
        var chunks = chunkNeeded(built.needed);

        if (!chunks.length) {
            passBusy = Math.max(0, passBusy - 1);
            if (trackBadge) finishBadgeTracking();
            if (onComplete) onComplete();
            return;
        }

        if (trackBadge) {
            setBadgeActive(true);
            document.documentElement.classList.add('lr-translating');
        }

        var jobsByHash = {};
        for (var i = 0; i < built.jobs.length; i++) {
            var job = built.jobs[i];
            if (!jobsByHash[job.hash]) jobsByHash[job.hash] = [];
            jobsByHash[job.hash].push(job);
        }

        runChunks(lang, epoch, chunks, jobsByHash, function () {
            if ((trackBadge && passId !== progressPassId) || !isCurrentEpoch(epoch)) {
                if (trackBadge) {
                    document.documentElement.classList.remove('lr-translating');
                    finishBadgeTracking();
                }
                passBusy = Math.max(0, passBusy - 1);
                if (onComplete) onComplete();
                return;
            }
            applying = true;
            try {
                for (var j = 0; j < built.jobs.length; j++) {
                    var pending = built.jobs[j];
                    var value = cacheGet(lang, pending.hash);
                    if (value !== undefined) pending.apply(value);
                }
            } finally {
                applying = false;
            }
            if (trackBadge) {
                document.documentElement.classList.remove('lr-translating');
                finishBadgeTracking();
            }
            scheduleFlush(lang);
            passBusy = Math.max(0, passBusy - 1);
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

        var epoch = langEpoch;
        postBatch(lang, [norm]).then(function (data) {
            if (data && data.ok !== false && Array.isArray(data.translations) && data.translations[0]) {
                cacheSet(lang, h, data.translations[0]);
                scheduleFlush(lang);
                if (isCurrentEpoch(epoch)) document.title = data.translations[0];
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
            // Wait until the main/queued pass finishes so we don't stampede /translate/batch.
            if (passBusy > 0 || batchActive > 0 || batchQueue.length > 0) {
                timer = setTimeout(flush, OBSERVER_DEBOUNCE_MS);
                return;
            }
            var roots = queue;
            queue = [];
            if (currentLang === ORIGINAL) return;

            // Coalesce many tiny mutations into one subtree union when possible.
            var seen = [];
            for (var i = 0; i < roots.length; i++) {
                var node = roots[i];
                if (!node || !node.isConnected) continue;
                var el = node.nodeType === 1 ? node : node.parentElement;
                if (!el) continue;
                var covered = false;
                for (var s = 0; s < seen.length; s++) {
                    if (seen[s].contains && seen[s].contains(el)) { covered = true; break; }
                    if (el.contains && el.contains(seen[s])) { seen[s] = el; covered = true; break; }
                }
                if (!covered) seen.push(el);
            }
            for (var r = 0; r < seen.length; r++) {
                translatePass(seen[r], currentLang);
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

            // Invalidate anything already in flight for the previous selection.
            langEpoch++;
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
                hideBadge();
                document.dispatchEvent(new CustomEvent('lr:translated', { detail: { lang: ORIGINAL } }));
                return;
            }

            translatedOnce = true;
            loadLangCache(next);
            beginBadgeTracking();
            translateDocumentTitle(next);
            translatePass(document.body, next, function () {
                document.dispatchEvent(new CustomEvent('lr:translated', { detail: { lang: next } }));
            }, { trackBadge: true });
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
                .then(function (data) {
                    if (data && data.ok !== false && data.translations) callback(data.translations);
                    else callback(texts);
                })
                .catch(function () { callback(texts); });
        },

        clearCache: function () {
            memCache = Object.create(null);
            hashPending = Object.create(null);
            hashCooldown = Object.create(null);
            hashWaiters = Object.create(null);
            consecutiveFailures = 0;
            circuitOpenUntil = 0;
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
            ensureBadge();

            if (titleOriginal === null) titleOriginal = document.title;

            // If a page script already switched language before DOMContentLoaded,
            // that pass stands — redoing it here would translate everything twice.
            if (currentLang === ORIGINAL) currentLang = readStoredLang();

            if (currentLang !== ORIGINAL && !translatedOnce) {
                document.documentElement.setAttribute('lang', currentLang);
                document.documentElement.setAttribute('dir', isRtl(currentLang) ? 'rtl' : 'ltr');
                translatedOnce = true;
                loadLangCache(currentLang);
                beginBadgeTracking();
                translateDocumentTitle(currentLang);
                translatePass(document.body, currentLang, function () {
                    document.dispatchEvent(new CustomEvent('lr:translated', { detail: { lang: currentLang } }));
                }, { trackBadge: true });
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
