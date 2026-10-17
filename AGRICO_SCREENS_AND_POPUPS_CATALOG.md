# AgriCo System — Complete Catalog of Screens, Pop-ups, Modals & Menus

> **System Name:** AgriCo (Agricultural Soil Classification & GIS Mapping Platform)  
> **Last Updated:** October 2026  
> **Mobile & Responsive Status:** Fully Optimized & Verified (Zero Horizontal Page Overflows across 320px, 360px, 375px, 812×375 landscape, 768px tablet, and 1280px desktop)  
> **Touch Target Standard:** All interactive elements and modal close buttons $\ge 40\times 40\text{ px}$  

---

## Part 1: All Screens (Pages & Routes)

### 1. Public & Core Website Screens
| # | Screen Name | Route / URI | Blade View / Controller | Purpose |
|---|---|---|---|---|
| **1** | **Splash Screen** | `/` | `resources/views/splash.blade.php` | Animated entrance & platform splash branding. |
| **2** | **Security Verification Screen** | `/verify` | `resources/views/verify.blade.php` | Turnstile/CAPTCHA challenge verification before proceeding. |
| **3** | **Home / Landing Page** | `/home` | `resources/views/website/home.blade.php` | Main landing page highlighting features, stats, workflow, and CTA. |
| **4** | **Soil Color Scan Screen** | `/scan` | `resources/views/website/scan.blade.php` | Core AI camera capture, image upload, Munsell classification, soil analysis, and PDF report export. |
| **5** | **Scan Progress Tracker** | `/scan/progress/{scanId}` | `resources/views/website/scan-progress.blade.php` | Real-time AI inference processing status screen. |
| **6** | **Interactive Soil Map** | `/map` | `resources/views/website/map.blade.php` | Full-screen interactive Leaflet map of Lal-lo Cagayan with GPS soil pins, barangay filters, and layer toggles. |
| **7** | **Munsell Soil Color Chart** | `/munsell` | `resources/views/website/munsell.blade.php` | Interactive digital Munsell chart (Hue, Value, Chroma swatches, and soil associations). |
| **8** | **AI Model Training Hub** | `/training` | `resources/views/website/training.blade.php` | Soil dataset upload, labeling, and training metrics inspection. |
| **9** | **Knowledge Base / FAQs** | `/faq` | `resources/views/website/faq.blade.php` | Categorized searchable FAQs and accordion troubleshooting guides. |
| **10** | **User Profile & Settings** | `/profile` | `resources/views/website/profile.blade.php` | User credentials, personal info, avatar, security, email change, and account management. |
| **11** | **Notifications Center** | `/notifications` | `resources/views/website/notifications.blade.php` | Real-time activity notifications, scan alerts, and system announcements. |
| **12** | **Privacy Policy** | `/privacy` | `resources/views/website/privacy.blade.php` | Data collection, privacy compliance, and storage disclosures. |
| **13** | **Terms of Service** | `/terms` | `resources/views/website/terms.blade.php` | Usage terms, agricultural advisory disclaimers, and user obligations. |
| **14** | **Mobile App Download** | `/download` | `resources/views/website/download.blade.php` | Direct APK / Mobile app download page with version release notes. |

---

### 2. Authentication & Account Recovery Screens
| # | Screen Name | Route / URI | Controller / View | Purpose |
|---|---|---|---|---|
| **15** | **User Login Screen** | `/login` (or `/user/login`) | `resources/views/website/auth/login.blade.php` | Standard & student authentication with email, password, and remember-me. |
| **16** | **User Registration Screen** | `/register` (or `/user/register`) | `resources/views/website/auth/register.blade.php` | New user/student onboarding with real-time email verification. |
| **17** | **Forgot Password - Request Code** | `/forgot-password` | `resources/views/auth/forgot-password.blade.php` | Step 1 of password reset: enter email to receive 6-digit OTP. |
| **18** | **Forgot Password - Verify OTP** | `/forgot-password/verify-code` | `resources/views/auth/verify-code.blade.php` | Step 2 of password reset: 6-digit numeric verification code entry. |
| **19** | **Forgot Password - Reset Password** | `/forgot-password/reset` | `resources/views/auth/reset-password.blade.php` | Step 3 of password reset: enter new secure password and confirmation. |
| **20** | **Admin Registration Screen** | `/admin/register` | `resources/views/admin/auth/register.blade.php` | Dedicated admin staff account onboarding. |

---

### 3. Admin Management Portal Screens
| # | Screen Name | Route / URI | Livewire Component | Purpose |
|---|---|---|---|---|
| **21** | **Admin Dashboard** | `/dashboard` | `App\Livewire\Admin\Dashboard\AdminDashboard` | System KPIs, recent scans, activity timeline, user stats, and quick metrics. |
| **22** | **Soil Scan History** | `/soil` | `App\Livewire\Admin\Soil\SoilScanHistory` | Complete datatable of all processed soil scans, Munsell values, and metrics. |
| **23** | **Munsell Color Management** | `/munsell-color` | `App\Livewire\Admin\MunsellColor\ManageMunsellColor` | CRUD catalog of standard Munsell hues, values, chromas, and RGB values. |
| **24** | **FAQ Management** | `/faqs` | `App\Livewire\Admin\Faq\ManageFaq` | CRUD management of public FAQs, categories, and published status. |
| **25** | **Mapping History** | `/mapping-history` | `App\Livewire\Admin\Mapping\ManageMappingHistory` | Historical log of all GPS-pinned coordinates, barangays, and survey submissions. |
| **26** | **Map Management & Live Pins** | `/manage-map` | `App\Livewire\Admin\Map\ManageMap` | Interactive admin GIS map with pin visibility toggling (Active / Hidden). |
| **27** | **User Management** | `/users` | `App\Livewire\Admin\User\ManageUser` | Administrative user accounts table, role assignments, status, and audits. |
| **28** | **Student Records** | `/students` | `App\Livewire\Admin\Student\StudentRecord` | Academic sections (Section A, B, etc.), student scan portfolios, and activity. |
| **29** | **User Requests Center** | `/user-requests` | `App\Livewire\Admin\Request\ManageRequest` | Student verification approvals, role upgrade requests, and reactivation appeals. |
| **30** | **Application Upload Hub** | `/upload-application` | `App\Livewire\Admin\Application\UploadApplication` | Management and deployment of latest mobile APK builds. |

---

## Part 2: All Pop-ups, Modals, Drawers & Action Menus

### 1. Global & Website Modals / Pop-ups
| # | Pop-up Name | Location / View | Trigger / Event | Features & Functionality |
|---|---|---|---|---|
| **1** | **Global Login Required Modal** | `resources/views/website/layouts/website-layout.blade.php` (`#globalLoginRequiredModal`) | Accessing restricted action (e.g. saving scans, pinning location) while unauthenticated | Glassmorphic prompt with direct Login/Register buttons and seamless return URL preservation. |
| **2** | **Account Pending Approval Modal** | `resources/views/website/layouts/website-layout.blade.php` (`#globalAccountPendingModal`) | User logged in but awaiting admin/faculty verification | Informs user their student/specialist verification is currently under review by administrators. |
| **3** | **Account Suspension Notice & Appeal Modal** | `resources/views/website/auth/login.blade.php` (`#suspensionModal`) | Attempting login with suspended account credentials | Shows suspension reason, duration, and provides a direct self-service reactivation appeal submission form. |
| **4** | **Website Soil Detail Modal** | `resources/views/website/map.blade.php` (`#websiteSoilDetailModal`) | Clicking any map marker or "View Details" link on the interactive map | Full modal presenting soil RGB/HSV swatches, Munsell notation, pH, crop suitability, and coordinates. |
| **5** | **Leaflet Interactive Map Pin Popups** | `resources/views/website/map.blade.php` & `resources/views/website/scan.blade.php` | Clicking any interactive map pin | Rich Leaflet popup card showing thumbnail image, soil name, barangay, and "Inspect" button. |
| **6** | **Resolution Layer Selection Dropdown** | `resources/views/website/scan.blade.php` (`.res-layer-dropdown-menu`) | Clicking resolution layer selector in Scan workbench | Allows switching between default, satellite, terrain, and high-contrast analytical layers. |
| **7** | **Email Change Verification OTP Modal** | `resources/views/website/profile.blade.php` (`.profile-modal-overlay`) | Initiating email address change in profile settings | Sends 6-digit verification code to the new email address with OTP input fields and countdown timer. |
| **8** | **Two-Factor Authentication Setup Modal** | `resources/views/website/pages/settings/two-factor-setup-modal.blade.php` | Enabling 2FA in user profile settings | QR Code display for Google Authenticator/Authy, secret key, and OTP confirmation code. |
| **9** | **Delete Account Confirmation Modal** | `resources/views/website/pages/settings/delete-user-modal.blade.php` | Requesting account termination in profile settings | Permanent deletion warning, password re-entry challenge, and irreversible confirmation. |

---

### 2. Admin Entity Creation & Detail Modals
| # | Pop-up Name | Component / View | Trigger / Event | Features & Functionality |
|---|---|---|---|---|
| **10** | **Admin Profile & Settings Modal** | `resources/views/admin/livewire/admin/admin-profile-modal.blade.php` | Clicking admin avatar/profile button in top nav | Update admin profile photo, name, email, credentials, and password. |
| **11** | **User View Details Modal** | `resources/views/admin/livewire/users/manage-user.blade.php` (`.user-view-modal-container`) | Clicking "View Details" action on any user | Comprehensive dossier showing user avatar, contact, roles, status history, and activity timestamps. |
| **12** | **User Edit Modal (2-Step)** | `resources/views/admin/livewire/users/user-edit-modal.blade.php` | Clicking "Edit User" in User Management | **Step 1:** Name, email, phone, role, and department.<br>**Step 2:** Password change and security enforcement. |
| **13** | **Soil Scan Detail Modal** | `resources/views/admin/livewire/soil/soil-detail-modal.blade.php` | Clicking "View Scan Details" in Soil Scan History | High-res scanned image preview, Munsell chip, RGB/HSV, Nitrogen/Phosphorus/Potassium, pH, confidence score, and barangay location. |
| **14** | **Soil Scan Edit Modal** | `resources/views/admin/livewire/soil/soil-form-modal.blade.php` | Clicking "Edit Scan Notes" in Soil Scan History | Form to correct classification notes, tags, crop recommendations, or surveyor metadata. |
| **15** | **Mapping History Detail Modal** | `resources/views/admin/livewire/mapping/mapping-detail-modal.blade.php` | Clicking "View Pin" in Mapping History | Detailed breakdown of GPS pinned location, surveyed barangay, surveyor name, timestamp, and soil match. |
| **16** | **Add Munsell Color Modal** | `resources/views/admin/livewire/munsell-color/munsell-create-modal.blade.php` | Clicking "+ Add Munsell Color" button | Color picker, Hue/Value/Chroma notation fields, RGB HEX code, and standard soil description. |
| **17** | **Munsell Color Detail Modal** | `resources/views/admin/livewire/munsell-color/munsell-detail-modal.blade.php` | Clicking "View Color" in Munsell Catalog | Large visual color chip preview, color gamut coordinates, and list of associated Philippine soil types. |
| **18** | **Edit Munsell Color Modal** | `resources/views/admin/livewire/munsell-color/munsell-edit-modal.blade.php` | Clicking "Edit Color" in Munsell Catalog | Edit hue notation, value, chroma, RGB codes, and description. |
| **19** | **Add FAQ Modal** | `resources/views/admin/livewire/faq/faq-create-modal.blade.php` | Clicking "+ Add FAQ" in FAQ Management | Category dropdown, question input, rich text answer editor, and published status toggle. |
| **20** | **FAQ Detail Modal** | `resources/views/admin/livewire/faq/faq-detail-modal.blade.php` | Clicking "Inspect FAQ" in FAQ Management | Read-only preview of question, answer, category badge, view counter, and last updated time. |
| **21** | **Edit FAQ Modal** | `resources/views/admin/livewire/faq/faq-edit-modal.blade.php` | Clicking "Edit FAQ" in FAQ Management | Modifying question text, answer content, ordering weight, and publication visibility. |
| **22** | **Review User Request Modal** | `resources/views/admin/livewire/request/manage-request.blade.php` (`.admin-review-request-modal`) | Clicking "Review" on registration/appeal request | Inspects student ID image, registration details, appeal message, with one-click Approve / Disapprove actions. |
| **23** | **Add Student Modal** | `resources/views/admin/livewire/student/partials/add-student-modal.blade.php` | Clicking "+ Add Student" in Student Records | Create student record: Student ID, Full Name, Section (A, B, C), and initial credentials. |
| **24** | **Student Section Records & Activity Modal** | `resources/views/admin/livewire/student/partials/section-records-modal.blade.php` | Clicking "Save as Copy / Section Activity" | Multi-column tabular portfolio of all students in that section, completed scans, pinned coordinates, and export options. |
| **25** | **Student Soil Scans Record Modal** | `resources/views/admin/livewire/student/student-record.blade.php` (`.scan-modal-card`) | Clicking "Scans Count" on a student row | Filtered modal displaying all soil scans completed specifically by that student. |
| **26** | **Student Pin Maps Record Modal** | `resources/views/admin/livewire/student/student-record.blade.php` (`.pin-modal-card`) | Clicking "Pins Count" on a student row | Filtered modal displaying all GPS map pins contributed specifically by that student. |

---

### 3. Admin Confirmation & Security Modals
| # | Pop-up Name | Component / View | Trigger / Event | Features & Functionality |
|---|---|---|---|---|
| **27** | **User Reset Password Modal** | `resources/views/admin/livewire/users/user-password-modal.blade.php` | Clicking "Reset Password" on user row | Generates secure temporary password or sends one-time reset link directly to the user's email. |
| **28** | **User Suspend Modal** | `resources/views/admin/livewire/users/user-suspend-modal.blade.php` | Clicking "Suspend User" on user row | Form with reason selection, custom suspension duration (1 day, 7 days, 30 days, or indefinite), and audit note. |
| **29** | **User Delete Confirmation Modal** | `resources/views/admin/livewire/users/user-delete-modal.blade.php` | Clicking "Delete User" on user row | High-caution confirmation dialog verifying user deletion and cascading record handling. |
| **30** | **Soil Scan Delete Modal** | `resources/views/admin/livewire/soil/soil-delete-modal.blade.php` | Clicking "Delete Scan" on soil scan row | Confirmation dialog to permanently delete or archive a soil scan record. |
| **31** | **Mapping History Delete Modal** | `resources/views/admin/livewire/mapping/mapping-delete-modal.blade.php` | Clicking "Delete Record" in Mapping History | Confirmation dialog to permanently remove a pinned survey location from the dataset. |
| **32** | **Munsell Color Delete Modal** | `resources/views/admin/livewire/munsell-color/munsell-delete-modal.blade.php` | Clicking "Delete Color" in Munsell Catalog | Confirmation dialog verifying no existing scans depend on this Munsell reference before deletion. |
| **33** | **FAQ Delete Confirmation Modal** | `resources/views/admin/livewire/faq/faq-delete-modal.blade.php` | Clicking "Delete FAQ" in FAQ Management | Confirmation dialog to remove FAQ item from public knowledge base. |
| **34** | **Student Delete Confirmation Modal** | `resources/views/admin/livewire/student/student-record.blade.php` (`showDeleteModal`) | Clicking "Delete Student" on student row | Confirmation dialog to remove student from academic section. |

---

### 4. Slide-Out Drawers & Flyouts
| # | Drawer / Flyout Name | Location | Trigger | Features & Functionality |
|---|---|---|---|---|
| **35** | **Website Mobile Navigation Drawer** | `resources/views/website/layouts/website-layout.blade.php` (`.ws-mobile-menu`) | Clicking hamburger icon on mobile viewports (< 1024px) | Smooth slide-out menu with all public pages, authentication links, and dark/light theme switch. |
| **36** | **Admin Mobile Sidebar Drawer** | `resources/views/admin/layouts/admin.blade.php` (`.admin-sidebar`) | Clicking mobile sidebar toggle button (< 1024px) | Slide-out admin navigation containing all management modules, badge counters, and collapse controls. |
| **37** | **Notifications Dropdown Flyout** | `resources/views/website/layouts/website-layout.blade.php` (`#notificationsFlyout`) | Clicking notification bell icon in top header | Interactive popover showing unread notifications, mark-all-read action, and direct links to events. |
| **38** | **Admin Topbar Profile Dropdown** | `resources/views/admin/layouts/admin.blade.php` (`#adminProfileDropdown`) | Clicking admin avatar pill in top header | Quick popover with links to Profile Settings, Activity Log, and Logout action. |

---

### 5. Floating Action Menus & Context Dropdowns
| # | Action Menu Name | Location | Trigger | Options Available |
|---|---|---|---|---|
| **39** | **User Row Action Menu** | `resources/views/admin/livewire/users/manage-user.blade.php` (`.agrico-mobile-dropdown`) | Clicking `.agrico-more-btn` (three dots) on any user | • View Details<br>• Edit User<br>• Reset Password<br>• Suspend User<br>• Delete User |
| **40** | **Request Row Action Menu** | `resources/views/admin/livewire/request/manage-request.blade.php` (`.agrico-mobile-dropdown`) | Clicking `.agrico-more-btn` on any request | • Review Request<br>• Fast Approve<br>• Fast Disapprove |
| **41** | **Munsell Row Action Menu** | `resources/views/admin/livewire/munsell-color/manage-munsell-color.blade.php` (`.agrico-mobile-dropdown`) | Clicking `.agrico-more-btn` on any Munsell chip | • View Color<br>• Edit Color<br>• Delete Color |
| **42** | **FAQ Row Action Menu** | `resources/views/admin/livewire/faq/manage-faq.blade.php` (`.agrico-mobile-dropdown`) | Clicking `.agrico-more-btn` on any FAQ | • View FAQ<br>• Edit FAQ<br>• Delete FAQ |
| **43** | **Student Row Action Menu** | `resources/views/admin/livewire/student/student-record.blade.php` (`.agrico-mobile-dropdown`) | Clicking `.agrico-more-btn` on any student | • View Scans Portfolio<br>• View Pin Maps<br>• Edit Student<br>• Delete Student |

---

## Part 3: Pop-up Dimensions & Responsive Standards

### Standard Pop-up Sizing Specification
All system dialogs and modal cards (with the exception of Admin Delete Modals which remain in their legacy isolation) follow this unified specification across devices:

```css
/* 1. Desktop (1280px+) & Tablet (768px) */
.agrico-standard-popup,
.record-popup-card,
.soil-detail-modal-container,
.mapping-detail-modal-container,
.admin-review-request-modal,
.user-view-modal-container,
.user-edit-modal-container {
    width: 100% !important;
    max-width: 680px !important;
    height: auto !important;
    max-height: min(85vh, calc(100dvh - 24px)) !important;
    border-radius: 28px !important;
    margin: auto !important;
}

/* 2. Mobile Viewports (320px – 375px) */
@media (max-width: 640px) {
    .soil-detail-backdrop,
    .record-popup-backdrop,
    .pin-modal-backdrop,
    .user-view-backdrop,
    .soil-form-backdrop,
    #soilRecordModal,
    #wsSoilDetailModal,
    .profile-modal-overlay {
        padding: 24px 16px !important;
        box-sizing: border-box !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .agrico-standard-popup,
    .record-popup-card,
    .soil-detail-modal-container,
    .mapping-detail-modal-container,
    .admin-review-request-modal,
    .user-view-modal-container,
    .user-edit-modal-container,
    .section-records-dialog {
        width: 100% !important;
        max-width: min(calc(100vw - 32px), 420px) !important;
        height: auto !important;
        max-height: min(72vh, 520px) !important;
        border-radius: 24px !important;
        margin: auto !important;
    }
}

/* 3. Landscape Phone (e.g. 812×375px) */
@media (max-height: 480px) and (orientation: landscape) {
    .agrico-standard-popup,
    .record-popup-card,
    .soil-detail-modal-container,
    .mapping-detail-modal-container,
    .admin-review-request-modal,
    .user-view-modal-container,
    .user-edit-modal-container {
        width: 100% !important;
        max-width: 540px !important;
        height: auto !important;
        max-height: min(85vh, calc(100dvh - 20px)) !important;
        border-radius: 24px !important;
    }
}
```

### Scope Isolation & Exceptions
1. **Admin Delete Confirmation Pop-ups (Protected & Unchanged):**
   - User Delete Confirmation Modal (`user-delete-modal.blade.php`)
   - Soil Scan Delete Modal (`soil-delete-modal.blade.php`)
   - Mapping History Delete Modal (`mapping-delete-modal.blade.php`)
   - Munsell Color Delete Modal (`munsell-delete-modal.blade.php`)
   - FAQ Delete Confirmation Modal (`faq-delete-modal.blade.php`)
   - Student Delete Confirmation Modal (`student-record.blade.php`, `showDeleteModal`)
   *These modals strictly maintain their original CSS rules and are scoped out from `.agrico-standard-popup`.*
2. **Student Section Records Table Exception:**
   - Outer modal dialog respects `max-width: calc(100vw - 24px)` and `border-radius: 24px` on mobile.
   - Internal horizontal scrolling is exclusively permitted within `.xl-wrap` for the multi-column data table.
3. **Close Buttons:**
   - Standardized to minimum touch target of $\ge 40\times 40\text{ px}$ across web and React Native mobile app (`PinSoilLocationModal.js`, `ScanSoilScreen.js`, `HomeScreen.js`).

---

## Part 4: Verification & Automated Auditing

Run either automated Puppeteer audit suite from your terminal:

```bash
# 1. Full Platform Route Audit (24 routes × 6 viewports = 144 configurations)
node tests/responsive/run_responsive_audit.js

# 2. Real DOM Pop-up & Modal Suite (18 interactive dialogs × 6 viewports = 108 tests)
node tests/responsive/test_all_modals.js
```
