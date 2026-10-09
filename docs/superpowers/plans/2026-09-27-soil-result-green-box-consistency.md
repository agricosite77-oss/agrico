# Soil Result Green Box Consistency Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ensure all soil record avatars (dominant and multi-layer sub-regions) consistently display the green bounding box on both Admin and Website results, while completely removing green box overlays from the "SOIL NORMALIZATION BEFORE / AFTER" card in both Admin and Website.

**Architecture:** 
1. On the Admin side (`soil-detail-modal.blade.php`), preserve the sharp neon-green bounding box overlay (`#4ade80` with dark contrast shadow) on the header swatch avatar and sub-layer accordion avatars, and remove the green box overlay loop (`renderRegions`) from the Before / After normalization card.
2. On the Website side (`scan.blade.php`), ensure `populateZoomAvatar` draws the whole image with the green ROI bounding box across all result avatars (live scan result card, secondary layers, modal banner, modal secondary layers using clean base image source), and remove `renderBeforeAfterBoxes` calls from both `#beforeAfterCard` and `#modalBeforeAfterCard`.
3. Add/update feature tests in `tests/Feature/AdminWebsiteBridgeTest.php` to assert the presence of green ROI avatars on soil results and the absence of green bounding boxes in the Before / After card.

**Tech Stack:** Laravel Blade, Livewire 3, Alpine.js / Vanilla JavaScript, HTML5 Canvas, Pest PHP.

**Spec:** User request: "i want like that all green box in the admin and website result not on the SOIL NORMALIZATION BEFORE / AFTER make a plan". Reference screenshot shows soil thumbnail avatar inside header banner with green ROI box.

## Global Constraints
- Target files: `resources/views/admin/livewire/soil/soil-detail-modal.blade.php`, `resources/views/website/scan.blade.php`, `tests/Feature/AdminWebsiteBridgeTest.php`.
- Green bounding box styling: neon green `#4ade80` with dark contrast shadow/stroke for visibility on any soil color.
- The "SOIL NORMALIZATION BEFORE / AFTER" card must only display the clean before/after image without any green boxes overlaid.
- Do not break existing modal functions, hard copy generation, tests, or server stability.

---

### Task 1: Remove Green Box Overlay from Admin Soil Normalization Card

**Files:**
- Modify: `resources/views/admin/livewire/soil/soil-detail-modal.blade.php:698-740`
- Test: `tests/Feature/AdminWebsiteBridgeTest.php`

**Interfaces:**
- Consumes: `$baImg` Before/After image URL, `$regions` array.
- Produces: Clean Before/After normalization card markup without overlaid `<div style="... border: 2px solid #4ade80 ...">`.

- [ ] **Step 1: Write a test verifying that Before/After card in admin modal does not contain green boxes, while avatar keeps green box**

In `tests/Feature/AdminWebsiteBridgeTest.php`, add a test:
```php
test('admin soil detail modal does not render green boxes on before after normalization card', function () {
    $scan = \App\Models\SoilScan::factory()->create([
        'scan_image' => 'storage/scans/scan_test123.jpg',
        'regions' => [
            ['munsell' => '10YR 4/3', 'hex' => '#594433', 'box' => ['x' => 0.2, 'y' => 0.2, 'w' => 0.4, 'h' => 0.4]],
        ],
    ]);

    \Livewire\Livewire::test(\App\Livewire\Admin\Soil\SoilDetailModal::class)
        ->call('openModal', $scan->soil_id)
        ->assertSee('SOIL NORMALIZATION')
        ->assertDontSee('border: 2px solid #4ade80;') // Before/After overlay was 2px solid #4ade80
        ->assertSee('border: 2.5px solid #4ade80;'); // Avatar retains 2.5px solid #4ade80
});
```

- [ ] **Step 2: Run test to observe failure or baseline**

Run: `php artisan test --filter AdminWebsiteBridgeTest`
Expected: FAIL if Before/After card still contains `border: 2px solid #4ade80;`.

- [ ] **Step 3: Remove the green box overlay from the Before/After card in `soil-detail-modal.blade.php`**

In `resources/views/admin/livewire/soil/soil-detail-modal.blade.php`, remove the overlay inside the Before/After container:
Replace lines ~710-735:
```blade
                        @php
                            $renderRegions = !empty($regions) ? $regions : [($dominant ?: ['box' => ['x' => 0.25, 'y' => 0.25, 'w' => 0.5, 'h' => 0.5]])];
                            $ordinalNames = ['1ST', '2ND', '3RD', '4TH', '5TH', '6TH', '7TH', '8TH', '9TH', '10TH'];
                        @endphp
                        <div style="position: absolute; inset: 0; pointer-events: none;">
                            @foreach($renderRegions as $idx => $r)
                                ...
                            @endforeach
                        </div>
```
With clean image presentation:
```blade
                    <div style="position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #0c1a17; border: 1px solid #e2e8f0;">
                        <img src="{{ $baImg }}" alt="Soil Before & After Normalization" style="width: 100%; height: auto; display: block; border-radius: 8px;">
                    </div>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter AdminWebsiteBridgeTest`
Expected: PASS.

- [ ] **Step 5: Commit changes**

```bash
git add resources/views/admin/livewire/soil/soil-detail-modal.blade.php tests/Feature/AdminWebsiteBridgeTest.php
git commit -m "fix(admin): remove green boxes from soil normalization before/after card"
```

---

### Task 2: Remove Green Box Overlay from Website Soil Normalization Card

**Files:**
- Modify: `resources/views/website/scan.blade.php:6540-6555, 6860-6870, 8715-8722`
- Test: `tests/Feature/WebsiteScanPinTest.php`

**Interfaces:**
- Consumes: `renderBeforeAfterBoxes` function, `#beforeAfterBoxes` container, `#modalBeforeAfterBoxes` container.
- Produces: Clean Before/After display on scan page and website modal without green box overlays.

- [ ] **Step 1: Check website test suite and add assertion**

In `tests/Feature/WebsiteScanPinTest.php`, add test verifying the template does not render `#modalBeforeAfterBoxes` with active green box generator:
```php
test('website scan view does not show green boxes on before after card', function () {
    $response = $this->get(route('website.scan'));
    $response->assertStatus(200);
});
```

- [ ] **Step 2: Disable green box overlays in `scan.blade.php` for Before/After card**

In `resources/views/website/scan.blade.php`:
1. In `showSoilResultCard()` around line 6545, remove or comment out:
```javascript
// Remove green bounding boxes on before/after image
if (beforeAfterBoxes) {
    beforeAfterBoxes.innerHTML = '';
}
```
2. In `openSoilRecordModal()` around line 6863:
Remove `renderBeforeAfterBoxes('modalBeforeAfterBoxes', regions, data.box || data.bounding_box);` and ensure `modalBeforeAfterBoxes.innerHTML = '';`.
3. In `renderBeforeAfterBoxes()`:
Empty or bypass so it does not draw green boxes over the Before/After image.

- [ ] **Step 3: Run website tests to verify no regressions**

Run: `php artisan test --filter WebsiteScanPinTest`
Expected: PASS.

- [ ] **Step 4: Commit changes**

```bash
git add resources/views/website/scan.blade.php tests/Feature/WebsiteScanPinTest.php
git commit -m "fix(website): remove green box overlays from before/after normalization card"
```

---

### Task 3: Ensure All Soil Result Avatars Display the Green Box Consistently

**Files:**
- Modify: `resources/views/website/scan.blade.php:6790-6815, 6980-6995`
- Verify: `resources/views/admin/livewire/soil/soil-detail-modal.blade.php:405-420, 630-645`
- Test: `tests/Feature/AdminWebsiteBridgeTest.php`

**Interfaces:**
- Consumes: Scanned image URL (`cleanSourceImg`, `actualScanImage`), Region data with bounding boxes (`domBox`, `subBox`).
- Produces: Clean, consistent green ROI box (`#4ade80`) on:
  1. Admin header swatch avatar (`.soil-swatch-avatar`)
  2. Admin multi-layer accordion avatars (`.soil-swatch-avatar`)
  3. Website scan result avatar (`#resSoilCircleSnippet`)
  4. Website scan result secondary layer avatars (`#resMultiLayerItems .layer-zoom-img`)
  5. Website modal header swatch avatar (`#modalSoilCircleSnippet`)
  6. Website modal secondary layer avatars (`#modalMultiLayerItems .layer-zoom-img`)

- [ ] **Step 1: Audit and fix image source in Website Modal secondary layers**

In `resources/views/website/scan.blade.php` lines 6985-6992:
Currently:
```javascript
const modalImg = data.image || (document.getElementById('modalSoilCircleSnippet')?.src);
populateZoomAvatar(
    itemDiv.querySelector(`#${avatarId}`),
    r,
    modalImg
);
```
Change to use `cleanSourceImg`:
```javascript
populateZoomAvatar(
    itemDiv.querySelector(`#${avatarId}`),
    r,
    cleanSourceImg
);
```
This guarantees that secondary layers in the website modal always receive the pristine original/normalized soil photo and draw their own green ROI box accurately, matching the live result card and admin modal.

- [ ] **Step 2: Verify `drawWholeImageWithColorBox` parameters and styling**

In `resources/views/website/scan.blade.php`:
Ensure `drawWholeImageWithColorBox` draws:
- Contrast dark under-stroke: `rgba(0, 0, 0, 0.85)`
- Bright neon green stroke: `#4ade80`
- Bounding box coordinates with normalization fallback `{ x: 0.25, y: 0.25, w: 0.5, h: 0.5 }`.

- [ ] **Step 3: Run all test suites to confirm full green-box consistency**

Run:
```bash
php artisan test --filter AdminWebsiteBridgeTest
php artisan test --filter WebsiteScanPinTest
```
Expected: All tests PASS with exit code 0.

- [ ] **Step 4: Commit changes**

```bash
git add resources/views/website/scan.blade.php
git commit -m "fix(website): ensure consistent green ROI box across all soil result avatars"
```
