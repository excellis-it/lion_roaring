/**
 * Regression test: a language switch must cancel the previous language's writes.
 *
 * Failure mode: batches already in flight resolve AFTER the user has switched away
 * and apply their text unconditionally. Switching to Original mid-flight therefore
 * re-translated the page a moment after it had been restored, and switching
 * es -> fr could leave Spanish fragments in a French page.
 *
 *   npm install --no-save jsdom && node tests/js/lr-translate-race.test.js
 */
const fs = require('fs');
const path = require('path');
const { JSDOM } = require('jsdom');

const ENGINE = path.join(__dirname, '../../public/frontend_assets/js/lr-translate.js');

const BODY_TEXT = 'The council will publish the quarterly report next week.';
const TITLE_TEXT = 'Standing committee notice for all registered members.';

const HTML = `<!doctype html><html><head><title>Board</title></head><body>
  <h1 id="t">${TITLE_TEXT}</h1>
  <p id="b">${BODY_TEXT}</p>
</body></html>`;

const dom = new JSDOM(HTML, { url: 'https://site.test/user/board', pretendToBeVisual: true, runScripts: 'dangerously' });
const { window } = dom;

// Slow responses so a language switch always lands while a batch is in flight.
const RESPONSE_DELAY_MS = 700;
window.fetch = function (url, opts) {
  const body = JSON.parse(opts.body);
  return new Promise(resolve => {
    setTimeout(() => resolve({
      ok: true,
      json: () => Promise.resolve({
        ok: true,
        target: body.target,
        translations: body.items.map(t => '[' + body.target + ']' + t)
      })
    }), RESPONSE_DELAY_MS);
  });
};

window.LR_TRANSLATE_CONFIG = { endpoint: '/translate/batch', csrf: 't', cacheVersion: 'test' };
window.localStorage.setItem('lr_content_lang', '__original__');

const tag = window.document.createElement('script');
tag.textContent = fs.readFileSync(ENGINE, 'utf8');
window.document.body.appendChild(tag);

const $ = id => window.document.getElementById(id);
const results = [];
const check = (label, pass, detail) => results.push({ label, pass, detail: detail || '' });

// Scenario 1: switch to Spanish, then back to Original before the response lands.
window.LrTranslate.setLanguage('es');
setTimeout(() => window.LrTranslate.setLanguage('__original__'), 150);

setTimeout(() => {
  check('Original survives a late Spanish response (body)', $('b').textContent === BODY_TEXT, $('b').textContent);
  check('Original survives a late Spanish response (heading)', $('t').textContent === TITLE_TEXT, $('t').textContent);
  check('language is still Original', window.LrTranslate.getLanguage() === '__original__', window.LrTranslate.getLanguage());

  // Scenario 2: es -> fr mid-flight. No Spanish may remain once French settles.
  window.LrTranslate.setLanguage('es');
  setTimeout(() => window.LrTranslate.setLanguage('fr'), 150);

  setTimeout(() => {
    const body = $('b').textContent;
    const head = $('t').textContent;
    check('no stale Spanish left in the French page (body)', body.indexOf('[es]') === -1, body);
    check('no stale Spanish left in the French page (heading)', head.indexOf('[es]') === -1, head);
    check('French actually applied', body.indexOf('[fr]') === 0, body);

    // And Original still returns the author's text after all that churn.
    window.LrTranslate.setLanguage('__original__');
    setTimeout(() => {
      check('Original still exact after two races', $('b').textContent === BODY_TEXT, $('b').textContent);

      const failed = results.filter(r => !r.pass);
      results.forEach(r => console.log((r.pass ? 'PASS  ' : 'FAIL  ') + r.label + (r.pass ? '' : '  → ' + r.detail)));
      console.log('\n' + (results.length - failed.length) + '/' + results.length + ' passed');
      process.exit(failed.length ? 1 : 0);
    }, 1600);
  }, 2200);
}, 1600);
