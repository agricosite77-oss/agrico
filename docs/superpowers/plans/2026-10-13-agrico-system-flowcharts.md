# AgriCo System Flowcharts Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create comprehensive, end-to-end architectural and user journey flowcharts for both the AgriCo Website (Laravel 11 / Livewire 3 / Blade) and Mobile Application (React Native / Android / On-Device ML).

**Architecture:** Visual Mermaid-based flowcharts mapping system topology, user authentication, on-device & server-side 6-stage soil scanning algorithms, offline sync queues, admin management workflows, and in-app auto-update mechanisms.

**Tech Stack:** Mermaid.js, GitHub Flavored Markdown, Laravel 11, Livewire 3, React Native, SQLite / AsyncStorage, Google Cloud / Railway.

**Spec:** `docs/superpowers/specs/2026-10-13-agrico-system-spec.md`

## Global Constraints
- All diagrams must use clean, valid Mermaid syntax (`graph TD`, `flowchart TD`, `sequenceDiagram`) compatible with GitHub and Markdown renderers.
- Must cover both Website (Public Portal + Admin Livewire Panel) and Mobile Application (React Native).
- Must accurately reflect the real codebase paths, database entities (`applications`, `soil_scans`, `locations`, `munsell_colors`, `users`), and API routes.

---

### Task 1: Generate Master System Architecture & High-Level Ecosystem Flowchart

**Files:**
- Create: `docs/AGRICO_SYSTEM_FLOWCHARTS.md`

**Interfaces:**
- Consumes: System routing from `routes/web.php`, `routes/api.php`, `routes/admin.php`, and mobile config in `rn-cli-app/src/config/env.js`.
- Produces: Master System Topology Flowchart connecting Web Client, Mobile App, Laravel Backend, MySQL, and GitHub/CDN.

- [ ] **Step 1: Draft the high-level system topology flowchart**
Write the comprehensive Mermaid architecture diagram illustrating the interaction between Mobile App, Web Browser, Laravel API Gateway, MySQL Database, Storage, and GitHub Releases CDN.

- [ ] **Step 2: Verify Mermaid diagram syntax**
Ensure valid node syntax and clear cluster boundaries (`subgraph Web`, `subgraph Mobile`, `subgraph Backend`).

- [ ] **Step 3: Commit initial architecture flowchart**
```bash
git add docs/AGRICO_SYSTEM_FLOWCHARTS.md
git commit -m "docs: add master system architecture flowchart"
```

---

### Task 2: Generate Website Flowcharts (Public Portal, Soil Scanner, Admin Panel)

**Files:**
- Modify: `docs/AGRICO_SYSTEM_FLOWCHARTS.md`

**Interfaces:**
- Consumes: `WebsiteController.php`, `resources/views/website/scan.blade.php`, Livewire components under `app/Livewire/Admin/`.
- Produces: Detailed website operational flowcharts (Public Journey, Web Scanning & Computer Vision ML, Admin Moderation).

- [ ] **Step 1: Document Website Public Journey & User Authentication Flow**
Diagram landing, login, registration with email check, forgot password 6-digit verification code, and reactivation request for suspended accounts.

- [ ] **Step 2: Document Web Soil Scanning & Recommendation Engine Pipeline**
Diagram webcam/upload input, HTML5 canvas pixel extraction, lighting & white balance correction, 6-stage ML classifier, Munsell color matching (207 chips), crop suitability matrix, NPK calculation, and map pin creation.

- [ ] **Step 3: Document Admin & Super Admin Management Flow (Livewire 3)**
Diagram dashboard metrics, soil scan approval/rejection, Munsell color table CRUD, map pin visibility toggling, user status & 1-day suspension, and application release management.

- [ ] **Step 4: Commit website flowcharts**
```bash
git add docs/AGRICO_SYSTEM_FLOWCHARTS.md
git commit -m "docs: add website public, scanning, and admin flowcharts"
```

---

### Task 3: Generate Mobile Application Flowcharts (Lifecycle, Scanning, Offline Sync, Auto-Update)

**Files:**
- Modify: `docs/AGRICO_SYSTEM_FLOWCHARTS.md`

**Interfaces:**
- Consumes: `rn-cli-app/src/screens/`, `rn-cli-app/src/services/updateService.js`, `rn-cli-app/src/services/api.js`.
- Produces: Complete mobile application workflow diagrams.

- [ ] **Step 1: Document Mobile App Lifecycle & In-App Auto-Update Flow**
Diagram splash initialization, auto-login keychain check, version check `/api/app-version`, UpdateModal trigger (v1.0.3 -> v1.0.4), direct CDN stream via `RNFS`, and Android package installer launch.

- [ ] **Step 2: Document On-Device 6-Stage Soil Scanning Flow**
Diagram camera reticle alignment, auto-zoom bounding box, frame sampling, offline Munsell color matching via CIELAB Delta-E, crop suitability determination, and result display.

- [ ] **Step 3: Document Offline-First Queue & Background Synchronization**
Diagram offline scan storage (AsyncStorage/SQLite), network connectivity monitoring (`NetInfo`), queue batch processing to `/api/soil-scan`, and cloud history sync.

- [ ] **Step 4: Commit mobile application flowcharts**
```bash
git add docs/AGRICO_SYSTEM_FLOWCHARTS.md
git commit -m "docs: add mobile app lifecycle, on-device ML, and sync flowcharts"
```

---

### Task 4: Generate End-to-End Cross-Platform Synchronization Flowchart

**Files:**
- Modify: `docs/AGRICO_SYSTEM_FLOWCHARTS.md`

**Interfaces:**
- Consumes: Mobile scanning, Laravel API, Web admin verification, and Interactive Map endpoints.
- Produces: Sequence diagram showing how mobile scans become verified web GIS map points.

- [ ] **Step 1: Document End-to-End Sequence Diagram**
Mermaid sequence diagram showing Farmer/User -> Mobile App -> Offline Storage -> Network Reconnection -> Laravel API -> MySQL -> Admin Review -> Public Web Map.

- [ ] **Step 2: Final document review and validation**
Verify all diagram blocks render cleanly and all decision diamond paths have appropriate labels (`Yes`/`No`, `Success`/`Failure`).

- [ ] **Step 3: Commit completed documentation**
```bash
git add docs/AGRICO_SYSTEM_FLOWCHARTS.md docs/superpowers/plans/2026-10-13-agrico-system-flowcharts.md
git commit -m "docs: finalize complete AgriCo system flowchart documentation"
```
