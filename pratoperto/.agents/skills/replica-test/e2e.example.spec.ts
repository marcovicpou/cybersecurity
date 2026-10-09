import { test, expect } from '@playwright/test';
test('core flow', async ({ page }) => {
  const errors: string[] = [];
  page.on('console', m => { if (m.type() === 'error') errors.push(m.text()); });
  page.on('response', r => { if (r.status() >= 500) errors.push(`${r.status()} ${r.url()}`); });
  await page.goto('/');
  await expect(page.getByRole('main')).toBeVisible();
  expect(errors).toEqual([]);
});
