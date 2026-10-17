<x-website-layout>
    <x-slot name="title">Soil Map</x-slot>
    <x-slot name="hideFooter">true</x-slot>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .ws-footer {
            display: none !important;
        }

        /* Smooth Map Fade-In Transition */
        @keyframes wsMapFadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .ws-single-screen-container {
            animation: wsMapFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            transition: background 0.3s ease;
        }
        .ws-map-card {
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Page Hero */
        .ws-page-hero {
            background: linear-gradient(135deg, #051F20 0%, #0B2B26 100%);
            padding: 16px 24px 14px;
            border-bottom: 1px solid rgba(142, 182, 155, 0.25);
            flex-shrink: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }
        .ws-page-hero-inner {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        .ws-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #8EB69B;
            margin-bottom: 3px;
            font-weight: 500;
        }
        .ws-breadcrumb a {
            color: #DAF1DE;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .ws-breadcrumb a:hover {
            text-decoration: underline;
            color: #8EB69B;
        }
        .ws-page-title {
            font-size: clamp(1.3rem, 2.5vw, 1.75rem);
            font-weight: 900;
            color: #DAF1DE;
            margin: 0 0 2px 0;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .ws-page-sub {
            font-size: 13px;
            color: #8EB69B;
            margin: 0;
            font-weight: 400;
        }

        /* Single-Screen Container */
        .ws-single-screen-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 68px);
            max-height: calc(100vh - 68px);
            overflow: hidden;
            background: #051F20;
            box-sizing: border-box;
        }

        .ws-map-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 12px 24px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            width: 100%;
            box-sizing: border-box;
        }
        /* ── MAP SEARCH CONTROLS (FAQ DESIGN & POSITIONING) ── */
        .map-controls {
            margin-bottom: 14px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
            flex-shrink: 0;
        }
        .map-search-box {
            position: relative;
            width: 100%;
        }
        .map-search-input {
            width: 100%;
            height: 50px;
            background: #0B2B26;
            border: 2px solid rgba(142, 182, 155, 0.28);
            border-radius: 16px;
            padding: 10px 48px 10px 48px;
            font-size: 15px;
            color: #DAF1DE;
            box-sizing: border-box;
            outline: none;
            font-family: inherit;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .map-search-input::placeholder {
            color: #8EB69B;
            opacity: 0.8;
            font-size: 14.5px;
        }
        .map-search-input:hover {
            border-color: rgba(142, 182, 155, 0.5);
        }
        .map-search-input:focus {
            border-color: #22c55e !important;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.25) !important;
        }
        .map-search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            stroke: #8EB69B;
            pointer-events: none;
            z-index: 2;
            transition: stroke 0.2s ease;
        }
        .map-search-input:focus ~ .map-search-icon,
        .map-search-box:focus-within .map-search-icon {
            stroke: #4ADE80 !important;
        }
        .map-search-clear {
            right: 10px !important;
        }

        /* Map Search Results Dropdown */
        .map-search-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            z-index: 1000;
            max-height: 280px;
            overflow-y: auto;
            background: rgba(11, 43, 38, 0.96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(142, 182, 155, 0.35);
            border-radius: 16px;
            padding: 8px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5), 0 0 20px rgba(35, 83, 71, 0.3);
        }
        .map-search-result-item {
            padding: 10px 14px;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            transition: all 0.15s ease;
            margin-bottom: 3px;
        }
        .map-search-result-item:hover {
            background: rgba(35, 83, 71, 0.55);
        }
        .map-search-result-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #DAF1DE;
        }
        .map-search-result-meta {
            font-size: 11.5px;
            color: #8EB69B;
            margin-top: 2px;
        }
        .map-search-result-badge {
            font-size: 11px;
            font-weight: 700;
            color: #ffffff;
            background: #22c55e;
            padding: 3px 10px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(34, 197, 94, 0.35);
        }

        .ws-map-card {
            background: #0B2B26;
            border-radius: 20px;
            border: 1px solid rgba(142, 182, 155, 0.25);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 30px rgba(142, 182, 155, 0.05);
            overflow: hidden;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            position: relative;
        }
        .ws-map-topbar {
            padding: 12px 20px;
            background: #051F20;
            border-bottom: 1px solid rgba(142, 182, 155, 0.18);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            flex-shrink: 0;
        }
        .ws-map-topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .map-title-icon {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: rgba(5, 31, 32, 0.7);
            border: 1.5px solid rgba(142, 182, 155, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .map-title-icon svg {
            stroke: #4ADE80 !important;
            transition: stroke 0.25s ease;
        }
        html.light-mode .map-title-icon {
            background: #f0fdf4 !important;
            border: 1.5px solid #86efac !important;
            box-shadow: 0 2px 6px rgba(34, 197, 94, 0.1) !important;
        }
        html.light-mode .map-title-icon svg {
            stroke: #16a34a !important;
        }
        .ws-map-topbar h2 {
            font-size: 15px;
            font-weight: 800;
            color: #DAF1DE;
            margin: 0;
            letter-spacing: -0.01em;
        }
        .ws-pin-count {
            background: rgba(35, 83, 71, 0.6);
            color: #DAF1DE;
            border: 1px solid rgba(142, 182, 155, 0.35);
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 11px;
            border-radius: 999px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        #soilMap {
            flex: 1;
            width: 100%;
            min-height: 0;
            z-index: 1;
            background: #e2e8f0;
            position: relative !important;
        }

        /* Responsive Mobile Map (Option A: min-height 60vh so popups never cut off) */
        @media (max-width: 768px) {
            .ws-single-screen-container {
                height: auto;
                max-height: none;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            .ws-map-wrapper {
                padding: 10px 12px 16px;
            }
            .ws-map-card {
                min-height: 60vh;
            }
            #soilMap {
                min-height: 60vh !important;
            }
        }

        /* Leaflet popup custom style - Clean White Card (Matching Screenshot) */
        .leaflet-popup-content-wrapper,
        .agrico-custom-popup .leaflet-popup-content-wrapper {
            background: #ffffff !important;
            color: #0f172a !important;
            border-radius: 20px !important;
            padding: 0 !important;
            overflow: hidden !important;
            box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.16), 0 6px 12px -2px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
        }
        .leaflet-popup-tip,
        .agrico-custom-popup .leaflet-popup-tip {
            background: #ffffff !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.06) !important;
        }
        .leaflet-popup-content,
        .agrico-custom-popup .leaflet-popup-content {
            margin: 0 !important;
            line-height: normal !important;
            width: 255px !important;
            max-width: calc(100vw - 64px) !important;
        }
        .leaflet-popup-tip-container,
        .agrico-custom-popup .leaflet-popup-tip-container {
            margin-top: -1px;
        }
        .leaflet-container a.leaflet-popup-close-button,
        .agrico-custom-popup a.leaflet-popup-close-button,
        a.leaflet-popup-close-button,
        .leaflet-popup-close-button {
            display: none !important;
        }

        /* ── LIGHT MODE OVERRIDES FOR SOIL MAP PAGE ── */
        html.light-mode, html.light-mode body {
            background: #F8FAFC !important;
        }

        html.light-mode .ws-single-screen-container {
            background: #F8FAFC !important;
        }

        html.light-mode .ws-page-hero {
            background: linear-gradient(135deg, #DAF1DE 0%, #EEF7F1 100%) !important;
            border-bottom: 1px solid rgba(142, 182, 155, 0.4) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .ws-page-title {
            color: #051F20 !important;
        }

        html.light-mode .ws-page-sub {
            color: #163832 !important;
        }

        html.light-mode .ws-breadcrumb {
            color: #235347 !important;
        }

        html.light-mode .ws-breadcrumb a {
            color: #051F20 !important;
        }

        html.light-mode .ws-breadcrumb-current,
        html.light-mode .ws-breadcrumb span {
            color: #051F20 !important;
            font-weight: 700 !important;
        }

        html.light-mode .ws-map-card {
            background: #FFFFFF !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06) !important;
        }

        html.light-mode .ws-map-topbar {
            background: #FFFFFF !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        html.light-mode .ws-map-topbar h2 {
            color: #0f172a !important;
        }

        html.light-mode .ws-pin-count {
            background: #eefbe7 !important;
            color: #16a34a !important;
            border: 1px solid #86efac !important;
            box-shadow: none !important;
        }

        html.light-mode .ws-map-topbar span {
            color: #64748b !important;
        }

        /* Search Bar in Light Mode (FAQ Design) */
        html.light-mode .map-search-input {
            background: #ffffff !important;
            border: 2px solid #e2e8f0 !important;
            border-radius: 16px !important;
            color: #0f172a !important;
            font-size: 15px !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03) !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }
        html.light-mode .map-search-input::placeholder {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }
        html.light-mode .map-search-icon {
            stroke: #94a3b8 !important;
        }
        html.light-mode .map-search-input:focus,
        html.light-mode .map-search-box:focus-within .map-search-input {
            border-color: #22c55e !important;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15) !important;
            background: #ffffff !important;
        }
        html.light-mode .map-search-box:focus-within .map-search-icon {
            stroke: #16a34a !important;
        }

        html.light-mode .map-search-dropdown {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12) !important;
        }
        html.light-mode .map-search-result-item:hover {
            background: #f0fdf4 !important;
        }
        html.light-mode .map-search-result-title {
            color: #0f172a !important;
        }
        html.light-mode .map-search-result-meta {
            color: #64748b !important;
        }

        /* Leaflet Popups in Light Mode */
        html.light-mode .leaflet-popup-content-wrapper,
        html.light-mode .leaflet-popup-tip {
            background: #FFFFFF !important;
            color: #0f172a !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.1) !important;
        }

        /* Custom Agrico Leaflet Popup Styling */
        .agrico-custom-popup .leaflet-popup-content-wrapper {
            background: #ffffff !important;
            border-radius: 20px !important;
            padding: 0 !important;
            overflow: hidden !important;
            box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.16), 0 6px 12px -2px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
        }
        .agrico-custom-popup .leaflet-popup-content {
            margin: 0 !important;
            line-height: normal !important;
            width: 280px !important;
            max-width: calc(100vw - 64px) !important;
        }
        .agrico-custom-popup .leaflet-popup-tip-container {
            margin-top: -1px;
        }
        .agrico-custom-popup .leaflet-popup-tip {
            background: #ffffff !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.06) !important;
        }
        .agrico-custom-popup a.leaflet-popup-close-button {
            top: 10px !important;
            right: 10px !important;
            width: 28px !important;
            height: 28px !important;
            border-radius: 50% !important;
            background: var(--close-btn-bg, rgba(5, 31, 32, 0.70)) !important;
            color: var(--close-btn-color, #8EB69B) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 16px !important;
            border: var(--close-btn-border-width, 1.5px) solid var(--close-btn-border, rgba(142, 182, 155, 0.35)) !important;
            transition: var(--close-btn-transition, all 0.2s cubic-bezier(0.16, 1, 0.3, 1)) !important;
            padding: 0 !important;
            z-index: 20 !important;
            text-decoration: none !important;
            box-sizing: border-box !important;
        }
        .agrico-custom-popup a.leaflet-popup-close-button:hover {
            background: var(--close-btn-bg-hover, rgba(35, 83, 71, 0.85)) !important;
            color: var(--close-btn-color-hover, #DAF1DE) !important;
            border-color: var(--close-btn-border-hover, #DAF1DE) !important;
            transform: scale(1.06) !important;
        }
        #mapSearchInput {
            color: #DAF1DE;
        }
    </style>

    <div class="ws-single-screen-container">
        {{-- Page Hero --}}
        <div class="ws-page-hero">
            <div class="ws-page-hero-inner ui-reveal">
                <div class="ws-breadcrumb">
                    <a href="{{ route('website.home') }}">Home</a>
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    <span class="ws-breadcrumb-current">Soil Map</span>
                </div>
                <h1 class="ws-page-title">Interactive Soil Map</h1>
                <p class="ws-page-sub">
                    Explore all geo-tagged soil scan locations across the municipality.
                    <a href="{{ route('website.faq') }}#map-pins" target="_blank" style="margin-left: 8px; color: #DAF1DE; text-decoration: underline; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span>How are pins verified? FAQ</span>
                    </a>
                </p>
            </div>
        </div>

        <div class="ws-map-wrapper">
            {{-- Map Search Controls (FAQ Design & Positioning) --}}
            <div class="map-controls ui-reveal">
                <div class="map-search-box">
                    <svg class="map-search-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input
                        type="text"
                        id="mapSearchInput"
                        class="map-search-input"
                        oninput="handleMapSearch(this.value)"
                        onkeydown="if (event.key === 'Escape') clearMapSearch()"
                        placeholder="Search soil name, code, barangay, soil type..."
                        autocomplete="off"
                        aria-label="Search map locations"
                    />
                    <x-search-clear-button
                        id="mapSearchClearBtn"
                        class="map-search-clear"
                        onclick="clearMapSearch()"
                        style="display:none;"
                    />

                    <!-- Dropdown Search Results List -->
                    <div id="mapSearchResults" class="map-search-dropdown" style="display:none;"></div>
                </div>
            </div>

            <div class="ws-map-card ui-reveal">
                <div class="ws-map-topbar">
                    <div class="ws-map-topbar-left">
                        <span class="map-title-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"
                                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </span>
                        <h2>Soil Scan Locations</h2>
                        <span class="ws-pin-count">{{ $mappings->count() }} locations</span>
                    </div>
                </div>

                <div id="soilMap"></div>
            </div>
        </div>
    </div>
    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/lallo-boundary.js') }}"></script>
    <script src="{{ asset('js/agrico-map-focus.js') }}"></script>
    <script src="{{ asset('js/agrico-pin.js') }}"></script>
    <script>
        window.mappingsData = @json($mappings);
        const mappingsData = window.mappingsData;

        // Initialize map — complete Lal-lo, Cagayan Municipality view [18.1972, 121.6612] at zoom 12
        const defaultLat = 18.1972, defaultLng = 121.6612, defaultZoom = 12;

        const map = L.map('soilMap').setView([defaultLat, defaultLng], defaultZoom);

        if (window.AgricoMapFocus) {
            window.AgricoMapFocus.attachMapListeners(map, { resetZoomOnClose: true });
        }

        // Standard bright OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Custom Glossy Red Vector Marker Pins with Rounded White Center
        function makeIcon(gradientStart, gradientEnd, dotColor) {
            return L.divIcon({
                html: `
                    <div style="position:relative; width:34px; height:44px; margin-left:-17px; margin-top:-44px;">
                        <div style="position:absolute; bottom:1px; left:50%; transform:translateX(-50%); width:16px; height:5px; background:rgba(0,0,0,0.35); border-radius:50%; filter:blur(1.5px);"></div>
                        <div style="position:absolute; top:0; left:50%; width:32px; height:32px; background:linear-gradient(135deg, ${gradientStart} 0%, ${gradientEnd} 100%); border-radius:50% 50% 50% 0; transform:translateX(-50%) rotate(-45deg); box-shadow:0 6px 14px rgba(0,0,0,0.4), inset 0 2px 3px rgba(255,255,255,0.6); display:flex; align-items:center; justify-content:center; border:1.5px solid #ffffff;">
                            <div style="width:15px; height:15px; background:#ffffff; border-radius:50%; transform:rotate(45deg); display:flex; align-items:center; justify-content:center; box-shadow:inset 0 1px 2px rgba(0,0,0,0.2);">
                                <div style="width:7px; height:7px; background:${dotColor}; border-radius:50%;"></div>
                            </div>
                        </div>
                    </div>
                `,
                className: '',
                iconSize: [34, 44],
                iconAnchor: [17, 44],
                popupAnchor: [0, -42]
            });
        }

        const redIcon = L.icon({
            iconUrl: "{{ asset('images/agrico-map-pin.png') }}",
            iconSize: [38, 56],
            iconAnchor: [19, 56],
            popupAnchor: [0, -50]
        });

        const grayIcon = L.icon({
            iconUrl: "{{ asset('images/agrico-map-pin-gray.png') }}",
            iconSize: [38, 56],
            iconAnchor: [19, 56],
            popupAnchor: [0, -50]
        });


        function getRoundedBoxCoords(centerLat, centerLng, widthMeters, heightMeters) {
            const latConversion = 111320;
            const lngConversion = 111320 * Math.cos(centerLat * Math.PI / 180);
            const halfW = widthMeters / 2;
            const halfH = heightMeters / 2;

            const north = centerLat + (halfH / latConversion);
            const south = centerLat - (halfH / latConversion);
            const east  = centerLng + (halfW / lngConversion);
            const west  = centerLng - (halfW / lngConversion);

            return [
                [north, west],
                [north, east],
                [south, east],
                [south, west]
            ];
        }

        function getParcelDimensions(covStr) {
            const s = String(covStr || '').toLowerCase();
            if (s.includes('50 m')) return { w: 35, h: 35 };
            if (s.includes('250 m')) return { w: 55, h: 55 };
            if (s.includes('500 m')) return { w: 75, h: 75 };
            if (s.includes('1,000') || s.includes('1000')) return { w: 100, h: 100 };
            if (s.includes('2,500') || s.includes('2500')) return { w: 130, h: 130 };
            if (s.includes('5,000') || s.includes('5000')) return { w: 160, h: 160 };
            if (s.includes('hectare')) return { w: 190, h: 190 };
            return { w: 75, h: 75 };
        }

        function centerMapOnPopup(mapInstance, lat, lng, offsetPx) {
            if (!mapInstance) return;
            offsetPx = typeof offsetPx === 'number' ? offsetPx : 175;
            const latlng = L.latLng(lat, lng);
            const zoom = mapInstance.getZoom();
            const point = mapInstance.project(latlng, zoom);
            const targetPoint = point.subtract([0, offsetPx]);
            const targetLatLng = mapInstance.unproject(targetPoint, zoom);
            mapInstance.panTo(targetLatLng, { animate: true, duration: 0.4 });
        }

        const boundsGroup = [];
        const markersMap = [];
        const allPolygons = [];
        window._agricoPinClusters = {};

        // Group mappings by coordinate cluster (~1m precision)
        const coordClusters = {};
        mappingsData.forEach((m) => {
            if (!m.latitude || !m.longitude) return;
            const lt = parseFloat(m.latitude);
            const ln = parseFloat(m.longitude);
            const key = `${lt.toFixed(5)},${ln.toFixed(5)}`;
            if (!coordClusters[key]) coordClusters[key] = [];
            coordClusters[key].push(m);
        });

        Object.keys(coordClusters).forEach((coordKey) => {
            const pins = coordClusters[coordKey];
            const m = pins[0];
            const lt = parseFloat(m.latitude);
            const ln = parseFloat(m.longitude);
            const locId = m.location_mapping_id || m.id;
            window._agricoPinClusters[locId] = pins;
            window._agricoPinClusters[coordKey] = pins;

            const isCluster = pins.length > 1;

            const soil   = m.soil;
            const munsell = soil?.munsell_color;
            const hue    = soil?.hue ?? munsell?.hue ?? '—';

            let icon = redIcon;

            const mColor   = soil?.munsell_color || {};
            const soilName = soil?.soil_name || mColor?.color_name || 'Soil Specimen';
            const fullCode = soil?.full_code || mColor?.full_code || '—';
            let hexColor = soil?.hex_color || mColor?.hex_code || '#8F694E';
            if (hexColor && !hexColor.startsWith('#')) hexColor = '#' + hexColor;
            let rawBrgy = (m.barangay && m.barangay !== '') ? m.barangay : 'Centro (Poblacion)';
            let brgyClean = rawBrgy.replace(/^(brgy\.?|barangay\.?)\s*/i, '').replace(/,\s*lal-lo\s*$/i, '').replace(/,\s*cagayan\s*$/i, '');
            const brgyLabel = `Brgy. ${brgyClean}, Lal-lo`;
            const soilType = m.munsell_soil_type || soil?.effective_soil_type || soil?.soil_type || mColor?.soil_type || 'Leached grayish brown';
            const covStr   = m.coverage || '500 m²';

            let regions = [];
            if (isCluster) {
                // Collect all pinned soils in this cluster
                pins.forEach(p => {
                    const pSoil = p.soil || {};
                    const pMColor = pSoil.munsell_color || {};
                    const pMeta = p.soil_layer_meta || {};
                    const pCode = pMeta.munsell || pMeta.munsell_code || pSoil.full_code || pMColor.full_code || '—';
                    let pHex = pMeta.hex || pSoil.hex_color || pMColor.hex_code || '#8F694E';
                    if (pHex && !pHex.startsWith('#')) pHex = '#' + pHex;
                    const pLabel = pMeta.label || pMeta.color_name || pSoil.soil_name || pMColor.color_name || 'Soil Specimen';
                    regions.push({
                        label: pLabel,
                        color_name: pLabel,
                        munsell: pCode,
                        munsell_code: pCode,
                        hex: pHex
                    });
                });
            } else {
                if (Array.isArray(m.regions) && m.regions.length > 0) {
                    regions = m.regions;
                } else if (typeof m.regions === 'string') {
                    try {
                        const parsedR = JSON.parse(m.regions);
                        if (Array.isArray(parsedR)) regions = parsedR;
                    } catch(e) {}
                }
            }

            const isMulti = regions.length > 1;
            const domSoil = regions[0] || {};
            const displayTitle = isCluster
                ? `${domSoil.label || soilName} (+${pins.length - 1} soils)`
                : ((isMulti && (domSoil.label || domSoil.color_name || domSoil.soil_name)) ? (domSoil.label || domSoil.color_name || domSoil.soil_name) : soilName);
            const displayCode = (isMulti && (domSoil.munsell || domSoil.munsell_code || domSoil.full_code)) ? (domSoil.munsell || domSoil.munsell_code || domSoil.full_code) : fullCode;
            let displayHex = (isMulti && (domSoil.hex || domSoil.hex_color)) ? (domSoil.hex || domSoil.hex_color) : hexColor;
            if (displayHex && !displayHex.startsWith('#')) displayHex = '#' + displayHex;

            let typeRowHtml = '';
            if (isCluster) {
                typeRowHtml = `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b; font-weight: 600; font-size: 11px;">Detected Soil:</span>
                        <span style="font-weight: 800; color: #0f172a; font-size: 11.5px;">${pins.length} Pinned</span>
                    </div>
                `;
            } else if (isMulti) {
                typeRowHtml = `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b; font-weight: 600; font-size: 11px;">Detected Soil:</span>
                        <span style="font-weight: 800; color: #0f172a; font-size: 11.5px;">${regions.length} Detected</span>
                    </div>
                `;
            } else {
                typeRowHtml = `
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b; font-weight: 600; font-size: 11px;">Type:</span>
                        <span style="font-weight: 800; color: #0f172a; font-size: 11.5px; max-width: 155px; text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${soilType}">${soilType}</span>
                    </div>
                `;
            }

            const codeRowHtml = (!isMulti || isCluster) ? `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 600; font-size: 11px;">Code:</span>
                    <span style="font-weight: 800; color: #0f172a; font-size: 11.5px;">${displayCode}</span>
                </div>
            ` : '';

            const titleHtml = !isMulti ? `
                <div style="font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 2px; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${displayTitle}">
                    ${displayTitle}
                </div>
            ` : `
                <div style="font-size: 15px; font-weight: 800; color: #0f172a; margin-bottom: 2px; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${displayTitle}">
                    ${displayTitle}
                </div>
            `;

            const locHeader = isCluster
                ? `LOCATION #${pins.map(p => p.location_mapping_id || p.id).join(', #')} (${pins.length} PINNED)`
                : `LOCATION #${locId}`;

            const popupHtml = `
                <div style="padding: 12px 14px; font-family: inherit; width: 100%; box-sizing: border-box;">
                    <div style="margin-bottom: 3px; padding-right: 28px;">
                        <span style="font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">${locHeader}</span>
                    </div>
                    ${titleHtml}
                    <div style="font-size: 11.5px; color: #15803d; font-weight: 700; display: flex; align-items: center; gap: 4px;${isMulti ? ' margin-top: 4px;' : ''} margin-bottom: 9px;">
                        <svg width="12" height="12" fill="#16a34a" viewBox="0 0 20 20" style="flex-shrink:0;"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${brgyLabel}</span>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px 11px; margin-bottom: 10px; font-size: 11.5px; display: flex; flex-direction: column; gap: 5px;">
                        ${codeRowHtml}
                        ${typeRowHtml}
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #64748b; font-weight: 600; font-size: 11px;">Covered Area:</span>
                            <span style="font-weight: 700; color: #0f172a; font-size: 11.5px;">${covStr}</span>
                        </div>
                        <div style="border-top: 1px dotted #cbd5e1; margin-top: 2px; padding-top: 5px; display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: #94a3b8;">
                            <span>Coords:</span>
                            <span style="font-family: ui-monospace, monospace; font-size: 10.5px; font-weight: 500;">${lt.toFixed(4)}, ${ln.toFixed(4)}</span>
                        </div>
                    </div>
                    <div>
                        <button type="button" onclick="viewWebsitePinDetails(${locId}, '${coordKey}')" style="width: 100%; padding: 8.5px 12px; background: #16a34a; color: #ffffff; border: none; border-radius: 12px; font-size: 12px; font-weight: 700; cursor: pointer; transition: background 0.15s ease; box-shadow: 0 1px 2px rgba(22, 163, 74, 0.2);" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                            View Details
                        </button>
                    </div>
                </div>
            `;

            const pinMeta = typeof window.getAgricoPinStatusMeta === 'function'
                ? window.getAgricoPinStatusMeta(m.status)
                : { isApproved: String(m.status || '').toLowerCase() === 'active' || String(m.status || '').toLowerCase() === 'approved' };
            const isApproved = pinMeta.isApproved;
            const markerIcon = typeof window.createSoilPinIcon === 'function'
                ? window.createSoilPinIcon(m.status)
                : (isApproved ? redIcon : grayIcon);

            const marker = L.marker([lt, ln], { icon: markerIcon, interactive: true });
            const popupOpts = window.AgricoMapFocus 
                ? window.AgricoMapFocus.getPopupOptions() 
                : { className: 'agrico-custom-popup', maxWidth: 320, autoPan: false };
            marker.bindPopup(popupHtml, popupOpts);

            marker.on('click', function(e) {
                if (e && e.originalEvent) L.DomEvent.stopPropagation(e);
                if (window.AgricoMapFocus) {
                    window.AgricoMapFocus.focusPin(map, marker, { targetZoom: 15 });
                } else {
                    centerMapOnPopup(map, lt, ln, 175);
                }
            });
            marker.on('popupopen', function() {
                if (window.AgricoMapFocus && !map._agricoIsNavigatingPins) {
                    window.AgricoMapFocus.focusPin(map, marker, { targetZoom: 15 });
                }
            });

            // Green semi-transparent parcel polygon box representing soil covered area
            const dims = getParcelDimensions(covStr);
            const boxCoords = getRoundedBoxCoords(lt, ln, dims.w, dims.h);

            const polyBorderColor = pinMeta.polyBorderColor || (isApproved ? '#16a34a' : '#6b7280');
            const polyFillColor   = pinMeta.polyFillColor || (isApproved ? '#22c55e' : '#9ca3af');
            const polyHoverColor  = pinMeta.polyHoverColor || (isApproved ? '#15803d' : '#4b5563');

            const poly = L.polygon(boxCoords, {
                color: polyBorderColor,
                fillColor: polyFillColor,
                fillOpacity: 0.22,
                weight: 2.5,
                interactive: isApproved
            });

            if (isApproved) {
                poly.bindTooltip(`<div style="font-family:inherit; font-size:11px; font-weight:700; color:#14532d;">Covered Area: <strong>${covStr}</strong></div>`, {
                    sticky: true,
                    direction: 'top',
                    offset: [0, -10]
                });

                poly.on('mouseover', function() {
                    this.setStyle({ fillOpacity: 0.42, weight: 3.5, color: polyHoverColor });
                });
                poly.on('mouseout', function() {
                    this.setStyle({ fillOpacity: 0.22, weight: 2.5, color: polyBorderColor });
                });
                poly.on('click', function(e) {
                    if (e && e.originalEvent) L.DomEvent.stopPropagation(e);
                    if (window.AgricoMapFocus) {
                        window.AgricoMapFocus.focusPin(map, marker, { targetZoom: 15 });
                    } else {
                        centerMapOnPopup(map, lt, ln, 175);
                        marker.openPopup();
                    }
                });
            }

            poly.addTo(map);
            marker.addTo(map);

            allPolygons.push({ poly: poly, lat: lt, lng: ln, covStr: covStr });

            markersMap.push({
                marker: marker,
                poly: poly,
                lat: lt,
                lng: ln,
                soilName: soilName,
                fullCode: fullCode,
                hue: hue,
                soilType: soilType,
                barangay: m.barangay ?? ''
            });

            boundsGroup.push([lt, ln]);
        });

        // Render official Lal-lo municipal & barangay boundary overlay with maxBounds enabled
        if (window.LalloBoundary) {
            window.LalloBoundary.render(map, {
                fitBounds: boundsGroup.length === 0,
                maxBounds: true,
                maxBoundsPadding: 0.08,
                minZoom: 11,
                showTooltips: true,
                bringMarkersToFront: markersMap.map(m => m.marker),
                onBarangayClick: function(e, feature, layer) {
                    const name = feature.properties && (feature.properties.name || feature.properties.adm4_name);
                    if (name) {
                        const input = document.getElementById('mapSearchInput');
                        if (input) {
                            input.value = name;
                            handleMapSearch(name);
                        }
                    }
                }
            }).then(() => {
                // If pins exist, fit bounds to the pins comfortably inside Lal-lo
                if (boundsGroup.length > 0) {
                    map._agricoOverviewBounds = boundsGroup;
                    map.fitBounds(boundsGroup, { padding: [40, 40] });
                }
            }).catch(err => console.log('Lal-lo boundary note:', err));
        } else {
            if (boundsGroup.length > 0) {
                map._agricoOverviewBounds = boundsGroup;
                map.fitBounds(boundsGroup, { padding: [40, 40] });
            } else {
                map.setView([defaultLat, defaultLng], defaultZoom);
            }
        }

        // Map Search Logic (FAQ Design & Positioning)
        function handleMapSearch(query) {
            const clearBtn = document.getElementById('mapSearchClearBtn');
            const resultsDiv = document.getElementById('mapSearchResults');
            const q = query.trim().toLowerCase();

            if (clearBtn) clearBtn.style.display = q ? 'flex' : 'none';

            if (!q) {
                if (resultsDiv) resultsDiv.style.display = 'none';
                return;
            }

            const matches = markersMap.filter(item => {
                return item.soilName.toLowerCase().includes(q) ||
                       item.fullCode.toLowerCase().includes(q) ||
                       item.hue.toLowerCase().includes(q) ||
                       (item.soilType && item.soilType.toLowerCase().includes(q)) ||
                       item.barangay.toLowerCase().includes(q);
            });

            if (matches.length === 0) {
                resultsDiv.innerHTML = '<div style="padding:14px 16px; font-size:13px; color:#8EB69B; text-align:center; font-weight:600;">No matching soil locations found</div>';
                resultsDiv.style.display = 'block';
                return;
            }

            let html = '';
            matches.slice(0, 8).forEach((item) => {
                const subMeta = [
                    item.barangay ? 'Brgy. ' + item.barangay : 'Lal-lo',
                    item.fullCode,
                    item.soilType || ''
                ].filter(Boolean).join(' • ');

                html += `
                    <div onclick="selectMapSearchResult(${item.lat}, ${item.lng})" class="map-search-result-item">
                        <div>
                            <div class="map-search-result-title">${item.soilName}</div>
                            <div class="map-search-result-meta">${subMeta}</div>
                        </div>
                        <span class="map-search-result-badge">View Pin</span>
                    </div>
                `;
            });

            resultsDiv.innerHTML = html;
            resultsDiv.style.display = 'block';
        }

        function selectMapSearchResult(lat, lng) {
            const item = markersMap.find(m => Math.abs(m.lat - lat) < 0.0001 && Math.abs(m.lng - lng) < 0.0001);
            if (item && item.marker) {
                if (window.AgricoMapFocus) {
                    window.AgricoMapFocus.focusPin(map, item.marker, { targetZoom: 15 });
                } else {
                    const targetZoom = 15;
                    const latlng = L.latLng(lat, lng);
                    const point = map.project(latlng, targetZoom);
                    const targetLatLng = map.unproject(point.subtract([0, 175]), targetZoom);
                    map.flyTo(targetLatLng, targetZoom, { duration: 0.65 });
                    setTimeout(() => item.marker.openPopup(), 650);
                }
            }
            const resultsDiv = document.getElementById('mapSearchResults');
            if (resultsDiv) resultsDiv.style.display = 'none';
        }

        function clearMapSearch() {
            const input = document.getElementById('mapSearchInput');
            const clearBtn = document.getElementById('mapSearchClearBtn');
            const resultsDiv = document.getElementById('mapSearchResults');
            if (input) {
                input.value = '';
                input.focus();
            }
            if (clearBtn) clearBtn.style.display = 'none';
            if (resultsDiv) resultsDiv.style.display = 'none';
            if (boundsGroup.length > 0) {
                map.fitBounds(boundsGroup, { padding: [40, 40] });
            }
        }

        // Hide map search dropdown on outside click
        document.addEventListener('click', e => {
            if (!e.target.closest('.map-search-box')) {
                const resultsDiv = document.getElementById('mapSearchResults');
                if (resultsDiv) resultsDiv.style.display = 'none';
            }
        });

        // Hide on Escape key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                const resultsDiv = document.getElementById('mapSearchResults');
                if (resultsDiv) resultsDiv.style.display = 'none';
            }
        });

        // Helper: normalize bounding box for ROI display
        function normalizeBox(r) {
            if (!r) {
                return { left: 25, top: 25, width: 50, height: 50, raw_x: 0.25, raw_y: 0.25, raw_w: 0.5, raw_h: 0.5 };
            }
            let box = r.box || r.bounding_box || null;
            if (!box && (r.x !== undefined && r.y !== undefined && r.w !== undefined && r.h !== undefined)) {
                box = { x: r.x, y: r.y, w: r.w, h: r.h };
            }
            if (!box && (r.norm_x !== undefined && r.norm_y !== undefined && r.norm_w !== undefined && r.norm_h !== undefined)) {
                box = { x: r.norm_x, y: r.norm_y, w: r.norm_w, h: r.norm_h };
            }
            if (typeof box === 'string') {
                try { box = JSON.parse(box); } catch(e) { box = null; }
            }
            if (!box) {
                return { left: 25, top: 25, width: 50, height: 50, raw_x: 0.25, raw_y: 0.25, raw_w: 0.5, raw_h: 0.5 };
            }
            let bx = parseFloat(box.norm_x !== undefined ? box.norm_x : (box.x !== undefined ? box.x : 0.25));
            let by = parseFloat(box.norm_y !== undefined ? box.norm_y : (box.y !== undefined ? box.y : 0.25));
            let bw = parseFloat(box.norm_w !== undefined ? box.norm_w : (box.w !== undefined ? box.w : 0.5));
            let bh = parseFloat(box.norm_h !== undefined ? box.norm_h : (box.h !== undefined ? box.h : 0.5));

            if (bx > 1.0 || by > 1.0 || bw > 1.0 || bh > 1.0) {
                bx = bx / 400; by = by / 400; bw = bw / 400; bh = bh / 400;
            }
            bx = Math.max(0.02, Math.min(0.88, bx));
            by = Math.max(0.02, Math.min(0.88, by));
            bw = Math.max(0.06, Math.min(0.96 - bx, bw));
            bh = Math.max(0.06, Math.min(0.96 - by, bh));

            return {
                left: Math.round(bx * 10000) / 100,
                top: Math.round(by * 10000) / 100,
                width: Math.round(bw * 10000) / 100,
                height: Math.round(bh * 10000) / 100,
                raw_x: bx,
                raw_y: by,
                raw_w: bw,
                raw_h: bh
            };
        }

        // Helper: calculate near match rating
        function calcNearMatchRating(item) {
            if (!item) return '98.7%';
            const de = item.color_delta_e ?? item.delta_e ?? item.distance;
            if (de !== undefined && de !== null && !isNaN(de) && parseFloat(de) >= 0) {
                return (Math.max(88.5, Math.min(99.6, 99.4 - (parseFloat(de) * 0.45)))).toFixed(1) + '%';
            }
            if (item.near_match && typeof item.near_match === 'string' && item.near_match !== '100%' && item.near_match !== '100.0%') {
                return item.near_match;
            }
            if (item.confidence && typeof item.confidence === 'string' && item.confidence !== '100%' && item.confidence !== '100.0%') {
                return item.confidence.includes('%') ? item.confidence : item.confidence + '%';
            }
            return '98.7%';
        }

        // Toggle secondary layer accordion
        window.toggleWebsiteLayerAccordion = function(idx) {
            const body = document.getElementById(`wsLayerBody_${idx}`);
            const arrow = document.getElementById(`wsLayerArrow_${idx}`);
            if (body) {
                const isHidden = body.style.display === 'none';
                body.style.display = isHidden ? 'block' : 'none';
                if (arrow) arrow.textContent = isHidden ? '▼' : '▶';
            }
        };

        let modalSoilDetailMapInstance = null;

        // View Details Modal Handler (Using Shared Soil Record Modal Partial)
        window.viewWebsitePinDetails = function(locId, clusterKey = null) {
            const m = (typeof locId === 'object' && locId !== null) ? locId : (window.mappingsData || mappingsData).find(item => (item.location_mapping_id || item.id) == locId);
            if (!m) return;

            const modal = document.getElementById('soilRecordModal');
            if (!modal) return;

            let clusterPins = (clusterKey && window._agricoPinClusters && window._agricoPinClusters[clusterKey])
                ? window._agricoPinClusters[clusterKey]
                : (window._agricoPinClusters && window._agricoPinClusters[locId] ? window._agricoPinClusters[locId] : [m]);
            const isCluster = clusterPins && clusterPins.length > 1;

            const soil = m.soil || {};
            const mColor = soil.munsell_color || {};
            const soilName = soil.soil_name || mColor.color_name || 'Soil Specimen';
            const fullCode = soil.full_code || mColor.full_code || '—';
            let hexColor = soil.hex_color || mColor.hex_code || '#8F694E';
            if (hexColor && !hexColor.startsWith('#')) hexColor = '#' + hexColor;
            const soilType = m.munsell_soil_type || soil.effective_soil_type || soil.soil_type_label || soil.soil_type || mColor.soil_type || 'Leached grayish brown';
            const covStr = m.coverage || '500 m²';
            const lt = parseFloat(m.latitude) || 0;
            const ln = parseFloat(m.longitude) || 0;
            const cleanBrgy = (m.barangay && m.barangay !== 'Lal-lo Overall') ? m.barangay : 'Centro (Poblacion)';
            const brgy = cleanBrgy.startsWith('Brgy.') ? `${cleanBrgy}, Lal-lo, Cagayan` : `Brgy. ${cleanBrgy}, Lal-lo, Cagayan`;
            const drainage = soil.drainage || mColor.drainage_profile || 'Well-Drained Clay / Latosol';
            const organic = soil.effective_organic_matter || mColor.organic_matter || 'Low (1.1% – 2.0%)';
            const desc = soil.description || (m.soil_scan && m.soil_scan.scan_note ? m.soil_scan.scan_note : `${fullCode} - ${soilName}`);

            // Build regions array
            let regions = [];
            if (isCluster) {
                clusterPins.forEach((p) => {
                    const pSoil = p.soil || {};
                    const pMColor = pSoil.munsell_color || {};
                    const pMeta = p.soil_layer_meta || {};
                    const pCode = pMeta.munsell || pMeta.munsell_code || pSoil.full_code || pMColor.full_code || '—';
                    let pHex = pMeta.hex || pSoil.hex_color || pMColor.hex_code || '#8F694E';
                    if (pHex && !pHex.startsWith('#')) pHex = '#' + pHex;
                    const pLabel = pMeta.label || pMeta.color_name || pSoil.soil_name || pMColor.color_name || 'Soil Specimen';
                    const pPct = p.soil_percentage ? parseFloat(String(p.soil_percentage).replace('%','')) : (pMeta.percent !== undefined ? parseFloat(pMeta.percent) : 100);
                    const pConf = p.soil_confidence || pMeta.confidence || calcNearMatchRating(pMeta);
                    const pOrg = pMeta.organic_matter || pSoil.effective_organic_matter || pMColor.organic_matter || 'Moderate (2.2% – 3.7%)';
                    const pDrain = pMeta.soil_name || pSoil.soil_type || 'Well-Drained Clay / Latosol';
                    const pDesc = pMeta.description || pSoil.description || `${pCode} - ${pLabel}`;
                    const pBox = normalizeBox(pMeta.box || pMeta);

                    regions.push({
                        munsell: pCode,
                        munsell_code: pCode,
                        hex: pHex,
                        label: pLabel,
                        color_name: pLabel,
                        percent: pPct,
                        proportion: pPct / 100,
                        confidence: pConf,
                        organic_matter: pOrg,
                        soil_name: pDrain,
                        soil_type: pDrain,
                        description: pDesc,
                        box: pBox
                    });
                });
            } else {
                regions = Array.isArray(m.regions) && m.regions.length > 0 ? m.regions : [];
                if (regions.length === 0) {
                    regions = [{
                        munsell: fullCode,
                        munsell_code: fullCode,
                        hex: hexColor,
                        label: soilName,
                        color_name: soilName,
                        percent: 100,
                        proportion: 1.0,
                        confidence: '98.7%',
                        organic_matter: organic,
                        soil_name: drainage,
                        description: desc,
                        box: { x: 0.25, y: 0.25, w: 0.5, h: 0.5 }
                    }];
                }
            }

            const isMulti = regions.length > 1;
            const dominant = regions[0] || {};
            const domBox = normalizeBox(dominant);
            const domCode = dominant.munsell || dominant.munsell_code || fullCode;
            let domHex = dominant.hex || hexColor;
            if (domHex && !domHex.startsWith('#')) domHex = '#' + domHex;
            const domName = dominant.label || dominant.color_name || soilName;
            const domPct = dominant.percent !== undefined ? Math.round(dominant.percent) : (dominant.proportion !== undefined ? Math.round(dominant.proportion * 100) : 100);
            const domConf = calcNearMatchRating(dominant);
            const domOrganic = dominant.organic_matter || organic;
            const domDrainage = dominant.soil_name || drainage;
            const domDesc = dominant.description || desc;

            const scanImgUrl = m.scan_image_url || '';
            const baImgUrl = m.before_after_image_url || '';
            const recordedDate = m.scan_date_formatted || '09/27/2026';
            const scannedBy = m.scanned_by || 'Admin Analyst';
            const scanPlatform = m.scan_platform || 'Web';
            const scanCount = soil.scan_count || (m.soil_scan_id ? 4 : 1);
            const noteText = m.scan_note_text || (m.soil_scan && m.soil_scan.scan_note ? m.soil_scan.scan_note : 'No scan note available for this record.');

            // 1. Header Title
            const modalRecordTitle = document.getElementById('modalRecordTitle');
            if (modalRecordTitle) {
                if (isCluster) {
                    modalRecordTitle.textContent = `Soil Record Details (${regions.length} PINNED SOILS)`;
                } else if (isMulti) {
                    modalRecordTitle.textContent = `Soil Record Details (${regions.length} DETECTED)`;
                } else {
                    modalRecordTitle.textContent = 'Soil Record Details';
                }
            }

            // 2. Photo Thumbnail Avatar (Single clean box, no nested frames or ROI boxes)
            const avatarHost = document.getElementById('modalSoilSnippetAvatarContainer');
            if (avatarHost) {
                const oldRoi = avatarHost.querySelectorAll('.soil-roi-box, .soil-avatar-box');
                oldRoi.forEach(el => el.remove());
            }
            const modalSoilCircleSnippet = document.getElementById('modalSoilCircleSnippet');
            const modalSoilSnippetFallback = document.getElementById('modalSoilSnippetFallback');
            const fallbackThumb = (regions[0] && regions[0].region_thumbnail) ? (regions[0].region_thumbnail.startsWith('data:') ? regions[0].region_thumbnail : 'data:image/png;base64,' + regions[0].region_thumbnail) : '';
            const effectiveAvatarUrl = scanImgUrl || fallbackThumb;

            if (effectiveAvatarUrl && modalSoilCircleSnippet) {
                modalSoilCircleSnippet.style.display = 'none';
                if (modalSoilSnippetFallback) modalSoilSnippetFallback.style.display = 'flex';
                modalSoilCircleSnippet.onload = function() {
                    modalSoilCircleSnippet.style.display = 'block';
                    if (modalSoilSnippetFallback) modalSoilSnippetFallback.style.display = 'none';
                };
                modalSoilCircleSnippet.onerror = function() {
                    modalSoilCircleSnippet.style.display = 'none';
                    if (modalSoilSnippetFallback) modalSoilSnippetFallback.style.display = 'flex';
                };
                modalSoilCircleSnippet.src = effectiveAvatarUrl;
                if (modalSoilCircleSnippet.complete && modalSoilCircleSnippet.naturalWidth > 0) {
                    modalSoilCircleSnippet.style.display = 'block';
                    if (modalSoilSnippetFallback) modalSoilSnippetFallback.style.display = 'none';
                }
            } else {
                if (modalSoilCircleSnippet) modalSoilCircleSnippet.style.display = 'none';
                if (modalSoilSnippetFallback) modalSoilSnippetFallback.style.display = 'flex';
            }

            // 3. Dual-Preview Swatch Banner (Word-break safe, hex pill never squeezed)
            const heroBadge = document.getElementById('modalSoilDualPreviewBadge');
            const modalCodeDisplay = document.getElementById('modalCodeDisplay');
            const modalSoilPercentageDisplay = document.getElementById('modalSoilPercentageDisplay');
            const modalHexPill = document.getElementById('modalHexPill');
            const modalDominantChevron = document.getElementById('modalDominantChevron');
            const modalSwatchBlock = document.getElementById('modalSwatchBlock');

            if (heroBadge) {
                heroBadge.style.setProperty('background', domHex, 'important');
                heroBadge.style.setProperty('background-color', domHex, 'important');
            }
            const mTheme = window.getContrastColor ? window.getContrastColor(domHex) : { text: '#ffffff', subtext: '#ffffff', chevron: '#ffffff', pillBg: 'rgba(0,0,0,0.6)', pillBorder: 'rgba(255,255,255,0.2)', pillText: '#ffffff' };
            if (modalCodeDisplay) {
                modalCodeDisplay.textContent = domCode;
                modalCodeDisplay.style.color = mTheme.text;
            }
            if (modalHexPill) {
                modalHexPill.textContent = domHex.toUpperCase();
                modalHexPill.style.backgroundColor = mTheme.pillBg;
                modalHexPill.style.borderColor = mTheme.pillBorder;
                modalHexPill.style.color = mTheme.pillText;
            }
            if (modalDominantChevron) modalDominantChevron.style.color = mTheme.chevron;

            if (isMulti) {
                if (modalDominantChevron) modalDominantChevron.style.display = 'inline';
                if (modalSoilPercentageDisplay) {
                    modalSoilPercentageDisplay.style.display = 'block';
                    modalSoilPercentageDisplay.style.color = mTheme.subtext;
                    modalSoilPercentageDisplay.textContent = `Soil Percentage (${domPct}%)`;
                }
            } else {
                if (modalDominantChevron) modalDominantChevron.style.display = 'none';
                if (modalSoilPercentageDisplay && domPct && domPct < 100) {
                    modalSoilPercentageDisplay.style.display = 'block';
                    modalSoilPercentageDisplay.style.color = mTheme.subtext;
                    modalSoilPercentageDisplay.textContent = `Soil Percentage (${domPct}%)`;
                } else if (modalSoilPercentageDisplay) {
                    modalSoilPercentageDisplay.style.display = 'none';
                }
            }

            // Interactive Dropdown Menu for detected soil layers
            function toggleMapModalLayerDropdown(ev) {
                if (ev) ev.stopPropagation();
                const menu = document.getElementById('modalLayerDropdownMenu');
                const chevron = document.getElementById('modalDominantChevron');
                if (!menu) return;
                const isOpen = menu.style.display === 'block';
                menu.style.display = isOpen ? 'none' : 'block';
                if (chevron) {
                    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            }

            if (isMulti && modalSwatchBlock) {
                modalSwatchBlock.onclick = toggleMapModalLayerDropdown;
                modalSwatchBlock.style.cursor = 'pointer';
            } else if (modalSwatchBlock) {
                modalSwatchBlock.onclick = null;
                modalSwatchBlock.style.cursor = 'default';
            }

            window.selectActiveMapModalLayer = function(layerIdx) {
                if (!regions || !regions[layerIdx]) return;
                const r = regions[layerIdx];
                const rPct = r.percent !== undefined ? r.percent : Math.round((r.proportion || 0.2) * 100);
                const rCode = r.munsell || r.munsell_code || fullCode;
                let rHex = r.hex || hexColor;
                if (rHex && !rHex.startsWith('#')) rHex = '#' + rHex;
                const rLabel = r.label || r.color_name || soilName;
                const rOrganic = r.organic_matter || organic;
                const rDrainage = r.soil_type || r.soil_name || drainage;
                const rConf = calcNearMatchRating(r);
                let rDesc = r.description || `${rCode} - ${rLabel}`;

                const rTheme = window.getContrastColor ? window.getContrastColor(rHex) : { text: '#ffffff', subtext: '#ffffff', chevron: '#ffffff', pillBg: 'rgba(0,0,0,0.6)', pillBorder: 'rgba(255,255,255,0.2)', pillText: '#ffffff' };
                if (heroBadge) {
                    heroBadge.style.setProperty('background', rHex, 'important');
                    heroBadge.style.setProperty('background-color', rHex, 'important');
                }
                if (modalCodeDisplay) {
                    modalCodeDisplay.textContent = rCode;
                    modalCodeDisplay.style.color = rTheme.text;
                }
                if (modalHexPill) {
                    modalHexPill.textContent = rHex.toUpperCase();
                    modalHexPill.style.backgroundColor = rTheme.pillBg;
                    modalHexPill.style.borderColor = rTheme.pillBorder;
                    modalHexPill.style.color = rTheme.pillText;
                }
                if (modalDominantChevron) modalDominantChevron.style.color = rTheme.chevron;
                if (modalSoilPercentageDisplay) {
                    modalSoilPercentageDisplay.style.display = 'block';
                    modalSoilPercentageDisplay.style.color = rTheme.subtext;
                    modalSoilPercentageDisplay.textContent = `Soil Percentage (${rPct}%)`;
                }

                if (modalCardColorName) modalCardColorName.textContent = rLabel;
                if (modalCardFullCode) modalCardFullCode.textContent = rCode;
                if (modalCardOrganic) modalCardOrganic.textContent = rOrganic;
                if (modalCardDrainage) modalCardDrainage.textContent = rDrainage;
                if (modalCardConfidence) modalCardConfidence.textContent = rConf;
                if (modalCardDesc) modalCardDesc.textContent = rDesc;

                const dropdownItems = document.querySelectorAll('#modalLayerDropdownList .res-layer-dropdown-item');
                dropdownItems.forEach((it, idx) => {
                    if (idx === layerIdx) it.classList.add('is-selected');
                    else it.classList.remove('is-selected');
                });

                const menu = document.getElementById('modalLayerDropdownMenu');
                const chevron = document.getElementById('modalDominantChevron');
                if (menu) menu.style.display = 'none';
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            };

            const modalLayerDropdownList = document.getElementById('modalLayerDropdownList');
            const modalLayerDropdownCount = document.getElementById('modalLayerDropdownCount');
            if (modalLayerDropdownList) {
                modalLayerDropdownList.innerHTML = '';
                if (isMulti) {
                    if (modalLayerDropdownCount) modalLayerDropdownCount.textContent = `${regions.length} DETECTED`;
                    regions.forEach((r, idx) => {
                        const rPct = r.percent !== undefined ? r.percent : Math.round((r.proportion || 0.2) * 100);
                        const rCode = r.munsell || r.munsell_code || domCode;
                        let rHex = r.hex || domHex;
                        if (rHex && !rHex.startsWith('#')) rHex = '#' + rHex;
                        const rLabel = r.label || r.color_name || domName;
                        const isDom = idx === 0;

                        const item = document.createElement('div');
                        item.className = 'res-layer-dropdown-item' + (idx === 0 ? ' is-selected' : '');
                        item.innerHTML = `
                            <div style="display: flex; align-items: center; gap: 9px; min-width: 0;">
                                <span class="res-dropdown-swatch-dot" style="background: ${rHex};"></span>
                                <div style="min-width: 0; line-height: 1.25;">
                                    <div class="res-dropdown-item-code">${rCode}</div>
                                    <div class="res-dropdown-item-label" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${rLabel}</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                <span class="res-dropdown-item-pct">${rPct}%</span>
                                ${isDom ? '<span style="font-size: 8.5px; font-weight: 800; background: rgba(34, 197, 94, 0.2); color: #22c55e; padding: 2px 6px; border-radius: 9999px;">DOMINANT</span>' : `<span style="font-size: 8.5px; font-weight: 700; color: #8EB69B;">#${idx+1}</span>`}
                            </div>
                        `;
                        item.addEventListener('click', (ev) => {
                            ev.stopPropagation();
                            window.selectActiveMapModalLayer(idx);
                        });
                        modalLayerDropdownList.appendChild(item);
                    });
                }
            }

            // 4. Row 1: Code Details (Color Name, Full Code, Scan Count)
            const modalCardColorName = document.getElementById('modalCardColorName');
            const modalCardFullCode = document.getElementById('modalCardFullCode');
            const modalCardScanCount = document.getElementById('modalCardScanCount');
            if (modalCardColorName) modalCardColorName.textContent = domName;
            if (modalCardFullCode) modalCardFullCode.textContent = domCode;
            if (modalCardScanCount) modalCardScanCount.textContent = scanCount;

            // 5. Row 2: Soil Properties (Soil Organic, Soil Type, Near-Match)
            const modalCardOrganic = document.getElementById('modalCardOrganic');
            const modalCardDrainage = document.getElementById('modalCardDrainage');
            const modalCardConfidence = document.getElementById('modalCardConfidence');
            if (modalCardOrganic) modalCardOrganic.textContent = domOrganic;
            if (modalCardDrainage) modalCardDrainage.textContent = soilType || domDrainage;
            if (modalCardConfidence) modalCardConfidence.textContent = domConf;

            // 6. Row 3: Scan Date & Scanned By
            const modalCardDate = document.getElementById('modalCardDate');
            const modalCardDateContainer = document.getElementById('modalCardDateContainer');
            const modalCardScannedBy = document.getElementById('modalCardScannedBy');
            const modalCardPlatform = document.getElementById('modalCardPlatform');
            if (modalCardDate) modalCardDate.textContent = recordedDate;
            if (modalCardDateContainer) modalCardDateContainer.style.display = 'block';
            if (modalCardScannedBy) modalCardScannedBy.textContent = scannedBy;
            if (modalCardPlatform) {
                modalCardPlatform.textContent = scanPlatform;
                modalCardPlatform.style.display = 'inline-block';
            }

            // 7. Scan Note & Description Cards
            const modalCardScanNote = document.getElementById('modalCardScanNote');
            const modalCardDesc = document.getElementById('modalCardDesc');
            if (modalCardScanNote) modalCardScanNote.textContent = noteText;
            if (modalCardDesc) modalCardDesc.textContent = isMulti ? domDesc : desc;

            // 8. Multi-Layer Accordions (Secondary & Tertiary Detected Soils)
            const modalMultiLayerSection = document.getElementById('modalMultiLayerSection');
            const modalMultiLayerItems = document.getElementById('modalMultiLayerItems');
            if (isMulti && modalMultiLayerSection && modalMultiLayerItems) {
                modalMultiLayerSection.style.display = 'block';
                modalMultiLayerItems.innerHTML = '';
                regions.slice(1).forEach((sub, subIdx) => {
                    const idx = subIdx + 1;
                    const subPct = sub.percent !== undefined ? Math.round(sub.percent) : (sub.proportion !== undefined ? Math.round(sub.proportion * 100) : 18);
                    const subCode = sub.munsell || sub.munsell_code || 'Soil Color';
                    let subHex = sub.hex || '#8F694E';
                    if (subHex && !subHex.startsWith('#')) subHex = '#' + subHex;
                    const subName = sub.label || sub.color_name || 'Soil Color';
                    const subConf = calcNearMatchRating(sub);
                    const subOrganic = sub.organic_matter || organic;
                    const subSoilType = sub.soil_type || sub.soil_name || soilType;
                    const subDrainage = sub.soil_name || drainage;
                    const subDesc = sub.description || `${subCode} - ${subName}`;

                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'layer-accordion-item';
                    itemDiv.innerHTML = `
                        <button
                            type="button"
                            class="layer-accordion-header"
                            style="width: 100%; padding: 11px 14px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; border: none; text-align: left; font-family: inherit; user-select: none;"
                        >
                            <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13px;">
                                <span class="layer-chevron" style="font-size: 11px; transition: transform 0.2s ease;">▶</span>
                                <span>${subCode} (Soil Percentage - ${subPct}%)</span>
                            </div>
                            <span style="font-size: 11px; font-weight: 800; color: #ffffff; background: ${subHex}; padding: 3px 10px; border-radius: 12px; font-family: monospace; text-shadow: none; flex-shrink: 0;">
                                ${subHex.toUpperCase()}
                            </span>
                        </button>
                        <div class="layer-accordion-body" style="display: none; padding: 14px;">
                            <div class="soil-swatch-banner" style="background: ${subHex}; border-radius: 14px; box-shadow: 0 4px 14px rgba(0,0,0,0.18); display: flex; align-items: center; gap: 12px; padding: 10px 16px; margin-bottom: 10px; box-sizing: border-box; min-height: 64px;">
                                ${effectiveAvatarUrl ? `
                                    <div class="soil-swatch-avatar" style="width: 48px; height: 48px; min-width: 48px; border-radius: 10px; overflow: hidden; background: rgba(0,0,0,0.2); position: relative; flex-shrink: 0;">
                                        <img src="${effectiveAvatarUrl}" alt="Soil sample photo" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
                                    </div>
                                ` : ''}
                                <div style="flex: 1; min-width: 0;">
                                    <span style="font-size: 18px; font-weight: 900; color: #ffffff; text-shadow: none;">${subCode}</span>
                                    <div style="font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.95); margin-top: 2px;">Soil Percentage (${subPct}%)</div>
                                </div>
                                <span style="font-size: 11px; font-weight: 800; color: #ffffff; background: rgba(0,0,0,0.58); border: 1px solid rgba(255,255,255,0.25); padding: 4px 10px; border-radius: 20px; font-family: monospace; flex-shrink: 0;">${subHex.toUpperCase()}</span>
                            </div>
                            <div class="soil-modal-grid-3" style="margin-bottom: 8px;">
                                <div class="soil-modal-grid-card"><div class="soil-modal-label">Color Name</div><div class="soil-modal-value truncate" title="${subName}">${subName}</div></div>
                                <div class="soil-modal-grid-card"><div class="soil-modal-label">Full Code</div><div class="soil-modal-value">${subCode}</div></div>
                                <div class="soil-modal-grid-card"><div class="soil-modal-label">Proportion</div><div class="soil-modal-value">${subPct}%</div></div>
                            </div>
                            <div class="soil-modal-grid-3" style="margin-bottom: 8px;">
                                <div class="soil-modal-grid-card"><div class="soil-modal-label">Soil Organic</div><div class="soil-modal-value">${subOrganic}</div></div>
                                <div class="soil-modal-grid-card"><div class="soil-modal-label">Soil Type</div><div class="soil-modal-value truncate" title="${subSoilType}">${subSoilType}</div></div>
                                <div class="soil-modal-grid-card"><div class="soil-modal-label">Near-Match</div><div class="soil-modal-value" style="color: #10b981; font-weight: 800;">${subConf}</div></div>
                            </div>
                            <div class="soil-modal-grid-card" style="margin-bottom: 8px; padding: 12px 16px;">
                                <div class="soil-modal-label">Scan Note</div>
                                <div class="soil-modal-value" style="font-size: 13px; font-weight: 700; line-height: 1.5;">No scan note available for this record.</div>
                            </div>
                            <div class="soil-modal-grid-card" style="padding: 12px 16px;">
                                <div class="soil-modal-label">Description</div>
                                <div class="soil-modal-value" style="font-size: 13px; font-weight: 700; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">${subDesc}</div>
                            </div>
                        </div>
                    `;
                    const headerBtn = itemDiv.querySelector('.layer-accordion-header');
                    const bodyEl = itemDiv.querySelector('.layer-accordion-body');
                    const chevronEl = itemDiv.querySelector('.layer-chevron');
                    headerBtn.addEventListener('click', () => {
                        const willOpen = bodyEl.style.display !== 'block';
                        bodyEl.style.display = willOpen ? 'block' : 'none';
                        chevronEl.textContent = willOpen ? '▼' : '▶';
                    });
                    modalMultiLayerItems.appendChild(itemDiv);
                });
            } else if (modalMultiLayerSection) {
                modalMultiLayerSection.style.display = 'none';
            }

            // 9. Soil Pin Location & Covered Area Preview Card
            const modalSoilLocationCard = document.getElementById('modalSoilLocationCard');
            const modalLocStatusBadge = document.getElementById('modalLocStatusBadge');
            const modalLocBrgyBadge = document.getElementById('modalLocBrgyBadge');
            const modalLocCoverageBadge = document.getElementById('modalLocCoverageBadge');
            const modalLocCoordsBadge = document.getElementById('modalLocCoordsBadge');

            if (modalSoilLocationCard && lt && ln) {
                modalSoilLocationCard.style.display = 'block';
                if (modalLocStatusBadge) {
                    modalLocStatusBadge.textContent = (m.status || 'VERIFIED').toUpperCase();
                    modalLocStatusBadge.style.display = 'inline-block';
                }
                if (modalLocBrgyBadge) modalLocBrgyBadge.textContent = brgy;
                if (modalLocCoordsBadge) modalLocCoordsBadge.textContent = `${lt.toFixed(4)}, ${ln.toFixed(4)}`;
                if (modalLocCoverageBadge) {
                    modalLocCoverageBadge.textContent = covStr;
                    modalLocCoverageBadge.style.display = 'inline-block';
                }
                setTimeout(() => {
                    const mapEl = document.getElementById('modalSoilDetailMap');
                    if (!mapEl || typeof L === 'undefined') return;
                    if (modalSoilDetailMapInstance) {
                        try { modalSoilDetailMapInstance.remove(); } catch(e) {}
                        modalSoilDetailMapInstance = null;
                    }
                    modalSoilDetailMapInstance = L.map(mapEl, {
                        zoomControl: true,
                        attributionControl: false
                    }).setView([lt, ln], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(modalSoilDetailMapInstance);
                    const marker = L.marker([lt, ln]).addTo(modalSoilDetailMapInstance);

                    // Optional coverage polygon
                    let boxW = 85, boxH = 85, cornerR = 20;
                    if (covStr.includes('50 m')) { boxW = 35; boxH = 35; cornerR = 8; }
                    else if (covStr.includes('250 m')) { boxW = 55; boxH = 55; cornerR = 13; }
                    else if (covStr.includes('500 m')) { boxW = 75; boxH = 75; cornerR = 18; }
                    else if (covStr.includes('1,000') || covStr.includes('1000')) { boxW = 100; boxH = 100; cornerR = 24; }
                    else if (covStr.includes('2,500') || covStr.includes('2500')) { boxW = 130; boxH = 130; cornerR = 30; }
                    else if (covStr.includes('5,000') || covStr.includes('5000')) { boxW = 160; boxH = 160; cornerR = 36; }
                    else if (covStr.toLowerCase().includes('hectare')) { boxW = 190; boxH = 190; cornerR = 42; }

                    if (typeof getRoundedBoxCoords === 'function') {
                        const roundedCoords = getRoundedBoxCoords(lt, ln, boxW, boxH, cornerR);
                        if (roundedCoords && roundedCoords.length > 0) {
                            const poly = L.polygon(roundedCoords, {
                                color: '#16a34a',
                                fillColor: '#22c55e',
                                fillOpacity: 0.25,
                                weight: 2.5
                            }).addTo(modalSoilDetailMapInstance);
                            poly.bindTooltip(`Covered Area (Sakop): ${covStr}`, { sticky: true });
                        }
                    }

                    setTimeout(() => { if (modalSoilDetailMapInstance) modalSoilDetailMapInstance.invalidateSize(); }, 150);
                    setTimeout(() => { if (modalSoilDetailMapInstance) modalSoilDetailMapInstance.invalidateSize(); }, 350);
                }, 80);
            } else if (modalSoilLocationCard) {
                modalSoilLocationCard.style.display = 'none';
            }

            // 10. Soil Normalization: Before & After Card
            const ordinalNames = ['1ST', '2ND', '3RD', '4TH', '5TH', '6TH', '7TH', '8TH', '9TH', '10TH'];
            let overlayBoxesHtml = '';
            let swatchPillsHtml = '';

            regions.forEach((r, idx) => {
                const bData = normalizeBox(r);
                const bx = bData.raw_x;
                const by = bData.raw_y;
                const bw = bData.raw_w;
                const bh = bData.raw_h;
                const leftPct = 50 + (bx * 50);
                const topPct = 7.41 + (by * 92.59);
                const widthPct = bw * 50;
                const heightPct = bh * 92.59;
                const roleLabel = (ordinalNames[idx] || (idx + 1) + 'TH') + ' COLOR';

                overlayBoxesHtml += `
                    <div style="position: absolute; left: ${leftPct}%; top: ${topPct}%; width: ${widthPct}%; height: ${heightPct}%; border: 2.5px solid #4ade80; pointer-events: none; border-radius: 4px; box-sizing: border-box;">
                        <span style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); font-size: 8px; font-weight: 800; background: #4ade80; color: #000000; padding: 1px 6px; border-radius: 4px; white-space: nowrap; line-height: 1.2; letter-spacing: 0.03em;">
                            ${roleLabel}
                        </span>
                    </div>
                `;

                let rHex = r.hex || domHex;
                if (rHex && !rHex.startsWith('#')) rHex = '#' + rHex;
                const rCode = r.munsell || r.munsell_code || domCode;
                const rPct = r.percent !== undefined ? Math.round(r.percent) : (r.proportion !== undefined ? Math.round(r.proportion * 100) : domPct);

                swatchPillsHtml += `
                    <div class="soil-swatch-capsule-pill">
                        <span class="capsule-pill-swatch" style="width: 14px; height: 14px; border-radius: 3.5px; background: ${rHex}; border: 1px solid rgba(255,255,255,0.25); display: inline-block; flex-shrink: 0; box-shadow: none;"></span>
                        <span class="capsule-pill-hex" style="font-weight: 700; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">${rHex.toUpperCase()}</span>
                        ${rCode ? `<span class="capsule-pill-code" style="font-weight: 600; font-size: 11px;">(${rCode})</span>` : ''}
                        <span class="capsule-pill-pct" style="font-weight: 800; font-size: 11.5px; margin-left: 2px;">${rPct}%</span>
                    </div>
                `;
            });

            const modalBeforeAfterCard = document.getElementById('modalBeforeAfterCard');
            const modalBeforeAfterImg = document.getElementById('modalBeforeAfterImg');
            const modalBeforeAfterBoxes = document.getElementById('modalBeforeAfterBoxes');
            const modalBeforeAfterPlaceholder = document.getElementById('modalBeforeAfterPlaceholder');
            const modalBeforeAfterSwatches = document.getElementById('modalBeforeAfterSwatches');
            const modalBeforeAfterMetaText = document.getElementById('modalBeforeAfterMetaText');

            if (modalBeforeAfterCard) {
                modalBeforeAfterCard.style.display = 'block';
                if (modalBeforeAfterMetaText) {
                    modalBeforeAfterMetaText.textContent = `CREATED: ${recordedDate}  |  DETECTED COLORS: ${regions.length}`;
                }
                if (modalBeforeAfterSwatches) {
                    modalBeforeAfterSwatches.style.display = 'flex';
                    modalBeforeAfterSwatches.innerHTML = swatchPillsHtml;
                }
                if (baImgUrl && modalBeforeAfterImg) {
                    modalBeforeAfterImg.onload = function() {
                        modalBeforeAfterImg.style.display = 'block';
                        if (modalBeforeAfterPlaceholder) modalBeforeAfterPlaceholder.style.display = 'none';
                        if (modalBeforeAfterBoxes) modalBeforeAfterBoxes.innerHTML = overlayBoxesHtml;
                    };
                    modalBeforeAfterImg.onerror = function() {
                        modalBeforeAfterImg.style.display = 'none';
                        if (modalBeforeAfterPlaceholder) modalBeforeAfterPlaceholder.style.display = 'flex';
                        if (modalBeforeAfterBoxes) modalBeforeAfterBoxes.innerHTML = '';
                    };
                    modalBeforeAfterImg.src = baImgUrl;
                    if (modalBeforeAfterImg.complete && modalBeforeAfterImg.naturalWidth > 0) {
                        modalBeforeAfterImg.style.display = 'block';
                        if (modalBeforeAfterPlaceholder) modalBeforeAfterPlaceholder.style.display = 'none';
                        if (modalBeforeAfterBoxes) modalBeforeAfterBoxes.innerHTML = overlayBoxesHtml;
                    }
                } else {
                    if (modalBeforeAfterImg) modalBeforeAfterImg.style.display = 'none';
                    if (modalBeforeAfterPlaceholder) modalBeforeAfterPlaceholder.style.display = 'flex';
                    if (modalBeforeAfterBoxes) modalBeforeAfterBoxes.innerHTML = '';
                }
            }

            modal.style.setProperty('display', 'flex', 'important');
            document.body.style.overflow = 'hidden';
        };

        window.closeSoilRecordModal = function() {
            const modal = document.getElementById('soilRecordModal');
            if (modal) modal.style.setProperty('display', 'none', 'important');
            document.body.style.overflow = '';
            if (modalSoilDetailMapInstance) {
                try { modalSoilDetailMapInstance.remove(); } catch(e) {}
                modalSoilDetailMapInstance = null;
            }
            const menu = document.getElementById('modalLayerDropdownMenu');
            if (menu) menu.style.display = 'none';
            const chevron = document.getElementById('modalDominantChevron');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        };

        window.closeWebsiteSoilDetailModal = window.closeSoilRecordModal;

        window.downloadSoilRecordHardCopy = function() {
            const btn = document.querySelector('#soilRecordModal .btn-save-hardcopy');
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = `
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Saved Copy ✓</span>
                `;
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }, 2200);
            }

            const title = document.getElementById('modalRecordTitle')?.textContent || 'Soil Record Details';
            const code = document.getElementById('modalCardFullCode')?.textContent || '—';
            const name = document.getElementById('modalCardColorName')?.textContent || '—';
            const date = document.getElementById('modalCardDate')?.textContent || '—';
            const desc = document.getElementById('modalCardDesc')?.textContent || '—';
            const summaryText = `AgriCo Soil Record\nTitle: ${title}\nSoil Name: ${name}\nMunsell Code: ${code}\nDate: ${date}\nDescription: ${desc}`;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(summaryText).catch(() => {});
            }
        };

        // Force Leaflet to recalculate container size & render tiles instantly without blank canvas
        setTimeout(() => {
            map.invalidateSize();
        }, 200);

        window.addEventListener('resize', () => {
            map.invalidateSize();
        });
    </script>

    {{-- Unified Single Source of Truth Soil Record Details Pop-up Modal --}}
    @include('website.partials.soil-record-modal')

</x-website-layout>
