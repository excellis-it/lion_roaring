import { test, expect } from '@playwright/test';

test.describe('Home Page', () => {
  test('should load and show banner', async ({ page }) => {
    const response = await page.goto('/');
    expect(response && response.ok()).toBeTruthy();

    const banner = page.locator('.banner__slider');
    await expect(banner).toBeVisible({ timeout: 5000 });

    // Optional: check for a common button
    const readMore = page.getByRole('link', { name: /read more/i });
    await expect(readMore.first()).toBeVisible();
  });
});
