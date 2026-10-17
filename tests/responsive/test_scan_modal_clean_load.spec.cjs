const puppeteer = require('c:/Users/Carl Ashley/Herd/agrico/node_modules/puppeteer-core');
const fs = require('fs');
const path = require('path');
const assert = require('assert');

const EDGE_PATH = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
const SCREENSHOT_DIR = path.join(__dirname, 'screenshots');

if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function runAutomatedTest() {
    console.log('=== Starting Automated Modal Regression & Responsiveness Test ===');
    const browser = await puppeteer.launch({
        executablePath: EDGE_PATH,
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const page = await browser.newPage();

    const viewports = [
        { name: '320x568', width: 320, height: 568 },
        { name: '360x720', width: 360, height: 720 },
        { name: '375x812', width: 375, height: 812 },
        { name: '812x375_landscape', width: 812, height: 375 },
        { name: '768x1024_tablet', width: 768, height: 1024 },
        { name: '1280x800_desktop', width: 1280, height: 800 }
    ];

    for (const vp of viewports) {
        console.log(`\nTesting Viewport: ${vp.name} (${vp.width}x${vp.height})`);
        await page.setViewport({ width: vp.width, height: vp.height });

        // 1. Fresh page load: NO POP-UP OR BACKDROP MUST BE VISIBLE
        await page.goto('http://127.0.0.1:8000/scan', { waitUntil: 'networkidle2' });

        const checkFresh = await page.evaluate(() => {
            const soilModal = document.getElementById('soilRecordModal');
            const pinModal = document.getElementById('pinSoilLocationModal');
            const soilStyle = soilModal ? window.getComputedStyle(soilModal) : null;
            const pinStyle = pinModal ? window.getComputedStyle(pinModal) : null;
            return {
                soilDisplay: soilStyle ? soilStyle.display : null,
                pinDisplay: pinStyle ? pinStyle.display : null,
                bodyOverflow: document.body.style.overflow
            };
        });

        assert.strictEqual(checkFresh.soilDisplay, 'none', `FAIL: Soil Record modal visible on fresh load at ${vp.name}`);
        assert.strictEqual(checkFresh.pinDisplay, 'none', `FAIL: Pin Location modal visible on fresh load at ${vp.name}`);
        assert.strictEqual(checkFresh.bodyOverflow, '', `FAIL: Body overflow locked on fresh load at ${vp.name}`);
        console.log('  ✓ Fresh page load: All modals cleanly hidden (display: none)');

        // Take after screenshot of clean scan page
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, `scan_clean_${vp.name}.png`), fullPage: false });

        // 2. Refresh test: NO POP-UP OR BACKDROP MUST BE VISIBLE AFTER RELOAD
        await page.reload({ waitUntil: 'networkidle2' });
        const checkReload = await page.evaluate(() => {
            const soilModal = document.getElementById('soilRecordModal');
            const soilStyle = soilModal ? window.getComputedStyle(soilModal) : null;
            return { soilDisplay: soilStyle ? soilStyle.display : null };
        });
        assert.strictEqual(checkReload.soilDisplay, 'none', `FAIL: Soil Record modal visible after reload at ${vp.name}`);
        console.log('  ✓ Page reload: Modals remain cleanly hidden (display: none)');

        // 3. User interaction test: Click "View" opens the modal
        const checkOpen = await page.evaluate(() => {
            viewScanDetails({
                name: 'Reddish Yellow',
                code: '7.5YR 7/6',
                hue: '7.5YR',
                count: 2,
                date: '16 Oct 2026',
                organic: 'Moderate (2.2% – 3.7%)',
                drainage: 'Well-Drained Clay / Latosol',
                confidence: '97.4%'
            });
            const modal = document.getElementById('soilRecordModal');
            const card = document.querySelector('.soil-modal-card');
            const mStyle = modal ? window.getComputedStyle(modal) : null;
            const cStyle = card ? window.getComputedStyle(card) : null;
            return {
                display: mStyle ? mStyle.display : null,
                cardWidth: cStyle ? cStyle.width : null,
                cardMaxWidth: cStyle ? cStyle.maxWidth : null,
                cardMaxHeight: cStyle ? cStyle.maxHeight : null,
                cardBorderRadius: cStyle ? cStyle.borderRadius : null,
                bodyOverflow: document.body.style.overflow
            };
        });

        assert.strictEqual(checkOpen.display, 'flex', `FAIL: Modal failed to open with viewScanDetails at ${vp.name}`);
        assert.strictEqual(checkOpen.bodyOverflow, 'hidden', `FAIL: Body overflow not locked when modal is open at ${vp.name}`);
        console.log(`  ✓ Open action: Modal displays flex (width: ${checkOpen.cardWidth}, max-width: ${checkOpen.cardMaxWidth}, border-radius: ${checkOpen.cardBorderRadius})`);

        // Screenshot with modal open
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, `soil_modal_open_${vp.name}.png`), fullPage: false });

        // 4. Close test: Close modal and verify clean state & restored overflow
        const checkClose = await page.evaluate(() => {
            closeSoilRecordModal();
            const modal = document.getElementById('soilRecordModal');
            const mStyle = modal ? window.getComputedStyle(modal) : null;
            return {
                display: mStyle ? mStyle.display : null,
                bodyOverflow: document.body.style.overflow
            };
        });

        assert.strictEqual(checkClose.display, 'none', `FAIL: Modal failed to close at ${vp.name}`);
        assert.strictEqual(checkClose.bodyOverflow, '', `FAIL: Body overflow not restored after close at ${vp.name}`);
        console.log('  ✓ Close action: Modal closed (display: none) and body overflow restored');
    }

    await browser.close();
    console.log('\n=== All Tests Passed Successfully! ===');
}

runAutomatedTest().catch(err => {
    console.error('\n❌ Test Suite Failed:', err.message);
    process.exit(1);
});
