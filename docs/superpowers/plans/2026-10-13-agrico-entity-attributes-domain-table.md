# AgriCo Entity, Attributes, and Domain Specifications Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Author a complete, academic-standard Entity-Attribute-Domain specification table matching the user's reference table format (`ENTITY | ATTRIBUTES | DOMAIN`) for all Website and Mobile Application entities, defining character sets, ranges, primary keys, and field validation constraints.

**Architecture:** Formatted Markdown tables adhering strictly to the 3-column reference schema (`ENTITY`, `ATTRIBUTES`, `DOMAIN`), integrating into `docs/AGRICO_ENTITY_RELATIONSHIPS.md` and providing a dedicated reference document `docs/AGRICO_ENTITY_ATTRIBUTES_DOMAIN.md`.

**Tech Stack:** GitHub Flavored Markdown, MySQL 8.0, Laravel 11, React Native Android Storage.

**Spec:** `docs/superpowers/specs/2026-10-13-agrico-system-spec.md`

## Global Constraints
- Every entity must follow the exact 3-column structure shown in the reference image:
  `| ENTITY | ATTRIBUTES | DOMAIN |`
- Primary Key attributes must be clearly designated with `_` or `(PK)` / underline formatting.
- Domain values must specify allowed character sets (e.g. `0-9`, `a-z, A-Z`, `a-z, A-Z, 0-9, @, .`, `Symbols`), value ranges, or exact enumerated sets.
- Must cover both Central Database Entities (12 core entities) and Mobile On-Device Client Entities (2 local storage entities).

---

### Task 1: Core User, Identity & Authentication Entities

**Files:**
- Create: `docs/AGRICO_ENTITY_ATTRIBUTES_DOMAIN.md`
- Modify: `docs/AGRICO_ENTITY_RELATIONSHIPS.md`

**Interfaces:**
- Consumes: `users`, `admins`, `personal_access_tokens`, `password_reset_tokens` schemas.
- Produces: 3-column `ENTITY | ATTRIBUTES | DOMAIN` tables for User, Admin, Personal_Access_Token, and Password_Reset_Token.

- [x] **Step 1: Write document header, methodology, and domain notation conventions**
- [x] **Step 2: Define `User` entity attributes and domains matching reference image**
- [x] **Step 3: Define `Admin`, `Personal_Access_Token`, and `Password_Reset_Token` attributes and domains**

---

### Task 2: Soil Analysis, Munsell Standard & Computer Vision Entities

**Files:**
- Modify: `docs/AGRICO_ENTITY_ATTRIBUTES_DOMAIN.md`
- Modify: `docs/AGRICO_ENTITY_RELATIONSHIPS.md`

**Interfaces:**
- Consumes: `soils`, `soil_scans`, `munsell_colors` schemas.
- Produces: 3-column `ENTITY | ATTRIBUTES | DOMAIN` tables for Soil, Soil_Scan, and Munsell_Color.

- [x] **Step 1: Define `Soil` entity attributes and domains**
- [x] **Step 2: Define `Soil_Scan` entity attributes and domains (including 6-stage ML JSON structure)**
- [x] **Step 3: Define `Munsell_Color` entity attributes and domains (207 standard chips)**

---

### Task 3: Spatial GIS, Mobile Releases, Telemetry & Support Entities

**Files:**
- Modify: `docs/AGRICO_ENTITY_ATTRIBUTES_DOMAIN.md`
- Modify: `docs/AGRICO_ENTITY_RELATIONSHIPS.md`

**Interfaces:**
- Consumes: `location_mappings`, `applications`, `downloads`, `user_requests`, `faqs`, `faq_interactions`, `system_actors`.
- Produces: 3-column `ENTITY | ATTRIBUTES | DOMAIN` tables for all spatial, application, support, and audit entities.

- [x] **Step 1: Define `Location_Mapping` (GIS Pins) attributes and domains**
- [x] **Step 2: Define `Application`, `Download`, and `Application_Actor` attributes and domains**
- [x] **Step 3: Define `User_Request` (Appeals), `FAQ`, `FAQ_Interaction`, and `System_Actor` attributes and domains**

---

### Task 4: Mobile Client Local Storage Entities & Cross-Reference Verification

**Files:**
- Modify: `docs/AGRICO_ENTITY_ATTRIBUTES_DOMAIN.md`
- Modify: `docs/AGRICO_ENTITY_RELATIONSHIPS.md`

**Interfaces:**
- Consumes: `local_scan_history.json`, `agrico_offline_scans_queue`.
- Produces: 3-column `ENTITY | ATTRIBUTES | DOMAIN` tables for mobile on-device entities and cross-verification matrix.

- [x] **Step 1: Define `Local_Scan_Record` and `Offline_Sync_Queue` mobile client entities**
- [x] **Step 2: Embed full Entity-Attribute-Domain tables into `docs/AGRICO_ENTITY_RELATIONSHIPS.md`**
- [x] **Step 3: Validate all entities against migrations and model properties**
