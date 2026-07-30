/**
 * Regression test: hyphenated words and form placeholders must translate, while
 * the user's own name data must not.
 *
 * Reported: on a fully Chinese-translated profile page, "E-Learning"/"E-Store" in
 * the nav and sidebar stayed English, and the "Middle Name" placeholder stayed
 * English. Causes were (a) the identifier heuristic treating any hyphenated token
 * as a slug and (b) protect-names flagging the whole input, which also suppressed
 * its placeholder.
 *
 *   npm install --no-save jsdom && node tests/js/lr-translate-fields.test.js
 */
const fs = require('fs'), path = require('path'), { JSDOM } = require('jsdom');
const ROOT = path.join(__dirname, '../..');
const ENGINE = path.join(ROOT, 'public/frontend_assets/js/lr-translate.js');
const PROTECT = path.join(ROOT, 'public/frontend_assets/js/protect-names-from-translate.js');

const HTML = `<!doctype html><html><head><title>Profile</title></head><body>
  <a class="nav-link" id="nav1">E-Learning</a>
  <a class="nav-link" id="nav2">E-Store</a>
  <span id="side1">E-Store</span>
  <span id="hyph">non-profit</span>
  <span id="slug">ID-4471</span>

  <label id="lbl">Middle Name</label>
  <input type="text" id="middle_name" name="middle_name" placeholder="Middle Name" value="Hugo">
  <input type="text" id="first_name" name="first_name" placeholder="First Name" value="Pablo">
  <input type="submit" id="save" value="Update">
  <p class="name_bull" id="author">Pablo Hugo Chia</p>
</body></html>`;

const dom = new JSDOM(HTML, { url: 'https://s.test/user/profile', pretendToBeVisual: true, runScripts: 'dangerously' });
const w = dom.window;
const sent = [];
w.fetch = (u, o) => {
  const b = JSON.parse(o.body);
  b.items.forEach(t => sent.push(t));
  return Promise.resolve({ ok: true, json: () => Promise.resolve({
    ok: true, target: b.target, translations: b.items.map(t => 'ZH<' + t + '>') })});
};
w.LR_TRANSLATE_CONFIG = { endpoint: '/t', csrf: 'x', cacheVersion: 't' };
w.localStorage.setItem('lr_content_lang', '__original__');

for (const f of [PROTECT, ENGINE]) {
  const s = w.document.createElement('script');
  s.textContent = fs.readFileSync(f, 'utf8');
  w.document.body.appendChild(s);
}

w.LrTranslate.setLanguage('zh-CN');

setTimeout(() => {
  const $ = id => w.document.getElementById(id);
  const r = [], ck = (l, p, d) => r.push({ l, p, d: d || '' });

  ck('nav "E-Learning" translated', $('nav1').textContent === 'ZH<E-Learning>', $('nav1').textContent);
  ck('nav "E-Store" translated', $('nav2').textContent === 'ZH<E-Store>', $('nav2').textContent);
  ck('sidebar "E-Store" translated', $('side1').textContent === 'ZH<E-Store>', $('side1').textContent);
  ck('hyphenated word translated', $('hyph').textContent === 'ZH<non-profit>', $('hyph').textContent);
  ck('real slug still skipped', $('slug').textContent === 'ID-4471', $('slug').textContent);

  ck('"Middle Name" placeholder translated', $('middle_name').getAttribute('placeholder') === 'ZH<Middle Name>', $('middle_name').getAttribute('placeholder'));
  ck('"First Name" placeholder translated', $('first_name').getAttribute('placeholder') === 'ZH<First Name>', $('first_name').getAttribute('placeholder'));
  ck('label translated', $('lbl').textContent === 'ZH<Middle Name>', $('lbl').textContent);

  ck('name field VALUE untouched (middle)', $('middle_name').getAttribute('value') === 'Hugo', $('middle_name').getAttribute('value'));
  ck('name field VALUE untouched (first)', $('first_name').getAttribute('value') === 'Pablo', $('first_name').getAttribute('value'));
  ck('name value never transmitted', !sent.includes('Hugo') && !sent.includes('Pablo'));
  ck('submit caption translated', $('save').getAttribute('value') === 'ZH<Update>', $('save').getAttribute('value'));
  ck('author name untouched', $('author').textContent === 'Pablo Hugo Chia', $('author').textContent);
  ck('author name never transmitted', !sent.includes('Pablo Hugo Chia'));

  const f = r.filter(x => !x.p);
  r.forEach(x => console.log((x.p ? 'PASS  ' : 'FAIL  ') + x.l + (x.p ? '' : '  → ' + x.d)));
  console.log('\n' + (r.length - f.length) + '/' + r.length + ' passed');
  process.exit(f.length ? 1 : 0);
}, 700);
