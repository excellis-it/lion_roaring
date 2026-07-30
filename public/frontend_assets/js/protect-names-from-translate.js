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

    var NAME_INPUT_SELECTORS = [
        'input[name="first_name"]',
        'input[name="last_name"]',
        'input[name="middle_name"]',
        'input[name="user_name"]',
        'input[name="full_name"]',
        'input[name="edit_user_name"]',
        'input[name="edit_first_name"]',
        'input[name="edit_last_name"]',
        'input[id="first_name"]',
        'input[id="last_name"]',
        'input[id="middle_name"]',
        'input[id="user_name"]',
        'input[id="register-first-name"]',
        'input[id="register-last-name"]',
        'textarea[name="first_name"]',
        'textarea[name="last_name"]',
        'textarea[name="full_name"]'
    ].join(',');

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
        root.querySelectorAll(NAME_INPUT_SELECTORS).forEach(protectElement);
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
                    if (node.matches && (node.matches(NAME_NODE_SELECTORS) || node.matches(NAME_INPUT_SELECTORS))) {
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
