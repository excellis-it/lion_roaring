/**
 * Prevent Google Website Translator from altering person names / name fields.
 * Complements server-side no_translate() wrappers on rendered name text.
 */
(function () {
    'use strict';

    var NAME_NODE_SELECTORS = [
        '.GroupName',
        '.namemember',
        '.name_bull',
        '.person-name',
        '.profile-name',
        '.user-name',
        '.username',
        '.member-name',
        '.author-name',
        '.full-name',
        '[data-nt]',
        '[data-person-name]',
        '[data-user-name]'
    ].join(',');

    /*
     * Name INPUTS are deliberately not marked any more.
     *
     * The old Google widget rewrote input values, so the whole field had to be
     * flagged `notranslate`. LrTranslate never touches a text input's value — it
     * only translates submit/button captions, and it already excludes name fields
     * there. Flagging the element made `isSkipped()` true for the element as a
     * whole, which also suppressed its **placeholder** and `title`: that is why
     * "Middle Name" stayed English on a fully translated profile page.
     */

    function protectElement(el) {
        if (!el || el.nodeType !== 1) {
            return;
        }
        if (!el.classList.contains('notranslate')) {
            el.classList.add('notranslate');
        }
        if (el.getAttribute('translate') !== 'no') {
            el.setAttribute('translate', 'no');
        }
    }

    function scan(root) {
        if (!root || typeof root.querySelectorAll !== 'function') {
            return;
        }
        root.querySelectorAll(NAME_NODE_SELECTORS).forEach(protectElement);
    }

    function boot() {
        scan(document);

        if (typeof MutationObserver === 'undefined') {
            return;
        }

        var observer = new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var nodes = mutations[i].addedNodes;
                for (var j = 0; j < nodes.length; j++) {
                    var node = nodes[j];
                    if (node.nodeType !== 1) {
                        continue;
                    }
                    if (node.matches && node.matches(NAME_NODE_SELECTORS)) {
                        protectElement(node);
                    }
                    scan(node);
                }
            }
        });

        observer.observe(document.documentElement, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
