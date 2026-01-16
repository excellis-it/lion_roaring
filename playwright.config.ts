import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests',
  timeout: 30_000,
  expect: { timeout: 5_000 },
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  use: {
    baseURL: 'http://127.0.0.1:8000',
    headless: true,
    viewport: { width: 1600, height: 900 },
    actionTimeout: 5_000,
    ignoreHTTPSErrors: true,
  },
  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
    {
      name: 'chrome-devtools',
      use: {
        channel: 'chrome',
        headless: false,
        // launchOptions: { devtools: true },
        // viewport: { width: 1600, height: 900 }
        launchOptions: {
          devtools: true,
          args: [
            '--window-size=1920,1080',
            '--window-position=1920,0', // 👈 second monitor (right)
          ],
        },

        viewport: null,
      }
    },
  ],
});


// for test example command
// npx playwright test tests/login.spec.ts --project=chrome-devtools --debug
// npx playwright test tests/login.spec.ts --project=chrome-devtools --headed
