# AgriCo Full-System Audit & Codebase Cleanup Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Clean up dead/orphaned files, resolve routing and controller stub exceptions, synchronize API contracts, and fix automated test suites across the Laravel backend, Python ML pipeline, and React Native mobile app.

**Architecture:** Three-tiered modular audit and remediation: Layer 1 addresses the Laravel PHP backend (controllers, routes, config, migrations, Pest test suite); Layer 2 addresses the Python ML microservice (CSV deduplication, CLI defaults, and backup directory pruning); Layer 3 addresses the React Native mobile app (ESLint tooling configuration, authentication persistence contract, and obsolete label cleanup).

**Tech Stack:** 
- Laravel 11 / PHP 8.2+ / Livewire 3 / Pest PHP
- Python 3.12 / Scikit-Learn / Pillow / SQLite3
- React Native 0.73.6 / React Navigation / Axios / React Native Keychain

**Spec:** Audit findings across AgriCo repository conducted via `systematic-debugging`.

## Global Constraints
- Do not modify or alter the core Munsell color determination algorithms in `python/munsell_lookup.py` and `python/soil_classifier.py`.
- Preserve the 2-way classification contract (`real_soil` vs `fake_soil`) and storage hierarchy (`Scan_Soil_Image/Real_Soil/<hue>/<hue value>/<chroma>/` and flat `Scan_Soil_Image/Fake_Soil/`).
- Maintain backward compatibility for mobile endpoints and Sanctum token authentication.
- Zero code modifications until this implementation plan is approved.

---

## Findings Summary

| Layer | Component | Severity | Description |
|---|---|---|---|
| **Backend** | `app/Services/` | Cleanup | 4 empty (0-byte) service files (`ImageUploadService`, `MapService`, `NotificationService`, `SoilAnalysisService`) unreferenced anywhere in code. |
| **Backend** | `routes/api.php` | Bug | Missing `Route::get('/user')` endpoint inside `auth:sanctum` group; causes mobile app session check to 404 and log users out on reload. |
| **Backend** | `app/Http/Controllers/Admin/` | Bug | `ManageSoilController` and `ManageApplicationController` are empty skeletons (`//`), but routed with 9 active endpoints in `routes/api.php`. |
| **Backend** | `app/Http/Controllers/Mobile/Api/` | Bug / Cleanup | 6 mobile controllers (`SoilController`, `MunsellColorController`, `FAQController`, `DownloadController`, `DashboardController`, `UserRequestController`) contain empty stubs returning `null`. 2 controllers (`ApplicationController`, `UserController`) are dead unrouted skeletons. |
| **Backend** | `config/services.php:54` | Bug | Default `'munsell_csv'` points to legacy `munsell_reference_sample.csv` instead of `munsell_reference_full.csv`. |
| **Backend** | `database/migrations/` | Bug | `2026_07_10_075720_create_downloads_table.php.php` has a malformed `.php.php` double extension. |
| **Backend** | `tests/Pest.php:18` | Bug | `// ->use(RefreshDatabase::class)` commented out, breaking all 21 in-memory SQLite feature tests with `no such table: users`. |
| **Backend** | `tests/Feature/ExampleTest.php:4` | Bug | Calls undefined route `'home'` instead of `'website.home'`. |
| **ML Pipeline** | `python/` | Cleanup | `munsell_reference_sample.csv` is an exact binary duplicate of `munsell_reference_full.csv`. |
| **ML Pipeline** | `python/reindex.py:106,124` | Bug / Polish | CLI help string mentions deleted `Color_Only`; default CSV argument points to sample CSV instead of full CSV. |
| **ML Pipeline** | `python/init_scan_tree.py:17` | Cleanup | `MUNSELL_HUES` contains obsolete `"Unclassified"` category. |
| **ML Pipeline** | `.agents/kkkkkk/` | Cleanup | 11 superseded handover backup files completely orphaned and unreferenced. |
| **Mobile App** | `rn-cli-app/` | Bug | Missing `.eslintrc.js`; running `npm run lint` crashes with configuration error. |
| **Mobile App** | `rn-cli-app/src/screens/ScanSoilScreen.js:19` | Cleanup | `LABEL_DISPLAY` retains dead `color_only` entry from obsolete 3-way classifier. |

---

## Layer 1: Laravel Backend Tasks

### Task 1.1: Remove 0-Byte Orphan Services
**Files:**
- Delete: `app/Services/ImageUploadService.php`
- Delete: `app/Services/MapService.php`
- Delete: `app/Services/NotificationService.php`
- Delete: `app/Services/SoilAnalysisService.php`

**Interfaces:**
- Consumes: None (0 references across entire codebase)
- Produces: None

- [ ] **Step 1: Verify 0 references in codebase**
Run: `grep -rn "ImageUploadService\|MapService\|NotificationService\|SoilAnalysisService" app/ routes/ config/`
Expected: Only the service files themselves or 0 results.

- [ ] **Step 2: Delete the 4 empty service files**
Command:
```powershell
Remove-Item "app/Services/ImageUploadService.php" -Force
Remove-Item "app/Services/MapService.php" -Force
Remove-Item "app/Services/NotificationService.php" -Force
Remove-Item "app/Services/SoilAnalysisService.php" -Force
```

- [ ] **Step 3: Run Laravel Pint to verify clean service namespace**
Run: `vendor/bin/pint --test`
Expected: PASS

---

### Task 1.2: Add Missing `/api/user` Route and Fix Empty Mobile API Controllers
**Files:**
- Modify: `routes/api.php:35-47`
- Modify: `app/Http/Controllers/Mobile/Api/DashboardController.php`
- Modify: `app/Http/Controllers/Mobile/Api/SoilController.php`
- Modify: `app/Http/Controllers/Mobile/Api/MunsellColorController.php`
- Modify: `app/Http/Controllers/Mobile/Api/FAQController.php`
- Modify: `app/Http/Controllers/Mobile/Api/DownloadController.php`
- Modify: `app/Http/Controllers/Mobile/Api/UserRequestController.php`
- Delete: `app/Http/Controllers/Mobile/Api/ApplicationController.php` (unrouted skeleton)
- Delete: `app/Http/Controllers/Mobile/Api/UserController.php` (unrouted skeleton)

**Interfaces:**
- Consumes: `Auth::user()`, Eloquent models (`Soil`, `MunsellColor`, `FAQ`, `Download`, `UserRequest`, `SoilScan`)
- Produces: JSON API contract responses `{ status: "success", data: ... }` for mobile endpoints

- [ ] **Step 1: Add `/api/user` route in `routes/api.php`**
Inside `Route::middleware('auth:sanctum')->group(...)`:
```php
Route::get('/user', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'status' => 'success',
        'data' => $request->user(),
    ]);
});
```

- [ ] **Step 2: Implement `Mobile\Api\SoilController`**
```php
public function index(Request $request)
{
    $soils = Soil::with('munsellColor')->paginate(20);
    return response()->json(['status' => 'success', 'data' => $soils]);
}

public function show(string $id)
{
    $soil = Soil::with('munsellColor')->findOrFail($id);
    return response()->json(['status' => 'success', 'data' => $soil]);
}
```

- [ ] **Step 3: Implement `Mobile\Api\MunsellColorController`**
```php
public function index(Request $request)
{
    $colors = MunsellColor::all();
    return response()->json(['status' => 'success', 'data' => $colors]);
}
```

- [ ] **Step 4: Implement `Mobile\Api\FAQController`**
```php
public function index(Request $request)
{
    $faqs = FAQ::where('status', 'Active')->orderBy('order')->get();
    return response()->json(['status' => 'success', 'data' => $faqs]);
}
```

- [ ] **Step 5: Implement `Mobile\Api\DownloadController`**
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'application_id' => 'required|exists:applications,application_id',
        'device_type'    => 'nullable|string|max:50',
    ]);
    $download = Download::create([
        'application_id' => $validated['application_id'],
        'user_id'        => Auth::id(),
        'device_type'    => $validated['device_type'] ?? 'Mobile',
        'downloaded_at'  => now(),
    ]);
    return response()->json(['status' => 'success', 'data' => $download], 201);
}
```

- [ ] **Step 6: Implement `Mobile\Api\DashboardController`**
```php
public function index(Request $request)
{
    $userId = Auth::id();
    $recentScans = SoilScan::with(['soil.munsellColor', 'location'])
        ->where('user_id', $userId)
        ->latest('scan_date')
        ->limit(5)
        ->get();
    $totalScans = SoilScan::where('user_id', $userId)->count();
    return response()->json([
        'status' => 'success',
        'data' => [
            'total_scans'  => $totalScans,
            'recent_scans' => $recentScans,
        ],
    ]);
}
```

- [ ] **Step 7: Implement `Mobile\Api\UserRequestController`**
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'request_type' => 'required|string|max:100',
        'notes'        => 'required|string',
    ]);
    $userReq = UserRequest::create([
        'user_id'      => Auth::id(),
        'request_type' => $validated['request_type'],
        'notes'        => $validated['notes'],
        'status'       => 'Pending',
    ]);
    return response()->json(['status' => 'success', 'data' => $userReq], 201);
}
```

- [ ] **Step 8: Remove dead unrouted mobile controllers**
```powershell
Remove-Item "app/Http/Controllers/Mobile/Api/ApplicationController.php" -Force
Remove-Item "app/Http/Controllers/Mobile/Api/UserController.php" -Force
```

- [ ] **Step 9: Verify route resolution**
Run: `php artisan route:list --path=api`
Expected: All endpoints resolve to valid controller actions.

---

### Task 1.3: Fix Admin API Routes & Skeleton Controllers
**Files:**
- Modify: `routes/api.php:55-80`
- Modify: `app/Http/Controllers/Admin/ManageSoilController.php`
- Modify: `app/Http/Controllers/Admin/ManageApplicationController.php`
- Modify: `app/Http/Controllers/Admin/DashboardController.php`

**Interfaces:**
- Consumes: Admin Sanctum auth middleware
- Produces: JSON CRUD endpoints for Admin API

- [ ] **Step 1: Implement `Admin\ManageSoilController`**
Implement standard REST methods (`index`, `store`, `show`, `update`, `destroy`) with validation against `Soil` model.
- [ ] **Step 2: Implement `Admin\ManageApplicationController`**
Implement standard REST methods (`index`, `store`, `update`, `destroy`) with validation against `Application` model.
- [ ] **Step 3: Implement `Admin\DashboardController`**
Return admin system metrics (counts of users, scans, soils, locations, active requests).
- [ ] **Step 4: Update `routes/api.php:56` import**
Change `use App\Http\Controllers\Mobile\Api\DashboardController;` inside admin group to explicitly point to `App\Http\Controllers\Admin\DashboardController::class`.

---

### Task 1.4: Fix Migration File Extension, Services Config, and Pest Test Suite
**Files:**
- Rename: `database/migrations/2026_07_10_075720_create_downloads_table.php.php` -> `2026_07_10_075720_create_downloads_table.php`
- Modify: `config/services.php:54`
- Modify: `tests/Pest.php:18`
- Modify: `tests/Feature/ExampleTest.php:4`

**Interfaces:**
- Consumes: Laravel SQLite in-memory test runner, service configuration
- Produces: Passing test suite across all feature tests

- [ ] **Step 1: Rename migration file with single extension**
```powershell
Rename-Item "database/migrations/2026_07_10_075720_create_downloads_table.php.php" "2026_07_10_075720_create_downloads_table.php"
```
Verify `php artisan migrate:status` runs cleanly.

- [ ] **Step 2: Update `config/services.php:54` default CSV**
Change `base_path('python/munsell_reference_sample.csv')` to `base_path('python/munsell_reference_full.csv')`.

- [ ] **Step 3: Enable `RefreshDatabase` in `tests/Pest.php:18`**
Uncomment `->use(RefreshDatabase::class)` in `tests/Pest.php`.

- [ ] **Step 4: Fix route name in `tests/Feature/ExampleTest.php`**
Change `route('home')` to `route('website.home')`.

- [ ] **Step 5: Run complete Laravel test suite**
Run: `php artisan test`
Expected: All tests pass.

---

## Layer 2: Python ML Pipeline Tasks

### Task 2.1: Deduplicate Munsell Reference CSV & Update CLI Defaults
**Files:**
- Delete: `python/munsell_reference_sample.csv` (or replace with a minimal 10-line sample if explicitly desired, else standardize on `munsell_reference_full.csv`)
- Modify: `python/reindex.py:106,124`
- Modify: `python/init_scan_tree.py:17`

**Interfaces:**
- Consumes: CSV file path
- Produces: Consistent reference dataset across CLI tools

- [ ] **Step 1: Remove duplicate CSV and alias in Python scripts**
Ensure `python/colorimetry_service.py`, `python/reindex.py`, and `python/init_scan_tree.py` default to `munsell_reference_full.csv`.
Delete the redundant duplicate `python/munsell_reference_sample.csv`.

- [ ] **Step 2: Clean CLI help text in `python/reindex.py:106`**
Remove `"Color_Only"` from the `--init` help text so it reads: `"Pre-create the full Scan_Soil_Image folder skeleton (Real_Soil, Fake_Soil)"`.

- [ ] **Step 3: Clean `MUNSELL_HUES` in `python/init_scan_tree.py:17`**
Remove `"Unclassified"` from `MUNSELL_HUES = ["10R", "2.5YR", "5YR", "7.5YR", "10YR", "2.5Y"]`.

- [ ] **Step 4: Verify Python syntax and execution**
Run: `python -m py_compile python/*.py`
Expected: 0 errors.

---

### Task 2.2: Purge Superseded `.agents/kkkkkk/` Backup Directory
**Files:**
- Delete: `.agents/kkkkkk/` (all 11 archived files)

**Interfaces:**
- Consumes: None (0 code references)
- Produces: Clean `.agents/` customization directory

- [ ] **Step 1: Verify `.agents/kkkkkk/` has no active references**
Run: `grep -rn "kkkkkk" .`
Expected: 0 matches.

- [ ] **Step 2: Remove directory**
```powershell
Remove-Item -Recurse -Force ".agents/kkkkkk"
```

---

## Layer 3: React Native Mobile App Tasks

### Task 3.1: Add Missing ESLint Configuration to `rn-cli-app`
**Files:**
- Create: `rn-cli-app/.eslintrc.js`

**Interfaces:**
- Consumes: ESLint 8.x + `@react-native/babel-preset`
- Produces: Working `npm run lint` script

- [ ] **Step 1: Create `rn-cli-app/.eslintrc.js`**
```javascript
module.exports = {
  root: true,
  extends: '@react-native',
  rules: {
    'react-native/no-inline-styles': 'off',
    'prettier/prettier': 'off',
  },
};
```

- [ ] **Step 2: Run ESLint**
Run: `cd rn-cli-app && npm run lint`
Expected: Runs across all `.js` files in `src/` without crash.

---

### Task 3.2: Clean Obsolete Labels and Harmonize Mobile Pipeline
**Files:**
- Modify: `rn-cli-app/src/screens/ScanSoilScreen.js:17-20`
- Modify: `rn-cli-app/src/services/auth.js:51`

**Interfaces:**
- Consumes: `GET /api/user` response from Layer 1 Task 1.2
- Produces: Stable user authentication persistence and clean 2-way UI indicators

- [ ] **Step 1: Clean `LABEL_DISPLAY` in `ScanSoilScreen.js`**
Remove `color_only` entry from `LABEL_DISPLAY`:
```javascript
const LABEL_DISPLAY = {
  real_soil: { label: 'Real Soil', color: '#22c55e', bg: 'rgba(34, 197, 94, 0.15)', border: '#22c55e' },
  fake_soil: { label: 'Fake / Non-Soil', color: '#ef4444', bg: 'rgba(239, 68, 68, 0.15)', border: '#ef4444' },
};
```

- [ ] **Step 2: Verify `fetchCurrentUser` contract in `rn-cli-app/src/services/auth.js`**
Confirm `api.get('/user')` aligns with the new endpoint added in Task 1.2 (`response.data.data`).

- [ ] **Step 3: Run Node syntax compilation across mobile source**
Run Babel parse test across `rn-cli-app/src/**/*.js`.
Expected: PASS.

---

## Verification Plan

### Automated Tests
1. **Laravel Pint Code Quality:**
   `vendor/bin/pint --test`
2. **Laravel Complete Test Suite:**
   `php artisan test`
3. **Python Bytecode Compilation:**
   `python -m py_compile python/*.py`
4. **Python Seed & Real Scan Verification:**
   `python python/colorimetry_service.py python/external_datasets/CyAUG-Dataset/Alluvial_Soil/1.jpg`
5. **React Native Linting & Syntax Check:**
   `cd rn-cli-app && npm run lint`
   `node -e "/* Babel parse verification */"`

### Manual Verification
1. Log in on mobile app, restart app, verify session remains active (Keychain token verified via `GET /api/user` without 404 logout).
2. Trigger scan on mobile app and verify 2-way classification modal renders properly (`Real Soil` or `Fake / Non-Soil`).
