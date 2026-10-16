import puppeteer from 'puppeteer-core';

const browser = await puppeteer.launch({ executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', headless: 'new' });
const pages = ['/login', '/register', '/forgot-password', '/verify', '/admin/login', '/admin/register'];
const widths = [375, 360, 320];

for (const p of pages) {
  for (const w of widths) {
    const page = await browser.newPage();
    await page.setViewport({ width: w, height: 700, isMobile: true });
    await page.goto('http://127.0.0.1:8000' + p, { waitUntil: 'networkidle2' });
    const sw = await page.evaluate(() => Math.max(document.documentElement.scrollWidth, document.body.scrollWidth));
    const status = sw <= w ? 'OK' : 'OVERFLOW ' + (sw - w) + 'px';
    console.log(`${p} @ ${w}px: scrollWidth = ${sw}px -> ${status}`);
    await page.close();
  }
}
await browser.close();
