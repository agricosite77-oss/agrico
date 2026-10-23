# Website Scan Result Metrics & Detected Layers Dropdown Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Fix the unstyled stacked metadata rows in the scan result box to display clean 2-column paired cards matching Image 1's intended card design, and render detected soil layer accordion dropdowns matching Image 2's pill design (`▶ {Munsell} (Soil Percentage - {Pct}%)` with hex color badges).

**Architecture:** 
- In `resources/views/website/scan.blade.php`, harden CSS styling for `.soil-metrics-container`, `.soil-modal-grid-2`, `.soil-modal-grid-card`, `.soil-modal-label`, and `.soil-modal-value` so result cards always render as rounded cards with slate labels and bold values in light mode and dark mode, rather than collapsing into unstyled full-width stacked rows with divider borders.
- Refactor the dynamic layer accordion generator in `processSelectedPhoto` and the swatch layer dropdown in `scan.blade.php` to render accordion headers strictly matching Image 2: rounded pill items with chevron `▶`, label `${rMunsell} (Soil Percentage - ${rPct}%)`, and a right-aligned pill badge displaying the uppercase hex code with that layer's hex color background.
- Guarantee that `#resMultiLayerSection` and `#resMultiLayerItems` correctly display and expand/collapse without JavaScript runtime errors.

**Tech Stack:** Laravel Blade, CSS3 Flexbox/Grid, Vanilla JavaScript (ES6+), Pest PHP testing.

**Spec / Requirements:**
- Image 1 Reference: Fix result metrics (`Color Name`, `Full Code`, `Scan Count`, `Soil Organic`, `Soil Type`, `Near-Match`) so they display as paired card boxes with rounded corners (`16px`), padding, clean backgrounds, and green Near-Match rating.
- Image 2 Reference: Accordion items must have rounded pill headers (`border-radius: 18px; border: 1.5px solid #e2e8f0; background: #ffffff; padding: 13px 18px;`), left-aligned `▶ {Munsell} (Soil Percentage - {Pct}%)`, and right-aligned color pill with `background: {Hex}; color: #ffffff; padding: 4px 14px; border-radius: 9999px; font-family: monospace;`.

## Global Constraints
- Do NOT break existing scan functionality, camera streaming, geolocation pinning, or test suites.
- Maintain full light mode and dark mode theme switching (`html.light-mode` vs `html:not(.light-mode)`).
- Preserve existing data binding IDs (`#resName`, `#resFullCode`, `#resOrganic`, `#resDrainage`, `#resScanCount`, `#resConfidence`, `#resMultiLayerSection`, `#resMultiLayerItems`).
- All existing tests in `tests/Feature/WebsiteScanTest.php` must pass.

---

### Task 1: CSS Hardening for Result Metrics Cards (Image 1 Fix)

**Files:**
- Modify: `resources/views/website/scan.blade.php:2605-2720` and `resources/views/website/scan.blade.php:3440-3475`
- Test: `tests/Feature/WebsiteScanTest.php`

**Interfaces:**
- Consumes: Existing DOM classes `.soil-metrics-container`, `.soil-modal-grid-2`, `.soil-modal-grid-card`, `.soil-modal-label`, `.soil-modal-value`.
- Produces: CSS rules ensuring `#resultContent .soil-modal-grid-card` always renders as paired cards with distinct background, borders, rounded corners, uppercase labels, and bold values, preventing list-style border-bottom collapse.

- [ ] **Step 1: Check existing scan feature tests to establish passing baseline**

Run: `php artisan test --filter=WebsiteScanTest`
Expected: 9 passed.

- [ ] **Step 2: Add CSS rules for `#resultContent` grid cards and labels**

In `resources/views/website/scan.blade.php`, ensure the following CSS rules are added to both general styles and light mode overrides:

```css
/* Result Card Metrics (Paired Grid Cards matching Image 1 Design) */
#resultContent .soil-metrics-container {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
    margin-bottom: 12px !important;
    width: 100% !important;
}

#resultContent .soil-modal-grid-2,
.soil-modal-grid-2 {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 10px !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

/* Light mode grid cards */
html.light-mode #resultContent .soil-modal-grid-card,
html.light-mode .soil-modal-grid-card,
.light-mode .soil-modal-grid-card {
    background: #ffffff !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 16px !important;
    padding: 12px 16px !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04) !important;
    box-sizing: border-box !important;
    min-width: 0 !important;
}

/* Light mode label and values */
html.light-mode #resultContent .soil-modal-label,
html.light-mode .soil-modal-label,
.light-mode .soil-modal-label {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #475569 !important;
    margin-bottom: 4px !important;
    letter-spacing: 0.03em !important;
    text-transform: uppercase !important;
    line-height: 1.2 !important;
}

html.light-mode #resultContent .soil-modal-value,
html.light-mode .soil-modal-value,
.light-mode .soil-modal-value {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #0f172a !important;
    line-height: 1.3 !important;
    word-break: break-word !important;
}

/* Dark mode grid cards */
html:not(.light-mode) #resultContent .soil-modal-grid-card {
    background: #163832 !important;
    border: 1px solid rgba(142, 182, 155, 0.25) !important;
    border-radius: 16px !important;
    padding: 12px 16px !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2) !important;
}

html:not(.light-mode) #resultContent .soil-modal-label {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #8EB69B !important;
    margin-bottom: 4px !important;
    text-transform: uppercase !important;
}

html:not(.light-mode) #resultContent .soil-modal-value {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #DAF1DE !important;
}

/* Near match green highlight */
#resConfidence,
#modalCardConfidence,
.card-near-match .soil-modal-value {
    color: #10b981 !important;
    font-weight: 800 !important;
}
```

- [ ] **Step 3: Verify metrics HTML in `#resultContent`**

Inspect lines 5385–5431 of `resources/views/website/scan.blade.php` to ensure the structure has 3 distinct rows of `.soil-modal-grid-2`:
- Row 1: `Color Name` (`#resName`) and `Full Code` (`#resFullCode`)
- Row 2: `Soil Organic` (`#resOrganic`) and `Soil Type` (`#resDrainage`)
- Row 3: `Scan Count` (`#resScanCount`) and `Near-Match` (`#resConfidence`)

- [ ] **Step 4: Run test to verify no syntax errors or regressions**

Run: `php artisan test --filter=WebsiteScanTest`
Expected: 9 passed.

- [ ] **Step 5: Commit changes**

```bash
git add resources/views/website/scan.blade.php
git commit -m "fix(scan): harden result metrics paired grid card styling for light and dark modes"
```

---

### Task 2: Detected Layers Accordion Header & Pill Redesign (Image 2 Fix)

**Files:**
- Modify: `resources/views/website/scan.blade.php:8580-8630`
- Modify: `resources/views/website/scan.blade.php:2670-2730` (accordion CSS)
- Test: `tests/Feature/WebsiteScanTest.php`

**Interfaces:**
- Consumes: `regions` array from scan response in `processSelectedPhoto`.
- Produces: Layer accordion item elements matching Image 2:
  - Header: Pill button with `▶ ${rMunsell} (Soil Percentage - ${rPct}%)`
  - Right badge: Pill badge with `background: ${rHex}; color: #ffffff;` showing `${rHex.toUpperCase()}`.

- [ ] **Step 1: Add CSS styling for `.layer-accordion-item` and `.layer-accordion-header`**

In `resources/views/website/scan.blade.php` stylesheet:

```css
/* Detected Soil Layers Accordion (Image 2 Pill Design) */
.layer-accordion-item {
    border-radius: 18px !important;
    border: 1.5px solid #e2e8f0 !important;
    background: #ffffff !important;
    overflow: hidden !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    margin-bottom: 8px !important;
}

html:not(.light-mode) .layer-accordion-item {
    background: #163832 !important;
    border-color: rgba(142, 182, 155, 0.25) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
}

.layer-accordion-header {
    width: 100% !important;
    padding: 13px 18px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    cursor: pointer !important;
    background: transparent !important;
    border: none !important;
    text-align: left !important;
    font-family: inherit !important;
    user-select: none !important;
    transition: background-color 0.15s ease !important;
}

.layer-accordion-header:hover {
    background: rgba(0, 0, 0, 0.02) !important;
}

html:not(.light-mode) .layer-accordion-header:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}

.layer-accordion-title {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    font-weight: 800 !important;
    font-size: 13.5px !important;
    color: #0f172a !important;
    min-width: 0 !important;
}

html:not(.light-mode) .layer-accordion-title {
    color: #DAF1DE !important;
}

.layer-chevron {
    display: inline-block !important;
    font-size: 11px !important;
    color: #0f172a !important;
    transition: transform 0.2s ease !important;
    flex-shrink: 0 !important;
}

html:not(.light-mode) .layer-chevron {
    color: #8EB69B !important;
}

.layer-hex-pill {
    font-size: 12px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    padding: 4px 14px !important;
    border-radius: 9999px !important;
    font-family: monospace !important;
    letter-spacing: 0.03em !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
    flex-shrink: 0 !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}
```

- [ ] **Step 2: Update dynamic accordion rendering in `processSelectedPhoto`**

In `resources/views/website/scan.blade.php`, replace the `itemDiv.innerHTML` template inside `regions.slice(1).forEach`:

```javascript
itemDiv.innerHTML = `
    <button type="button" class="layer-accordion-header">
        <div class="layer-accordion-title">
            <span class="layer-chevron">▶</span>
            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${rMunsell} (Soil Percentage - ${rPct}%)</span>
        </div>
        <span class="layer-hex-pill" style="background-color: ${rHex};">
            ${rHex.toUpperCase()}
        </span>
    </button>
    <div class="layer-accordion-body" style="display: none; padding: 14px; border-top: 1px solid #f1f5f9;">
        <!-- Swatch banner matching dominant layer hero style -->
        <div style="width: 100%; height: 66px; border-radius: 14px; background: ${rHex}; box-shadow: 0 3px 10px rgba(0,0,0,0.18); display: flex; align-items: center; gap: 12px; padding: 0 14px; margin-bottom: 10px; box-sizing: border-box;">
            <span id="${avatarId}" style="width:52px;height:52px;aspect-ratio:1/1;border-radius:10px;background:rgba(0,0,0,0.2);flex-shrink:0;overflow:hidden;position:relative;display:flex;align-items:center;justify-content:center;">
                <img class="layer-zoom-img" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none;" />
                <span class="layer-zoom-fallback" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;"></span>
            </span>
            <div style="flex: 1; min-width: 0; line-height: 1.25;">
                <div style="font-size: 16px; font-weight: 800; color: #ffffff;">${rMunsell}</div>
                <div style="font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.9);">${rLabel}</div>
            </div>
            <span style="font-size: 11px; font-weight: 800; color: #ffffff; background: rgba(0,0,0,0.45); border: 1px solid rgba(255,255,255,0.25); padding: 3px 9px; border-radius: 9999px; font-family: monospace; flex-shrink: 0;">${rHex.toUpperCase()}</span>
        </div>

        <!-- Row 1: Color Name, Full Code, Proportion -->
        <div class="soil-modal-grid-3" style="margin-bottom: 8px;">
            <div class="soil-modal-grid-card">
                <div class="soil-modal-label">Color Name</div>
                <div class="soil-modal-value truncate" title="${rLabel}">${rLabel}</div>
            </div>
            <div class="soil-modal-grid-card">
                <div class="soil-modal-label">Full Code</div>
                <div class="soil-modal-value">${rMunsell}</div>
            </div>
            <div class="soil-modal-grid-card">
                <div class="soil-modal-label">Proportion</div>
                <div class="soil-modal-value">${rPct}%</div>
            </div>
        </div>

        <!-- Row 2: Soil Organic, Soil Type, Near-Match -->
        <div class="soil-modal-grid-3" style="margin-bottom: 8px;">
            <div class="soil-modal-grid-card">
                <div class="soil-modal-label">Soil Organic</div>
                <div class="soil-modal-value">${rOrganic}</div>
            </div>
            <div class="soil-modal-grid-card">
                <div class="soil-modal-label">Soil Type</div>
                <div class="soil-modal-value">${rDrainage}</div>
            </div>
            <div class="soil-modal-grid-card">
                <div class="soil-modal-label">Near-Match</div>
                <div class="soil-modal-value" style="color: #10b981; font-weight: 800;">${rConf}</div>
            </div>
        </div>
    </div>
`;
```

- [ ] **Step 3: Run tests to verify script integrity**

Run: `php artisan test --filter=WebsiteScanTest`
Expected: 9 passed.

- [ ] **Step 4: Commit changes**

```bash
git add resources/views/website/scan.blade.php
git commit -m "fix(scan): update detected layer accordion header design to match Image 2 pill format"
```

---

### Task 3: Ensure Dropdown Visibility and Fallback for Scans

**Files:**
- Modify: `resources/views/website/scan.blade.php:8530-8710`
- Test: `tests/Feature/WebsiteScanTest.php`

**Interfaces:**
- Consumes: `#resMultiLayerSection`, `#resMultiLayerItems`, `regions`.
- Produces: Robust visibility logic ensuring multi-layer sections are displayed whenever multiple detected layers exist (`regions.length > 1`), and handling layer expansion smoothly without clipping.

- [ ] **Step 1: Audit and protect `#resMultiLayerSection` visibility logic**

Check line 8532 in `scan.blade.php`:
```javascript
const hasMultipleLayers = Boolean(regions && Array.isArray(regions) && regions.length > 1);
if (hasMultipleLayers && resMultiLayerSection && resMultiLayerItems) {
    resMultiLayerSection.style.display = 'block';
    // populate items
} else if (resMultiLayerSection) {
    resMultiLayerSection.style.display = 'none';
}
```
Ensure that if mock/demo scans or multi-color scans are selected, `hasMultipleLayers` triggers display cleanly without runtime errors.

- [ ] **Step 2: Add click event listener to toggle chevron and body**

Confirm click handler on `.layer-accordion-header`:
```javascript
const headerBtn = itemDiv.querySelector('.layer-accordion-header');
const bodyEl = itemDiv.querySelector('.layer-accordion-body');
const chevronEl = itemDiv.querySelector('.layer-chevron');

if (headerBtn) {
    headerBtn.addEventListener('click', () => {
        if (!bodyEl) return;
        const willOpen = bodyEl.style.display !== 'block';
        bodyEl.style.display = willOpen ? 'block' : 'none';
        if (chevronEl) chevronEl.style.transform = willOpen ? 'rotate(90deg)' : 'rotate(0deg)';
        if (typeof syncScannerCardHeights === 'function') syncScannerCardHeights();
    });
}
```

- [ ] **Step 3: Run automated test suite**

Run: `php artisan test --filter=WebsiteScanTest`
Expected: 9 passed.

- [ ] **Step 4: Commit changes**

```bash
git add resources/views/website/scan.blade.php
git commit -m "fix(scan): ensure robust layer accordion expansion and multi-layer visibility"
```

---

### Task 4: Full Verification and Visual Alignment Check

**Files:**
- Review: `resources/views/website/scan.blade.php`
- Run: `php artisan test`

- [ ] **Step 1: Run complete test suite**

Run: `php artisan test --filter=WebsiteScan`
Expected: All tests pass.

- [ ] **Step 2: Compare against Image 1 and Image 2**
- Image 1: Verify `Color Name`, `Full Code`, `Scan Count`, `Soil Organic`, `Soil Type`, `Near-Match` render inside rounded `.soil-modal-grid-card` cards arranged in 2 columns.
- Image 2: Verify detected layer headers display as rounded pills with `▶ {Munsell} (Soil Percentage - {Pct}%)` and right-aligned hex badges with matching background color.

- [ ] **Step 3: Final Git status check**

Run: `git status`
Expected: Clean working tree on current branch.
