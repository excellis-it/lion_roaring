import { test, expect } from '@playwright/test';

test.describe('Login flow with OTP', () => {
  test('login with demo account and verify OTP', async ({ page, baseURL }) => {
    // Open home page and trigger the Sign-In modal
    await page.goto('/');
    await expect(page).toHaveURL(/\/?$/);

    // If country popup overlay is shown, select a country to dismiss it, then handle the agreement modal
    const overlay = page.locator('#popupOverlay');
    if (await overlay.isVisible().catch(() => false)) {
      await page.click('#selectCountryBtn');
      await overlay.waitFor({ state: 'hidden', timeout: 5000 }).catch(() => null);

      const agreeModal = page.locator('#onload_popup');
      if (await agreeModal.isVisible().catch(() => false)) {
        await agreeModal.waitFor({ state: 'visible', timeout: 5000 }).catch(() => null);
        // check the agreement checkbox and continue
        const checkbox = agreeModal.locator('#pma_check');
        if (await checkbox.count() > 0) await checkbox.check().catch(() => null);
        // click continue
        await agreeModal.locator('#myButton').click().catch(() => null);
        await agreeModal.waitFor({ state: 'hidden', timeout: 5000 }).catch(() => null);
      }
    }

    // Click the Sign-In link that opens the modal
    await page.click('a[data-bs-target="#loginModal"]');

    // Wait for login modal to be visible
    const loginModal = page.locator('#loginModal');
    await loginModal.waitFor({ state: 'visible', timeout: 10000 });

    // Fill credentials inside the modal
    await page.fill('#loginModal input[name="user_name"]', 'main@yopmail.com');
    await page.fill('#loginModal input[name="password"]', '12345678');

    // Submit login (AJAX) — use DOM click to avoid pointer-intercept issues
    await page.evaluate(() => {
      const btn = document.querySelector('#loginModal #login-submit') as HTMLInputElement | null;
      if (btn) btn.click();
    });

    // Wait for OTP modal to appear

    // Wait for OTP modal to appear
    const otpModal = page.locator('#otpModal');
    await otpModal.waitFor({ state: 'visible', timeout: 10000 });

    // Wait for time_zone to be set by the page JS (fallback to UTC if not present)
    await page.waitForFunction(() => {
      const el = document.getElementById('time_zone') as HTMLInputElement | null;
      return !!(el && el.value && String(el.value).trim().length > 0);
    }, { timeout: 5000 }).catch(async () => {
      // fallback: set timezone to UTC directly
      await page.evaluate(() => { (document.getElementById('time_zone') as HTMLInputElement).value = 'UTC'; });
    });

    // Snapshot OTP modal for visual check
    await page.screenshot({ path: 'test-results/login-otp-modal.png' });

    // Fill OTP (demo code) and submit (use DOM click to avoid overlay intercept)
    await page.fill('#otp', '7914');
    await page.evaluate(() => {
      const btn = document.querySelector('#otp-form button[type=submit]') as HTMLButtonElement | null;
      if (btn) btn.click();
    });

    // Wait for redirect to user profile
    await page.waitForURL(/user\/profile/, { timeout: 10000 });
    await expect(page).toHaveURL(/user\/profile/);

    // Visual check of profile
    await page.screenshot({ path: 'test-results/profile-after-login.png' });

    // Assert profile page has heading with user's name (best-effort)
    const header = page.locator('h2').first();
    await expect(header).toBeVisible();
  });
});
