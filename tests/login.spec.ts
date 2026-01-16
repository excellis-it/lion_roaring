import { test, expect } from '@playwright/test';

test.describe('Login flow with OTP', () => {
  test('login with demo account and verify OTP', async ({ page, baseURL }) => {
    await page.goto('/');
    await expect(page).toHaveURL(/\/?$/);

    // quick: dismiss country overlay if present
    if (await page.locator('#popupOverlay').isVisible().catch(() => false)) {
      await page.click('#selectCountryBtn').catch(() => null);
      await page.locator('#popupOverlay').waitFor({ state: 'hidden', timeout: 3000 }).catch(() => null);
    }

    // agree disclaimer modal if shown
    const agree = page.locator('#onload_popup');
    if (await agree.isVisible().catch(() => false)) {
      const chk = agree.locator('#pma_check');
      if (await chk.count() > 0) await chk.check().catch(() => null);
      await agree.locator('#myButton').click().catch(() => null);
      await agree.waitFor({ state: 'hidden', timeout: 3000 }).catch(() => null);
      await page.evaluate(() => { document.querySelectorAll('.modal-backdrop').forEach(b => b.remove()); });
    }

    // open login modal (short, with fallback)
    const signIn = page.locator('a[data-bs-target="#loginModal"]');
    await signIn.click().catch(() => signIn.click({ force: true }));
    // small delay to allow animations; fallback to force-click/removal if modal not visible
    await page.waitForTimeout(300);
    if (!(await page.locator('#loginModal').isVisible().catch(() => false))) {
      await page.evaluate(() => {
        const l = document.getElementById('loading'); if (l) { (l as HTMLElement).style.pointerEvents = 'none'; (l as HTMLElement).style.display = 'none'; }
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        const m = document.getElementById('onload_popup'); if (m) { (m as HTMLElement).classList.remove('show'); (m as HTMLElement).style.display = 'none'; (m as HTMLElement).style.pointerEvents = 'none'; }
      });
      await signIn.click({ force: true });
    }

    await page.locator('#loginModal').waitFor({ state: 'visible', timeout: 10000 });

    await page.fill('#loginModal input[name="user_name"]', 'main@yopmail.com');
    await page.fill('#loginModal input[name="password"]', '12345678');
    await page.click('#loginModal #login-submit');

    await page.locator('#otpModal').waitFor({ state: 'visible', timeout: 10000 });

    // Ensure timezone set (fallback to UTC) and submit OTP
    await page.waitForFunction(() => !!(document.getElementById('time_zone') as HTMLInputElement | null)?.value?.trim(), { timeout: 3000 })
      .catch(() => page.evaluate(() => { (document.getElementById('time_zone') as HTMLInputElement).value = 'UTC'; }));

    await page.fill('#otp', '7914');
    await Promise.all([
      page.waitForURL(/user\/profile/, { timeout: 10000 }),
      page.click('#otp-form button[type=submit]')
    ]);

    // Assert profile page loaded
    await expect(page).toHaveURL(/user\/profile/);
    await expect(page.locator('h2').first()).toBeVisible();
  });
});
