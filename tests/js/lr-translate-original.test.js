/**
 * Regression test: Original must always return the AUTHOR's words.
 *
 * Reported failure: a Spanish bulletin ("vestido Real") shown correctly on first
 * load, translated to English correctly, but Original afterwards did not bring the
 * Spanish back. Root cause was server-side bulletin translation — the page arrived
 * already in English whenever `content_lang` was set, so the engine snapshotted the
 * English as the "original". Server-side bulletin translation has been removed;
 * this locks in the behaviour from the client side.
 *
 *   npm install --no-save jsdom && node tests/js/lr-translate-original.test.js
 */
const fs = require('fs');
const path = require('path');
const { JSDOM } = require('jsdom');

const ENGINE = path.join(__dirname, '../../public/frontend_assets/js/lr-translate.js');

const SPANISH_TITLE = 'vestido Real';
const SPANISH_BODY = 'El vestido Real es vida limpia recta y consagrada vestido en la nueva naturaleza.';
const ENGLISH_BODY = 'Everyone, please check the bulletin for the latest announcements and plans.';

const HTML = `<!doctype html><html><head><title>Bulletins</title></head><body>
  <div class="bulletin">
    <p class="name_bull" id="author">Pablo Hugo Chia</p>
    <h4 id="title">${SPANISH_TITLE}</h4>
    <p id="body">${SPANISH_BODY}</p>
  </div>
  <div class="bulletin">
    <p class="name_bull" id="author2">Daud Santosa</p>
    <p id="body2">${ENGLISH_BODY}</p>
  </div>
</body></html>`;

// The page is served with a language already selected — the case that used to break.
const dom = new JSDOM(HTML, { url: 'https://site.test/user/bulletin-board', pretendToBeVisual: true, runScripts: 'dangerously' });
const { window } = dom;

window.fetch = function (url, opts) {
  const body = JSON.parse(opts.body);
  return Promise.resolve({
    ok: true,
    json: () => Promise.resolve({
      ok: true,
      target: body.target,
      translations: body.items.map(t => 'EN<' + t + '>')
    })
  });
};

window.LR_TRANSLATE_CONFIG = { endpoint: '/translate/batch', csrf: 't', cacheVersion: 'test' };
// Simulates arriving on the page with English already chosen in a previous visit.
window.localStorage.setItem('lr_content_lang', 'en');

const tag = window.document.createElement('script');
tag.textContent = fs.readFileSync(ENGINE, 'utf8');
window.document.body.appendChild(tag);

const $ = id => window.document.getElementById(id);
const results = [];
const check = (label, pass, detail) => results.push({ label, pass, detail: detail || '' });

setTimeout(() => {
  check('page auto-translated on load', $('body').textContent.startsWith('EN<'), $('body').textContent.slice(0, 40));
  check('author name never translated', $('author').textContent === 'Pablo Hugo Chia', $('author').textContent);

  window.LrTranslate.setLanguage('__original__');

  setTimeout(() => {
    check('Original restores the Spanish title', $('title').textContent === SPANISH_TITLE, $('title').textContent);
    check('Original restores the Spanish body', $('body').textContent === SPANISH_BODY, $('body').textContent.slice(0, 60));
    check('Original restores the English post untouched', $('body2').textContent === ENGLISH_BODY, $('body2').textContent.slice(0, 60));
    check('author still intact', $('author').textContent === 'Pablo Hugo Chia', $('author').textContent);

    // Full round trip a second time — the reported sequence.
    window.LrTranslate.setLanguage('en');
    setTimeout(() => {
      check('second switch to English works', $('body').textContent.startsWith('EN<'), $('body').textContent.slice(0, 40));
      window.LrTranslate.setLanguage('__original__');
      setTimeout(() => {
        check('second Original still returns Spanish', $('body').textContent === SPANISH_BODY, $('body').textContent.slice(0, 60));

        const failed = results.filter(r => !r.pass);
        results.forEach(r => console.log((r.pass ? 'PASS  ' : 'FAIL  ') + r.label + (r.pass ? '' : '  → ' + r.detail)));
        console.log('\n' + (results.length - failed.length) + '/' + results.length + ' passed');
        process.exit(failed.length ? 1 : 0);
      }, 400);
    }, 400);
  }, 400);
}, 600);
