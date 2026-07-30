/**
 * Regression test for the LrTranslate exclusion rules.
 *
 * Guards the two requirements that cannot be verified by reading the code:
 * icons and person names are never translated AND never transmitted, and
 * switching back to Original costs nothing.
 *
 *   npm install --no-save jsdom && node tests/js/lr-translate.test.js
 */
const fs = require('fs');
const { JSDOM } = require('jsdom');

const ENGINE = require('path').join(__dirname, '../../public/frontend_assets/js/lr-translate.js');

const HTML = `<!doctype html><html><head><title>Dashboard</title></head><body>
  <h1 id="h">Welcome to the member area</h1>
  <p id="p">Please review your <strong>membership details</strong> before continuing.</p>

  <i class="fas fa-user" id="icon1">person</i>
  <span class="material-icons" id="icon2">settings</span>
  <span class="icon-bell" id="icon3">notifications</span>

  <p class="GroupName" id="name1">Maria Gonzalez</p>
  <span class="notranslate" id="name2">Jean-Luc Picard</span>
  <span translate="no" id="name3">Aisha Okonkwo</span>
  <span data-nt id="name4">Kenji Tanaka</span>
  <span class="user-name" id="name5">Priya Sharma</span>

  <span id="num">1,200.00</span>
  <span id="money">$45.00</span>
  <span id="mail">someone@example.com</span>
  <span id="url">https://example.com/page</span>
  <span id="short">OK</span>

  <input id="search" placeholder="Search members" title="Search the directory">
  <input id="fname" name="first_name" value="Ludmila" placeholder="First name">
  <input id="submit" type="submit" value="Save changes">

  <script id="js">var greeting = "Do not translate me";</script>
  <code id="code">npm install --save</code>

  <select id="languageSwitcher"><option value="__original__">Original</option><option value="es">Spanish</option></select>
</body></html>`;

const dom = new JSDOM(HTML, { url: 'https://site.test/user/dashboard', pretendToBeVisual: true, runScripts: 'dangerously' });
const { window } = dom;

const sent = [];
window.fetch = function (url, opts) {
  const body = JSON.parse(opts.body);
  body.items.forEach(t => sent.push(t));
  return Promise.resolve({
    ok: true,
    json: () => Promise.resolve({
      ok: true,
      target: body.target,
      translations: body.items.map(t => '«' + t + '»')
    })
  });
};

window.LR_TRANSLATE_CONFIG = { endpoint: '/translate/batch', csrf: 'test', cacheVersion: 'test' };
window.localStorage.setItem('lr_content_lang', '__original__');

const tag = window.document.createElement('script');
tag.textContent = fs.readFileSync(ENGINE, 'utf8');
window.document.body.appendChild(tag);

const $ = id => window.document.getElementById(id);
const results = [];
function check(label, pass, detail) {
  results.push({ label, pass, detail: detail || '' });
}

window.LrTranslate.setLanguage('es');

setTimeout(() => {
  const sentSet = new Set(sent);

  // ── things that MUST be translated ────────────────────────────────────
  check('heading translated', $('h').textContent === '«Welcome to the member area»', $('h').textContent);
  check('paragraph text translated', $('p').textContent.includes('«'), $('p').textContent);
  check('placeholder translated', $('search').getAttribute('placeholder') === '«Search members»', $('search').getAttribute('placeholder'));
  check('title attr translated', $('search').getAttribute('title') === '«Search the directory»', $('search').getAttribute('title'));
  check('submit button value translated', $('submit').getAttribute('value') === '«Save changes»', $('submit').getAttribute('value'));

  // ── icons: never sent, never changed ──────────────────────────────────
  ['icon1', 'icon2', 'icon3'].forEach(id => {
    const before = { icon1: 'person', icon2: 'settings', icon3: 'notifications' }[id];
    check('icon untouched: ' + id, $(id).textContent === before, $(id).textContent);
  });
  check('no icon text was sent', !sentSet.has('person') && !sentSet.has('settings') && !sentSet.has('notifications'));

  // ── names: never sent, never changed ──────────────────────────────────
  const names = { name1: 'Maria Gonzalez', name2: 'Jean-Luc Picard', name3: 'Aisha Okonkwo', name4: 'Kenji Tanaka', name5: 'Priya Sharma' };
  Object.keys(names).forEach(id => {
    check('name untouched: ' + id, $(id).textContent === names[id], $(id).textContent);
    check('name not transmitted: ' + id, !sentSet.has(names[id]));
  });
  check('name input value untouched', $('fname').getAttribute('value') === 'Ludmila', $('fname').getAttribute('value'));

  // ── non-translatable content: never sent (cost control) ───────────────
  ['1,200.00', '$45.00', 'someone@example.com', 'https://example.com/page'].forEach(t => {
    check('not sent: ' + t, !sentSet.has(t));
  });

  // ── script / code excluded ────────────────────────────────────────────
  check('script content not sent', !sentSet.has('var greeting = "Do not translate me";'));
  check('code block not sent', !sentSet.has('npm install --save'));
  check('code block untouched', $('code').textContent === 'npm install --save', $('code').textContent);

  // ── Original restores, with zero extra network calls ──────────────────
  const sentBeforeRestore = sent.length;
  window.LrTranslate.setLanguage('__original__');

  setTimeout(() => {
    check('Original restores heading', $('h').textContent === 'Welcome to the member area', $('h').textContent);
    check('Original restores placeholder', $('search').getAttribute('placeholder') === 'Search members', $('search').getAttribute('placeholder'));
    check('Original restores submit value', $('submit').getAttribute('value') === 'Save changes', $('submit').getAttribute('value'));
    check('Original made zero API calls', sent.length === sentBeforeRestore, sent.length + ' vs ' + sentBeforeRestore);

    // ── MutationObserver picks up injected content ──────────────────────
    window.LrTranslate.setLanguage('es');
    setTimeout(() => {
      const div = window.document.createElement('div');
      div.innerHTML = '<span id="late">Loaded by ajax</span><span class="notranslate" id="lateName">Olu Adeyemi</span>';
      window.document.body.appendChild(div);

      setTimeout(() => {
        check('ajax content translated', $('late').textContent === '«Loaded by ajax»', $('late').textContent);
        check('ajax name untouched', $('lateName').textContent === 'Olu Adeyemi', $('lateName').textContent);

        const failed = results.filter(r => !r.pass);
        results.forEach(r => console.log((r.pass ? 'PASS  ' : 'FAIL  ') + r.label + (r.pass ? '' : '  → ' + r.detail)));
        console.log('\nSent to API (' + sent.length + ' strings):');
        console.log('  ' + JSON.stringify(sent));
        console.log('\n' + (results.length - failed.length) + '/' + results.length + ' passed');
        process.exit(failed.length ? 1 : 0);
      }, 500);
    }, 300);
  }, 300);
}, 400);
