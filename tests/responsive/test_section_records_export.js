import puppeteer from 'puppeteer-core';
import { execSync } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.join(__dirname, '..', 'exports', 'screenshots');
if (!fs.existsSync(SCREENSHOT_DIR)) {
  fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

let adminCookie = null;
try {
  const cookieJson = execSync('php tests/responsive/get_admin_session.php', { cwd: path.join(__dirname, '../..') }).toString();
  adminCookie = JSON.parse(cookieJson);
  console.log(`[AUTH] Admin cookie obtained for: ${adminCookie.user.name}`);
} catch (e) {
  console.error('[AUTH ERROR] Could not obtain admin cookie:', e.message);
  process.exit(1);
}

const VIEWPORTS = [
  { name: 'Mobile_320px', width: 320, height: 568 },
  { name: 'Mobile_360px', width: 360, height: 720 },
  { name: 'Mobile_375px', width: 375, height: 812 },
  { name: 'Landscape_Phone_812px', width: 812, height: 375 },
  { name: 'Tablet_768px', width: 768, height: 1024 },
  { name: 'Desktop_1280px', width: 1280, height: 800 },
];

async function run() {
  const browser = await puppeteer.launch({
    executablePath: CHROME_PATH,
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu'],
  });

  const results = [];

  for (const vp of VIEWPORTS) {
    const page = await browser.newPage();
    await page.setViewport({ width: vp.width, height: vp.height });

    if (adminCookie && adminCookie.cookies) {
      await page.setCookie(...adminCookie.cookies);
    }

    await page.goto('http://127.0.0.1:8000/students', { waitUntil: 'networkidle2' });

    page.on('console', msg => console.log(`[PAGE ${vp.name}]`, msg.text()));
    page.on('pageerror', err => console.log(`[PAGE ERROR ${vp.name}]`, err.message));

    // Open Section Records Modal via button
    await page.waitForSelector('button[wire\\:click*="openSectionRecordsModal"]', { timeout: 8000 });
    await page.evaluate(() => {
      const btn = document.querySelector('button[wire\\:click*="openSectionRecordsModal"]');
      if (btn) btn.click();
    });

    // Wait for modal to render
    await page.waitForSelector('.section-records-dialog', { timeout: 10000 });

    // Check Document Title input
    const docTitleValue = await page.evaluate(() => {
      const input = document.querySelector('input[wire\\:model*="documentTitle"]');
      return input ? input.value : null;
    });

    // Check modal overflow
    const overflowCheck = await page.evaluate(() => {
      const dialog = document.querySelector('.section-records-dialog');
      const docW = document.documentElement.clientWidth;
      const rect = dialog.getBoundingClientRect();
      return {
        dialogWidth: rect.width,
        docWidth: docW,
        fitsScreen: rect.width <= docW + 2,
      };
    });

    // Click "Print / Save Copy" button to open 3-option dropdown menu
    await page.evaluate(() => {
      const printBtn = Array.from(document.querySelectorAll('button')).find(b => b.innerText.includes('Print / Save Copy'));
      if (printBtn) printBtn.click();
    });

    // Wait for dropdown to be visible
    await new Promise(r => setTimeout(r, 250));

    const menuCheck = await page.evaluate(() => {
      const buttons = Array.from(document.querySelectorAll('button'));
      const printOption = buttons.find(b => b.innerText.trim() === 'Print');
      const docxOption = buttons.find(b => b.innerText.includes('.docx'));
      const xlsxOption = buttons.find(b => b.innerText.includes('.xlsx'));
      return {
        hasPrint: !!printOption,
        hasDocx: !!docxOption,
        hasXlsx: !!xlsxOption,
        printHeight: printOption?.offsetHeight,
        docxHeight: docxOption?.offsetHeight,
        xlsxHeight: xlsxOption?.offsetHeight,
      };
    });

    // Capture screenshot
    const shotPath = path.join(SCREENSHOT_DIR, `section_records_${vp.name}.png`);
    await page.screenshot({ path: shotPath });

    results.push({
      viewport: vp.name,
      width: vp.width,
      docTitleValue,
      overflowCheck,
      menuCheck,
      screenshot: shotPath,
    });

    console.log(`[PASS] ${vp.name}: Modal dialog width=${Math.round(overflowCheck.dialogWidth)}px (doc=${overflowCheck.docWidth}px), Menu: Print=${menuCheck.hasPrint}, Word=${menuCheck.hasDocx}, Excel=${menuCheck.hasXlsx}`);

    await page.close();
  }

  await browser.close();
  console.log('\n--- Responsive Browser Test Finished Successfully ---');
}

run().catch(err => {
  console.error('[TEST ERROR]:', err);
  process.exit(1);
});
