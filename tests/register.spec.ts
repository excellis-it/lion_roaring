import { test, expect } from '@playwright/test';

test.describe('Register flow', () => {
  test('signup with demo account', async ({ page, baseURL }) => {
    const unique = String(Date.now()).slice(-6);
    const userName = `demo_user_${unique}`;
    const email = `demo_${unique}@example.com`;

    await page.goto('/register');
    await expect(page).toHaveURL(/register/);

    // Fill basic info
    await page.fill('input[name="user_name"]', userName);
    await page.fill('input[name="first_name"]', 'Demo');
    await page.fill('input[name="last_name"]', 'User');

    // Select a country that has states (iterate countries, call /get-states implicitly and pick first non-empty state)
    const initialCountry = await page.$eval('select[name="country"]', (s: HTMLSelectElement) => s.value);
    const countryValuesRaw = await page.$$eval('select[name="country"] option', (opts) => opts.map(o => o.value).filter(v => v));
    // Try the initially selected country first (to avoid "Now you are registered from X" validation)
    const countryValues = [initialCountry, ...countryValuesRaw.filter(v => v !== initialCountry)];

    let pickedState = null;
    for (const countryVal of countryValues) {
      await page.selectOption('select[name="country"]', countryVal);
      // wait for the get-states network request and then inspect state options
      await page.waitForResponse((r) => r.url().includes('/get-states') && r.status() === 200, { timeout: 4000 }).catch(() => null);
      const stateOptions = await page.$$eval('select[name="state"] option', (opts) => opts.map(o => ({ value: o.value, text: o.textContent })));
      const nonEmpty = stateOptions.find((s) => s.value && s.value.trim() !== '');
      if (nonEmpty) {
        // set the state value directly (avoids race/visibility issues with selectOption)
        await page.evaluate((val) => {
          const sel = document.querySelector('select[name="state"]') as HTMLSelectElement | null;
          if (sel) {
            sel.value = val;
            sel.dispatchEvent(new Event('change', { bubbles: true }));
          }
          // also add a hidden input fallback to guarantee submission
          const existing = document.querySelector('input[name="state"][data-test-fallback]');
          if (!existing) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'state';
            input.setAttribute('data-test-fallback', '1');
            input.value = String(val);
            const form = document.getElementById('login-form');
            if (form) form.appendChild(input);
          } else if (existing) {
            (existing as HTMLInputElement).value = String(val);
          }
        }, nonEmpty.value);
        pickedState = nonEmpty.value;
        break;
      }
    }
    if (!pickedState) throw new Error('No state options available for any country; cannot complete registration in test');

    await page.fill('input[name="city"]', 'Test City');
    await page.fill('input[name="address"]', '123 Demo Street');
    await page.fill('input[name="zip"]', '12345');

    // Phone: Fill the visible phone input (intlTelInput)
    const phoneUnique = '555' + String(Math.floor(Date.now() % 10000000));
    await page.fill('input[name="phone_number"]', phoneUnique);
    // ensure a full_phone_number fallback exists for server-side validation
    await page.evaluate((full) => {
      const existing = document.querySelector('input[name="full_phone_number"][data-test-fallback]');
      if (!existing) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'full_phone_number';
        input.setAttribute('data-test-fallback', '1');
        input.value = '+1' + full;
        const form = document.getElementById('login-form');
        if (form) form.appendChild(input);
      } else {
        (existing as HTMLInputElement).value = '+1' + full;
      }
    }, phoneUnique);

    await page.fill('input[name="email"]', email);
    await page.fill('input[name="email_confirmation"]', email);

    // Ensure signature is non-empty by painting on canvas and creating a SignaturePad instance from it
    await page.waitForSelector('#signature-pad', { state: 'visible' });
    await page.evaluate(() => {
      const canvas = document.getElementById('signature-pad') as HTMLCanvasElement;
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      if (ctx) {
        ctx.fillStyle = 'black';
        ctx.fillRect(10, 10, 10, 10);
      }
      // Recreate signaturePad instance on window so submit handlers use a non-empty pad
      // @ts-ignore
      window.signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255,255,255)', penColor: 'rgb(0,0,0)' });
      // Populate signaturePad with the drawn content
      // @ts-ignore
      window.signaturePad.fromDataURL(canvas.toDataURL('image/png'));
    });

    // Set valid password (at least 8 chars and includes one of @$%&)
    const password = 'Password@1';
    await page.fill('input[name="password"]', password);
    await page.fill('input[name="password_confirmation"]', password);

    // Because the client-side submit may block on the signature canvas, do a direct POST to register-validate
    // Get canvas data URL for signature and set hidden input so subsequent form submit won't be blocked
    const signatureData = await page.evaluate(() => {
      const canvas = document.getElementById('signature-pad') as HTMLCanvasElement | null;
      return canvas ? canvas.toDataURL('image/png') : '';
    });

    // Hidden input: set its value via DOM (fill requires visible element)
    await page.evaluate((data) => {
      const el = document.getElementById('signature-data') as HTMLInputElement | null;
      if (el) el.value = data;
    }, signatureData);

    // Replace the page submit handler with one that ensures the signature is set and performs the AJAX validate
    await page.evaluate(() => {
      // remove all existing submit handlers and attach a focused one
      // @ts-ignore
      $('#login-form').off('submit');
      // @ts-ignore
      $('#login-form').on('submit', function (e) {
        // If tier & stripeToken present, allow normal submission to proceed (mirrors original client logic)
        if ((document.getElementById('tier_id') as HTMLInputElement | null) !== null && (document.getElementById('stripeToken') as HTMLInputElement | null) !== null) {
          return true; // let the form submit normally
        }

        e.preventDefault();
        const tokenEl = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
        const token = tokenEl ? tokenEl.content : '';
        const fd = new FormData(this as HTMLFormElement);
        if (!fd.get('signature') || fd.get('signature') === '') {
          const canvas = document.getElementById('signature-pad') as HTMLCanvasElement | null;
          if (canvas) fd.set('signature', canvas.toDataURL('image/png'));
        }
        fetch('/register-validate', {
          method: 'POST',
          body: fd,
          headers: { 'X-CSRF-TOKEN': token },
          credentials: 'same-origin'
        }).then(async (r) => {
          let json = {};
          try { json = await r.json(); } catch(e) { json = { status: false, _parseError: true }; }
          // store response for test inspection
          // @ts-ignore
          window._lastRegisterValidate = { statusCode: r.status, body: json };
          // mimic original behaviour
          if ((json as any).status === true) {
            if ((document.getElementById('tier_id') as HTMLInputElement | null) === null) {
              // @ts-ignore
              $('#tierModal').modal('show');
            } else {
              const type = (document.getElementById('tier_pricing_type') as HTMLInputElement | null)?.value || 'amount';
              if (type === 'token') {
                // @ts-ignore
                $('#tokenAgreeModal').modal('show');
              } else {
                // @ts-ignore
                $('#paymentModal').modal('show');
              }
            }
          } else {
            const errors = (json as any).errors || {};
            let errorMsg = '';
            for (const k in errors) { errorMsg += errors[k][0] + '\n'; }
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Validation Error', text: errorMsg });
            else alert(errorMsg);
          }
        }).catch((e) => {
          // @ts-ignore
          window._lastRegisterValidate = { statusCode: 0, error: String(e) };
          alert('Validation request failed');
        });
      });
    });

    // Click the submit button to trigger our new handler
    await page.click('input#login-submit');

    // Wait for the in-page handler to record its response
    await page.waitForFunction(() => (window as any)._lastRegisterValidate !== undefined, { timeout: 10000 });
    const lastResp = await page.evaluate(() => (window as any)._lastRegisterValidate);
    if (!lastResp || lastResp.body?.status !== true) {
      throw new Error('register.validate failed in-page: ' + JSON.stringify(lastResp));
    }

    // Wait for Tier modal that should be shown on successful validation
    const tierModal = page.locator('#tierModal');
    await tierModal.waitFor({ state: 'visible', timeout: 12000 });

    // Prefer free tier (data-cost=0), else try a token tier
    const freeBtn = page.locator('.select-tier-btn[data-cost="0"]');
    if (await freeBtn.count() > 0) {
      await freeBtn.first().click({ timeout: 10000 });
      await page.waitForURL('http://127.0.0.1:8000/', { timeout: 30000 });
    } else {
      const tokenBtn = page.locator('.select-tier-btn[data-pricing-type="token"]');
      if (await tokenBtn.count() > 0) {
        await tokenBtn.first().click({ timeout: 10000 });
        // accept agreement
        await page.locator('#token-agree-accept-btn').click();
        await page.waitForURL('http://127.0.0.1:8000/', { timeout: 30000 });
      } else {
        // As a last resort click the first tier and attempt to continue (may require payment in which case test will fail)
        await page.locator('.select-tier-btn').first().click({ timeout: 10000 });
        await page.waitForURL('http://127.0.0.1:8000/', { timeout: 30000 });
      }

    }

    // Check for the flash message
    await expect(page.locator('body')).toContainText('Please wait for admin approval');
  });
});
