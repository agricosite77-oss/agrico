# Scan Result Automatic Full Display Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Automatically and immediately display all scan results (all 6 detected soil layers, the Soil Normalization Before/After card, and the Save Copy / Pin All action buttons) upon scan completion, without requiring manual clicking of "Expand Result" or "Collapse View".

**Architecture:** Fix the asynchronous height synchronization logic in `syncScannerCardHeights()` and `processSelectedPhoto()` so that the result card never gets clamped to the camera viewfinder's height (`leftH`) during skeleton transitions, and gracefully recalculates natural auto-height upon completion of rendering and image loading.

**Tech Stack:** Laravel Blade, Vanilla JavaScript, CSS Flexbox/Grid, PHPUnit (`WebsiteScanPinTest`).

## Global Constraints
- Preserve existing scan pipeline and server-side responses intact.
- Retain the dual-mode expand toggle button (`#btnToggleExpandResultCard`) for users who still wish to toggle full-width grid expansion (`.is-expanded`).
- Maintain the ordering: Soil Record Layers 1–6 -> Soil Normalization Before/After Card -> Save Copy & Pin All action buttons.
- Do not reintroduce removed quick chips.

---

### Root Cause Analysis

1. **Premature Height Sync:** When a scan finishes, `processSelectedPhoto()` triggers `window.AgriMotion.transitionSkeleton(scanLoadingOverlay, resContent, { minShow: 220, step: 110 })`. This animation runs for approximately 350ms to 400ms.
2. **Fixed Height Lock:** At line 7822, `setTimeout(syncScannerCardHeights, 50)` executes at 50ms—while `resContent` still has `display: none` in the DOM.
3. **Condition Evaluates to False:** Inside `syncScannerCardHeights()`, `isContentVisible` checks `resultContent.style.display !== 'none'`. Because it is still `none`, it executes:
   ```javascript
   resultCard.style.height = leftH + 'px';
   resultCard.style.minHeight = leftH + 'px';
   ```
   This locks `#resultCard` to the exact height of the camera viewfinder (~560px).
4. **Content Clipped:** Any elements rendered below ~560px (layers 2–6, Before/After card, action buttons) are clipped or pushed below the card's boundary.
5. **Why Toggling Expand/Collapse Revealed Everything:**
   - Clicking **Expand Result** toggled `.is-expanded`, where `syncScannerCardHeights()` has a special check:
     `if (resultCard.classList.contains('is-expanded')) { resultCard.style.height = 'auto'; return; }`
     This instantly removed the 560px lock.
   - Clicking **Collapse View** removed `.is-expanded` and ran `syncScannerCardHeights()`. But because `transitionSkeleton` was already finished, `isContentVisible` was now `true`, so it set `resultCard.style.height = 'auto'`.
   - The user observed that either toggle immediately showed all content.

---

### Task 1: Harden `syncScannerCardHeights()` to Prevent Premature Fixed Height Locks

**Files:**
- Modify: `resources/views/website/scan.blade.php:6502-6531`

**Interfaces:**
- Consumes: `#scannerCard`, `#resultCard`, `#resultContent`, `#resultPlaceholder`, `window.currentScanResult`, `lastScanMatch`
- Produces: Robust card height syncing where `#resultCard` uses `height: auto` whenever scan results are loaded or placeholder is hidden.

- [ ] **Step 1: Inspect and update `syncScannerCardHeights` logic**
  Update `syncScannerCardHeights()` so that:
  - If `#resultPlaceholder` is hidden (`style.display === 'none'`), OR `#resultContent` has content, OR `lastScanMatch` is defined, the system recognizes that results are active.
  - When results are active, `#resultCard.style.height` MUST be set to `'auto'`, and `minHeight` set to `leftH + 'px'`.
  - When no results are active (initial placeholder state), `#resultCard.style.height` matches `leftH + 'px'`.

```javascript
function syncScannerCardHeights() {
    const scannerCard = document.querySelector('.scanner-card');
    const resultCard = document.querySelector('.result-card');
    const resultContent = document.getElementById('resultContent');
    const resultPlaceholder = document.getElementById('resultPlaceholder');

    if (scannerCard && resultCard) {
        if (resultCard.classList.contains('is-expanded')) {
            resultCard.style.height = 'auto';
            resultCard.style.minHeight = '480px';
            return;
        }
        if (window.innerWidth > 900) {
            const leftH = scannerCard.offsetHeight;
            if (leftH > 0) {
                const hasScanResult = (resultPlaceholder && resultPlaceholder.style.display === 'none') ||
                                      (resultContent && resultContent.style.display !== 'none' && window.getComputedStyle(resultContent).display !== 'none') ||
                                      (typeof lastScanMatch !== 'undefined' && lastScanMatch !== null);
                if (!hasScanResult) {
                    resultCard.style.height = leftH + 'px';
                    resultCard.style.minHeight = leftH + 'px';
                } else {
                    resultCard.style.height = 'auto';
                    resultCard.style.minHeight = leftH + 'px';
                }
            }
        } else {
            resultCard.style.minHeight = '420px';
            resultCard.style.height = 'auto';
        }
    }
}
```

---

### Task 2: Chain Height Recalculation to `transitionSkeleton()` and Image Load Events

**Files:**
- Modify: `resources/views/website/scan.blade.php:7810-7835`

**Interfaces:**
- Consumes: `window.AgriMotion.transitionSkeleton()`, `resCardContainer`, `syncScannerCardHeights()`
- Produces: Smooth transition where `#resultCard` expands dynamically to accommodate all rendered rows and images as they load.

- [ ] **Step 1: Update completion handler in `processSelectedPhoto()`**
  - Immediately set `resCardContainer.style.height = 'auto'` and `resCardContainer.style.minHeight = '480px'`.
  - If `transitionSkeleton` returns a Promise, attach `.then(() => { syncScannerCardHeights(); })`.
  - Add debounced/staggered `syncScannerCardHeights()` calls at 100ms, 300ms, and 600ms to guarantee that dynamic content (such as multi-layer accordion rows and images) never gets clipped.

```javascript
const resCardContainer = document.querySelector('.result-card');
if (resCardContainer) {
    resCardContainer.style.height = 'auto';
    resCardContainer.style.minHeight = '480px';
}

if (window.AgriMotion && typeof window.AgriMotion.transitionSkeleton === 'function' && scanLoadingOverlay && scanLoadingOverlay.style.display !== 'none') {
    window.AgriMotion.transitionSkeleton(scanLoadingOverlay, resContent, { minShow: 220, step: 110 }).then(() => {
        if (resCardContainer) resCardContainer.style.height = 'auto';
        syncScannerCardHeights();
    }).catch(() => {
        if (resCardContainer) resCardContainer.style.height = 'auto';
        syncScannerCardHeights();
    });
} else {
    if (scanLoadingOverlay) scanLoadingOverlay.style.display = 'none';
    if (resContent) resContent.style.display = 'block';
    if (resCardContainer) resCardContainer.style.height = 'auto';
}

setTimeout(syncScannerCardHeights, 100);
setTimeout(syncScannerCardHeights, 350);
setTimeout(syncScannerCardHeights, 650);
```

- [ ] **Step 2: Attach `onload` to Before & After image**
  When `#resBeforeAfterImg.src` is set, attach an `onload` callback to trigger `syncScannerCardHeights()`, ensuring the card automatically expands to accommodate the image dimensions without scrollbars or cutoff.

---

### Task 3: Verification and Test Suite Execution

**Files:**
- Test: `tests/Feature/WebsiteScanPinTest.php`
- Cache Clear: `php artisan view:clear; php artisan view:cache`

- [ ] **Step 1: Run feature test suite**
  Execute `php artisan test tests/Feature/WebsiteScanPinTest.php` to ensure zero regressions in scan results or pin flows.

- [ ] **Step 2: Clear and rebuild view cache**
  Execute `php artisan view:clear` to ensure the blade changes take effect immediately on the live server.

- [ ] **Step 3: Commit and push changes**
  Commit to `main` and push to remote.
