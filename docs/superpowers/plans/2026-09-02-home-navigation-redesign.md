# Home Page & Bottom Navigation Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Rebuild the mobile app's Home page to match the component structure from reference Image 1 (greeting header, search bar with icon, horizontal category chips [All, Indoor, Outdoor, Flower], 2-column product grid with favorites and add buttons, and "Recent View" cards) using AgriCo's existing brand colors, paired with a floating rounded bottom navigation bar from reference Image 2 featuring the app's 5 items (Home, Munsell Color Chart, Scan Soil [floating center action], Map, Profile).

**Architecture:** 
A modular component structure under `rn-cli-app/src/components/` and `rn-cli-app/src/screens/`:
1. `BottomNavBar.js`: A custom floating curved bottom bar component supporting 5 tabs with an elevated center action button for "Scan Soil", active tab highlights, and safe area insets.
2. `HomeScreen.js`: Redesigned with greeting header, notification bell, pill search bar, horizontal category filter chips, dynamic 2-column product grid with heart toggles and (+) add buttons, and a "Recent View" horizontal list section.
3. Tab view integration for `Munsell Color Chart`, `Map`, and `Profile` (including sign out) directly within the authenticated navigation experience.

**Tech Stack:** React Native (0.73), React Navigation (v6 Native Stack), pure React Native animated/vector shapes, existing AgriCo color palette (`#22c55e`, `#DAF1DE`, `#051F20`, `#0B2B26`, `#8EB69B`, `#f6faf7`, `#ffffff`).

**Spec:** Matches reference Image 1 (Home layout & components) and reference Image 2 (floating curved bottom bar with elevated center button).

## Global Constraints
- Do NOT copy the olive/moss greens from Image 1 or the purple from Image 2.
- Strictly use AgriCo's brand design tokens: Emerald Green (`#22c55e`), Soft Mint (`#DAF1DE`), Forest Green (`#051F20` / `#0B2B26`), White (`#ffffff`), and Slate Gray (`#64748b` / `#94a3b8`).
- Bottom navigation must feature the 5 app items: `Home`, `Munsell Color Chart`, `Scan Soil` (floating center action button), `Map`, `Profile`.
- Category filter chips must include `All`, `Indoor`, `Outdoor`, `Flower`.

---

### Task 1: Create Lightweight Visual Vector Icons for Home & Navigation
**Files:**
- Create: `rn-cli-app/src/components/AppIcons.js`
- Test: Node Babel syntax check

**Interfaces:**
- Produces: `BellIcon`, `SearchIcon`, `HeartIcon`, `PlusIcon`, `HomeNavIcon`, `MunsellNavIcon`, `ScanCenterIcon`, `MapNavIcon`, `ProfileNavIcon`

- [ ] **Step 1: Create `rn-cli-app/src/components/AppIcons.js`**
  Implement clean, scalable pure React Native shape icons for all elements across the home screen and navbar.
- [ ] **Step 2: Verify syntax using Babel**
  Run: `node -e "require('@babel/core').parseSync(require('fs').readFileSync('src/components/AppIcons.js', 'utf8'), { presets: ['module:@react-native/babel-preset'] })"`

---

### Task 2: Create Floating Rounded Bottom Navigation Bar (`BottomNavBar.js`)
**Files:**
- Create: `rn-cli-app/src/components/BottomNavBar.js`
- Test: Node Babel syntax check

**Interfaces:**
- Consumes: `AppIcons`
- Produces: `<BottomNavBar activeTab={activeTab} onTabPress={handleTabPress} />`
- 5 items:
  - Tab 1: `Home` (`HomeNavIcon`)
  - Tab 2: `Munsell` (`MunsellNavIcon`, label "Munsell")
  - Tab 3 (Center Floating Button): `Scan Soil` (`ScanCenterIcon`, raised circular button with `#22c55e` background, shadow, and white scan icon)
  - Tab 4: `Map` (`MapNavIcon`, label "Map")
  - Tab 5: `Profile` (`ProfileNavIcon`, label "Profile")

- [ ] **Step 1: Implement `BottomNavBar.js` matching Image 2 layout with floating center button and scooped dip**
- [ ] **Step 2: Verify syntax using Babel**

---

### Task 3: Rebuild `HomeScreen.js` Matching Image 1 Layout with AgriCo Brand Palette
**Files:**
- Modify: `rn-cli-app/src/screens/HomeScreen.js`
- Test: Android Emulator live render & screencap

**Features:**
- Header: "Choose your" (subtle) + "favorite" (bold) with notification bell + green unread dot.
- Search Bar: Pill container with search input and circular `#22c55e` search icon button.
- Category Chips: Horizontal list with `All`, `Indoor`, `Outdoor`, `Flower`. Active chip styled in `#22c55e` with white text; inactive in `#ffffff` with `#e2e8f0` border.
- 2-Column Grid: Item cards with plant/soil imagery, top-right heart favorite toggle, name, category, price, and green `(+)` button.
- "Recent View" Section: Horizontal cards with item photo, title, multiline description, price, and heart icon.
- Integrated Tab switching for Home, Munsell, Map, Profile (with active session and Sign Out).
- Embedded `BottomNavBar`.

- [ ] **Step 1: Write complete `HomeScreen.js` with catalog data, filtering logic, and sub-views**
- [ ] **Step 2: Verify syntax using Babel**
- [ ] **Step 3: Reload Metro & React Native app on Android emulator**

---

### Task 4: Verify Full Flow on Android Emulator
- [ ] **Step 1: Capture screenshot of Home Screen with 2-column grid and recent view**
- [ ] **Step 2: Test category chip filtering (e.g., tap "Indoor", "Outdoor")**
- [ ] **Step 3: Test floating Scan Soil button navigation to `ScanSoilScreen`**
- [ ] **Step 4: Verify Munsell, Map, and Profile tab transitions**
- [ ] **Step 5: Document verification in `walkthrough.md`**
