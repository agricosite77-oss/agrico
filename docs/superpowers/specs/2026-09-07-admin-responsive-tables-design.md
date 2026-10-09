# Architectural Design Specification: Unified Responsive Table System for Admin Records

- **Date:** 2026-09-07
- **Author:** Antigravity (AI Assistant) & Carl Ashley
- **Status:** Approved / Spec Review

---

## 1. Executive Summary & Goal

All record/data tables across the AgriCo Admin panel must share a unified, consistent, and graceful responsive behavior:
1. **Phase 1 (Proportional Fluid Shrinking)**: As the viewport or table container shrinks, all columns shrink proportionally (via fluid cell padding, compact styling, and CSS ellipsis text truncation) while everything fits on screen — **zero horizontal scrollbar**.
2. **Phase 2 (Progressive Column Hiding)**: Only when columns can no longer be compressed further without sacrificing readability, columns are progressively hidden one at a time based on a predefined priority order (least important dropped first).
3. **Core Guarantees**:
   - The **Actions** column (`.col-action`) is **never hidden** and remains fully clickable at all screen sizes.
   - Core identifying columns (`ID`, `Name/Title/User`) remain visible as long as possible down to small mobile viewports.
   - No horizontal or vertical scrollbars caused by table overflow.

---

## 2. Shared CSS Architecture & Utility Classes

The system is defined centrally in `resources/views/admin/partials/responsive-table-styles.blade.php` (included globally in `resources/views/admin/layouts/admin.blade.php`) so that any existing or new table automatically inherits the standard behavior by applying consistent utility classes.

### 2.1. Base Table Styling
```css
.agrico-table {
    table-layout: auto !important;
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
}

.agrico-table th,
.agrico-table td {
    box-sizing: border-box !important;
    vertical-align: top !important;
    padding-top: 11px !important;
    padding-bottom: 11px !important;
}
```

### 2.2. Column Width & Content Sizing Classes
- **`.col-shrink`**:
  ```css
  .agrico-table th.col-shrink,
  .agrico-table td.col-shrink {
      width: 1% !important;
      white-space: nowrap !important;
  }
  ```
  Forces compact columns (`ID`, `Status`, `Timestamp`, etc.) to occupy only their minimum intrinsic content width plus padding. Eliminates unwanted stretching or large dead gaps.
- **`.col-expand`**:
  ```css
  .agrico-table th.col-expand,
  .agrico-table td.col-expand {
      width: 50%;
      min-width: 160px;
      max-width: 380px;
      overflow: hidden;
  }
  ```
  Allows primary content columns (`Coordinates`, `Scan Note`, `Question & Answer`, `Description`) to share remaining fluid horizontal space equally, with built-in text clipping.
- **`.col-action` (Guaranteed Actions Column)**:
  ```css
  .agrico-table th.col-action,
  .agrico-table td.col-action {
      width: 1% !important;
      white-space: nowrap !important;
      text-align: center !important;
  }
  ```
  Protected from all `display: none` hiding rules across all viewports.
- **`.col-id`**:
  ```css
  .agrico-table th.col-id,
  .agrico-table td.col-id {
      width: 1% !important;
      white-space: nowrap !important;
      padding-left: 20px !important;
      padding-right: 16px !important;
  }
  ```
  Guarantees generous padding so the ID text `#47` can never collide with the adjacent name column.

### 2.3. Progressive Column Drop Priority Classes
- **`.col-drop-1`** (Hidden at $\le$ 1200px): Long auxiliary descriptions, secondary notes.
- **`.col-drop-2`** (Hidden at $\le$ 1000px): Secondary timestamps, indices, or categories.
- **`.col-drop-3`** (Hidden at $\le$ 800px): Tertiary details, leaving 4 core columns on tablets/phones.
- **`.col-core`** (Always visible): Primary identifiers, status, and actions.

---

## 3. Table Column Priority Mappings

### 3.1. Manage Map (`resources/views/admin/livewire/map/manage-map.blade.php`)
- **Columns**: `ID`, `Soil Name`, `Coordinates`, `Scan Note`, `Timestamp`, `Status`, `Actions`
- **Drop 1 ($\le$ 1200px)**: `Scan Note` (`.col-drop-1`)
- **Drop 2 ($\le$ 1000px)**: `Timestamp` (`.col-drop-2`)
- **Drop 3 ($\le$ 800px)**: `Coordinates` (`.col-drop-3`)
- **Core (Always Visible)**: `ID`, `Soil Name`, `Status`, `Actions`

### 3.2. Manage Munsell Color (`resources/views/admin/livewire/munsell-color/manage-munsell-color.blade.php`)
- **Columns**: `ID`, `Color Name`, `Image`, `Full Code`, `Soil Organic Index`, `Soil Type`, `Created`, `Actions`
- **Drop 1 ($\le$ 1200px)**: `Created` & `Image` (`.col-drop-1`)
- **Drop 2 ($\le$ 1000px)**: `Soil Organic Index` (`.col-drop-2`)
- **Drop 3 ($\le$ 800px)**: `Soil Type` (`.col-drop-3`)
- **Core (Always Visible)**: `ID`, `Color Name`, `Full Code`, `Actions`

### 3.3. Soil Scan History (`resources/views/admin/livewire/soil/soil-scan-history.blade.php`)
- **Columns**: `ID`, `Soil Name`, `Code`, `Scan Note`, `User`, `Date/Time`, `Actions`
- **Drop 1 ($\le$ 1200px)**: `Scan Note` (`.col-drop-1`)
- **Drop 2 ($\le$ 1000px)**: `User` (`.col-drop-2`)
- **Drop 3 ($\le$ 800px)**: `Date/Time` (`.col-drop-3`)
- **Core (Always Visible)**: `ID`, `Soil Name`, `Code`, `Actions`

### 3.4. Manage FAQ (`resources/views/admin/livewire/faq/manage-faq.blade.php`)
- **Columns**: `ID`, `Question & Answer`, `Category`, `Status`, `Actions`
- **Drop 1 ($\le$ 900px)**: `Category` (`.col-drop-2`)
- **Core (Always Visible)**: `ID`, `Question & Answer` (`.col-expand`), `Status`, `Actions`
- *Fix*: Removes hardcoded `min-w-[1000px]` causing scrollbars.

### 3.5. User Requests (`resources/views/admin/livewire/request/manage-request.blade.php`)
- **Columns**: `Request ID`, `User`, `Type`, `Description`, `Submitted`, `Status`, `Actions`
- **Drop 1 ($\le$ 1200px)**: `Description` (`.col-drop-1`)
- **Drop 2 ($\le$ 1000px)**: `Submitted` (`.col-drop-2`)
- **Drop 3 ($\le$ 800px)**: `Type` (`.col-drop-3`)
- **Core (Always Visible)**: `Request ID`, `User` (User Name + Email subtitle), `Status`, `Actions`
- *Fix*: Removes hardcoded `min-width: 900px` causing scrollbars.

### 3.6. Manage Users (`resources/views/admin/livewire/users/manage-user.blade.php`)
- **Columns**: `ID`, `Name & Avatar`, `Email`, `Phone`, `Role`, `Status`, `Created At`, `Actions`
- **Drop 1 ($\le$ 1200px)**: `Created At` & `Phone` (`.col-drop-1`)
- **Drop 2 ($\le$ 1000px)**: `Email` (`.col-drop-2`)
- **Drop 3 ($\le$ 800px)**: `Role` (`.col-drop-3`)
- **Core (Always Visible)**: `ID`, `Name & Avatar`, `Status`, `Actions`
- *Fix*: Removes hardcoded `min-w-[1100px]` causing scrollbars.

---

## 4. Implementation Steps

1. Create shared responsive stylesheet: `resources/views/admin/partials/responsive-table-styles.blade.php`.
2. Include the shared styles in `resources/views/admin/layouts/admin.blade.php`.
3. Refactor each admin view to adopt `.agrico-table` and the standard priority classes:
   - `manage-map.blade.php`
   - `manage-munsell-color.blade.php`
   - `soil-scan-history.blade.php`
   - `manage-faq.blade.php`
   - `manage-request.blade.php`
   - `manage-user.blade.php`
4. Clean up legacy component-specific media queries and remove all `min-w-[...]` and `min-width` table constraints.
5. Clear view cache and verify rendering with PHPUnit / Livewire component tests.
