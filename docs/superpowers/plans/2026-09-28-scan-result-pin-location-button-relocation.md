# Scan Soil Result Pin Location Button Relocation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Move the "Pin Location" button directly beneath the "Description" card in every scan soil result (both the primary result and each secondary/tertiary multi-layer result), and leave only the "Save Copy" button in the action row below the "SOIL NORMALIZATION: BEFORE / AFTER" card.

**Architecture:** Update Blade views in `resources/views/website/scan.blade.php`. Reposition the primary `#btnPinLocationScan` button immediately after `#resDescCard`. In the dynamic multi-layer accordion renderer, append a dedicated "Pin Location" button immediately after each layer's Description card, wired to `openPinLocationModal(layerData)`. Clean up the post-normalization action row `#resActionButtonsRow` so that it exclusively houses `#btnSaveCopyScan`. Update `openPinLocationModal` to support layer context if provided.

**Tech Stack:** Laravel Blade, JavaScript, HTML5/CSS3, Pest/PHPUnit

**Spec:** User request: "i want that scan soil result every result after the Description there is a Pin Location button and now remove the Pin Location button after the SOIL NORMALIZATION BEFORE / AFTER so that only button there is the Save Copy only"

## Global Constraints
- Do NOT alter scan analysis algorithms, normalization pipeline, or API contracts.
- The "Save Copy" button must remain fully functional and positioned after the "SOIL NORMALIZATION: BEFORE / AFTER" section as the sole button in `#resActionButtonsRow`.
- The "Pin Location" button must appear immediately after the "Description" card on every soil result (primary and any multi-layer sub-results).
- Clicking "Pin Location" on any result opens the Pin Soil Location modal pre-populated with that specific result's soil details.
- All existing tests in `tests/Feature/WebsiteScanPinTest.php` must continue to pass.

---

### Task 1: Relocate Pin Location Button on Primary Scan Result Card

**Files:**
- Modify: `resources/views/website/scan.blade.php:4318-4365`
- Test: `tests/Feature/WebsiteScanPinTest.php`

**Interfaces:**
- Consumes: `#resDescCard`, `#resBeforeAfterCard`, `#resActionButtonsRow`, `openPinLocationModal()`
- Produces: Primary Pin Location button positioned directly under `#resDescCard`; `#resActionButtonsRow` containing only `#btnSaveCopyScan`.

- [ ] **Step 1: Inspect and relocate `#btnPinLocationScan` markup**
In `resources/views/website/scan.blade.php`:
1. Move `#btnPinLocationScan` out of `#resActionButtonsRow` and place it in a dedicated container immediately following `#resDescCard` (before `#resMultiLayerSection`):
```html
{{-- Soil Description --}}
<div id="resDescCard" class="soil-modal-grid-card soil-modal-desc-card" style="margin-bottom: 10px; padding: 12px 16px; overflow: hidden; max-width: 100%; box-sizing: border-box;">
    <div class="soil-modal-label">
        Description
    </div>
    <div id="resDesc" class="soil-modal-value" style="font-size: 13px; font-weight: 700; margin: 0; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
        Sample processed successfully using AgriCo Munsell Determination Algorithm.
    </div>
</div>

{{-- Primary Soil Result: Pin Location Button (Placed directly after Description) --}}
<div id="resPrimaryPinLocationWrapper" style="margin-bottom: 12px;">
    <button type="button" id="btnPinLocationScan" class="btn-res-action btn-res-pin-location" onclick="openPinLocationModal()" style="width: 100%; justify-content: center;" title="Pin Soil Location to Map">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>Pin Location</span>
    </button>
</div>
```

2. In the action row beneath `#resBeforeAfterCard`, keep only `#btnSaveCopyScan`:
```html
{{-- Action Buttons Row: Save Copy Only --}}
<div id="resActionButtonsRow" class="res-action-buttons-row">
    <button type="button" id="btnSaveCopyScan" class="btn-res-action btn-res-save-copy" onclick="saveCurrentScanCopy()" style="width: 100%; justify-content: center;" title="Save Copy of Soil Record">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
        </svg>
        <span>Save Copy</span>
    </button>
</div>
```

- [ ] **Step 2: Run tests to verify primary button placement**
Run: `php artisan test --filter=WebsiteScanPinTest`
Expected: PASS

- [ ] **Step 3: Commit Task 1 changes**
```bash
git add resources/views/website/scan.blade.php
git commit -m "refactor(scan): relocate primary pin location button after description and leave save copy only after normalization"
```

---

### Task 2: Add Pin Location Button to Each Multi-Layer Result Card

**Files:**
- Modify: `resources/views/website/scan.blade.php:6945-7045` and `resources/views/website/scan.blade.php:8160-8200`
- Test: `tests/Feature/WebsiteScanPinTest.php`

**Interfaces:**
- Consumes: `resMultiLayerItems`, `regions`, `openPinLocationModal(layerData)`
- Produces: Pin Location button rendered inside each accordion item right after the Description card.

- [ ] **Step 1: Enhance `openPinLocationModal` to accept optional `layerData`**
In `resources/views/website/scan.blade.php` around line 8162:
```javascript
let currentPinTargetSoil = null;

function openPinLocationModal(layerData = null) {
    currentPinTargetSoil = layerData;
    const modal = document.getElementById('pinSoilLocationModal');
    if (!modal) return;

    // Populate soil specimen details from layerData or current primary scan
    const soilName = layerData?.label || layerData?.color_name || layerData?.soil_name || document.getElementById('resName')?.textContent || (lastScanMatch?.name) || 'Determined Soil Specimen';
    const fullCode = layerData?.munsell || layerData?.munsell_code || layerData?.full_code || document.getElementById('resFullCode')?.textContent || (lastScanMatch?.code) || '10YR 4/3';
    let hexColor = layerData?.hex || layerData?.hex_color || document.getElementById('resHexPill')?.textContent || (lastScanMatch?.hex) || '#66503C';
    if (hexColor && !hexColor.startsWith('#')) hexColor = '#' + hexColor;

    const swatchEl = document.getElementById('pinModalSwatch');
    const codeEl   = document.getElementById('pinModalCode');
    const nameEl   = document.getElementById('pinModalName');
    const noteEl   = document.getElementById('pinScanNoteInput');

    if (swatchEl) swatchEl.style.backgroundColor = hexColor;
    if (codeEl) codeEl.textContent = fullCode;
    if (nameEl) nameEl.textContent = soilName;
    if (noteEl && (!noteEl.value || noteEl.value.includes('Soil sample classified as'))) {
        noteEl.value = `Soil sample classified as ${soilName} (${fullCode}).`;
    }
    // ... continue standard pin modal initialization
}
```

And in `submitPinLocation()`:
```javascript
const soilId = (currentPinTargetSoil && (currentPinTargetSoil.soil_id || currentPinTargetSoil.id)) || (lastScanMatch && lastScanMatch.soil_id) || null;
```

- [ ] **Step 2: Add Pin Location button inside the multi-layer accordion body**
In `resources/views/website/scan.blade.php` inside `regions.slice(1).forEach((r, idx) => { ... })`:
Immediately following the `<!-- Description -->` card inside `layer-accordion-body`:
```html
<!-- Description -->
<div class="soil-modal-grid-card" style="margin-bottom: 8px;">
    <div class="soil-modal-label">Description</div>
    <div class="soil-modal-value layer-accordion-desc" style="font-size: 13px; font-weight: 700; line-height: 1.5; white-space: pre-line;">${rDesc}</div>
</div>

<!-- Layer Pin Location Button -->
<div style="margin-top: 8px;">
    <button type="button" class="btn-res-action btn-res-pin-location btn-layer-pin-btn" style="width: 100%; justify-content: center; padding: 9px 14px; font-size: 12.5px;" title="Pin This Soil Layer to Map">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>Pin Location</span>
    </button>
</div>
```
Attach click event listener to the button passing `r`:
```javascript
const layerPinBtn = itemDiv.querySelector('.btn-layer-pin-btn');
if (layerPinBtn) {
    layerPinBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        openPinLocationModal(r);
    });
}
```

- [ ] **Step 3: Run automated test suite**
Run: `php artisan test --filter=WebsiteScanPinTest`
Expected: PASS

- [ ] **Step 4: Commit Task 2 changes**
```bash
git add resources/views/website/scan.blade.php
git commit -m "feat(scan): add pin location button to each multi-layer result accordion item"
```

---

### Task 3: Also Update Multi-Layer Items in History Soil Record Modal for Consistency

**Files:**
- Modify: `resources/views/website/scan.blade.php:7580-7615`
- Test: `tests/Feature/WebsiteScanPinTest.php`

**Interfaces:**
- Consumes: `#modalMultiLayerItems`, `openSoilRecordModal`
- Produces: Consistent Pin Location button after Description in modal multi-layer items.

- [ ] **Step 1: Add Pin Location button after Description in `modalMultiLayerItems`**
In `resources/views/website/scan.blade.php` around lines 7586-7590:
In the modal's multi-layer accordion template, add the Pin Location button directly after Description:
```html
<div class="soil-modal-grid-card" style="padding: 12px 16px; margin-bottom: 8px;">
    <div class="soil-modal-label">Description</div>
    <div class="soil-modal-value" style="font-size: 13px; font-weight: 700; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">${rDesc}</div>
</div>
<div style="margin-top: 8px;">
    <button type="button" class="btn-res-action btn-res-pin-location modal-layer-pin-btn" style="width: 100%; justify-content: center; padding: 9px 14px; font-size: 12.5px;" title="Pin This Soil Layer to Map">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>Pin Location</span>
    </button>
</div>
```
Attach listener:
```javascript
const modalLayerPinBtn = itemDiv.querySelector('.modal-layer-pin-btn');
if (modalLayerPinBtn) {
    modalLayerPinBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        closeSoilRecordModal();
        openPinLocationModal(r);
    });
}
```

- [ ] **Step 2: Run automated test suite**
Run: `php artisan test --filter=WebsiteScanPinTest`
Expected: PASS

- [ ] **Step 3: Commit Task 3 changes**
```bash
git add resources/views/website/scan.blade.php
git commit -m "feat(scan): add pin location button to modal multi-layer items"
```

---

### Task 4: Cache Clear & End-to-End Verification

**Files:**
- Verify: `resources/views/website/scan.blade.php`
- Run: `php artisan view:clear; php artisan optimize:clear; php artisan test`

- [ ] **Step 1: Clear cached views and configuration**
Run: `php artisan view:clear; php artisan optimize:clear`
Expected: All caches cleared successfully.

- [ ] **Step 2: Run full regression tests**
Run: `php artisan test --filter=Website`
Expected: PASS (9 tests, 49+ assertions)

- [ ] **Step 3: Verify visual behavior**
Confirm in the scanner view that:
1. Under the Primary result Description, the "Pin Location" button is cleanly displayed.
2. In any Multi-Layer (secondary/tertiary) accordion row, under Description, a "Pin Location" button is displayed.
3. Below "SOIL NORMALIZATION: BEFORE / AFTER", ONLY the "Save Copy" button appears, taking up the full row width neatly.
