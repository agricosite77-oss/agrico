# AgriCo Data Flow Diagram (Level 0) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create an exhaustive, academic-standard Data Flow Diagram Level 0 specification (both Level 0 System Context Diagram and Decomposed DFD Level 0 Subsystem Processes) for the AgriCo Website (Laravel 11 / MySQL) and Mobile Application (React Native / On-Device ML / Offline Sync).

**Architecture:** Gane & Sarson / Yourdon & Coad structured analysis standards rendered via Mermaid.js diagrams, accompanied by comprehensive Data Flow Dictionary tables, Data Store descriptions (D1–D7), External Entity matrices (E1–E5), and Web vs. Mobile boundary mappings.

**Tech Stack:** GitHub Flavored Markdown, Mermaid.js Flowchart syntax, Laravel 11 REST API, React Native, MySQL 8.0.

**Spec:** `docs/AGRICO_ENTITY_RELATIONSHIPS.md` and `docs/AGRICO_SYSTEM_FLOWCHARTS.md`.

## Global Constraints
- Must include both the **Level 0 System Context Diagram** (single system bubble `0.0` with external entities) and the **Decomposed Level 0 Diagram** (major processes `1.0` through `6.0` with data stores `D1` through `D7`).
- Must strictly distinguish between **Central Cloud Website processes** and **Mobile Application on-device processes** (especially offline scanning and sync).
- Must adhere to standard DFD notation rules: External Entities (`E`), Processes (`P`), Data Stores (`D`), and directed Data Flows with explicit data packet descriptions.
- Output file must be saved to `docs/AGRICO_DATA_FLOW_DIAGRAM_LEVEL_0.md`.

---

### Task 1: Level 0 System Context Diagram (Context-Level DFD)

**Files:**
- Create: `docs/AGRICO_DATA_FLOW_DIAGRAM_LEVEL_0.md`

**Interfaces:**
- Consumes: External actors and system boundaries from `docs/AGRICO_USE_CASES.md`.
- Produces: Context Level Mermaid diagram with Process `0.0 AgriCo Soil Analysis and Crop Recommendation System`, external entities E1–E5, and boundary data flow tables.

- [x] **Step 1: Write document header, methodology, and DFD notation definitions**
  Define standard symbols: External Entities (`[Square]`), Processes (`((Circle / Rounded))` or `([P])`), Data Stores (`[(Cylinder / Double Line)]`), and Data Flows (`-->|packet|`).

- [x] **Step 2: Construct Mermaid Level 0 System Context Diagram**
  Include Process 0.0 interacting with:
  - `E1: Farmer & Community User`
  - `E2: Agriculture Student & Researcher`
  - `E3: System Administrator`
  - `E4: Mobile Device Hardware (Camera & GPS)`
  - `E5: Cloud Storage & CDN (Azure Blob / GitHub Releases)`

- [x] **Step 3: Document Context Level Inflow/Outflow Data Packet Matrix**
  Tabulate all data inputs and outputs entering and leaving Process 0.0 with exact packet fields.

---

### Task 2: Decomposed DFD Level 0 Diagram (Processes 1.0 to 6.0 & Stores D1 to D7)

**Files:**
- Modify: `docs/AGRICO_DATA_FLOW_DIAGRAM_LEVEL_0.md`

**Interfaces:**
- Consumes: Relational schemas and stores from `docs/AGRICO_ENTITY_ATTRIBUTES_DOMAIN.md`.
- Produces: Complete decomposed Level 0 DFD with 6 core processes and 7 data stores.

- [x] **Step 1: Define the 6 core subsystem processes**
  - `1.0 User Authentication & Role Access Governance`
  - `2.0 Soil Color Capture & Computer Vision Classification`
  - `3.0 Crop Suitability & NPK Fertilizer Recommendation Engine`
  - `4.0 Lal-lo Geospatial Barangay Mapping & Telemetry Tagging`
  - `5.0 Mobile Application Versioning & Distribution Manager`
  - `6.0 Offline Queue Buffering & Network Synchronization`

- [x] **Step 2: Define the 7 data stores (Central DB + Mobile Client)**
  - `D1: User Accounts & Token Store` (`users`, `admins`, `personal_access_tokens`, `password_reset_tokens`)
  - `D2: Munsell Color Standards Database` (`munsell_colors` - 207 reference chips)
  - `D3: Soil Taxonomy & Agronomic Profile Store` (`soils`)
  - `D4: Soil Scans & Multi-Stage ML Payloads Store` (`soil_scans`)
  - `D5: Lal-lo Barangay GIS Geolocation Store` (`location_mappings`)
  - `D6: Mobile Releases & Download Audit Store` (`applications`, `downloads`, `application_actors`)
  - `D7: Mobile Client Local Storage & Queue` (`local_scan_history.json`, `agrico_offline_scans_queue`, `Keychain`)

- [x] **Step 3: Render complete Mermaid Decomposed Level 0 Diagram**
  Connect external entities E1–E5, processes 1.0–6.0, and data stores D1–D7 with directed data flows.

---

### Task 3: Data Flow Dictionary & Packet Specifications

**Files:**
- Modify: `docs/AGRICO_DATA_FLOW_DIAGRAM_LEVEL_0.md`

**Interfaces:**
- Consumes: Flow data from Task 2.
- Produces: Academic Data Flow Dictionary defining every packet structure.

- [x] **Step 1: Author Process-to-Process and Entity-to-Process Data Flow Dictionary**
  Table columns: `Data Flow ID`, `Flow Name`, `Source`, `Destination`, `Data Packet Composition`, `Transmission Protocol`.
  Examples: `DF-01 User Login Credentials`, `DF-07 Camera Frame & GPS Geotag`, `DF-12 6-Stage ML JSON Output`, `DF-18 Multipart Sync Payload`.

- [x] **Step 2: Author Data Store Inflow/Outflow CRUD Operations Matrix**
  Document `Create`, `Read`, `Update`, `Delete` interaction flows between processes 1.0–6.0 and stores D1–D7.

---

### Task 4: Platform Boundary Analysis (Website vs. Mobile App) & Verification

**Files:**
- Modify: `docs/AGRICO_DATA_FLOW_DIAGRAM_LEVEL_0.md`

**Interfaces:**
- Consumes: Controllers and mobile service code (`SoilScanController.php`, `scanSyncService.js`, `localScanStore.js`).
- Produces: Platform boundary analysis, cross-verification checklist, and final review.

- [x] **Step 1: Document Website (Cloud) vs. Mobile App (Edge/Offline) Execution Boundaries**
  Map which data flows execute entirely on-device, which execute on the cloud web portal, and which cross the network boundary via HTTPS REST API.

- [x] **Step 2: Cross-verify all flows and data stores against existing models and migrations**
  Ensure all entity references, field names, and external systems align with the actual Laravel and React Native codebase.
