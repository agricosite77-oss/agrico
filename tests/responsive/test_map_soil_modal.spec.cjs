const puppeteer = require('c:/Users/Carl Ashley/Herd/agrico/node_modules/puppeteer-core');
const fs = require('fs');
const path = require('path');
const assert = require('assert');

const EDGE_PATH = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
const SCREENSHOT_DIR = path.join(__dirname, 'screenshots');

if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function runMapModalTest() {
    console.log('=== Starting Map Soil Record Details Pop-up Responsiveness Test ===');
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
        console.log(`\nTesting Map Viewport: ${vp.name} (${vp.width}x${vp.height})`);
        await page.setViewport({ width: vp.width, height: vp.height });

        // 1. Fresh page load on /map
        await page.goto('http://127.0.0.1:8000/map', { waitUntil: 'networkidle2' });

        const checkFresh = await page.evaluate(() => {
            const modal = document.getElementById('soilRecordModal');
            const style = modal ? window.getComputedStyle(modal) : null;
            return {
                display: style ? style.display : null,
                bodyOverflow: document.body.style.overflow
            };
        });

        assert.strictEqual(checkFresh.display, 'none', `FAIL: Soil Record modal visible on fresh load at ${vp.name}`);
        assert.strictEqual(checkFresh.bodyOverflow, '', `FAIL: Body overflow locked on fresh load at ${vp.name}`);
        console.log('  ✓ Fresh map load: Modal cleanly hidden (display: none)');

        // 2. Open modal via viewWebsitePinDetails
        const checkOpen = await page.evaluate(() => {
            // Trigger pin details modal with sample multi-layer soil data matching the user's issue:
            // "5YR 7/6", Soil Percentage (29.1%), #E38558
            const samplePin = {
                id: 9999,
                location_mapping_id: 9999,
                barangay: 'Brgy. Centro (Poblacion)',
                latitude: 18.2145,
                longitude: 121.7234,
                coverage: '500 m²',
                status: 'active',
                scan_date_formatted: '10/16/2026',
                scanned_by: 'Admin Analyst',
                scan_platform: 'Web',
                regions: [
                    {
                        munsell: '5YR 7/6',
                        munsell_code: '5YR 7/6',
                        hex: '#E38558',
                        label: 'Reddish Yellow',
                        percent: 29.1,
                        proportion: 0.291,
                        confidence: '97.4%',
                        organic_matter: 'Moderate (2.2% – 3.7%)',
                        soil_name: 'Well-Drained Clay / Latosol',
                        soil_type: 'Well-Drained Clay / Latosol',
                        description: '5YR 7/6 - Reddish Yellow clay loam with moderate organic matter.',
                        region_thumbnail: 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
                    },
                    {
                        munsell: '7.5YR 5/3',
                        munsell_code: '7.5YR 5/3',
                        hex: '#8F694E',
                        label: 'Brown',
                        percent: 70.9,
                        proportion: 0.709,
                        confidence: '98.2%',
                        organic_matter: 'High (3.8% – 5.0%)',
                        soil_name: 'Clay Loam',
                        soil_type: 'Clay Loam',
                        description: '7.5YR 5/3 - Brown sub-layer.'
                    }
                ]
            };

            if (!window.mappingsData) window.mappingsData = [];
            window.mappingsData.push(samplePin);

            window.viewWebsitePinDetails(9999);

            const modal = document.getElementById('soilRecordModal');
            const card = document.querySelector('.soil-modal-card');
            const mStyle = modal ? window.getComputedStyle(modal) : null;
            const cStyle = card ? window.getComputedStyle(card) : null;

            // Check single box avatar (no nested frame or roi box)
            const avatarContainer = document.getElementById('modalSoilSnippetAvatarContainer');
            const roiBoxes = avatarContainer ? avatarContainer.querySelectorAll('.soil-roi-box, .soil-avatar-box') : [];

            // Check word-wrap of percentage display
            const pctDisplay = document.getElementById('modalSoilPercentageDisplay');
            const hexPill = document.getElementById('modalHexPill');

            // Check grid columns of metrics row 1
            const metricsRow1 = document.querySelector('.soil-metrics-row-1');
            const r1Cols = metricsRow1 ? window.getComputedStyle(metricsRow1).gridTemplateColumns : null;

            return {
                display: mStyle ? mStyle.display : null,
                cardWidth: cStyle ? cStyle.width : null,
                cardMaxWidth: cStyle ? cStyle.maxWidth : null,
                cardMaxHeight: cStyle ? cStyle.maxHeight : null,
                cardBorderRadius: cStyle ? cStyle.borderRadius : null,
                roiBoxCount: roiBoxes.length,
                pctText: pctDisplay ? pctDisplay.textContent.trim() : null,
                hexText: hexPill ? hexPill.textContent.trim() : null,
                gridColumns: r1Cols,
                bodyOverflow: document.body.style.overflow
            };
        });

        assert.strictEqual(checkOpen.display, 'flex', `FAIL: Map modal failed to open at ${vp.name}`);
        assert.strictEqual(checkOpen.roiBoxCount, 0, `FAIL: Green ROI box or double box found in avatar at ${vp.name}`);
        assert.strictEqual(checkOpen.bodyOverflow, 'hidden', `FAIL: Body overflow not locked when modal is open at ${vp.name}`);
        assert(checkOpen.pctText.includes('29.1%') || checkOpen.pctText.includes('29%'), `FAIL: Incorrect percentage text: ${checkOpen.pctText}`);
        assert.strictEqual(checkOpen.hexText, '#E38558', `FAIL: Hex pill incorrect at ${vp.name}`);

        console.log(`  ✓ Open action: Modal displays flex (width: ${checkOpen.cardWidth}, max-width: ${checkOpen.cardMaxWidth}, border-radius: ${checkOpen.cardBorderRadius})`);
        console.log(`  ✓ Photo thumbnail: Single box verified (roiBoxCount: ${checkOpen.roiBoxCount})`);
        console.log(`  ✓ Banner text: "${checkOpen.pctText}" | Hex: "${checkOpen.hexText}"`);
        console.log(`  ✓ Grid columns: ${checkOpen.gridColumns}`);

        // Screenshot with modal open
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, `map_soil_modal_open_${vp.name}.png`), fullPage: false });

        // 3. Close modal
        const checkClose = await page.evaluate(() => {
            window.closeSoilRecordModal();
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
    console.log('\n=== All Map Soil Modal Responsiveness Tests Passed Successfully! ===');
}

runMapModalTest().catch(err => {
    console.error('\n❌ Map Test Suite Failed:', err.message);
    process.exit(1);
});
