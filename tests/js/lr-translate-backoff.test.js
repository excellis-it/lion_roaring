/**
 * Regression test for the 429 storm.
 *
 * The failure mode: a batch fails, its nodes keep their original text, the next
 * MutationObserver pass re-collects them, re-requests them, and the loop feeds
 * itself until the console fills with "Too Many Requests".
 *
 * This asserts that repeated DOM churn after a failure does NOT produce more
 * requests for the same strings — the per-hash cooldown and circuit breaker hold.
 *
 *   npm install --no-save jsdom && node tests/js/lr-translate-backoff.test.js
 */
const fs = require('fs');
const path = require('path');
const { JSDOM } = require('jsdom');

const ENGINE = path.join(__dirname, '../../public/frontend_assets/js/lr-translate.js');

const rows = [];
for (let i = 0; i < 40; i++) {
  rows.push(`<p class="row">Bulletin notice number ${i} for the standing council review.</p>`);
}

const HTML = `<!doctype html><html><head><title>Bulletins</title></head>
<body><div id="list">${rows.join('')}</div></body></html>`;

const dom = new JSDOM(HTML, { url: 'https://site.test/user/bulletin-board', pretendToBeVisual: true, runScripts: 'dangerously' });
const { window } = dom;

let requestCount = 0;
let sentStrings = 0;

// Every request fails with 429, exactly like the reported incident.
window.fetch = function (url, opts) {
  requestCount++;
  try { sentStrings += JSON.parse(opts.body).items.length; } catch (e) {}
  return Promise.resolve({
    ok: false,
    status: 429,
    headers: { get: () => null },
    json: () => Promise.resolve({})
  });
};

window.LR_TRANSLATE_CONFIG = { endpoint: '/translate/batch', csrf: 't', cacheVersion: 'test' };
window.localStorage.setItem('lr_content_lang', '__original__');

const tag = window.document.createElement('script');
tag.textContent = fs.readFileSync(ENGINE, 'utf8');
window.document.body.appendChild(tag);

window.LrTranslate.setLanguage('es');

// Churn the DOM repeatedly — each burst would previously start a fresh round of
// requests for the same untranslated strings.
let bursts = 0;
const churn = setInterval(() => {
  bursts++;
  const el = window.document.createElement('span');
  el.textContent = 'filler ' + bursts;
  window.document.getElementById('list').appendChild(el);
  window.LrTranslate.refresh();
}, 120);

setTimeout(() => {
  clearInterval(churn);

  setTimeout(() => {
    const results = [];
    const check = (label, pass, detail) => results.push({ label, pass, detail: detail || '' });

    // Retries are bounded (MAX_429_RETRIES) and the circuit trips; without the
    // cooldown this climbed with every burst and never stopped.
    check('request count stays bounded', requestCount <= 40, requestCount + ' requests after ' + bursts + ' DOM bursts');
    check('did not re-send the same strings endlessly', sentStrings <= 2000, sentStrings + ' strings sent');
    check('page still readable in source language',
      window.document.querySelector('.row').textContent.startsWith('Bulletin notice number 0'),
      window.document.querySelector('.row').textContent);
    check('engine still responsive after failures', typeof window.LrTranslate.setLanguage === 'function');

    // Original must still work even though every request failed.
    window.LrTranslate.setLanguage('__original__');
    check('Original restores after failure storm',
      window.document.querySelector('.row').textContent.startsWith('Bulletin notice number 0'));

    const failed = results.filter(r => !r.pass);
    results.forEach(r => console.log((r.pass ? 'PASS  ' : 'FAIL  ') + r.label + (r.pass ? '  (' + r.detail + ')' : '  → ' + r.detail)));
    console.log('\n' + (results.length - failed.length) + '/' + results.length + ' passed');
    process.exit(failed.length ? 1 : 0);
  }, 1500);
}, 2000);
