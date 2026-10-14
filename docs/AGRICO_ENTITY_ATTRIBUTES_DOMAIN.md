# 🌾 AgriCo Database Data Dictionary: Entities, Attributes & Domains

This document provides the formal **Entity, Attributes, and Domain Specification** for the **AgriCo Soil Analysis and Crop Recommendation System** (Website and Mobile Application). Every entity, primary key, foreign key, attribute, and domain constraint is documented following the standard academic data modeling structure.

---

## 📌 Notation & Domain Conventions

* **Underlined attribute (e.g. `<u>Attribute_Name</u>`):** Designates the **Primary Key (PK)** of the entity.
* **Italicized attribute (e.g. *Attribute_Name*):** Designates a **Foreign Key (FK)** referencing another entity.
* **Domain:** Specifies the permitted character set, alphabet, numerical range, or enumerated set allowed for valid instances of the attribute.

---

## 1. Central Database Entities, Attributes & Domains

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **User** | <u>User_ID</u> | `0-9` *(or formatted `USR-XXXX`, `ADM-XXXX`)* |
| | Full_Name | `a-z, A-Z, spaces` |
| | Email | `a-z, A-Z, 0-9, @, ., _, -` |
| | Password | `a-z, A-Z, 0-9, Symbols` *(Bcrypt hash string)* |
| | Role | `'farmer', 'student', 'researcher', 'admin', 'superadmin'` |
| | Student_ID | `a-z, A-Z, 0-9, -` *(Optional)* |
| | Section | `a-z, A-Z, 0-9, spaces` *(Optional)* |
| | Student_Record_Status | `'active', 'pending', 'graduated'` |
| | Status | `'ACTIVE', 'SUSPENDED'` |
| | Suspend_Reason | `a-z, A-Z, 0-9, Symbols, spaces` *(Text)* |
| | Suspended_Until | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | *Suspended_By_Admin_ID* | `a-z, A-Z, 0-9, -` *(FK -> Admin.Admin_ID)* |
| | *Last_Admin_ID* | `a-z, A-Z, 0-9, -` *(FK -> Admin.Admin_ID)* |
| | Profile_Photo_Path | `a-z, A-Z, 0-9, /, ., _, -` *(File path)* |
| | Is_Online | `0, 1` *(Boolean)* |
| | Theme | `'light', 'dark'` |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Scheduled_Deletion_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Admin** | <u>Admin_ID</u> | `0-9, a-z, A-Z, -` *(e.g. ADM-001, ADM-002)* |
| | Full_Name | `a-z, A-Z, spaces` |
| | Email | `a-z, A-Z, 0-9, @, ., _, -` |
| | Password | `a-z, A-Z, 0-9, Symbols` *(Bcrypt hash)* |
| | Profile_Photo_Path | `a-z, A-Z, 0-9, /, ., _, -` *(File path)* |
| | Remember_Token | `a-z, A-Z, 0-9` *(60-100 characters string)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Personal_Access_Token** | <u>ID</u> | `0-9` *(Positive Integer)* |
| | Tokenable_Type | `a-z, A-Z, \` *(e.g. 'App\Models\User')* |
| | *Tokenable_ID* | `0-9, a-z, A-Z, -` *(FK -> User.User_ID)* |
| | Name | `a-z, A-Z, 0-9, spaces, -` *(Device client tag)* |
| | Token | `0-9, a-f` *(64-character SHA-256 hash)* |
| | Abilities | `a-z, A-Z, 0-9, *, [, ], ", ,` *(JSON array)* |
| | Last_Used_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Expires_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Password_Reset_Token** | <u>Email</u> | `a-z, A-Z, 0-9, @, ., _, -` |
| | Token | `0-9` *(6-digit numeric OTP e.g. 849201)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Soil** | <u>Soil_ID</u> | `0-9` *(Positive Integer)* |
| | Soil_Name | `a-z, A-Z, 0-9, spaces` *(e.g. 'Lal-lo Clay Loam')* |
| | Description | `a-z, A-Z, 0-9, Symbols, spaces` *(Text)* |
| | Hue | `0-9, ., a-z, A-Z` *(e.g. '10YR', '7.5YR', '5Y')* |
| | Full_Code | `0-9, ., /, a-z, A-Z, spaces` *(e.g. '10YR 4/4')* |
| | Soil_Type | `a-z, A-Z, spaces` *(e.g. 'Clay Loam', 'Sandy Loam')* |
| | Organic_Matter | `a-z, A-Z, 0-9, %, spaces` *(e.g. 'Medium Humus (2.5%)')* |
| | Hex_Color | `#, 0-9, a-f, A-F` *(7 characters e.g. '#9A7B4F')* |
| | Status | `'ACTIVE', 'ARCHIVED'` |
| | Scan_Count | `0-9` *(Integer >= 0)* |
| | *Color_ID* | `0-9` *(FK -> Munsell_Color.Munsell_Color_ID)* |
| | *Admin_ID* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID)* |
| | *Last_Admin_ID* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Soil_Scan** | <u>Soil_Scan_ID</u> | `0-9` *(Positive Integer)* |
| | Scan_Image | `a-z, A-Z, 0-9, /, ., _, -` *(Storage file path)* |
| | Scan_Date | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Platform | `'web', 'android'` |
| | Scan_Note | `a-z, A-Z, 0-9, Symbols, spaces` *(Text)* |
| | Regions_Data | `a-z, A-Z, 0-9, Symbols` *(Valid JSON structure)* |
| | *User_ID* | `0-9, a-z, A-Z, -` *(FK -> User.User_ID or NULL)* |
| | *Soil_ID* | `0-9` *(FK -> Soil.Soil_ID)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Munsell_Color** | <u>Munsell_Color_ID</u>| `0-9` *(Positive Integer, 1 to 207)* |
| | Hue | `'10R', '2.5YR', '5YR', '7.5YR', '10YR', '2.5Y', '5Y', 'GLEY'` |
| | Value | `2.00 to 8.00` *(Decimal 4,2)* |
| | Chroma | `1.00 to 8.00` *(Decimal 4,2)* |
| | Full_Code | `0-9, ., /, a-z, A-Z, spaces` *(e.g. '10YR 5/6')* |
| | Soil_Organic_Index | `'Low Humus', 'Medium Humus', 'High Humus'` |
| | Soil_Type | `'Clay', 'Loam', 'Sandy Loam', 'Silt Loam', 'Heavy Clay'` |
| | Color_Name | `a-z, A-Z, spaces` *(e.g. 'Yellowish Brown')* |
| | Hex_Code | `#, 0-9, a-f, A-F` *(7 characters e.g. '#A87948')* |
| | Image_Path | `a-z, A-Z, 0-9, /, ., _, -` *(File path)* |
| | Description | `a-z, A-Z, 0-9, Symbols, spaces` *(Text)* |
| | Status | `'ACTIVE', 'INACTIVE'` |
| | *Admin_ID* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID)* |
| | *Last_Admin_ID* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Location_Mapping** | <u>Location_Mapping_ID</u>| `0-9` *(Positive Integer)* |
| | Latitude | `4.50000000 to 21.50000000` *(Decimal 10,8 N)* |
| | Longitude | `116.00000000 to 127.00000000` *(Decimal 11,8 E)* |
| | Barangay | `a-z, A-Z, spaces, (), -` *(Lal-lo Barangay Name)* |
| | Timestamp | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Status | `'ACTIVE', 'PENDING', 'FLAGGED', 'HIDDEN'` |
| | *Soil_Scan_ID* | `0-9` *(FK -> Soil_Scan.Soil_Scan_ID)* |
| | *Soil_ID* | `0-9` *(FK -> Soil.Soil_ID)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Application** | <u>Application_ID</u> | `0-9` *(Positive Integer)* |
| | Application_Name | `a-z, A-Z, 0-9, spaces` *(e.g. 'AgriCo Mobile App')* |
| | Version | `v0.0.0 to v99.99.99` *(e.g. 'v1.0.4')* |
| | File_Path | `a-z, A-Z, 0-9, :, /, ., _, -, ?` *(URL / Path)* |
| | Filename | `a-z, A-Z, 0-9, ., -, _` *(e.g. 'agrico-app-v1.0.4.apk')* |
| | File_Size | `0-9, ., a-z, A-Z, spaces` *(e.g. '59.5 MB')* |
| | Release_Notes | `a-z, A-Z, 0-9, Symbols, spaces` *(Markdown Text)* |
| | Status | `'ACTIVE', 'ARCHIVED'` |
| | Is_Maintenance | `0, 1` *(Boolean)* |
| | *Uploaded_By* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Download** | <u>Download_ID</u> | `0-9` *(Positive Integer)* |
| | *User_ID* | `0-9, a-z, A-Z, -` *(FK -> User.User_ID or NULL)* |
| | *Application_ID* | `0-9` *(FK -> Application.Application_ID)* |
| | Download_Type | `'direct_apk', 'in_app_update', 'cdn_redirect', 'qr_code'` |
| | Download_Date | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Application_Actor** | <u>Application_Actor_ID</u>| `0-9` *(Positive Integer)* |
| | *Admin_ID* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID)* |
| | *Application_ID* | `0-9` *(FK -> Application.Application_ID)* |
| | Role | `'publisher', 'release_manager', 'auditor'` |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **User_Request** | <u>User_Request_ID</u> | `0-9` *(Positive Integer)* |
| | Request_Code | `REQ-00000 to REQ-99999` *(Unique string)* |
| | User_Name | `a-z, A-Z, spaces` |
| | User_Email | `a-z, A-Z, 0-9, @, ., _, -` |
| | Type | `'suspension_appeal', 'student_verification'` |
| | Description | `a-z, A-Z, 0-9, Symbols, spaces` *(Text)* |
| | Status | `'Pending', 'Approved', 'Rejected'` |
| | *Admin_ID* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID or NULL)* |
| | Submitted_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **FAQ** | <u>FAQ_ID</u> | `0-9` *(Positive Integer)* |
| | Question | `a-z, A-Z, 0-9, ?, spaces` *(Text)* |
| | Category | `'scanning', 'munsell', 'sync', 'updates', 'fertilizer'` |
| | Answer | `a-z, A-Z, 0-9, Symbols, spaces` *(Markdown Text)* |
| | Status | `'ACTIVE', 'DRAFT', 'ARCHIVED'` |
| | *Admin_ID* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID)* |
| | *Last_Admin_ID* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **FAQ_Interaction** | <u>FAQ_Interaction_ID</u> | `0-9` *(Positive Integer)* |
| | Action_Type | `'view', 'helpful', 'unhelpful', 'search'` |
| | *User_ID* | `0-9, a-z, A-Z, -` *(FK -> User.User_ID or NULL)* |
| | *Admin_ID* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID or NULL)* |
| | *FAQ_ID* | `0-9` *(FK -> FAQ.FAQ_ID)* |
| | Timestamp | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **System_Actor** | <u>System_Actor_ID</u> | `0-9` *(Positive Integer)* |
| | Actor_Name | `a-z, A-Z, 0-9, _, spaces` |
| | Actor_Type | `'USER', 'ADMIN', 'SYSTEM_SERVICE'` |
| | Description | `a-z, A-Z, 0-9, Symbols, spaces` *(Text)* |
| | Status | `'LOGGED', 'AUDITED'` |
| | *Soil_ID* | `0-9` *(FK -> Soil.Soil_ID or NULL)* |
| | *User_ID* | `0-9, a-z, A-Z, -` *(FK -> User.User_ID or NULL)* |
| | Map_ID | `0-9` *(Integer or NULL)* |
| | *Admin_ID* | `0-9, a-z, A-Z, -` *(FK -> Admin.Admin_ID or NULL)* |
| | Role | `a-z, A-Z, _` *(Role at action execution)* |
| | Action | `a-z, A-Z, _` *(e.g. 'APPROVE_SCAN', 'SUSPEND_USER')* |
| | Created_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |
| | Updated_At | `YYYY-MM-DD HH:MM:SS` *(Datetime)* |

---

## 2. Mobile On-Device Local Entities, Attributes & Domains

These entities reside on the physical Android mobile device (`rn-cli-app`) within encrypted storage or the local file system sandbox to power offline scanning and background synchronization:

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Local_Scan_Record** | <u>Scan_ID</u> | `0-9, a-f, -` *(UUIDv4 36-characters string)* |
| *(local_scan_history.json)* | *Server_ID* | `0-9` *(Integer or NULL if un-synced)* |
| | Image_URI | `file://, a-z, A-Z, 0-9, /, ., _` *(Device file path)* |
| | Munsell_Code | `0-9, ., /, a-z, A-Z, spaces` *(e.g. '10YR 4/3')* |
| | Color_Name | `a-z, A-Z, spaces` *(e.g. 'Dark Brown')* |
| | Hex_Color | `#, 0-9, a-f, A-F` *(e.g. '#594331')* |
| | Soil_Type | `a-z, A-Z, spaces` *(e.g. 'Clay Loam')* |
| | Organic_Index | `0.00 to 100.00` *(Decimal %)* |
| | Delta_E | `0.00 to 99.99` *(Color difference metric)* |
| | Crop_Recommendations | `a-z, A-Z, 0-9, %, Symbols` *(JSON array of crops & scores)* |
| | Fertilizer_Dosage | `0-9, a-z, A-Z, Symbols` *(JSON of bags & kg)* |
| | Latitude | `-90.000000 to +90.000000` *(Decimal)* |
| | Longitude | `-180.000000 to +180.000000` *(Decimal)* |
| | Barangay | `a-z, A-Z, spaces, (), -` |
| | Sync_State | `'queued', 'synced', 'failed'` |
| | Timestamp | `YYYY-MM-DDTHH:MM:SSZ` *(ISO 8601 String)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Offline_Sync_Queue** | <u>Queue_Item_ID</u> | `0-9, a-f, -` *(UUIDv4 string)* |
| *(agrico_offline_scans_queue)* | Image_Path | `file://, a-z, A-Z, 0-9, /, ., _` *(Detached JPEG path)* |
| | Latitude | `-90.000000 to +90.000000` *(Decimal)* |
| | Longitude | `-180.000000 to +180.000000` *(Decimal)* |
| | Location_Name | `a-z, A-Z, spaces, (), -` |
| | Attempt_Count | `0 to 5` *(Integer counter)* |
| | Last_Error | `a-z, A-Z, 0-9, Symbols, spaces` *(Error text or NULL)* |
| | Queued_At | `YYYY-MM-DDTHH:MM:SSZ` *(ISO 8601 String)* |

<br>

| ENTITY | ATTRIBUTES | DOMAIN |
| :--- | :--- | :--- |
| **Auth_Credential_Session** | <u>Service</u> | `'agrico_auth_session'` *(Constant identifier)* |
| *(Android Keystore / Keychain)*| Sanctum_Token | `0-9, a-zA-Z, \|` *(e.g. '14\|N28e3b...')* |
| | User_ID | `0-9, a-z, A-Z, -` *(e.g. 'USR-0482')* |
| | Email | `a-z, A-Z, 0-9, @, ., _, -` |
| | Role | `'farmer', 'student'` |

---
