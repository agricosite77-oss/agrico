import puppeteer from 'puppeteer-core';
import { execSync } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.join(__dirname, 'screenshots');
if (!fs.existsSync(SCREENSHOT_DIR)) {
  fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

// 1. Get Admin Session Cookie from Laravel
let adminCookie = null;
try {
  const cookieJson = execSync('php tests/responsive/get_admin_session.php', { cwd: path.join(__dirname, '../..') }).toString();
  adminCookie = JSON.parse(cookieJson);
  console.log(`[AUTH] Admin cookie obtained for user: ${adminCookie.user.name} (${adminCookie.user.email})`);
} catch (e) {
  console.error('[AUTH ERROR] Could not obtain admin cookie:', e.message);
}

// 2. Define Devices
const DEVICES = [
  { name: 'iPhone 13 mini', slug: 'iphone13mini', width: 375, height: 812, dpr: 3, isMobile: true, hasTouch: true },
  { name: 'Galaxy A6s', slug: 'galaxy_a6s', width: 360, height: 720, dpr: 2, isMobile: true, hasTouch: true },
  { name: 'Small Phone (320px)', slug: 'small_320', width: 320, height: 568, dpr: 2, isMobile: true, hasTouch: true },
  { name: 'Landscape (812x375)', slug: 'landscape_812x375', width: 812, height: 375, dpr: 3, isMobile: true, hasTouch: true },
  { name: 'Tablet (768px)', slug: 'tablet_768', width: 768, height: 1024, dpr: 2, isMobile: true, hasTouch: true },
  { name: 'Desktop (1280px)', slug: 'desktop_1280', width: 1280, height: 800, dpr: 1, isMobile: false, hasTouch: false },
];

// 3. Define Pages to Audit
const PAGES = [
  // Public / Website Pages
  { group: 'Website', name: 'Splash Screen', path: '/', auth: false },
  { group: 'Website', name: 'Home Landing', path: '/home', auth: false },
  { group: 'Website', name: 'Login Form', path: '/login', auth: false },
  { group: 'Website', name: 'Register Form', path: '/register', auth: false },
  { group: 'Website', name: 'Forgot Password', path: '/forgot-password', auth: false },
  { group: 'Website', name: 'Interactive Map', path: '/map', auth: false },
  { group: 'Website', name: 'Munsell Color Chart', path: '/munsell', auth: false },
  { group: 'Website', name: 'FAQ Page', path: '/faq', auth: false },
  { group: 'Website', name: 'Soil Scanner', path: '/scan', auth: false },
  { group: 'Website', name: 'Model Training', path: '/training', auth: false },
  { group: 'Website', name: 'Download App', path: '/download', auth: false },
  { group: 'Website', name: 'Terms of Service', path: '/terms', auth: false },
  { group: 'Website', name: 'Privacy Policy', path: '/privacy', auth: false },
  { group: 'Website', name: 'User Profile', path: '/profile', auth: true },

  // Admin Portal Pages
  { group: 'Admin', name: 'Dashboard', path: '/dashboard', auth: true },
  { group: 'Admin', name: 'Manage Users', path: '/users', auth: true },
  { group: 'Admin', name: 'Soil Scan History', path: '/soil', auth: true },
  { group: 'Admin', name: 'Manage Map Pins', path: '/manage-map', auth: true },
  { group: 'Admin', name: 'Mapping History', path: '/mapping-history', auth: true },
  { group: 'Admin', name: 'Manage Munsell Colors', path: '/munsell-color', auth: true },
  { group: 'Admin', name: 'User Requests', path: '/user-requests', auth: true },
  { group: 'Admin', name: 'Manage FAQs', path: '/faqs', auth: true },
  { group: 'Admin', name: 'Student Records', path: '/students', auth: true },
  { group: 'Admin', name: 'Upload Application', path: '/upload-application', auth: true },
];

async function run() {
  const browser = await puppeteer.launch({
    executablePath: CHROME_PATH,
    headless: 'new',
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-dev-shm-usage',
      '--disable-gpu',
    ],
  });

  const auditResults = [];

  for (const pageConfig of PAGES) {
    console.log(`\n==================================================`);
    console.log(`[TESTING PAGE] ${pageConfig.group}: ${pageConfig.name} (${pageConfig.path})`);
    console.log(`==================================================`);

    for (const dev of DEVICES) {
      // Landscape, Tablet, and Desktop tested on key main and admin pages
      if (['landscape_812x375', 'tablet_768', 'desktop_1280'].includes(dev.slug) && 
          !['Home Landing', 'Interactive Map', 'Login Form', 'Dashboard', 'Manage Users', 'Soil Scan History', 'Mapping History', 'Manage Munsell Colors', 'User Requests', 'Manage FAQs', 'Student Records'].includes(pageConfig.name)) {
        continue;
      }

      const page = await browser.newPage();
      try {
        await page.setViewport({
          width: dev.width,
          height: dev.height,
          deviceScaleFactor: dev.dpr,
          isMobile: dev.isMobile,
          hasTouch: dev.hasTouch,
        });

        // Set Cookie if Auth Required
        if (pageConfig.auth && adminCookie && Array.isArray(adminCookie.cookies)) {
          for (const c of adminCookie.cookies) {
            await page.setCookie({
              name: c.name,
              value: c.value,
              domain: '127.0.0.1',
              path: '/',
              httpOnly: c.httpOnly,
              secure: false,
            });
          }
        }

        const url = `http://127.0.0.1:8000${pageConfig.path}`;
        await page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });
        await new Promise(r => setTimeout(r, 600)); // wait for animations/Livewire hydration

        // Detect horizontal overflow and DOM elements
        const overflowData = await page.evaluate(() => {
          const doc = document.documentElement;
          const body = document.body;
          const windowWidth = window.innerWidth;
          const scrollWidth = Math.max(doc.scrollWidth, body ? body.scrollWidth : 0);
          const hasOverflow = scrollWidth > (windowWidth + 2); // 2px buffer for rounding

          // Find culprit elements causing horizontal overflow
          const culprits = [];
          if (hasOverflow) {
            const all = document.querySelectorAll('*');
            for (const el of all) {
              const rect = el.getBoundingClientRect();
              if (rect.right > (windowWidth + 2)) {
                // Ignore allowed section: "Save as Copy — Section Records & Activity"
                const inAllowedSection = el.closest('[data-section*="section-records"]') || el.closest('#sectionRecordsContainer');
                if (inAllowedSection) continue;

                culprits.push({
                  tag: el.tagName.toLowerCase(),
                  id: el.id || null,
                  className: typeof el.className === 'string' ? el.className.split(' ').slice(0, 3).join('.') : '',
                  right: Math.round(rect.right),
                  width: Math.round(rect.width),
                  overflowPx: Math.round(rect.right - windowWidth),
                  text: (el.innerText || '').trim().replace(/\s+/g, ' ').slice(0, 35)
                });
              }
            }
          }

          // Check if Hamburger / Mobile Toggle exists
          const hamburger = document.querySelector('[data-mobile-menu-toggle], .mobile-menu-btn, .admin-mobile-toggle, #sidebarToggle, button[aria-label*="menu" i]');
          const hamburgerVisible = hamburger ? (window.getComputedStyle(hamburger).display !== 'none') : false;

          // Check for small tap targets
          const smallTargets = [];
          const clickables = Array.from(document.querySelectorAll('button, a, input, select, textarea, [role="button"]'));
          for (const el of clickables) {
            if (el.offsetParent === null) continue;
            const style = window.getComputedStyle(el);
            if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') continue;
            const rect = el.getBoundingClientRect();
            if (rect.width > 0 && rect.height > 0 && (rect.width < 34 || rect.height < 34)) {
              smallTargets.push({
                tag: el.tagName.toLowerCase(),
                text: (el.innerText || el.getAttribute('aria-label') || el.title || '').trim().slice(0, 25),
                w: Math.round(rect.width),
                h: Math.round(rect.height),
              });
            }
          }

          // Check tables on page
          const tables = Array.from(document.querySelectorAll('table'));
          const tableInfo = tables.map(t => {
            const rect = t.getBoundingClientRect();
            const parent = t.parentElement;
            const parentStyle = parent ? window.getComputedStyle(parent) : null;
            const hasScrollWrapper = parentStyle ? (parentStyle.overflowX === 'auto' || parentStyle.overflowX === 'scroll') : false;
            return {
              width: Math.round(rect.width),
              overflowsScreen: rect.width > windowWidth,
              hasScrollWrapper,
              headers: Array.from(t.querySelectorAll('th')).map(th => th.innerText.trim()).filter(Boolean).slice(0, 5)
            };
          });

          return {
            windowWidth,
            scrollWidth,
            hasOverflow,
            culprits: culprits.slice(0, 5),
            hamburgerVisible,
            smallTargetsCount: smallTargets.length,
            smallTargetsSample: smallTargets.slice(0, 3),
            tables: tableInfo,
          };
        });

        // Screenshot filename
        const filename = `${pageConfig.group.toLowerCase()}_${pageConfig.name.toLowerCase().replace(/[^a-z0-9]+/g, '_')}_${dev.slug}.png`;
        const filepath = path.join(SCREENSHOT_DIR, filename);
        await page.screenshot({ path: filepath, fullPage: false });

        const resultStatus = overflowData.hasOverflow ? 'Problem' : 'OK';
        let issueSummary = 'Responsive layout fits screen cleanly';
        if (overflowData.hasOverflow) {
          const culpritDesc = overflowData.culprits.map(c => `<${c.tag}${c.id ? '#' + c.id : ''}${c.className ? '.' + c.className : ''}> (+${c.overflowPx}px)`).join(', ');
          issueSummary = `Horizontal overflow: scrollWidth ${overflowData.scrollWidth}px > ${overflowData.windowWidth}px (${culpritDesc || 'child elements wider than viewport'})`;
        }
        if (overflowData.tables && overflowData.tables.some(t => t.overflowsScreen && !t.hasScrollWrapper)) {
          issueSummary += '; Unwrapped table exceeding screen';
        }

        console.log(`  -> [${dev.name}] ${resultStatus}: ${issueSummary}`);

        auditResults.push({
          page: `${pageConfig.group}: ${pageConfig.name}`,
          path: pageConfig.path,
          device: dev.name,
          dimensions: `${dev.width}x${dev.height}`,
          result: resultStatus,
          scrollWidth: overflowData.scrollWidth,
          windowWidth: overflowData.windowWidth,
          hasOverflow: overflowData.hasOverflow,
          culprits: overflowData.culprits,
          tables: overflowData.tables,
          smallTargetsCount: overflowData.smallTargetsCount,
          issueSummary,
          screenshot: filename,
        });

      } catch (err) {
        console.error(`  -> [${dev.name}] ERROR:`, err.message);
        auditResults.push({
          page: `${pageConfig.group}: ${pageConfig.name}`,
          path: pageConfig.path,
          device: dev.name,
          dimensions: `${dev.width}x${dev.height}`,
          result: 'Error',
          issueSummary: `Page error: ${err.message}`,
          screenshot: 'N/A',
        });
      } finally {
        await page.close();
      }
    }
  }

  // Also test interactive Modals & Drawer navigation on mobile (iPhone 13 mini: 375x812)
  console.log(`\n==================================================`);
  console.log(`[TESTING INTERACTIVE MODALS & DRAWERS]`);
  console.log(`==================================================`);

  const modalTests = [
    { name: 'Admin Mobile Sidebar Drawer', page: '/dashboard', auth: true, triggerSelector: '#sidebarToggle, button[aria-label*="menu" i], .admin-mobile-toggle, [onclick*="toggleSidebar"]' },
    { name: 'Website Mobile Navigation Menu', page: '/home', auth: false, triggerSelector: '#mobileMenuBtn, .ws-hamburger-btn, button[aria-label*="mobile menu" i]' },
    { name: 'Soil Record Details Modal', page: '/soil', auth: true, triggerSelector: 'table tr[wire\\:click*="openDetail"], table tr button[title*="View" i], table tbody tr:first-child button:first-child' },
    { name: 'Add Student Modal', page: '/students', auth: true, triggerSelector: 'button[wire\\:click*="showAddStudentModal"], button[wire\\:click*="createStudent"], button[wire\\:click*="openModal"]' },
  ];

  for (const mTest of modalTests) {
    const page = await browser.newPage();
    try {
      await page.setViewport({ width: 375, height: 812, deviceScaleFactor: 3, isMobile: true, hasTouch: true });
      if (mTest.auth && adminCookie && Array.isArray(adminCookie.cookies)) {
        for (const c of adminCookie.cookies) {
          await page.setCookie({
            name: c.name,
            value: c.value,
            domain: '127.0.0.1',
            path: '/',
            httpOnly: c.httpOnly,
            secure: false,
          });
        }
      }

      await page.goto(`http://127.0.0.1:8000${mTest.page}`, { waitUntil: 'networkidle2', timeout: 30000 });
      await new Promise(r => setTimeout(r, 600));

      // Attempt to trigger interactive modal/drawer
      const triggered = await page.evaluate((sel) => {
        const btn = document.querySelector(sel) || Array.from(document.querySelectorAll('button, a')).find(b => b.innerText && (b.innerText.includes('Add Student') || b.innerText.includes('Menu') || b.getAttribute('aria-label') === 'Menu'));
        if (btn) {
          btn.click();
          return true;
        }
        return false;
      }, mTest.triggerSelector);

      await new Promise(r => setTimeout(r, 600));

      const modalCheck = await page.evaluate(() => {
        const doc = document.documentElement;
        const windowWidth = window.innerWidth;
        const hasOverflow = doc.scrollWidth > (windowWidth + 2);
        const openModal = document.querySelector('.modal, [role="dialog"], .sidebar-open, #mobileMenu.active, #mobileNav.open, div[style*="z-index: 99"], div[style*="z-index: 100"]');
        let modalRect = null;
        if (openModal) {
          const r = openModal.getBoundingClientRect();
          modalRect = { width: Math.round(r.width), right: Math.round(r.right), overflows: r.right > windowWidth };
        }
        return { hasOverflow, scrollWidth: doc.scrollWidth, modalRect };
      });

      const filename = `interactive_${mTest.name.toLowerCase().replace(/[^a-z0-9]+/g, '_')}_375x812.png`;
      const filepath = path.join(SCREENSHOT_DIR, filename);
      await page.screenshot({ path: filepath, fullPage: false });

      const mResult = modalCheck.hasOverflow || (modalCheck.modalRect && modalCheck.modalRect.overflows) ? 'Problem' : 'OK';
      const mSummary = mResult === 'Problem' 
        ? `Modal/Drawer extends past viewport: scrollWidth ${modalCheck.scrollWidth}px`
        : `Interactive view opens cleanly within screen bounds (triggered: ${triggered})`;

      console.log(`  -> [${mTest.name}] ${mResult}: ${mSummary}`);

      auditResults.push({
        page: `Modal: ${mTest.name}`,
        path: mTest.page,
        device: 'iPhone 13 mini',
        dimensions: '375x812',
        result: mResult,
        scrollWidth: modalCheck.scrollWidth,
        windowWidth: 375,
        hasOverflow: modalCheck.hasOverflow,
        issueSummary: mSummary,
        screenshot: filename,
      });

    } catch (err) {
      console.error(`  -> [${mTest.name}] ERROR:`, err.message);
    } finally {
      await page.close();
    }
  }

  await browser.close();

  // Save full JSON report
  fs.writeFileSync(path.join(__dirname, 'responsive_audit_report.json'), JSON.stringify(auditResults, null, 2));
  console.log(`\nAudit complete! Processed ${auditResults.length} test configurations.`);
  console.log(`Saved report to tests/responsive/responsive_audit_report.json`);
}

run().catch(console.error);
