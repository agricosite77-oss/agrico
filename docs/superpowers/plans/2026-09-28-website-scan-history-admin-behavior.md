# Website Scan History Admin-Style Table Behavior Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transform the Website Scan History table into an interactive, full-featured data table mirroring the Admin side table behavior — including live search with clear button, Munsell auto-suggestions, hue and date filter controls, sortable column headers with chevron indicators, search match row highlighting, responsive column collapsing, and consistent pagination.

**Architecture:** Extend `WebsiteController@scan` to accept `search`, `hue`, `date`, `sort`, `direction`, and `per_page` query parameters with robust sanitization and model relations; implement a lightweight suggestions API route `/scan/suggestions` for instant color-chip search dropdowns; and redesign the Scan History card in `resources/views/website/scan.blade.php` with a matching search/filter bar, interactive sort headers, responsive column meta, and seamless `#scanHistory` navigation in both Dark and Light modes.

**Tech Stack:** Laravel 11, Blade, Vanilla JS / Fetch API, Alpine.js, Tailwind CSS utilities + AgriCo Forest Green CSS Design Tokens, Pest PHP for testing.

**Spec:** The Website Scan History table should replicate the capabilities of the Admin side table (`SoilScanHistory.php` / `soil-scan-history.blade.php`):
1. **Search & Auto-Suggestions Bar**:
   - Live search input for Soil Name, Full Code, Hue, Note, and User/Scan ID.
   - Clear button (`×`) when search query is present.
   - Dropdown showing up to 8 matched soils with Munsell color circle chip, soil name, and code/hue meta.
2. **Filters & Status**:
   - Hue dropdown ("All Hues", "10R", "2.5YR", "5YR", "7.5YR", "10YR", "2.5Y").
   - Calendar date picker with clear button (`×`).
   - Results count indicator ("X results" / "X total scans").
3. **Interactive Column Sorting**:
   - Sortable columns: `soil_name`, `full_code`, `scan_count`, `created_at` (scan date).
   - Visual active sort chevrons (up/down green indicator) and idle chevron pairs.
4. **Table Rows & Visual Polish**:
   - Bold Soil Name with subtitle description/note.
   - Search term match highlighting (subtle emerald highlight and left green border).
   - Clean Action column with the existing "View" modal trigger without ellipsis clipping.
5. **Responsive Column Hierarchy**:
   - Desktop (≥1024px): All columns visible.
   - Tablet (680px–1023px): Drops secondary columns with graceful spacing.
   - Mobile (<680px): Collapses to 2 columns (Soil Characteristics with embedded code & date + Action button) with zero horizontal clipping.
6. **Pagination Controls**:
   - Rows per page dropdown (`5`, `10`, `20`, `50`).
   - Records range counter (`1–10 of 45`).
   - Numbered pagination with active green state and smooth Prev/Next controls.

## Global Constraints
- Must support both dark mode (default rich forest green) and light mode (`html.light-mode`).
- Must preserve camera, viewfinder, and live scanning functionality on `/scan`.
- Must anchor table filter/pagination interactions to `#scanHistory` so the viewport does not jump back to top.
- Must restrict scanned records to the authenticated user's own scans (`user_id = $userId`).
- All Blade views must be clear and syntax-valid.

---

### Task 1: Backend Query Filters, Sorting, and Suggestions API

**Files:**
- Modify: `app/Http/Controllers/Website/WebsiteController.php:560-600`
- Modify: `routes/web.php:61-66`
- Create: `tests/Feature/WebsiteScanTableBehaviorTest.php`

**Interfaces:**
- Consumes: `Request` with `search`, `hue`, `date`, `sort`, `direction`, `per_page`, `page`.
- Produces: Filtered & sorted `SoilScan` paginator, suggestions JSON endpoint `/scan/suggestions`.

- [ ] **Step 1: Write the failing Pest feature test**

Create `tests/Feature/WebsiteScanTableBehaviorTest.php`:
```php
<?php

use App\Models\MunsellColor;
use App\Models\Soil;
use App\Models\SoilScan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('website scan history filters by search query and hue', function () {
    $user = User::factory()->create(['status' => 'Active', 'user_id' => 'USR-TEST-01']);

    $color1 = MunsellColor::create([
        'hue' => '10YR',
        'value' => 4,
        'chroma' => 3,
        'full_code' => '10YR 4/3',
        'color_name' => 'Brown Soil',
        'hex_code' => '#8B5A2B',
    ]);

    $soil1 = Soil::create([
        'soil_name' => 'Alulao Clay Loam',
        'full_code' => '10YR 4/3',
        'hue' => '10YR',
        'color_id' => $color1->color_id,
        'status' => 'Active',
    ]);

    $scan1 = SoilScan::create([
        'user_id' => $user->user_id,
        'soil_id' => $soil1->soil_id,
        'scan_date' => '2026-09-20',
        'scan_note' => 'Field A Sample',
    ]);

    $response = $this->actingAs($user, 'web')
        ->get(route('website.scan', ['search' => 'Alulao', 'hue' => '10YR']));

    $response->assertStatus(200);
    $response->assertSee('Alulao Clay Loam');
});

test('website scan history suggestions endpoint returns matching soils with color hex', function () {
    $user = User::factory()->create(['status' => 'Active', 'user_id' => 'USR-TEST-02']);

    $color = MunsellColor::create([
        'hue' => '5YR',
        'value' => 5,
        'chroma' => 6,
        'full_code' => '5YR 5/6',
        'color_name' => 'Yellowish Red',
        'hex_code' => '#B85D19',
    ]);

    $soil = Soil::create([
        'soil_name' => 'Banga Sandy Clay',
        'full_code' => '5YR 5/6',
        'hue' => '5YR',
        'color_id' => $color->color_id,
        'status' => 'Active',
    ]);

    SoilScan::create([
        'user_id' => $user->user_id,
        'soil_id' => $soil->soil_id,
        'scan_date' => '2026-09-25',
    ]);

    $response = $this->actingAs($user, 'web')
        ->get(route('website.scan.suggestions', ['q' => 'Banga']));

    $response->assertStatus(200);
    $response->assertJsonFragment(['name' => 'Banga Sandy Clay', 'code' => '5YR 5/6']);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/WebsiteScanTableBehaviorTest.php`
Expected: FAIL (route `website.scan.suggestions` not defined, filters not implemented in `WebsiteController@scan`).

- [ ] **Step 3: Implement route and Controller logic**

In `routes/web.php`:
Add:
```php
Route::get('/scan/suggestions', [WebsiteController::class, 'scanSuggestions'])->name('scan.suggestions');
```

In `app/Http/Controllers/Website/WebsiteController.php`:
Update `scan(Request $request)` to:
1. Read `search`, `hue`, `date`, `sort`, `direction`, and `per_page`.
2. Build query on `SoilScan::with(['soil.munsellColor', 'location', 'user'])->where('user_id', $userId)`.
3. Apply date filter: `whereDate('scan_date', $date)` or `created_at`.
4. Apply hue filter: `whereHas('soil', fn($q) => $q->where('hue', $hue)->orWhereHas('munsellColor', fn($mq) => $mq->where('hue', $hue)))`.
5. Apply search filter: matching `#id`, `soil_name`, `full_code`, `hue`, `description`, `scan_note`.
6. Apply sorting: allowed fields `soil_name`, `full_code`, `scan_count`, `created_at`.
7. Fetch unique hues: `MunsellColor::select('hue')->distinct()->orderBy('hue')->pluck('hue')`.
8. Pass `$munsellHues`, `$search`, `$selectedHue`, `$selectedDate`, `$sortField`, `$sortDirection` to `website.scan`.

Add `scanSuggestions(Request $request)`:
```php
public function scanSuggestions(Request $request)
{
    $user = Auth::guard('web')->user() ?? Auth::user() ?? Auth::guard('admin')->user();
    if (!$user && session()->has('user_id')) {
        $user = User::where('user_id', session('user_id'))->first();
    }
    $userId = $user?->user_id ?? $user?->id;
    if (!$userId) {
        return response()->json([]);
    }

    $q = trim($request->input('q', ''));
    if (strlen($q) < 1) {
        return response()->json([]);
    }

    $scans = SoilScan::with(['soil.munsellColor'])
        ->where('user_id', $userId)
        ->where(function ($sub) use ($q) {
            if (str_starts_with($q, '#')) {
                $sub->where('soil_scan_id', ltrim($q, '#'));
            } else {
                $sub->where('scan_note', 'like', "%{$q}%")
                    ->orWhereHas('soil', function ($sq) use ($q) {
                        $sq->where('soil_name', 'like', "%{$q}%")
                           ->orWhere('full_code', 'like', "%{$q}%")
                           ->orWhere('hue', 'like', "%{$q}%");
                    });
            }
        })
        ->limit(8)
        ->get();

    $suggestions = $scans->map(function ($s) {
        $soil = $s->soil;
        $mColor = $soil?->munsellColor;
        $sHue = $soil?->hue ?? $mColor?->hue ?? '10YR';
        $sVal = $mColor?->value ?? 4;
        $sChr = $mColor?->chroma ?? 3;
        $sHex = $soil?->hex_color ?? $mColor?->hex_code ?? \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex($sHue, $sVal, $sChr);

        return [
            'id' => $s->soil_scan_id,
            'name' => $soil?->soil_name ?? 'Scanned Soil',
            'code' => $soil?->full_code ?? $mColor?->full_code ?? '—',
            'hue' => $sHue,
            'hex' => $sHex,
        ];
    })->unique('name')->values();

    return response()->json($suggestions);
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/WebsiteScanTableBehaviorTest.php`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Website/WebsiteController.php routes/web.php tests/Feature/WebsiteScanTableBehaviorTest.php
git commit -m "feat(website): add scan history query filters, sorting, and suggestions api"
```

---

### Task 2: Search & Filter Control Bar UI in Website Scan Table

**Files:**
- Modify: `resources/views/website/scan.blade.php:4370-4420` (search and filter markup)
- Modify: `resources/views/website/scan.blade.php:1775-1840` & `2680-2750` (CSS for search bar in dark & light modes)

**Interfaces:**
- Consumes: `$munsellHues`, `$search`, `$selectedHue`, `$selectedDate`, `$scans->total()`.
- Produces: Interactive search input with live auto-suggestions dropdown, hue dropdown, date filter with clear button, and result counter.

- [ ] **Step 1: Write CSS rules for `.scan-filter-bar` and suggestion dropdown**

In `resources/views/website/scan.blade.php`:
Add CSS styles for `.scan-filter-bar`, `.scan-search-input`, `.scan-suggest-box`, `.scan-select-filter`, and `.scan-date-filter` supporting both dark mode (forest green glass) and light mode (clean white/slate cards with emerald focus highlights).

- [ ] **Step 2: Add Search & Filter HTML block above `ws-table`**

Directly under `<div class="ws-table-header">`:
```blade
<div class="scan-filter-bar">
    {{-- Search with auto-suggestions --}}
    <div class="scan-search-wrapper" id="scanSearchWrapper">
        <svg class="scan-search-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" id="scanSearchInput" class="scan-search-input" placeholder="Search soil name, full code, hue..." value="{{ $search ?? '' }}" autocomplete="off" />
        @if(!empty($search))
            <button type="button" class="scan-clear-btn" onclick="clearScanFilter('search')" title="Clear Search">&times;</button>
        @endif
        <div id="scanSuggestBox" class="scan-suggest-dropdown" style="display: none;"></div>
    </div>

    {{-- Funnel Icon --}}
    <svg class="scan-filter-funnel" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
    </svg>

    {{-- Hue Filter Dropdown --}}
    <select id="scanHueFilter" class="scan-filter-select" onchange="applyScanFilter('hue', this.value)">
        <option value="">All Hues</option>
        @foreach($munsellHues as $hueOption)
            <option value="{{ $hueOption }}" {{ ($selectedHue ?? '') === $hueOption ? 'selected' : '' }}>{{ $hueOption }}</option>
        @endforeach
    </select>

    {{-- Date Filter Picker --}}
    <div class="scan-date-wrapper">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        <input type="date" id="scanDateFilter" class="scan-date-input" value="{{ $selectedDate ?? '' }}" onchange="applyScanFilter('date', this.value)" title="Filter by Date" />
        @if(!empty($selectedDate))
            <button type="button" class="scan-clear-btn" onclick="clearScanFilter('date')" title="Clear Date">&times;</button>
        @endif
    </div>

    {{-- Results Counter --}}
    <div class="scan-results-count">
        {{ $scans->total() }} results
    </div>
</div>
```

- [ ] **Step 3: Add JavaScript handlers for live suggestions & filtering**

Add client-side script for `scanSearchInput` debounce (`300ms`), fetching `/scan/suggestions?q=...`, rendering clickable suggestion items with color circles, and navigation functions `applyScanFilter(key, value)` and `clearScanFilter(key)` that construct query parameters with `#scanHistory` anchor.

- [ ] **Step 4: Verify in browser / Pest test**

Run: `php artisan test tests/Feature/WebsiteScanTableBehaviorTest.php`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views/website/scan.blade.php
git commit -m "feat(website): add search and filter controls bar to scan history table"
```

---

### Task 3: Interactive Column Sorting & Chevrons

**Files:**
- Modify: `resources/views/website/scan.blade.php:4420-4460` (column headers `<thead>`)
- Modify: `resources/views/website/scan.blade.php:1810-1860` (CSS for sort chevrons)

**Interfaces:**
- Consumes: `$sortField`, `$sortDirection`.
- Produces: Sortable column headers (`Soil Characteristics`, `Full Code`, `Scan Count`, `Scan Date`) with chevron indicators.

- [ ] **Step 1: Add Sort Chevrons CSS**

Mirror the exact SVG chevron styles from `soil-scan-history.blade.php`:
- `.sort-chevron-pair` with idle up and down chevrons.
- `.sort-chevron-active` in `#4ADE80` (dark mode) / `#22c55e` (light mode).
- Hover state on headers with transition to green accent.

- [ ] **Step 2: Update Table Headers with sorting links / onclick triggers**

Update `<th>` elements:
- `#` (Seq/ID): fixed.
- `Soil Characteristics`: `onclick="toggleScanSort('soil_name')"`
- `Full Code`: `onclick="toggleScanSort('full_code')"`
- `Scan Count`: `onclick="toggleScanSort('scan_count')"`
- `Scan Date`: `onclick="toggleScanSort('created_at')"`
- `Action`: fixed without sorting.

- [ ] **Step 3: Implement `toggleScanSort(field)` JS helper**

Builds the target URL retaining existing `search`, `hue`, `date`, `per_page`, updates `sort=field` and toggles `direction=asc|desc`, navigating with `#scanHistory`.

- [ ] **Step 4: Verify sorting via Pest tests**

Add sorting test assertion to `tests/Feature/WebsiteScanTableBehaviorTest.php` verifying sort direction toggles and orders records correctly.
Run: `php artisan test tests/Feature/WebsiteScanTableBehaviorTest.php`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views/website/scan.blade.php tests/Feature/WebsiteScanTableBehaviorTest.php
git commit -m "feat(website): add interactive sortable columns and chevrons to scan history"
```

---

### Task 4: Responsive Column Dropping & Search Match Highlighting

**Files:**
- Modify: `resources/views/website/scan.blade.php:4460-4550` (tbody rows & mobile metadata)
- Modify: `resources/views/website/scan.blade.php:280-330` & `1800-1840` (responsive media queries)

**Interfaces:**
- Consumes: `$search`, `$scan` attributes.
- Produces: Highlighted search matching rows, responsive column display on tablets/mobile, embedded subtitle metadata on mobile.

- [ ] **Step 1: Add Search Match Highlighting to Table Rows**

In the Blade loop:
```blade
@php
    $searchTrim = trim($search ?? '');
    $isSearchMatch = !empty($searchTrim) && (
        stripos($soilName, $searchTrim) !== false ||
        stripos($fCode, $searchTrim) !== false ||
        stripos($sHue, $searchTrim) !== false ||
        stripos($soilDesc, $searchTrim) !== false ||
        (str_starts_with($searchTrim, '#') && '#' . $scan->soil_scan_id === $searchTrim)
    );
@endphp
<tr class="{{ $isSearchMatch ? 'ws-scan-row-highlight' : '' }}">
```
Define `.ws-scan-row-highlight`:
- Dark mode: `background: rgba(34, 197, 94, 0.12) !important; border-left: 4px solid #4ADE80 !important;`
- Light mode: `background: #ecfdf5 !important; border-left: 4px solid #22c55e !important;`

- [ ] **Step 2: Add Mobile Subtitle Metadata & Responsive Breakpoints**

Inside `col-soil-name`:
```blade
<div class="soil-mobile-meta">
    <span>{{ $fCode }}</span>
    <span class="soil-mobile-date">{{ $scan->scan_date ? \Carbon\Carbon::parse($scan->scan_date)->format('m/d/Y') : '—' }}</span>
</div>
```
Under `@media (max-width: 679px)`:
- Hide `.col-soil-code`, `.col-soil-hue`, `.col-soil-seq`, `.col-soil-date`.
- Display `.soil-mobile-meta`.
- Ensure Action column and Soil Characteristics fit phone screens 100% without horizontal table overflow.

- [ ] **Step 3: Verify responsive layout and match highlighting**

Clear views (`php artisan view:clear`) and run Pest test suite.
Run: `php artisan test tests/Feature/WebsiteScanTableBehaviorTest.php`
Expected: PASS.

- [ ] **Step 4: Commit**

```bash
git add resources/views/website/scan.blade.php
git commit -m "feat(website): add search match highlighting and mobile responsive column hierarchy"
```

---

### Task 5: Pagination Alignment & Verification

**Files:**
- Modify: `resources/views/website/scan.blade.php:4560-4620` (footer pagination controls)
- Test: `tests/Feature/WebsiteScanTableBehaviorTest.php`
- Test: `tests/Feature/WebsiteScanPinTest.php`

**Interfaces:**
- Consumes: Filtered `$scans` paginator with preserved query parameters.
- Produces: Complete Admin-style pagination footer with smooth transitions.

- [ ] **Step 1: Ensure all pagination links preserve search, filter, and sort params**

Verify `$scans->appends(request()->query())->fragment('scanHistory')` is utilized for all pagination links (`Prev`, numbers, `Next`).
Ensure `perPageSelect` passes active filter query strings.

- [ ] **Step 2: Run all website feature tests to ensure no regressions**

Run: `php artisan test --filter=Website`
Expected: All tests PASS.

- [ ] **Step 3: Commit**

```bash
git add resources/views/website/scan.blade.php app/Http/Controllers/Website/WebsiteController.php tests/Feature/WebsiteScanTableBehaviorTest.php
git commit -m "feat(website): finalize scan history admin-style table behavior and pagination"
```
