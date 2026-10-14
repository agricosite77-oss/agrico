# 🌾 AgriCo System Flowcharts: Website & Mobile Application

Comprehensive architectural, operational, and user journey flowcharts for the **AgriCo Soil Analysis and Crop Recommendation System**, covering both the **Web Portal (Laravel 11 / Livewire 3)** and the **Mobile Application (React Native / On-Device ML)**.

---

## 📑 Table of Contents
1. [Master Ecosystem Architecture](#1-master-ecosystem-architecture)
2. [Website Flowcharts](#2-website-flowcharts)
   - [2.1 Website User Journey & Public Navigation](#21-website-user-journey--public-navigation)
   - [2.2 Web Soil Scanning & 6-Stage ML Analysis Pipeline](#22-web-soil-scanning--6-stage-ml-analysis-pipeline)
   - [2.3 Website Authentication & Password Reset Flow](#23-website-authentication--password-reset-flow)
   - [2.4 Admin & Super Admin Management Flow (Livewire 3)](#24-admin--super-admin-management-flow-livewire-3)
3. [Mobile Application Flowcharts](#3-mobile-application-flowcharts)
   - [3.1 Cold-Start, Auth Verification & In-App Auto-Update Flow](#31-cold-start-auth-verification--in-app-auto-update-flow)
   - [3.2 Mobile Authentication & Guest Onboarding](#32-mobile-authentication--guest-onboarding)
   - [3.3 On-Device 6-Stage Soil Scanning & Offline ML Pipeline](#33-on-device-6-stage-soil-scanning--offline-ml-pipeline)
   - [3.4 Offline Scan Queue & Background Cloud Sync](#34-offline-scan-queue--background-cloud-sync)
   - [3.5 Mobile Interactive GIS Map & GPS Pinning](#35-mobile-interactive-gis-map--gps-pinning)
4. [Cross-Platform End-to-End Sequence Diagram](#4-cross-platform-end-to-end-sequence-diagram)

---

## 1. Master Ecosystem Architecture

This diagram illustrates how all clients, backend services, cloud databases, and distribution pipelines connect.

```mermaid
flowchart TB
    subgraph Clients ["📱 Client Ecosystem"]
        direction TB
        WebApp["🌐 Web Browser (Desktop / Mobile)\n• Blade Templates\n• Livewire 3 SPA Components\n• Canvas Image Processing\n• Leaflet GIS Map"]
        MobileApp["📱 React Native Mobile App\n• Android Native (v1.0.4)\n• On-Device 6-Stage ML Engine\n• Offline Munsell 207-Color Database\n• AsyncStorage / SQLite Sync Queue\n• In-App APK Auto-Updater"]
    end

    subgraph CDN ["☁️ Storage & Releases CDN"]
        GitHubRelease["📦 GitHub Releases (agrico)\n• Tag v1.0.4\n• Binary: agrico-app-v1.0.4.apk\n• SHA-256 Verified"]
        AzureBlob["⚡ Microsoft Azure Blob Storage CDN\n(release-assets.githubusercontent.com)\n• Fast CDN Direct Stream (HTTP 200)"]
        GitHubRelease --> AzureBlob
    end

    subgraph BackendGateway ["🚀 Backend Infrastructure (Laravel 11)"]
        direction TB
        HttpGateway["🌐 HTTP / HTTPS Reverse Proxy\n(Railway / Host Server)"]
        
        subgraph WebControllers ["Web & Admin Layer"]
            WebCtrl["WebsiteController\n• /home, /scan, /map, /munsell\n• /download (Direct CDN Redirect)"]
            AdminLivewire["Livewire 3 Admin Suite\n• Dashboard & Analytics\n• Soil Scan Moderation\n• Munsell Color CRUD\n• Map Pin Management\n• UploadApplication Release Hub"]
        end

        subgraph MobileAPIs ["REST API Gateway (/api)"]
            AuthAPI["AuthController\n• /api/login, /api/register\n• /api/forgot-password/*"]
            AppVersionAPI["AppVersionController\n• /api/app-version\n(Dynamic CDN Resolution)"]
            SoilScanAPI["SoilScanController\n• /api/soil-scan (Sync Ingestion)\n• /api/soil-scans (History)"]
            GeoAPI["LocationController & SoilController\n• /api/locations, /api/soils\n• /api/munsell-colors"]
        end
    end

    subgraph DataStorage ["💾 Database & Asset Persistence"]
        MySQL[("🗄️ MySQL Database\n• users, admins\n• soil_scans, soils\n• munsell_colors\n• locations, pins\n• applications, downloads")]
        LocalStorage["📁 Public Storage Disk\n• /storage/scans/ (Images)\n• /storage/applications/ (Local APKs)"]
    end

    WebApp -->|HTTPS / Sessions| HttpGateway
    MobileApp -->|REST API / Sanctum Token| HttpGateway
    MobileApp -.->|1-Tap Direct Download| AzureBlob

    HttpGateway --> WebCtrl
    HttpGateway --> AdminLivewire
    HttpGateway --> AuthAPI
    HttpGateway --> AppVersionAPI
    HttpGateway --> SoilScanAPI
    HttpGateway --> GeoAPI

    WebCtrl --> MySQL
    WebCtrl --> LocalStorage
    AdminLivewire --> MySQL
    AdminLivewire --> LocalStorage
    SoilScanAPI --> MySQL
    SoilScanAPI --> LocalStorage
    GeoAPI --> MySQL
    AppVersionAPI --> GitHubRelease
```

---

## 2. Website Flowcharts

### 2.1 Website User Journey & Public Navigation

The high-level user navigation flow through the public website portal.

```mermaid
flowchart TD
    Start([User visits Website /]) --> Splash[Splash / Landing Screen]
    
    Splash --> NavChoice{User Action}
    
    NavChoice -->|Explore| Home[Home Dashboard /home]
    NavChoice -->|Direct Scan| ScanPage[Soil Scanning Page /scan]
    NavChoice -->|View GIS Map| MapPage[Interactive Soil Map /map]
    NavChoice -->|Browse Colors| MunsellPage[Munsell Color Chart /munsell]
    NavChoice -->|Download App| DownloadPage[Download APK /download]
    NavChoice -->|Help & FAQ| FAQPage[FAQ Page /faq]
    NavChoice -->|Sign In / Register| AuthPage[Login & Register /login]

    Home --> ScanCTA[Click 'Start Soil Analysis'] --> ScanPage
    Home --> MapCTA[Click 'Explore Soil Map'] --> MapPage
    Home --> DownloadCTA[Click 'Get Android App'] --> DownloadPage

    DownloadPage --> StreamAPK[WebsiteController::downloadApp\nRedirects to verified GitHub CDN] --> APKDownload([Download agrico-app-v1.0.4.apk])

    AuthPage --> AuthSuccess{Authenticated?}
    AuthSuccess -->|Farmer / Public User| ProfilePage[User Profile /profile\n• Scan History\n• Account Settings]
    AuthSuccess -->|Admin / Super Admin| AdminRedirect[Redirect to /dashboard]
```

---

### 2.2 Web Soil Scanning & 6-Stage ML Analysis Pipeline

Detailed flowchart showing how a soil image is captured or uploaded on the web, analyzed through computer vision, matched with Munsell charts, and converted into fertilizer recommendations.

```mermaid
flowchart TD
    StartScan([User opens /scan]) --> InputMethod{Input Source}
    
    InputMethod -->|Webcam / Phone Camera| LiveCam[Activate Camera Stream\nnavigator.mediaDevices.getUserMedia]
    InputMethod -->|Upload Image| UploadFile[Upload Soil Image file\nJPEG / PNG]

    LiveCam --> CaptureFrame[Align Soil Sample in Reticle & Capture]
    UploadFile --> LoadCanvas[Load Image into HTML5 Canvas]
    CaptureFrame --> LoadCanvas

    subgraph CVPipeline ["🔬 Computer Vision & Color Normalization"]
        LoadCanvas --> CropRegion[Extract Center 50% ROI\nEliminate Background Clutter]
        CropRegion --> ColorNorm[White-Balance & Lighting Normalization\nStandardize RGB to D65 Illuminant]
        ColorNorm --> ExtractRGB[Extract Mean RGB & HSV Values]
        ExtractRGB --> ConvertLab[Convert RGB to CIELAB Color Space\nL*, a*, b*]
    end

    subgraph MLEngine ["🧠 6-Stage Machine Learning Determination"]
        ConvertLab --> Stage1[Stage 1: Soil Organic Matter & Moisture Index\nDerived from L* and Chroma]
        Stage1 --> Stage2[Stage 2: Munsell Nearest Neighbor Search\nCalculate Delta-E against 207 Color Chips]
        Stage2 --> Stage3[Stage 3: Texture & Soil Classification\nClay, Loam, Sandy Loam, Silt Loam]
        Stage3 --> Stage4[Stage 4: Nutrient Assessment\nEstimated N-P-K Ranges & pH Value]
        Stage4 --> Stage5[Stage 5: Crop Suitability Matrix\nScore Corn, Rice, Vegetables, Fruit Trees]
        Stage5 --> Stage6[Stage 6: Fertilizer Formulation\nRecommend 14-14-14, Urea, Organic Compost]
    end

    Stage6 --> DisplayResults[Display Comprehensive Result Card\n• Munsell Chip & Soil Name\n• Color Code & Sample ROI\n• Suitable Crops with Match %\n• NPK & Fertilizer Calculator]

    DisplayResults --> PinAction{Pin Soil Location to Map?}
    PinAction -->|Yes| FetchGPS[Get Geolocation Coordinates\nnavigator.geolocation]
    FetchGPS --> SubmitPin[Submit to /scan/pin\nStore in 'locations' table]
    SubmitPin --> PinnedSuccess[Pin added to Public Map]

    PinAction -->|No / Skip| SaveScan[Save Scan Record to /scan/store\nPersist in 'soil_scans' table]
    PinnedSuccess --> SaveScan
    SaveScan --> EndScan([Scan Completed & Viewable in History])
```

---

### 2.3 Website Authentication & Password Reset Flow

User registration, real-time validations, secure login, and 6-digit OTP code recovery.

```mermaid
flowchart TD
    StartAuth([User navigates to Auth]) --> AuthChoice{Action}
    
    %% Registration Flow
    AuthChoice -->|Register| RegForm[Registration Form /register]
    RegForm --> EmailType[Enter Email Address]
    EmailType --> LiveEmailCheck[AJAX /auth/check-email\nDebounced check against DB]
    LiveEmailCheck --> EmailValid{Email Available?}
    EmailValid -->|No| ShowEmailTaken[Display: 'Email is already registered'] --> RegForm
    EmailValid -->|Yes| FillDetails[Complete Name, Student/Farmer ID, Password]
    FillDetails --> SubmitReg[Submit Form POST /register]
    SubmitReg --> SaveUser[Hash Password & Create User Record]
    SaveUser --> AutoLogin[Auto-login & redirect to /home]

    %% Login Flow
    AuthChoice -->|Login| LoginForm[Login Form /login]
    LoginForm --> EnterCreds[Enter Email & Password]
    EnterCreds --> SubmitLogin[POST /login]
    SubmitLogin --> CheckCreds{Credentials Valid?}
    CheckCreds -->|No| LoginError[Show 'Invalid credentials' error] --> LoginForm
    CheckCreds -->|Yes| CheckStatus{Account Status}
    
    CheckStatus -->|Suspended| SuspendedScreen[Show Account Suspended Notice]
    SuspendedScreen --> ReactivateBtn[Click 'Request Reactivation']
    ReactivateBtn --> SubmitReactivate[POST /account/request-reactivation]
    SubmitReactivate --> PendingReview([Reactivation Request Sent to Admin])

    CheckStatus -->|Active & Admin/SuperAdmin| AdminHome[Redirect to /dashboard]
    CheckStatus -->|Active & Standard User| WebHome[Redirect to /home]

    %% Password Reset Flow
    AuthChoice -->|Forgot Password| ForgotForm[Forgot Password /forgot-password]
    ForgotForm --> EnterResetEmail[Enter Email Address]
    EnterResetEmail --> SendCode[POST /forgot-password/send-code]
    SendCode --> GenOTP[Generate 6-Digit Verification Code\nExpire in 15 mins & Email User]
    GenOTP --> VerifyCodeForm[Enter 6-Digit Code /forgot-password/verify-code]
    VerifyCodeForm --> ValidateOTP{Code Matches & Not Expired?}
    ValidateOTP -->|No| OTPError[Show 'Invalid or expired code'] --> VerifyCodeForm
    ValidateOTP -->|Yes| NewPassForm[Enter New Password /forgot-password/reset]
    NewPassForm --> UpdatePass[POST /forgot-password/reset\nUpdate Bcrypt Hash in DB]
    UpdatePass --> ResetSuccess[Show Success Alert & Redirect to Login]
```

---

### 2.4 Admin & Super Admin Management Flow (Livewire 3)

The internal management pipeline powered by Laravel Livewire 3 real-time components.

```mermaid
flowchart TD
    AdminLogin([Admin authenticated at /dashboard]) --> RoleGate{Check User Role}

    RoleGate -->|Admin| StandardAdmin[Admin Capabilities]
    RoleGate -->|Super Admin| SuperAdmin[Super Admin Full Capabilities]

    StandardAdmin --> Modules
    SuperAdmin --> Modules
    SuperAdmin --> SuperExclusive[Super Admin Exclusive Modules]

    subgraph Modules ["📊 Livewire 3 Administrative Modules"]
        direction TB
        Dashboard["AdminDashboard /dashboard\n• Live Scan Velocity Charts\n• Recent Scans Table\n• Crop Recommendation Stats\n• Active User Metrics"]
        SoilHistory["SoilScanHistory /soil\n• View Raw & Processed Soil Images\n• Soil Category & Munsell Chip\n• Approve / Flag Scan Records"]
        MunsellCRUD["ManageMunsellColor /munsell-color\n• Add / Edit 207 Munsell Colors\n• HEX, Hue, Chroma, Value\n• Real Soil Image Samples"]
        MapModeration["ManageMap & ManageMappingHistory\n• Review Geotagged Soil Pins\n• Fast AJAX Toggle Pin Status\n• Filter by Soil Type & Region"]
        UserMgmt["ManageUser & StudentRecord\n• View Farmer & Student Accounts\n• 1-Day Suspension Action\n• Review User Scans"]
        RequestMgmt["ManageRequest /user-requests\n• Suspended Account Reactivation Requests\n• Fast AJAX Approve / Disapprove"]
    end

    subgraph SuperExclusive ["👑 Super Admin Exclusive"]
        UploadApp["UploadApplication /upload-application\n• Release New Mobile Version\n• Upload APK to Server Storage\n• Set Version Number (e.g. v1.0.4)\n• Link Official GitHub Release URL\n• Toggle System Maintenance Mode\n• Broadcast Update to Mobile Clients"]
    end

    UploadApp --> UpdateAppVersionDB[Update 'applications' table\nActive version: v1.0.4]
    UpdateAppVersionDB --> MobileNotified([Mobile App checks /api/app-version\nTriggers Update Modal for v1.0.3 clients])
```

---

## 3. Mobile Application Flowcharts

### 3.1 Cold-Start, Auth Verification & In-App Auto-Update Flow

The launch sequence of the React Native mobile application, featuring automatic keychain credential verification and in-app APK auto-updating.

```mermaid
flowchart TD
    StartApp([User Launches AgriCo Mobile App]) --> SplashScreen[SplashScreen.js\nInitialize App State]
    
    SplashScreen --> NetCheck{Device Online?}
    NetCheck -->|Offline| OfflineMode[Set Offline Flag in Context] --> TokenProbe
    NetCheck -->|Online| OnlineMode[Set Online Flag in Context] --> CheckUpdate[Call updateService::checkForAppUpdate\nGET /api/app-version]

    subgraph AutoUpdate ["🚀 In-App Auto-Update Engine"]
        CheckUpdate --> VersionCompare{Server Version > Current App Version?\ne.g. v1.0.4 > v1.0.3}
        VersionCompare -->|No - Up to Date| TokenProbe
        VersionCompare -->|Yes - New Version| ShowModal[Present UpdateModal.js\n• Version v1.0.3 ➔ v1.0.4\n• Release Notes & File Size: 59.5 MB]
        
        ShowModal --> UpdateChoice{User Action}
        UpdateChoice -->|Later| DismissModal[Close Modal] --> TokenProbe
        UpdateChoice -->|Or download in browser| OpenChrome[Linking.openURL\nOpens verified GitHub CDN]
        UpdateChoice -->|Update Now| StartDirectDownload[Call downloadAndInstallApk]
        
        StartDirectDownload --> RNFSDownload[RNFS.downloadFile\nStream directly from Azure Blob CDN\nHTTP 200 - 62.3 MB]
        RNFSDownload --> ProgressUpdate[Update Progress Bar: 0% ➔ 100%]
        ProgressUpdate --> DownloadComplete{Download Status == 200?}
        
        DownloadComplete -->|Success| NativeInstall[Invoke NativeModules.InstallApk.install\nTarget: /cache/agrico-update.apk]
        NativeInstall --> AndroidInstaller([Launch Android System Package Installer\n'Update this app?'])
        
        DownloadComplete -->|Error / Fallback| FallbackBrowser[Fallback: Linking.openURL(directUrl)\nDownload in Browser]
    end

    subgraph AuthSession ["🔐 Keychain Credential Probe"]
        TokenProbe[Keychain.getGenericPassword\nService: agrico_user_token] --> HasToken{Token Found?}
        HasToken -->|Yes| ValidateToken[Call GET /api/user with Bearer Token]
        ValidateToken --> TokenValid{Token Valid?}
        TokenValid -->|Yes| SetUserContext[Set Authenticated User in AuthContext] --> HomeScreen[Navigate to HomeScreen.js]
        TokenValid -->|No / 401| ResetKeychain[Reset Keychain Token] --> SignInScreen[Navigate to SignInScreen.js]
        HasToken -->|No| SignInScreen
    end
```

---

### 3.2 Mobile Authentication & Guest Onboarding

The mobile authentication lifecycle with guest access for offline farm scanning.

```mermaid
flowchart TD
    StartAuth([User on SignInScreen]) --> Choice{Action}

    Choice -->|Sign In| EnterCreds[Enter Email & Password]
    EnterCreds --> SubmitLogin[POST /api/auth/login]
    SubmitLogin --> LoginResult{Success?}
    LoginResult -->|No| ShowLoginErr[Display Error Message] --> EnterCreds
    LoginResult -->|Yes| SaveToken[Store Token in react-native-keychain] --> EnterHome[Navigate to HomeScreen]

    Choice -->|Continue as Guest| GuestMode[Activate Guest Session\nOffline-ready local scanning] --> EnterHome

    Choice -->|Sign Up| SignUpScreen[Navigate to SignUpScreen]
    SignUpScreen --> TypeEmail[Enter Email]
    TypeEmail --> RealtimeEmailCheck[GET /api/auth/check-email]
    RealtimeEmailCheck --> EmailAvailable{Available?}
    EmailAvailable -->|No| EmailError[Show: 'Email already registered'] --> SignUpScreen
    EmailAvailable -->|Yes| FillProfile[Enter Name, Role, Password]
    FillProfile --> SubmitSignUp[POST /api/auth/register]
    SubmitSignUp --> RegResult{Success?}
    RegResult -->|Yes| SaveNewToken[Save Token to Keychain] --> EnterHome
    RegResult -->|No| ShowRegErr[Display Validation Error] --> SignUpScreen

    Choice -->|Forgot Password| ForgotScreen[Navigate to ForgotPasswordScreen]
    ForgotScreen --> SendOTP[POST /api/forgot-password/send-code]
    SendOTP --> VerifyScreen[Navigate to VerifyCodeScreen\nEnter 6-Digit OTP]
    VerifyScreen --> CheckOTP[POST /api/forgot-password/verify-code]
    CheckOTP --> OTPMatch{Valid OTP?}
    OTPMatch -->|Yes| ResetScreen[Navigate to ResetPasswordScreen]
    ResetScreen --> SubmitNewPass[POST /api/forgot-password/reset]
    SubmitNewPass --> PasswordUpdated[Password Reset Successfully\nRedirect to Sign In]
    OTPMatch -->|No| ShowOTPErr[Show 'Invalid or expired OTP'] --> VerifyScreen
```

---

### 3.3 On-Device 6-Stage Soil Scanning & Offline ML Pipeline

The heart of the mobile application: 100% on-device soil classification that operates even without internet access in remote agricultural fields.

```mermaid
flowchart TD
    OpenScanner([User taps 'Scan Soil' Tab]) --> CameraMount[Mount Camera Viewfinder\nreact-native-vision-camera / RNCamera]
    
    CameraMount --> AlignReticle[Display Target Reticle & Alignment Guide]
    AlignReticle --> AutoZoom[Auto-Zoom 1.5x on Central Soil Target]
    
    AlignReticle --> TriggerScan[User presses Capture Button]
    TriggerScan --> CapturePhoto[Capture Full-Res Soil Frame to Local Cache]

    subgraph OnDeviceML ["🧠 On-Device 6-Stage Machine Learning Pipeline"]
        CapturePhoto --> Step1[1. Pre-Processing & ROI Cropping\nExtract 300x300 center patch to eliminate background]
        Step1 --> Step2[2. Lighting & Color Normalization\nApply Gray-World algorithm to correct sunlight/shadows]
        Step2 --> Step3[3. Color Space Transformation\nExtract RGB ➔ Convert to CIELAB L*, a*, b*]
        
        Step3 --> Step4[4. Offline Munsell 207-Color Matching\nRun Delta-E (CIE76 / CMC) nearest neighbor against\nlocal munsellColors.json database]
        
        Step4 --> Step5[5. Soil Texture & Type Classification\nMap Munsell Hue/Value/Chroma to Soil Type:\nClay, Loam, Sandy Loam, Clay Loam, etc.]
        
        Step5 --> Step6[6. Agronomic Scoring & Fertilizer Engine\n• Estimate N-P-K nutrient status\n• Compute crop suitability index (% match)\n• Generate optimal fertilizer dosage]
    end

    Step6 --> DisplayMobileResult[Present Soil Analysis Result Sheet\n• Matched Munsell Code & Chip\n• Soil Classification & Soil Name\n• Ranked Suitable Crops\n• N-P-K Estimates & Recommended Fertilizer]

    DisplayMobileResult --> SaveDecision{Persist Scan?}
    SaveDecision -->|Yes| SaveToLocalQueue[Save to Local Offline Scan Queue\nStatus: 'QUEUED']
    SaveToLocalQueue --> CheckNetwork{Is Device Online?}
    
    CheckNetwork -->|Online| InstantSync[Sync immediately via /api/soil-scan] --> MarkedSynced[Mark Status: 'SYNCED']
    CheckNetwork -->|Offline| RemainQueued[Remain in Queue for Background Sync]

    MarkedSynced --> FinishedScan([Scan Complete & Stored in History])
    RemainQueued --> FinishedScan
```

---

### 3.4 Offline Scan Queue & Background Cloud Sync

How offline scans taken in remote fields are reliably buffered and synchronized once internet connection is restored.

```mermaid
flowchart TD
    ScanGenerated([New Soil Scan Created]) --> SaveLocal[Save Scan Object to AsyncStorage / SQLite\nStatus: 'QUEUED']
    SaveLocal --> UpdateHistoryList[Scan appears in History with ⚡ 'Queued' Badge]

    subgraph SyncEngine ["🔄 AgriCo Background Sync Worker"]
        direction TB
        ListenNet[NetInfo Network Listener\nMonitor WiFi / Cellular Connection]
        
        ListenNet --> NetChange{Network Available?}
        NetChange -->|No Internet| Idle[Wait in Idle State] --> ListenNet
        NetChange -->|Internet Restored| TriggerSync[Trigger Sync Worker]
        
        TriggerSync --> ReadQueue[Query Local Storage for status == 'QUEUED']
        ReadQueue --> HasItems{Items in Queue?}
        HasItems -->|Queue Empty| Idle
        HasItems -->|Found Items| ProcessLoop[Loop through queued scan items]

        ProcessLoop --> PreparePayload[Prepare Multipart Payload\n• Soil Image File\n• Munsell Code & Soil Type\n• GPS Lat/Lng & Timestamp]
        
        PreparePayload --> SendAPI[POST /api/soil-scan\nAttach Sanctum Bearer Token]
        SendAPI --> APIResponse{HTTP 200 / 201?}
        
        APIResponse -->|Success| MarkSynced[Update Local Record Status to 'SYNCED'\nRemove ⚡ Queued Badge]
        APIResponse -->|Server Error / 500| KeepQueued[Keep Queued & Schedule Exponential Backoff]
        
        MarkSynced --> NextItem{More items in queue?}
        NextItem -->|Yes| ProcessLoop
        NextItem -->|No| SyncComplete[Post Notification: 'All scans synced successfully!']
    end

    SyncComplete --> CloudPersisted([All Scans Mirrored in Web Central Database])
```

---

### 3.5 Mobile Interactive GIS Map & GPS Pinning

Interactive map allowing farmers to view regional soil distributions and geotag their field samples.

```mermaid
flowchart TD
    OpenMap([User opens 'Map' Tab in App]) --> RequestGPS[Request Location Permissions\nACCESS_FINE_LOCATION]
    
    RequestGPS --> GPSAllowed{Granted?}
    GPSAllowed -->|Yes| GetCoordinates[Fetch Device GPS Coordinates\nGeolocation.getCurrentPosition]
    GPSAllowed -->|No| UseDefault[Use Default Regional Map Center]

    GetCoordinates --> RenderMap[Render Interactive Map View]
    UseDefault --> RenderMap

    RenderMap --> FetchPins[Fetch Geotagged Pins from /api/locations]
    FetchPins --> DisplayPins[Plot Color-Coded Pins by Soil Category\n• Red: High Iron / Clay\n• Brown: Humic / Organic Loam\n• Yellow: Sandy / Low Organic]

    DisplayPins --> FilterOption{Apply Filter?}
    FilterOption -->|Filter by Soil Type| FilterPins[Filter visible markers on map]
    FilterOption -->|Tap Pin Marker| PinModal[Open Pin Detail Modal\n• Soil Munsell Color\n• Recommended Crops\n• Scanned Date & Location]

    RenderMap --> PinNewSample[User taps 'Pin Current Location']
    PinNewSample --> SelectRecentScan[Select Soil Scan from Recent History]
    SelectRecentScan --> SubmitPinAPI[POST /api/locations/pin\nAttach Lat, Lng, Soil ID]
    SubmitPinAPI --> PinCreated[New Pin appears on Map with 'Pending Review' or 'Active']
```

---

## 4. Cross-Platform End-to-End Sequence Diagram

This sequence diagram depicts the complete lifecycle of a soil scan from the farmer's mobile camera in the field to the agricultural admin's portal and the public GIS web map.

```mermaid
sequenceDiagram
    autonumber
    actor Farmer as 👨‍🌾 Farmer / User
    participant Mobile as 📱 Mobile App (v1.0.4)
    participant LocalDB as 💾 Local Queue (AsyncStorage)
    participant WebAPI as 🚀 Laravel API (/api)
    participant DB as 🗄️ MySQL Database
    participant Admin as 💻 Admin Panel (Livewire 3)
    participant PublicWeb as 🌐 Public Web Map (/map)

    %% Scan Phase
    Farmer->>Mobile: Points camera at soil sample & taps Capture
    Note over Mobile: On-Device 6-Stage ML Engine executes:<br/>1. Crop ROI & Normalization<br/>2. Munsell Delta-E Match<br/>3. Soil Type & NPK Calculation<br/>4. Crop Recommendation
    Mobile->>Farmer: Displays instant results on device screen

    %% Offline Queuing
    Mobile->>LocalDB: Store scan with status: 'QUEUED'
    LocalDB-->>Mobile: Scan persisted locally

    %% Cloud Sync Phase
    Note over Mobile: Internet connection detected (NetInfo)
    Mobile->>WebAPI: POST /api/soil-scan (Multipart: Image + ML Results + GPS)
    WebAPI->>DB: INSERT into soil_scans & locations
    DB-->>WebAPI: Record Created (ID: 1042)
    WebAPI-->>Mobile: HTTP 201 Created
    Mobile->>LocalDB: UPDATE status = 'SYNCED'

    %% Admin Moderation Phase
    Admin->>WebAPI: Visits /soil (SoilScanHistory Livewire)
    WebAPI->>DB: Query pending soil scans
    DB-->>Admin: Displays scan with Before/After soil images & NPK
    Admin->>WebAPI: Click 'Approve & Pin to Public Map'
    WebAPI->>DB: UPDATE locations SET is_active = 1

    %% Public Web GIS Phase
    Farmer->>PublicWeb: Visits /map on desktop browser
    PublicWeb->>WebAPI: GET /locations
    WebAPI->>DB: Query active pins
    DB-->>PublicWeb: Return verified coordinates & soil data
    PublicWeb-->>Farmer: Displays interactive Leaflet pin with crop recommendations
```

---

## 5. Summary of System Highlights

| Feature | Website Portal | Mobile Application (v1.0.4) |
| :--- | :--- | :--- |
| **Technology** | Laravel 11, Livewire 3, Blade, Tailwind CSS | React Native, Vision Camera, RNFS, Vector Icons |
| **Soil ML Execution** | Server-side / HTML5 Canvas Web ML | **100% On-Device Offline Engine** (6 Stages) |
| **Munsell Chart** | 207 Interactive SVG/CSS Color Chips | 207 Offline Local Chips with CIELAB Delta-E |
| **Offline Operation** | Cached assets, online required for scan | **Fully Offline Scanning & History Queue** |
| **Updates** | Continuous Web Deployment | **In-App Auto-Updater** (Direct GitHub CDN Stream) |
| **Mapping Engine** | Leaflet.js with interactive clustering | Mobile Native Map with GPS Pinning |
| **Administration** | Full Livewire 3 Multi-Role Management | User Profile & Scan History Management |
