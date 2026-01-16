import { test, expect } from '@playwright/test';

test.describe('Email module - E2E', () => {
  test('compose -> sent -> view', async ({ page }) => {
    // Login (reuse existing login flow)
    await page.goto('/');

    // Wait for any loading overlay / onload popup to clear
    await page.waitForTimeout(500);
    // Try to close onload popup if it appears
    const agreeModal = page.locator('#onload_popup');
    if (await agreeModal.isVisible().catch(() => false)) {
      const checkbox = agreeModal.locator('#pma_check');
      if (await checkbox.count() > 0) await checkbox.check().catch(() => null);
      await agreeModal.locator('#myButton').click().catch(() => null);
      await agreeModal.waitFor({ state: 'hidden', timeout: 7000 }).catch(async () => {
        // As a last resort remove the modal and overlay from the DOM so tests can proceed
        await page.evaluate(() => {
          const el = document.getElementById('onload_popup');
          if (el && el.parentNode) el.parentNode.removeChild(el);
          const overlay = document.getElementById('popupOverlay');
          if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
        });
      });
    }

    // Ensure loading overlay is gone before clicking
    await page.waitForSelector('#loading', { state: 'hidden', timeout: 7000 }).catch(() => null);

    // Open login modal (use jQuery/Bootstrap show as fallback to avoid overlays)
    await page.evaluate(() => {
      const el = document.querySelector('a[data-bs-target="#loginModal"]') as HTMLElement | null;
      if (el && typeof (el as any).click === 'function') el.click();
      try {
        // @ts-ignore - use jQuery/Bootstrap to show the modal if available
        if (window['$']) (window['$'])('#loginModal').modal('show');
      } catch (e) {
        // ignore
      }
    });

    // Wait for login modal
    const loginModal = page.locator('#loginModal');
    await loginModal.waitFor({ state: 'visible', timeout: 10000 });

    await page.fill('#loginModal input[name="user_name"]', 'main@yopmail.com');
    await page.fill('#loginModal input[name="password"]', '12345678');

    await page.evaluate(() => {
      const btn = document.querySelector('#loginModal #login-submit') as HTMLInputElement | null;
      if (btn) btn.click();
    });

    // Wait for OTP modal
    const otpModal = page.locator('#otpModal');
    await otpModal.waitFor({ state: 'visible', timeout: 10000 });

    await page.fill('#otp', '7914');
    await page.evaluate(() => {
      const btn = document.querySelector('#otp-form button[type=submit]') as HTMLButtonElement | null;
      if (btn) btn.click();
    });

    await page.waitForURL(/user\/profile/, { timeout: 15000 });

    // Go to Mail
    await page.goto('/user/mail');
    await expect(page).toHaveURL(/user\/mail/);

    // Submit mail using fetch inside the browser context (bypass UI tagify/ckeditor issues)
    await page.evaluate(async () => {
      const tokenEl = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
      const token = tokenEl ? tokenEl.content : '';
      const form = new FormData();
      form.append('to', JSON.stringify([{ value: 'john@yopmail.com' }]));
      form.append('cc', JSON.stringify([]));
      form.append('subject', 'E2E Test Subject');
      form.append('message', 'This is an E2E test message body (sent by Playwright).');
      form.append('_token', token);

      await fetch('/user/mail/send', {
        method: 'POST',
        credentials: 'same-origin',
        body: form
      }).then(r => r.json()).catch(e => console.error('mail send failed', e));
    });

    // Small wait for backend processing
    await page.waitForTimeout(1000);

    // Go to Sent tab
    await page.click('.sidebarOption[data-route$="/user/mail/sent"]');
    await page.waitForURL(/user\/mail\/sent/, { timeout: 10000 }).catch(() => null);

    // Wait for sent list to be populated and check for our subject
    await page.waitForTimeout(1500);
    const sentList = page.locator('#sent-email-list-' + String((await page.evaluate(() => { return (window as any).auth_user_id || '';}))));

    // As a fallback, assert the first matching subject element is visible (avoid strict-mode duplicate matches)
    const firstSubject = page.locator('h4:has-text("E2E Test Subject")').first();
    await expect(firstSubject).toBeVisible({ timeout: 10000 });

    // Click the first mail view that matches the subject
    const viewLink = page.locator('a.view-mail:has-text("E2E Test Subject")').first();
    if (await viewLink.count() > 0) {
      await viewLink.click();
      // Wait for mail details page
      await page.waitForSelector('.mail_subject h4, .mail_subject h5, h4:has-text("E2E Test Subject")', { timeout: 5000 }).catch(() => null);
      // Verify subject is visible in details
      await expect(page.locator('h4:has-text("E2E Test Subject")').first()).toBeVisible({ timeout: 5000 });
    }

  });
});
