import { expect, skipInStaticMode, test } from '../fixtures/auth.fixture';

test.describe('Style Lab developer tool', () => {
    test.beforeEach(({}, testInfo) => {
        skipInStaticMode(test, testInfo, 'Style Lab requires the dynamic developer routes');
    });

    test('loads a fixture preview through the real export pipeline', async ({ page }) => {
        await page.goto('/developer/style-lab?fixture=example.elpx&theme=base&export=html5&viewport=mobile', {
            waitUntil: 'domcontentloaded',
        });

        await expect(page.getByTestId('style-lab-fixture')).toHaveValue('example.elpx');
        await expect(page.getByTestId('style-lab-theme')).toHaveValue('base');
        await expect(page.getByTestId('vp-mobile')).toHaveClass(/active/);
        await expect(page.getByTestId('style-lab-iframe')).toBeVisible({ timeout: 30000 });
        await expect(page.getByTestId('style-lab-status')).toContainText('example.elpx', { timeout: 30000 });
        await expect(page.getByTestId('style-lab-status')).toContainText('base');

        const previewFrame = page.frameLocator('[data-testid="style-lab-iframe"]');
        await expect(previewFrame.locator('body')).toContainText(/eXe|Lorem|Duis/i, { timeout: 15000 });
    });
});
