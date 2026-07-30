/**
 * Regression test: an item the server could not translate must not be cached as
 * its own translation, and must not be re-requested on every observer pass.
 *
 *   npm install --no-save jsdom && node tests/js/lr-translate-nullsafe.test.js
 */
const fs = require('fs'), path = require('path'), { JSDOM } = require('jsdom');
const ENGINE = path.join(__dirname, '../../public/frontend_assets/js/lr-translate.js');

const OK_TEXT = 'The council will meet on the first Monday of the month.';
const BAD_TEXT = 'This particular sentence cannot be translated by the server.';

const HTML = `<!doctype html><html><head><title>T</title></head><body>
  <p id="ok">${OK_TEXT}</p><p id="bad">${BAD_TEXT}</p><div id="churn"></div></body></html>`;

const dom = new JSDOM(HTML, { url: 'https://s.test/', pretendToBeVisual: true, runScripts: 'dangerously' });
const w = dom.window;
let sentBad = 0;

w.fetch = (u, o) => {
  const b = JSON.parse(o.body);
  b.items.forEach(t => { if (t === BAD_TEXT) sentBad++; });
  return Promise.resolve({ ok: true, json: () => Promise.resolve({
    ok: true, target: b.target,
    // Server declines one string: returns null, exactly like the real endpoint.
    translations: b.items.map(t => t === BAD_TEXT ? null : '[es]' + t)
  })});
};
w.LR_TRANSLATE_CONFIG = { endpoint: '/t', csrf: 'x', cacheVersion: 't' };
w.localStorage.setItem('lr_content_lang', '__original__');
const s = w.document.createElement('script'); s.textContent = fs.readFileSync(ENGINE, 'utf8'); w.document.body.appendChild(s);

w.LrTranslate.setLanguage('es');

let bursts = 0;
const churn = setInterval(() => {
  bursts++;
  const el = w.document.createElement('span'); el.textContent = 'filler ' + bursts;
  w.document.getElementById('churn').appendChild(el);
  w.LrTranslate.refresh();
}, 150);

setTimeout(() => {
  clearInterval(churn);
  setTimeout(() => {
    const r = [], ck = (l, p, d) => r.push({ l, p, d: d || '' });
    ck('translatable string translated', w.document.getElementById('ok').textContent === '[es]' + OK_TEXT);
    ck('declined string left as source', w.document.getElementById('bad').textContent === BAD_TEXT, w.document.getElementById('bad').textContent);
    ck('declined string not cached as itself', !JSON.stringify(w.localStorage.getItem('lrtr:t:es') || '').includes('cannot be translated'), 'localStorage');
    ck('declined string not re-sent every pass', sentBad <= 2, sentBad + ' sends across ' + bursts + ' bursts');
    w.LrTranslate.setLanguage('__original__');
    setTimeout(() => {
      ck('Original restores translated string', w.document.getElementById('ok').textContent === OK_TEXT, w.document.getElementById('ok').textContent);
      const f = r.filter(x => !x.p);
      r.forEach(x => console.log((x.p ? 'PASS  ' : 'FAIL  ') + x.l + (x.p ? '  (' + x.d + ')' : '  → ' + x.d)));
      console.log('\n' + (r.length - f.length) + '/' + r.length + ' passed');
      process.exit(f.length ? 1 : 0);
    }, 400);
  }, 1200);
}, 1800);
