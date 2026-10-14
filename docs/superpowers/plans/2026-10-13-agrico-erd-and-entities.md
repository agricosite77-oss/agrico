# AgriCo Entity Relationship, Entities, and Attributes Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Author a definitive, production-grade Entity Relationship Diagram (ERD) and Comprehensive Data Dictionary covering all Entities, Attributes, Data Types, Constraints, and Relationships across the AgriCo Website (Laravel 11 / MySQL) and Mobile Application (React Native / Offline SQLite & Encrypted Stores).

**Architecture:** Mermaid ERD diagrams (`erDiagram`) combined with rigorous relational schemas, table attributes tables, normalization rationale (3NF), cardinality matrices, foreign key cascades, and mobile local cache entity mappings.

**Tech Stack:** Mermaid.js, GitHub Flavored Markdown, MySQL 8.0, Laravel 11 Eloquent ORM, React Native Keychain / File System Store.

**Spec:** `docs/superpowers/specs/2026-10-13-agrico-system-spec.md`

## Global Constraints
- Must reflect exact production schemas from `database/migrations` and Eloquent models in `app/Models`.
- Must document all 16 server-side tables (`users`, `admins`, `soils`, `munsell_colors`, `soil_scans`, `location_mappings`, `applications`, `downloads`, `application_actors`, `system_actors`, `user_requests`, `faqs`, `faq_interactions`, `personal_access_tokens`, `password_reset_tokens`, `cache/sessions`).
- Must document mobile client offline entities (`local_scan_history`, `offline_scans_queue`, `auth_session_credentials`).
- Must include Mermaid ER diagram with valid syntax, cardinality notation (`||--o{`, `}|--||`, etc.), and attribute lists.
- Output file path: `docs/AGRICO_ENTITY_RELATIONSHIPS.md`.

---

### Task 1: System-Wide Mermaid ERD Diagram & Conceptual Model

**Files:**
- Create: `docs/AGRICO_ENTITY_RELATIONSHIPS.md`

**Interfaces:**
- Consumes: `app/Models/*.php`, `database/migrations/*.php`.
- Produces: Visual Mermaid `erDiagram` connecting all 16 database tables and mobile offline stores.

- [x] **Step 1: Write header, architecture overview, and conceptual domain model**
- [x] **Step 2: Generate Master Mermaid ERD diagram with relationships and cardinalities**
- [x] **Step 3: Generate Mobile Offline Storage & Cloud Synchronization ERD**

---

### Task 2: Core Domain Entities & Attributes (Users, Scanning, Munsell & ML)

**Files:**
- Modify: `docs/AGRICO_ENTITY_RELATIONSHIPS.md`

**Interfaces:**
- Consumes: `User.php`, `Admin.php`, `Soil.php`, `SoilScan.php`, `MunsellColor.php`.
- Produces: Detailed attribute dictionaries, data types, nullability, defaults, keys, and validation rules for core entities.

- [x] **Step 1: Define `users` and `admins` entity schemas and suspension mechanics**
- [x] **Step 2: Define `soils`, `munsell_colors`, and `soil_scans` schemas with ML/agronomic attributes**
- [x] **Step 3: Document CIELAB color matching and 6-stage ML JSON schema (`regions_data`)**

---

### Task 3: Spatial, Mobile Release, Knowledge Base & System Entities

**Files:**
- Modify: `docs/AGRICO_ENTITY_RELATIONSHIPS.md`

**Interfaces:**
- Consumes: `LocationMapping.php`, `Application.php`, `Download.php`, `ApplicationActor.php`, `SystemActor.php`, `UserRequest.php`, `FAQ.php`, `FAQInteraction.php`.
- Produces: Detailed attribute dictionaries for GIS mapping, APK release hub, user appeals, and FAQ telemetry.

- [x] **Step 1: Define `location_mappings` schema with Philippine spatial bounds and barangay clustering**
- [x] **Step 2: Define `applications`, `downloads`, and `application_actors` for mobile release management**
- [x] **Step 3: Define `user_requests` (appeals), `system_actors`, `faqs`, and `faq_interactions`**

---

### Task 4: Mobile Client Local Schemas, Normalization & Cardinality Matrix

**Files:**
- Modify: `docs/AGRICO_ENTITY_RELATIONSHIPS.md`

**Interfaces:**
- Consumes: `rn-cli-app/src/services/localScanStore.js`, `scanSyncService.js`.
- Produces: Complete Mobile Offline Schema, Relational Cardinality Matrix, and Referential Integrity / Indexing Guide.

- [x] **Step 1: Define Mobile On-Device Local Entities (`local_scan_history`, `offline_scans_queue`)**
- [x] **Step 2: Create Comprehensive Entity Relationship Cardinality & Multiplicity Matrix**
- [x] **Step 3: Document Database Indexes, Foreign Key Constraints, and Cascading Delete Rules**
