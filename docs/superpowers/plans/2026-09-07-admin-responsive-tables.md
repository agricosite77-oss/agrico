# Admin Responsive Tables Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make all record/data tables across the AgriCo Admin panel share a unified responsive system with fluid proportional shrinking and progressive column hiding, zero horizontal scrollbars, and guaranteed Actions visibility.

**Architecture:** Create a central responsive CSS stylesheet `resources/views/admin/partials/responsive-table-styles.blade.php`, include it in `resources/views/admin/layouts/admin.blade.php`, and refactor all admin table views to apply standard classes (`.agrico-table`, `.col-shrink`, `.col-expand`, `.col-drop-1`, `.col-drop-2`, `.col-drop-3`, `.col-action`).

**Tech Stack:** Laravel Blade, Tailwind CSS, Livewire 3.

**Spec:** [docs/superpowers/specs/2026-09-07-admin-responsive-tables-design.md](file:///c:/Users/Carl%20Ashley/Herd/agrico/docs/superpowers/specs/2026-09-07-admin-responsive-tables-design.md)

## Global Constraints
- `table-layout: auto !important; width: 100% !important;` for all admin tables.
- Actions column (`.col-action`) must never be hidden on any screen size.
- No horizontal scrollbars (`overflow-x: auto;` wrapper should not trigger overflow under normal responsive widths).
- Zero collisions: ID and Title/Name must always maintain adequate padding.

---

### Task 1: Create Central Responsive Table Stylesheet

**Files:**
- Create: `resources/views/admin/partials/responsive-table-styles.blade.php`
- Modify: `resources/views/admin/layouts/admin.blade.php`

**Interfaces:**
- Produces: CSS classes `.agrico-table`, `.col-shrink`, `.col-expand`, `.col-action`, `.col-drop-1`, `.col-drop-2`, `.col-drop-3`, `.col-core`.

- [ ] **Step 1: Write `resources/views/admin/partials/responsive-table-styles.blade.php`**
  Define `.agrico-table` auto-layout rules, fluid padding, and media query breakpoints (1200px, 1000px, 800px, 480px).
- [ ] **Step 2: Include partial in `resources/views/admin/layouts/admin.blade.php`**
  Add `@include('admin.partials.responsive-table-styles')` within `<head>`.
- [ ] **Step 3: Clear view cache and verify syntax**
  Run `php artisan view:clear`.

---

### Task 2: Standardize Manage Map Table

**Files:**
- Modify: `resources/views/admin/livewire/map/manage-map.blade.php`

- [ ] **Step 1: Apply `.agrico-table` and priority classes**
  - Add `.agrico-table` to `<table>`.
  - Add `.col-drop-1` to `Scan Note`.
  - Add `.col-drop-2` to `Timestamp`.
  - Add `.col-drop-3` to `Coordinates`.
  - Keep `ID`, `Soil Name`, `Status`, `Actions` as core.
- [ ] **Step 2: Remove redundant component-scoped table CSS**
  Clean up conflicting duplicate styles from `<style>` block.
- [ ] **Step 3: Test render**
  Run `php artisan tinker --execute="echo Livewire\Livewire::test(App\Livewire\Admin\Map\ManageMap::class)->html() ? 'OK' : 'FAIL';"`.

---

### Task 3: Standardize Manage Munsell Color Table

**Files:**
- Modify: `resources/views/admin/livewire/munsell-color/manage-munsell-color.blade.php`

- [ ] **Step 1: Apply `.agrico-table` and priority classes**
  - Add `.agrico-table` to `<table>`.
  - Add `.col-drop-1` to `Created` and `Image`.
  - Add `.col-drop-2` to `Soil Organic Index`.
  - Add `.col-drop-3` to `Soil Type`.
  - Keep `ID`, `Color Name`, `Full Code`, `Actions` as core.
- [ ] **Step 2: Remove legacy `.munsell-color-table` fixed layout styles**
- [ ] **Step 3: Test render**
  Run `php artisan tinker --execute="echo Livewire\Livewire::test(App\Livewire\Admin\MunsellColor\ManageMunsellColor::class)->html() ? 'OK' : 'FAIL';"`.

---

### Task 4: Standardize Soil Scan History Table

**Files:**
- Modify: `resources/views/admin/livewire/soil/soil-scan-history.blade.php`

- [ ] **Step 1: Apply `.agrico-table` and priority classes**
  - Add `.agrico-table` to `<table>`.
  - Add `.col-drop-1` to `Scan Note`.
  - Add `.col-drop-2` to `User`.
  - Add `.col-drop-3` to `Date/Time`.
  - Keep `ID`, `Soil Name`, `Code`, `Actions` as core.
- [ ] **Step 2: Remove legacy `.soil-scan-table` fixed layout styles**
- [ ] **Step 3: Test render**
  Run `php artisan tinker --execute="echo Livewire\Livewire::test(App\Livewire\Admin\Soil\SoilScanHistory::class)->html() ? 'OK' : 'FAIL';"`.

---

### Task 5: Standardize Manage FAQ Table

**Files:**
- Modify: `resources/views/admin/livewire/faq/manage-faq.blade.php`

- [ ] **Step 1: Remove `min-w-[1000px]` and apply `.agrico-table`**
  - Replace `<table class="w-full min-w-[1000px]">` with `<table class="agrico-table">`.
  - Add `.col-drop-2` to `Category`.
  - Keep `ID`, `Question & Answer` (`.col-expand`), `Status`, `Actions` as core.
- [ ] **Step 2: Test render**
  Run `php artisan tinker --execute="echo Livewire\Livewire::test(App\Livewire\Admin\Faq\ManageFaq::class)->html() ? 'OK' : 'FAIL';"`.

---

### Task 6: Standardize User Requests Table

**Files:**
- Modify: `resources/views/admin/livewire/request/manage-request.blade.php`

- [ ] **Step 1: Remove `min-width:900px;` and apply `.agrico-table`**
  - Replace `<table style="... min-width:900px;">` with `<table class="agrico-table">`.
  - Add `.col-drop-1` to `Description`.
  - Add `.col-drop-2` to `Submitted`.
  - Add `.col-drop-3` to `Type`.
  - Keep `Request ID`, `User`, `Status`, `Actions` as core.
- [ ] **Step 2: Test render**
  Run `php artisan tinker --execute="echo Livewire\Livewire::test(App\Livewire\Admin\Request\ManageRequest::class)->html() ? 'OK' : 'FAIL';"`.

---

### Task 7: Standardize Manage Users Table

**Files:**
- Modify: `resources/views/admin/livewire/users/manage-user.blade.php`

- [ ] **Step 1: Remove `min-w-[1100px]` and apply `.agrico-table`**
  - Replace `<table class="w-full min-w-[1100px]">` with `<table class="agrico-table">`.
  - Add `.col-drop-1` to `Created At` & `Phone`.
  - Add `.col-drop-2` to `Email`.
  - Add `.col-drop-3` to `Role`.
  - Keep `ID`, `Name & Avatar`, `Status`, `Actions` as core.
- [ ] **Step 2: Test render**
  Run `php artisan tinker --execute="echo Livewire\Livewire::test(App\Livewire\Admin\Users\ManageUser::class)->html() ? 'OK' : 'FAIL';"`.

---

### Task 8: Verification & Cache Clearing

- [ ] **Step 1: Clear compiled views**
  Run `php artisan view:clear`.
- [ ] **Step 2: Run Livewire smoke tests across all modified components**
- [ ] **Step 3: Update documentation and walkthrough**
