# Single Scan History Record Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ensure each image scan creates exactly ONE `SoilScan` record in the database and history table, regardless of how many soil layers/colors are detected, while keeping all detected soil layers accessible in the detail modal and dropdown.

**Architecture:** In `SoilMLAnalysisService@persistScanResult`, remove the secondary layer loop that created redundant `SoilScan` database rows. Persist only a single `SoilScan` record containing the full detected layers array in `regions_data`. On the frontend, update `scan.blade.php` to insert only one history row per scan into the DOM table, displaying the multi-color badge and allowing complete inspection of all layers in the modal.

**Tech Stack:** Laravel 11, PHP 8.3, Pest PHP, Blade, Vanilla JavaScript, PostgreSQL / SQLite.

**Spec:** User issue: Multi-layer scans (e.g. 6 detected colors) created 6 separate database rows and 6 identical history table rows (scans #74 through #79). Expected behavior: Exactly 1 history entry per scan showing "6 Colors", with all detected layers accessible inside that single scan record.

## Global Constraints

- Do NOT delete the agronomic `Soil` catalog records; `soils` table can continue to catalog distinct Munsell colors.
- Exactly ONE `SoilScan` row must be created in `soil_scans` per scan event.
- All detected layers, coordinates, confidences, and thumbnails must remain intact inside `regions_data` on that single `SoilScan` record.
- The scan details modal (`viewScanDetails`) must continue to display all detected soil layers via its dropdown and allow toggling between layers seamlessly.
- Preserve backward compatibility for both website and mobile API scan endpoints.

---

### Task 1: Backend Single-Scan Persistence in SoilMLAnalysisService

**Files:**
- Create: `tests/Feature/SingleSoilScanPerAnalysisTest.php`
- Modify: `app/Services/SoilMLAnalysisService.php:598-698`

**Interfaces:**
- Consumes: `SoilMLAnalysisService::persistScanResult(array $pythonResult, string $scanId, ?string $userId, string $platform, array $locationData = []): array`
- Produces: `$analysis['created_scans']` containing layer info where all entries share the single `soil_scan_id`, with only 1 `SoilScan` row created in the database.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/SingleSoilScanPerAnalysisTest.php`:
```php
<?php

use App\Models\SoilScan;
use App\Models\Soil;
use App\Models\User;
use App\Models\MunsellColor;
use App\Services\SoilMLAnalysisService;
use App\Services\SoilScanService;
use App\Services\SoilValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()->bind(SoilScanService::class, function () {
        return new SoilScanService(new SoilValidationService());
    });
});

it('creates exactly one SoilScan record when analyzing a multi-layer soil image with 6 colors', function () {
    $user = User::factory()->create(['user_id' => 'TEST-USR-01']);

    MunsellColor::create([
        'full_code' => '5YR 3/2',
        'hue' => '5YR',
        'color_name' => 'Dark Reddish Brown',
        'hex_code' => '#5C382C',
        'description' => 'Dark reddish brown clay loam rich in organic matter.',
    ]);
    MunsellColor::create([
        'full_code' => '7.5YR 4/4',
        'hue' => '7.5YR',
        'color_name' => 'Brown',
        'hex_code' => '#8C5E39',
        'description' => 'Brown subsoil with moderate permeability.',
    ]);

    $regions = [
        ['munsell' => '5YR 3/2', 'munsell_code' => '5YR 3/2', 'label' => 'Dark Reddish Brown', 'percent' => 35, 'hex' => '#5C382C', 'color_delta_e' => 1.2],
        ['munsell' => '7.5YR 4/4', 'munsell_code' => '7.5YR 4/4', 'label' => 'Brown', 'percent' => 25, 'hex' => '#8C5E39', 'color_delta_e' => 1.5],
        ['munsell' => '2.5YR 3/4', 'munsell_code' => '2.5YR 3/4', 'label' => 'Dark Reddish Brown', 'percent' => 15, 'hex' => '#6A2E20', 'color_delta_e' => 2.1],
        ['munsell' => '2.5Y 5/4', 'munsell_code' => '2.5Y 5/4', 'label' => 'Light Olive Brown', 'percent' => 10, 'hex' => '#8D7B48', 'color_delta_e' => 2.4],
        ['munsell' => '10YR 4/3', 'munsell_code' => '10YR 4/3', 'label' => 'Brown', 'percent' => 10, 'hex' => '#7D6246', 'color_delta_e' => 2.0],
        ['munsell' => '10YR 6/3', 'munsell_code' => '10YR 6/3', 'label' => 'Pale Brown', 'percent' => 5, 'hex' => '#A8947A', 'color_delta_e' => 2.8],
    ];

    $pythonResult = [
        'success' => true,
        'label' => 'real_soil',
        'status' => 'verified',
        'confidence' => 0.96,
        'munsell_code' => '5YR 3/2',
        'hue' => '5YR',
        'soil_name' => 'Dark Reddish Brown',
        'regions' => $regions,
    ];

    $service = app(SoilMLAnalysisService::class);
    $scanUuid = 'test-uuid-multi-scan-6';

    $result = $service->persistScanResult($pythonResult, $scanUuid, $user->user_id, 'website');

    // CRITICAL: Exactly 1 SoilScan must exist in the database!
    expect(SoilScan::count())->toBe(1);

    $savedScan = SoilScan::first();
    expect($savedScan->soil_scan_id)->toBe($result['scan_id'])
        ->and($savedScan->regions_data)->toHaveCount(6)
        ->and($result['created_scans'])->toHaveCount(6);

    // All layer cards in created_scans must reference the single scan_id
    foreach ($result['created_scans'] as $layerCard) {
        expect($layerCard['scan_id'])->toBe($savedScan->soil_scan_id);
    }
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/SingleSoilScanPerAnalysisTest.php`
Expected: FAIL asserting that 6 is 1 (because the existing code creates 6 `SoilScan` records).

- [ ] **Step 3: Modify `app/Services/SoilMLAnalysisService.php`**

In [`app/Services/SoilMLAnalysisService.php`](file:///c:/Users/Carl%20Ashley/Herd/agrico/app/Services/SoilMLAnalysisService.php#L598-L698):
Replace the multi-layer block:
```php
        if ($isMultiLayer) {
            $domR    = $regions[0];
            $domCode = $domR['munsell'] ?? ($domR['munsell_code'] ?? ($analysis['munsell_code'] ?? '10YR 5/3'));
            $domMColor = MunsellColor::where('full_code', $domCode)->first();
            $domName = $domMColor?->color_name ?? ($analysis['soil_name'] ?? 'Dominant Soil Layer');
            $domHue  = $domMColor?->hue ?? ($analysis['hue'] ?? (explode(' ', $domCode)[0] ?? '10YR'));
            $domColorId = $domMColor?->munsell_color_id ?? ($analysis['color_id'] ?? null);
            $domDesc = (!empty($domMColor?->description) ? trim($domMColor->description) : null) ?? ($analysis['description'] ?? "{$domCode} - {$domName}");
            $domPct  = $domR['percent'] ?? (!empty($domR['proportion']) ? round($domR['proportion'] * 100, 1) : null);
            $domDeltaE = isset($domR['color_delta_e']) && is_numeric($domR['color_delta_e']) ? (float)$domR['color_delta_e'] : (isset($analysis['color_delta_e']) ? (float)$analysis['color_delta_e'] : (isset($analysis['delta_e']) ? (float)$analysis['delta_e'] : 3.0));
            $domConf = round(max(88.5, min(99.6, 99.4 - ($domDeltaE * 0.45))), 1) . '%';
            $soil = Soil::firstOrCreate(
                ['full_code' => $domCode],
                ['soil_name' => $domName, 'hue' => $domHue, 'full_code' => $domCode,
                 'description' => $domDesc, 'status' => 'Active', 'scan_count' => 0,
                 'color_id' => $domColorId, 'admin_id' => $userId]
            );
            if (!empty($domMColor?->description) && $soil->getRawOriginal('description') !== trim($domMColor->description)) {
                $soil->update(['description' => trim($domMColor->description), 'color_id' => $domColorId ?: $soil->color_id]);
            }
            $soil->increment('scan_count');
            $analysis['description']    = $soil->effective_description;
            $analysis['soil_name']      = $soil->effective_soil_name;
            $analysis['organic_matter'] = $soil->effective_organic_matter;
            $analysis['soil_type']      = $soil->effective_soil_type;
            $domImgPath = $saveRegionImage($domR['region_thumbnail'] ?? null, 0);
            $dominantScanImage = $publicScanRelativePath ?: ($domImgPath ?: null);

            // Create ONLY ONE SoilScan record for the entire scan session!
            $soilScan = SoilScan::create([
                'user_id' => $userId, 'soil_id' => $soil->soil_id, 'platform' => $platform,
                'scan_note' => "Determined Soil: {$soil->effective_soil_name} ({$domCode})" . ($domPct ? " - Soil Percentage: {$domPct}%" : "") . " - Confidence: {$domConf}",
                'regions_data' => $regions, 'scan_date' => now(),
                'scan_image' => $dominantScanImage,
                'before_after_image' => $publicBaRelativePath,
                'thumbnail_image' => $publicThumbRelativePath ?: $domImgPath,
            ]);

            // Dominant layer entry (index 0)
            $createdScans = [[
                'scan_id' => $soilScan->soil_scan_id,
                'soil_id' => $soil->soil_id,
                'scan_seq' => SoilScan::where('soil_id', $soil->soil_id)->count(),
                'name' => $soil->effective_soil_name, 'color_name' => $soil->effective_soil_name,
                'code' => $domCode, 'hue' => $soil->hue ?? $domHue,
                'hex' => $soil->effective_hex_color ?? ($domR['hex'] ?? '#8F694E'),
                'desc' => $soil->effective_description, 'organic' => $soil->effective_organic_matter,
                'drainage' => $soil->effective_soil_type, 'soil_type' => $soil->effective_soil_type,
                'confidence' => $domConf, 'percent' => $domPct, 'regions' => $regions,
                'image' => $dominantScanImage ? asset($dominantScanImage) : null,
                'scan_image' => $dominantScanImage ? asset($dominantScanImage) : null,
            ]];

            // Process secondary layers for soil catalog without creating extra SoilScan rows
            for ($i = 1; $i < count($regions); $i++) {
                $r = $regions[$i];
                $mCode = $r['munsell'] ?? ($r['munsell_code'] ?? ($r['notation'] ?? null));
                if (!$mCode) continue;
                $mColor = MunsellColor::where('full_code', $mCode)->first();
                $layerName = $mColor?->color_name ?? ($r['label'] ?? 'Soil Layer');
                $layerHue  = $mColor?->hue ?? (explode(' ', $mCode)[0] ?? '10YR');
                $layerColorId = $mColor?->munsell_color_id ?? null;
                $layerDesc = (!empty($mColor?->description) ? trim($mColor->description) : null) ?? ($r['description'] ?? "{$mCode} - {$layerName}");
                $pctVal  = $r['percent'] ?? (!empty($r['proportion']) ? round($r['proportion'] * 100, 1) : null);
                $rDeltaE = isset($r['color_delta_e']) && is_numeric($r['color_delta_e']) ? (float)$r['color_delta_e'] : (isset($analysis['color_delta_e']) ? (float)$analysis['color_delta_e'] : (isset($analysis['delta_e']) ? (float)$analysis['delta_e'] : 3.0));
                $confVal = round(max(88.5, min(99.6, 99.4 - ($rDeltaE * 0.45))), 1) . '%';
                $layerSoil = Soil::firstOrCreate(
                    ['full_code' => $mCode],
                    ['soil_name' => $layerName, 'hue' => $layerHue, 'full_code' => $mCode,
                     'description' => $layerDesc, 'status' => 'Active', 'scan_count' => 0,
                     'color_id' => $layerColorId, 'admin_id' => $userId]
                );
                if (!empty($mColor?->description) && $layerSoil->getRawOriginal('description') !== trim($mColor->description)) {
                    $layerSoil->update(['description' => trim($mColor->description), 'color_id' => $layerColorId ?: $layerSoil->color_id]);
                }
                $layerSoil->increment('scan_count');
                $rImgPath = $saveRegionImage($r['region_thumbnail'] ?? null, $i);

                $createdScans[] = [
                    'scan_id' => $soilScan->soil_scan_id,
                    'soil_id' => $layerSoil->soil_id,
                    'scan_seq' => 1,
                    'name' => $layerSoil->effective_soil_name, 'color_name' => $layerSoil->effective_soil_name,
                    'code' => $mCode, 'hue' => $layerSoil->hue ?? $layerHue,
                    'hex' => $layerSoil->effective_hex_color ?? ($r['hex'] ?? '#8F694E'),
                    'desc' => $layerSoil->effective_description, 'organic' => $layerSoil->effective_organic_matter,
                    'drainage' => $layerSoil->effective_soil_type, 'soil_type' => $layerSoil->effective_soil_type,
                    'confidence' => $confVal, 'percent' => $pctVal, 'regions' => $regions,
                    'image' => $rImgPath ? asset($rImgPath) : ($publicScanRelativePath ? asset($publicScanRelativePath) : null),
                    'scan_image' => $rImgPath ? asset($rImgPath) : ($publicScanRelativePath ? asset($publicScanRelativePath) : null),
                ];
            }
        }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/SingleSoilScanPerAnalysisTest.php`
Expected: PASS (1 test, 5 assertions).

- [ ] **Step 5: Run existing test suite to check for regressions**

Run: `php artisan test tests/Feature/SoilMLAnalysisServiceTest.php`
Expected: PASS.

- [ ] **Step 6: Commit backend changes**

```bash
git add tests/Feature/SingleSoilScanPerAnalysisTest.php app/Services/SoilMLAnalysisService.php
git commit -m "fix: persist exactly one SoilScan record per scan event for multi-layer scans"
```

---

### Task 2: Frontend History Table Single-Row Recording

**Files:**
- Modify: `resources/views/website/scan.blade.php:8887-8903`

**Interfaces:**
- Consumes: `data.created_scans`, `data.scan_id`, `data.regions`, `match`
- Produces: Exactly one invocation of `autoRecordScanToHistory(match, null, null, data.scan_seq, data.scan_id, false)` with `match.regions` populated.

- [ ] **Step 1: Inspect lines 8887-8903 in `resources/views/website/scan.blade.php`**

Currently, the code has:
```javascript
                if (data.created_scans && Array.isArray(data.created_scans) && data.created_scans.length > 1) {
                    const prevNewScans = document.querySelectorAll('#savedScansTbody .is-new-scan-row');
                    prevNewScans.forEach(r => r.classList.remove('is-new-scan-row'));

                    const scansToAdd = [...data.created_scans].reverse();
                    scansToAdd.forEach(sc => {
                        sc.image = actualScanImage;
                        sc.before_after_image_url = data.before_after_image_url || null;
                        sc.raw_image_url = data.raw_image_url || null;
                        autoRecordScanToHistory(sc, null, null, sc.scan_seq || 1, sc.scan_id, true);
                    });
                } else {
                    match.before_after_image_url = data.before_after_image_url || null;
                    match.raw_image_url = data.raw_image_url || null;
                    autoRecordScanToHistory(match, null, null, data.scan_seq, data.scan_id, false);
                }
```

- [ ] **Step 2: Replace with single-record history insertion**

Replace lines 8887-8903 with:
```javascript
                // Record exactly ONE entry in the Scan History table, keeping all detected layers in regions
                match.before_after_image_url = data.before_after_image_url || null;
                match.raw_image_url = data.raw_image_url || null;
                match.regions = regions;
                autoRecordScanToHistory(match, null, null, data.scan_seq, data.scan_id, false);
```

- [ ] **Step 3: Verify modal dropdown and layer switching**

Verify that in `autoRecordScanToHistory`, `match.regions` is serialized into `data-regions` on the "View" button (`tr.querySelector('.btn-view-scan')`), so that `openScanModalFromBtn` can deserialize `data.regions` and allow viewing all 6 layers inside the modal.

- [ ] **Step 4: Commit frontend changes**

```bash
git add resources/views/website/scan.blade.php
git commit -m "fix(website): record single Scan History row per scan with multi-layer regions intact"
```

---

### Task 3: Cleanup Duplicate Historical Scan Records

**Files:**
- Create: `app/Console/Commands/DeduplicateScanHistoryCommand.php`
- Test: `tests/Feature/DeduplicateScanHistoryTest.php`

**Interfaces:**
- Artisan command: `php artisan scan:deduplicate`
- Identifies scans belonging to the same user with identical `scan_date` (or created within 5 seconds of each other) where `regions_data` is identical. Keeps the primary/dominant scan (or lowest/highest ID) and removes the redundant sub-scan rows (such as rows 74-78 in the user's screenshot).

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/DeduplicateScanHistoryTest.php`:
```php
<?php

use App\Models\SoilScan;
use App\Models\Soil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

it('removes duplicate SoilScan rows that share identical regions_data and timestamp', function () {
    $user = User::factory()->create(['user_id' => 'DUP-USR-01']);
    $soil1 = Soil::factory()->create(['full_code' => '5YR 3/2']);
    $soil2 = Soil::factory()->create(['full_code' => '7.5YR 4/4']);

    $sharedRegions = [
        ['munsell' => '5YR 3/2', 'percent' => 60],
        ['munsell' => '7.5YR 4/4', 'percent' => 40],
    ];

    $now = now();

    // Simulate the old duplicate creation (2 scans for 1 image)
    $scan1 = SoilScan::create([
        'soil_scan_id' => 74,
        'user_id' => $user->user_id,
        'soil_id' => $soil2->soil_id,
        'scan_note' => 'Determined Soil: Brown (7.5YR 4/4)',
        'scan_date' => $now,
        'regions_data' => $sharedRegions,
        'created_at' => $now,
    ]);

    $scan2 = SoilScan::create([
        'soil_scan_id' => 75,
        'user_id' => $user->user_id,
        'soil_id' => $soil1->soil_id,
        'scan_note' => 'Determined Soil: Dark Reddish Brown (5YR 3/2)',
        'scan_date' => $now,
        'regions_data' => $sharedRegions,
        'created_at' => $now,
    ]);

    expect(SoilScan::count())->toBe(2);

    Artisan::call('scan:deduplicate');

    // Only 1 scan remains, retaining the full regions_data
    expect(SoilScan::count())->toBe(1);
    $remaining = SoilScan::first();
    expect($remaining->regions_data)->toHaveCount(2);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test tests/Feature/DeduplicateScanHistoryTest.php`
Expected: FAIL command "scan:deduplicate" does not exist.

- [ ] **Step 3: Implement `app/Console/Commands/DeduplicateScanHistoryCommand.php`**

```php
<?php

namespace App\Console\Commands;

use App\Models\SoilScan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeduplicateScanHistoryCommand extends Command
{
    protected $signature = 'scan:deduplicate {--dry-run : Only report duplicates without deleting}';
    protected $description = 'Consolidate duplicate SoilScan records from legacy multi-layer scans into a single record per scan';

    public function handle(): int
    {
        $this->info('Scanning for duplicate SoilScan records...');

        $scans = SoilScan::whereNotNull('regions_data')
            ->orderBy('user_id')
            ->orderBy('soil_scan_id', 'desc')
            ->get();

        $seenGroups = [];
        $idsToDelete = [];

        foreach ($scans as $scan) {
            $regions = is_array($scan->regions_data) ? $scan->regions_data : json_decode($scan->regions_data, true);
            if (empty($regions) || count($regions) <= 1) {
                continue;
            }

            // Fingerprint using user_id, date (down to minute), and sorted munsell codes
            $munsellCodes = array_map(fn($r) => $r['munsell'] ?? ($r['munsell_code'] ?? ''), $regions);
            sort($munsellCodes);
            $dateKey = $scan->scan_date ? substr((string)$scan->scan_date, 0, 16) : substr((string)$scan->created_at, 0, 16);
            $groupKey = $scan->user_id . '_' . $dateKey . '_' . md5(implode('|', $munsellCodes));

            if (!isset($seenGroups[$groupKey])) {
                // Keep this scan (the latest ID in the group)
                $seenGroups[$groupKey] = $scan->soil_scan_id;
            } else {
                // Duplicate scan to delete
                $idsToDelete[] = $scan->soil_scan_id;
            }
        }

        $count = count($idsToDelete);
        if ($count === 0) {
            $this->info('No duplicate scan records found.');
            return 0;
        }

        $this->warn("Found {$count} duplicate SoilScan records to prune.");

        if ($this->option('dry-run')) {
            $this->info('Dry-run complete. No records were deleted.');
            return 0;
        }

        SoilScan::whereIn('soil_scan_id', $idsToDelete)->delete();
        $this->info("Successfully pruned {$count} duplicate scan records.");

        return 0;
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/DeduplicateScanHistoryTest.php`
Expected: PASS.

- [ ] **Step 5: Run command to clean up local/production duplicates if desired**

Run: `php artisan scan:deduplicate`
Output: Prunes legacy duplicates (e.g. rows 74-78).

- [ ] **Step 6: Commit cleanup command**

```bash
git add app/Console/Commands/DeduplicateScanHistoryCommand.php tests/Feature/DeduplicateScanHistoryTest.php
git commit -m "feat: add scan:deduplicate artisan command to prune duplicate legacy multi-layer scans"
```

---

### Task 4: Full System Verification

- [ ] **Step 1: Run all scan-related tests**

Run: `php artisan test --filter=SoilScan` and `php artisan test tests/Feature/SingleSoilScanPerAnalysisTest.php`
Expected: All tests PASS.

- [ ] **Step 2: Verify git status and clean working tree**

Ensure no leftover scratch files or broken syntax.
