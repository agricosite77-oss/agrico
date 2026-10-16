import puppeteer from 'puppeteer-core';
import { execSync } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const MODAL_SCREENSHOT_DIR = path.join(__dirname, 'screenshots', 'modals');
if (!fs.existsSync(MODAL_SCREENSHOT_DIR)) {
  fs.mkdirSync(MODAL_SCREENSHOT_DIR, { recursive: true });
}

// 1. Get Admin Session Cookie
let adminCookie = null;
try {
  const cookieJson = execSync('php tests/responsive/get_admin_session.php', { cwd: path.join(__dirname, '../..') }).toString();
  adminCookie = JSON.parse(cookieJson);
  console.log(`[AUTH] Admin cookie obtained for user: ${adminCookie.user.name}`);
} catch (e) {
  console.error('[AUTH ERROR] Could not obtain admin cookie:', e.message);
  process.exit(1);
}

const DEVICES = [
  { name: 'Small Phone 320px', slug: '320x568', width: 320, height: 568, dpr: 2, isMobile: true, hasTouch: true },
  { name: 'Galaxy A6s', slug: '360x720', width: 360, height: 720, dpr: 2, isMobile: true, hasTouch: true },
  { name: 'iPhone 13 mini', slug: '375x812', width: 375, height: 812, dpr: 3, isMobile: true, hasTouch: true },
  { name: 'Landscape Phone', slug: '812x375', width: 812, height: 375, dpr: 3, isMobile: true, hasTouch: true },
  { name: 'Tablet 768px', slug: '768x1024', width: 768, height: 1024, dpr: 2, isMobile: true, hasTouch: true },
  { name: 'Desktop 1280px', slug: '1280x800', width: 1280, height: 800, dpr: 1, isMobile: false, hasTouch: false },
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

  const testReport = [];

  const MODAL_TESTS = [
    // 14. Manage FAQs - FAQ Detail Modal
    {
      name: 'FAQ Detail Modal',
      url: 'http://127.0.0.1:8000/faqs',
      open: async (page) => {
        return await page.evaluate(() => {
          const lw = window.Livewire?.all()?.find(c => c.name === 'faq.faq-detail-modal');
          if (lw) {
            lw.call('openModal', 1);
            return true;
          }
          return false;
        });
      },
      closeBtnSelector: '.faq-detail-modal-header button, .modal-close-circle-btn, button[title="Close modal"]',
      containerSelector: '.faq-detail-modal-container',
    },
    // 15. Student Records - Add Student Modal
    {
      name: 'Add Student Modal',
      url: 'http://127.0.0.1:8000/students',
      open: async (page) => {
        return await page.evaluate(() => {
          const lw = window.Livewire?.all()?.find(c => c.name === 'student.student-record');
          if (lw) {
            lw.set('showAddModal', true);
            return true;
          }
          return false;
        });
      },
      closeBtnSelector: 'button[title="Close modal"], x-close-button',
      containerSelector: '[style*="border-radius: 24px"], .add-student-modal-card',
    },
    // 16. Student Records - Section Records & Activity Modal (Allowed Horizontal Scroll Exception)
    {
      name: 'Section Records & Activity (Save as Copy)',
      url: 'http://127.0.0.1:8000/students',
      open: async (page) => {
        return await page.evaluate(() => {
          const lw = window.Livewire?.all()?.find(c => c.name === 'student.student-record');
          if (lw) {
            lw.call('openSectionRecordsModal', 'A');
            return true;
          }
          return false;
        });
      },
      closeBtnSelector: 'button[title="Close modal"], x-close-button',
      containerSelector: '.xl-wrap, [style*="border-radius: 24px"]',
    },
    // 17. Website Mobile Menu Drawer
    {
      name: 'Website Mobile Menu Drawer',
      url: 'http://127.0.0.1:8000/home',
      open: async (page) => {
        return await page.evaluate(() => {
          const toggle = document.querySelector('[data-mobile-menu-toggle], .mobile-menu-btn, button[aria-label="Toggle Navigation"]');
          if (toggle) {
            toggle.click();
            return true;
          }
          return false;
        });
      },
      containerSelector: '.ws-mobile-menu, [data-mobile-menu]',
    },
    // 18. Admin Mobile Sidebar Drawer
    {
      name: 'Admin Mobile Sidebar Drawer',
      url: 'http://127.0.0.1:8000/dashboard',
      open: async (page) => {
        return await page.evaluate(() => {
          const toggle = document.querySelector('.admin-mobile-toggle, #sidebarToggle, button[aria-label="Open sidebar menu"]');
          if (toggle) {
            toggle.click();
            return true;
          }
          return false;
        });
      },
      containerSelector: '.admin-sidebar',
    },
  ];

  for (const modalTest of MODAL_TESTS) {
    console.log(`\n==================================================`);
    console.log(`[TESTING POPUP] ${modalTest.name}`);
    console.log(`==================================================`);

    for (const dev of DEVICES) {
      const page = await browser.newPage();
      try {
        await page.setViewport({
          width: dev.width,
          height: dev.height,
          deviceScaleFactor: dev.dpr,
          isMobile: dev.isMobile,
          hasTouch: dev.hasTouch,
        });

        // Set Auth Cookies
        if (adminCookie && Array.isArray(adminCookie.cookies)) {
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

        let navSuccess = false;
        for (let attempt = 0; attempt < 3; attempt++) {
          try {
            await page.goto(modalTest.url, { waitUntil: 'networkidle2', timeout: 25000 });
            navSuccess = true;
            break;
          } catch (e) {
            await new Promise(r => setTimeout(r, 1000));
          }
        }
        if (!navSuccess) {
          throw new Error(`Failed to navigate to ${modalTest.url} after 3 attempts`);
        }
        await new Promise(r => setTimeout(r, 800));

        // Open Modal
        const opened = await modalTest.open(page);
        await new Promise(r => setTimeout(r, 600)); // wait for transition

        // Evaluate bounding box, horizontal overflow, and close button tap size
        const evalResult = await page.evaluate((testConfig, devWidth, devHeight) => {
          const windowWidth = window.innerWidth;
          const windowHeight = window.innerHeight;
          const docScrollWidth = document.documentElement.scrollWidth;
          const bodyScrollWidth = document.body ? document.body.scrollWidth : 0;
          const pageScrollWidth = Math.max(docScrollWidth, bodyScrollWidth);
          const hasPageOverflow = pageScrollWidth > (windowWidth + 2);

          // Find modal container element
          let containerEl = null;
          if (testConfig.containerSelector) {
            const matches = Array.from(document.querySelectorAll(testConfig.containerSelector));
            containerEl = matches.find(el => el.offsetParent !== null || el.getBoundingClientRect().width > 0);
          }

          let containerBox = null;
          let containerFits = true;
          let containerOverflowX = 0;
          let containerOverflowY = 0;

          if (containerEl) {
            const rect = containerEl.getBoundingClientRect();
            containerBox = {
              left: Math.round(rect.left),
              right: Math.round(rect.right),
              top: Math.round(rect.top),
              bottom: Math.round(rect.bottom),
              width: Math.round(rect.width),
              height: Math.round(rect.height),
            };

            // Exception: section records table container (.xl-wrap) allows internal scroll
            const isAllowedWrap = containerEl.classList.contains('xl-wrap') || containerEl.closest('.xl-wrap');
            if (!isAllowedWrap) {
              if (rect.right > (windowWidth + 2)) {
                containerFits = false;
                containerOverflowX = Math.round(rect.right - windowWidth);
              }
              if (rect.left < -2) {
                containerFits = false;
              }
            }
          }

          // Check Close Button
          let closeBtnBox = null;
          let closeBtnAdequate = true;
          if (testConfig.closeBtnSelector) {
            const closeBtns = Array.from(document.querySelectorAll(testConfig.closeBtnSelector));
            const activeBtn = closeBtns.find(b => b.offsetParent !== null || b.getBoundingClientRect().width > 0);
            if (activeBtn) {
              const r = activeBtn.getBoundingClientRect();
              closeBtnBox = {
                width: Math.round(r.width),
                height: Math.round(r.height),
              };
              // Target: >= 40x40 px (allowing 38px rounding margin)
              if (r.width < 38 || r.height < 38) {
                closeBtnAdequate = false;
              }
            }
          }

          return {
            windowWidth,
            windowHeight,
            pageScrollWidth,
            hasPageOverflow,
            containerBox,
            containerFits,
            containerOverflowX,
            closeBtnBox,
            closeBtnAdequate,
          };
        }, modalTest, dev.width, dev.height);

        const status = (evalResult.containerFits && (!evalResult.hasPageOverflow || modalTest.name.includes('Section Records'))) ? 'PASSED' : 'FAILED';
        
        console.log(`  -> [${dev.name} (${dev.width}x${dev.height})] ${status}`);
        if (evalResult.containerBox) {
          console.log(`     Container size: ${evalResult.containerBox.width}x${evalResult.containerBox.height}px | Fits: ${evalResult.containerFits}`);
        }
        if (evalResult.closeBtnBox) {
          console.log(`     Close Button: ${evalResult.closeBtnBox.width}x${evalResult.closeBtnBox.height}px | Target >= 40x40: ${evalResult.closeBtnAdequate}`);
        }

        // Take screenshot of opened modal
        const ssFilename = `${modalTest.name.toLowerCase().replace(/[^a-z0-9]/g, '_')}_${dev.slug}.png`;
        const ssPath = path.join(MODAL_SCREENSHOT_DIR, ssFilename);
        await page.screenshot({ path: ssPath, fullPage: false });

        testReport.push({
          modal: modalTest.name,
          device: dev.name,
          dimensions: `${dev.width}x${dev.height}`,
          status,
          containerFits: evalResult.containerFits,
          closeBtnAdequate: evalResult.closeBtnAdequate,
          details: evalResult,
        });

      } catch (err) {
        console.error(`  -> [${dev.name}] ERROR:`, err.message);
        testReport.push({
          modal: modalTest.name,
          device: dev.name,
          dimensions: `${dev.width}x${dev.height}`,
          status: 'ERROR',
          error: err.message,
        });
      } finally {
        await page.close();
        await new Promise(r => setTimeout(r, 400));
      }
    }
  }

  await browser.close();

  const reportPath = path.join(__dirname, 'modal_remaining_test_report.json');
  fs.writeFileSync(reportPath, JSON.stringify(testReport, null, 2));
  console.log(`\n==================================================`);
  console.log(`Remaining Modal Tests Completed! Report saved to ${reportPath}`);
  console.log(`==================================================`);
}

run().catch(console.error);
