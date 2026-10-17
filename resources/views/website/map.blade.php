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
        .agrico-custom-popup a.leaflet-popup-close-button:active {
            transform: scale(0.95) !important;
        }

        /* Soil Record Details Modal (Matching Screenshots 1, 2, 3, 4) */
        .ws-soil-detail-backdrop {
            position: fixed;
            inset: 0;
            z-index: 100001;
            background: rgba(5, 31, 32, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: none !important;
            align-items: center;
            justify-content: center;
            padding: 16px;
            box-sizing: border-box;
            animation: wsMapFadeIn 0.2s ease-out;
        }
        .ws-soil-detail-backdrop.active {
            display: flex !important;
        }
        .soil-detail-modal-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 680px;
            margin: auto;
            display: flex;
            flex-direction: column;
            height: auto;
            max-height: min(85vh, calc(100dvh - 24px));
            overflow: hidden;
            position: relative;
        }
        .soil-detail-modal-header {
            padding: 20px 26px 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            flex-shrink: 0;
        }
        .soil-detail-modal-header h3 {
            margin: 0;
            font-size: 17.5px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
        }
        .soil-detail-modal-body {
            padding: 18px 24px 20px;
            overflow-y: auto;
            flex: 1 1 auto;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            gap: 12px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
            scrollbar-gutter: stable;
            -webkit-overflow-scrolling: touch;
        }
        .soil-detail-modal-body::-webkit-scrollbar {
            width: 6px;
            display: block !important;
        }
        .soil-detail-modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
            margin: 4px 0;
        }
        .soil-detail-modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .soil-detail-modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .modal-close-circle-btn {
            background: transparent !important;
            border: none !important;
            color: #94a3b8 !important;
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            min-height: 40px !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            padding: 0 !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            flex-shrink: 0 !important;
            box-shadow: none !important;
        }
        .modal-close-circle-btn:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border: none !important;
            transform: scale(1.06) !important;
        }
        .modal-close-circle-btn:active {
            transform: scale(0.95) !important;
        }

        /* Swatch Banner */
        .soil-swatch-banner {
            position: relative;
            margin-top: 2px;
            margin-bottom: 12px;
            width: 100%;
            border-radius: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 20px;
            box-sizing: border-box;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
            min-height: 78px;
        }
        .soil-swatch-avatar {
            width: 56px;
            height: 56px;
            min-width: 56px;
            aspect-ratio: 1 / 1;
            border-radius: 12px;
            border: none;
            outline: none;
            box-shadow: none;
            background: transparent;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
        }
        .soil-swatch-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 0;
        }
        .soil-swatch-center {
            flex: 1;
            min-width: 0;
        }
        .soil-swatch-code {
            font-size: 24px;
            font-weight: 900;
            color: #ffffff;
            text-shadow: none;
            letter-spacing: -0.01em;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .soil-swatch-sub {
            font-size: 11.5px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.95);
            margin-top: 3px;
            letter-spacing: 0.02em;
        }
        .soil-swatch-hex-pill {
            font-size: 11px;
            font-weight: 800;
            color: #ffffff;
            background: rgba(0, 0, 0, 0.58);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            padding: 5px 12px;
            border-radius: 20px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            border: 1px solid rgba(255, 255, 255, 0.25);
            user-select: none;
            line-height: 1;
            flex-shrink: 0;
            white-space: nowrap;
        }

        /* Grids for Metric Cards */
        .soil-modal-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        .soil-modal-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        .soil-metric-card {
            background: #ffffff;
            border: 1.5px solid #f1f5f9;
            border-radius: 16px;
            padding: 12px 16px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
            box-sizing: border-box;
        }
        .soil-metric-card-label {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 4px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .soil-metric-card-val {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
        }

        .covered-area-highlight {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            display: inline !important;
            color: #0f172a;
            font-size: 13.5px;
            font-weight: 700;
        }

        .layer-accordion-item {
            border: 1px solid #e2e8f0 !important;
            border-radius: 14px !important;
            overflow: hidden !important;
            background: #ffffff !important;
            margin-bottom: 8px;
        }
        .layer-accordion-header {
            background: #f8fafc !important;
            color: #0f172a !important;
        }
        .layer-accordion-body {
            background: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
        }

        .soil-detail-modal-footer {
            padding: 16px 24px 20px;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: flex-end;
            flex-shrink: 0;
            width: 100%;
            box-sizing: border-box;
        }
        .modal-cancel-btn {
            background: #f8fafc !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #334155 !important;
            padding: 10px 24px !important;
            border-radius: 9999px !important;
            font-weight: 700 !important;
            font-size: 13.5px !important;
            cursor: pointer;
            transition: all 0.2s ease;
            width: auto !important;
            text-align: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .modal-cancel-btn:hover {
            background: #e2e8f0 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .modal-cancel-btn:active {
            transform: translateY(0);
        }

        .soil-normalization-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid rgba(142, 182, 155, 0.25);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .soil-normalization-meta-title {
            color: #4ade80;
            font-weight: 800;
            letter-spacing: 0.05em;
        }
        .soil-normalization-meta-info {
            color: #CBD5E1;
            font-weight: 600;
        }

        .soil-swatch-capsule-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 13px;
            background: #0B2B26;
            border: 1px solid rgba(74, 222, 128, 0.25);
            border-radius: 9999px;
            box-shadow: none;
            font-size: 11.5px;
            cursor: default;
            user-select: none;
        }
        .soil-swatch-capsule-pill:hover {
            border-color: rgba(74, 222, 128, 0.35);
        }
        .soil-swatch-capsule-pill .capsule-pill-hex {
            color: #FFFFFF;
        }
        .soil-swatch-capsule-pill .capsule-pill-code {
            color: #8EB69B;
        }
        .soil-swatch-capsule-pill .capsule-pill-pct {
            color: #00DC82;
        }

        .soil-metric-card-text {
            color: #334155;
        }
        .layer-accordion-header-title {
            color: #0f172a;
        }
        .layer-accordion-arrow {
            color: #475569;
        }
        .soil-platform-badge {
            font-size: 12px;
            font-weight: 700;
            background: none !important;
            border: none !important;
            padding: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            color: #475569;
            display: inline !important;
        }

        /* Light Mode Overrides */
        html.light-mode .soil-detail-modal-container {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
        }
        html.light-mode .soil-detail-modal-header {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }
        html.light-mode .soil-detail-modal-header h3 {
            color: #0f172a;
        }
        html.light-mode .modal-close-circle-btn {
            background: transparent !important;
            border: 1.5px solid transparent !important;
            color: #94a3b8 !important;
        }
        html.light-mode .modal-close-circle-btn:hover {
            background: #f1f5f9 !important;
            border-color: #f1f5f9 !important;
            color: #0f172a !important;
            transform: scale(1.06) !important;
        }
        html.light-mode .soil-detail-modal-body {
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        html.light-mode .soil-metric-card {
            background: #ffffff;
            border: 1.5px solid #f1f5f9;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        }
        html.light-mode .soil-metric-card-label {
            color: #475569 !important;
            font-weight: 800 !important;
        }
        html.light-mode .soil-metric-card-val,
        html.light-mode .soil-metric-card-text,
        html.light-mode .layer-accordion-header-title,
        html.light-mode .layer-accordion-header-title span,
        html.light-mode .soil-metric-card [style*="#DAF1DE"],
        html.light-mode .soil-metric-card [style*="color: #DAF1DE"],
        html.light-mode .soil-metric-card [style*="color:#DAF1DE"],
        html.light-mode .layer-accordion-item [style*="#DAF1DE"],
        html.light-mode .layer-accordion-item [style*="color: #DAF1DE"],
        html.light-mode .layer-accordion-item [style*="color:#DAF1DE"],
        html.light-mode #wsSoilDetailModal [style*="#DAF1DE"],
        html.light-mode #wsSoilDetailModal [style*="color: #DAF1DE"],
        html.light-mode #wsSoilDetailModal [style*="color:#DAF1DE"] {
            color: #0f172a !important;
        }
        html.light-mode #wsSoilDetailModal [style*="#8EB69B"],
        html.light-mode #wsSoilDetailModal [style*="color: #8EB69B"],
        html.light-mode #wsSoilDetailModal [style*="color:#8EB69B"] {
            color: #475569 !important;
        }
        html.light-mode .layer-accordion-arrow,
        html.light-mode [id^="wsLayerArrow_"] {
            color: #475569 !important;
        }
        html.light-mode .soil-platform-badge {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            color: #000000 !important;
        }
        html.light-mode .soil-swatch-capsule-pill {
            background: #f8fafc !important;
            border: 1.5px solid #cbd5e1 !important;
            box-shadow: none !important;
            cursor: default;
            user-select: none;
        }
        html.light-mode .soil-swatch-capsule-pill:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            box-shadow: none !important;
            transform: none !important;
        }
        html.light-mode .soil-swatch-capsule-pill .capsule-pill-hex,
        html.light-mode .soil-swatch-capsule-pill span[style*="monospace"] {
            color: #0f172a !important;
        }
        html.light-mode .soil-swatch-capsule-pill .capsule-pill-code,
        html.light-mode .soil-swatch-capsule-pill span[style*="#8EB69B"] {
            color: #475569 !important;
        }
        html.light-mode .soil-swatch-capsule-pill .capsule-pill-pct,
        html.light-mode .soil-swatch-capsule-pill span[style*="#00DC82"] {
            color: #15803d !important;
        }
        html.light-mode .soil-swatch-capsule-pill .capsule-pill-swatch,
        html.light-mode .soil-swatch-capsule-pill span[style*="border-radius: 3.5px"] {
            border: 1px solid rgba(0, 0, 0, 0.15) !important;
            box-shadow: none !important;
        }
        html.light-mode .soil-normalization-meta-row {
            border-top: 1px solid #e2e8f0 !important;
        }
        html.light-mode .soil-normalization-meta-title {
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        html.light-mode .soil-normalization-meta-info {
            color: #475569 !important;
            font-weight: 600 !important;
        }
        html.light-mode .layer-accordion-item {
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
        }
        html.light-mode .layer-accordion-header {
            background: #f8fafc !important;
            color: #0f172a !important;
        }
        html.light-mode .layer-accordion-body {
            background: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
        }
        html.light-mode .soil-detail-modal-footer {
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
        }
        html.light-mode .modal-cancel-btn {
            background: #f1f5f9 !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #1e293b !important;
            border-radius: 12px !important;
            font-weight: 800 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.25s ease !important;
        }
        html.light-mode .modal-cancel-btn:hover {
            background: #e2e8f0 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        html.light-mode .modal-cancel-btn:active {
            transform: translateY(0);
        }
        html.light-mode .covered-area-highlight {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            color: #000000 !important;
            font-size: 13.5px;
            font-weight: 700;
        }

        #mapSearchInput {
            color: #DAF1DE;
        }

        /* Standard Pop-up Mobile Responsiveness for Website Soil Detail Modal */
        @media (max-width: 640px) {
            .ws-soil-detail-backdrop.active {
                padding: 16px 12px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                box-sizing: border-box !important;
            }
            .soil-detail-modal-container {
                width: 100% !important;
                max-width: min(calc(100vw - 32px), 420px) !important;
                height: auto !important;
                max-height: min(80vh, 590px) !important;
                border-radius: 24px !important;
                margin: auto !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2) !important;
            }
            .soil-detail-modal-header {
                padding: 16px 20px 14px !important;
            }
            .soil-detail-modal-header h3 {
                font-size: 17.5px !important;
                font-weight: 800 !important;
            }
            .soil-detail-modal-body {
                padding: 14px 16px 16px !important;
                gap: 10px !important;
                flex: 1 1 auto !important;
                max-height: none !important;
                overflow-y: auto !important;
            }
            .soil-swatch-banner {
                padding: 10px 14px !important;
                gap: 10px !important;
                min-height: 64px !important;
                border-radius: 16px !important;
                margin-bottom: 8px !important;
                flex-wrap: wrap !important;
            }
            .soil-swatch-avatar {
                width: 44px !important;
                height: 44px !important;
                min-width: 44px !important;
                border-radius: 10px !important;
            }
            .soil-swatch-code {
                font-size: 20px !important;
                font-weight: 900 !important;
            }
            .soil-swatch-hex-pill {
                font-size: 11px !important;
                padding: 4px 8px !important;
                white-space: nowrap !important;
            }
            .soil-metrics-container {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .soil-modal-grid-3,
            .soil-modal-grid-2 {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 8px !important;
                margin-bottom: 8px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .soil-metric-card {
                min-width: 0 !important;
                padding: 10px 12px !important;
                border-radius: 14px !important;
                box-sizing: border-box !important;
            }
            .soil-metric-card-label {
                font-size: 11px !important;
                margin-bottom: 3px !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.03em !important;
            }
            .soil-metric-card-val {
                font-size: 14px !important;
                line-height: 1.35 !important;
                font-weight: 700 !important;
                overflow-wrap: anywhere !important;
                word-break: break-word !important;
                min-width: 0 !important;
            }
            .soil-metric-card-val.truncate {
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            .soil-metric-card-text {
                font-size: 14px !important;
                overflow-wrap: anywhere !important;
                word-break: break-word !important;
                min-width: 0 !important;
            }
            .soil-detail-modal-footer {
                padding: 8px 14px 10px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: flex-end !important;
                flex-shrink: 0 !important;
            }
            .modal-cancel-btn {
                width: auto !important;
                padding: 4px 18px !important;
                border-radius: 9999px !important;
                font-size: 12px !important;
                font-weight: 700 !important;
                height: 32px !important;
                min-height: 32px !important;
                color: #475569 !important;
                background: #ffffff !important;
                border: 1.5px solid #e2e8f0 !important;
                box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
            }
        }

        @media (max-height: 520px) and (orientation: landscape) {
            .soil-detail-modal-container {
                width: 100% !important;
                max-width: 540px !important;
                height: auto !important;
                max-height: min(85vh, calc(100dvh - 20px)) !important;
                border-radius: 24px !important;
                margin: auto !important;
            }
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
        const mappingsData = @json($mappings);

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

        // View Details Modal Handler (Matching Screenshots 1, 2, 3, 4 without Save Copy / Pin Location buttons)
        window.viewWebsitePinDetails = function(locId, clusterKey = null) {
            const m = mappingsData.find(item => (item.location_mapping_id || item.id) == locId);
            if (!m) return;

            const modal = document.getElementById('websiteSoilDetailModal');
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
                    const pBox = normalizeBox(pMeta.box || (p.regions && p.regions[0] ? p.regions[0].box : null) || pMeta);

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
                        box: pBox,
                        region_thumbnail: p.region_thumbnail || pMeta.region_thumbnail || (p.regions && p.regions[0] ? p.regions[0].region_thumbnail : null),
                        image: p.scan_image_url || p.image || null
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
            const rawPct = dominant.percent !== undefined ? parseFloat(dominant.percent) : (dominant.proportion !== undefined ? parseFloat(dominant.proportion) * 100 : 100);
            const domPct = (rawPct % 1 !== 0) ? rawPct.toFixed(1) : Math.round(rawPct);
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

            // Update Header Title
            if (isCluster) {
                document.getElementById('wsModalHeaderTitle').textContent = `Soil Record Details (${regions.length} PINNED SOILS)`;
            } else if (isMulti) {
                document.getElementById('wsModalHeaderTitle').textContent = `Soil Record Details (${regions.length} DETECTED)`;
            } else {
                document.getElementById('wsModalHeaderTitle').textContent = 'Soil Record Details';
            }

            let bodyHtml = '';

            const domThumb = (dominant && dominant.region_thumbnail)
                ? (dominant.region_thumbnail.startsWith('data:') ? dominant.region_thumbnail : 'data:image/png;base64,' + dominant.region_thumbnail)
                : ((m.region_thumbnail) ? (m.region_thumbnail.startsWith('data:') ? m.region_thumbnail : 'data:image/png;base64,' + m.region_thumbnail) : '');
            const cleanScanUrl = scanImgUrl ? scanImgUrl.replace(/_thumb\.jpg$/i, '_normalized.jpg') : '';
            const effectiveAvatarUrl = cleanScanUrl || scanImgUrl || (dominant && dominant.image ? dominant.image : '') || domThumb;

            bodyHtml += `
                <div class="soil-swatch-banner" style="background: ${domHex};">
                    <div class="soil-swatch-avatar" style="position: relative; overflow: hidden; width: 68px; height: 68px; min-width: 68px; border-radius: 14px; background: rgba(0,0,0,0.18);">
                        ${effectiveAvatarUrl ? `
                            <img src="${effectiveAvatarUrl}" alt="Soil sample photo" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 14px;"
                                 onerror="
                                     var p = this.parentElement;
                                     this.style.display='none';
                                     var b = p.querySelector('.soil-bounding-box');
                                     if(b) b.style.display='none';
                                     var f = p.querySelector('.soil-avatar-placeholder');
                                     if(f) f.style.display='flex';
                                 " />
                            <div class="soil-bounding-box" style="position: absolute; left: ${domBox.left}%; top: ${domBox.top}%; width: ${domBox.width}%; height: ${domBox.height}%; border: 1.5px solid #4ade80; pointer-events: none; box-sizing: border-box; z-index: 5;"></div>
                        ` : ''}
                        <div class="soil-avatar-placeholder" style="display: ${effectiveAvatarUrl ? 'none' : 'flex'}; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; background: rgba(0,0,0,0.24); color: rgba(255,255,255,0.85); text-align: center; padding: 4px; box-sizing: border-box;" title="Image not available for this record">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.85; margin-bottom: 2px;">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span style="font-size: 7.5px; font-weight: 800; line-height: 1.05; letter-spacing: -0.02em; opacity: 0.95; text-transform: uppercase;">Image Not Available</span>
                        </div>
                    </div>
                    <div class="soil-swatch-center" style="min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span class="soil-swatch-code">${domCode}</span>
                        </div>
                        <div class="soil-swatch-sub" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Soil Percentage (${domPct}%)</div>
                    </div>
                    <span class="soil-swatch-hex-pill" style="white-space: nowrap; flex-shrink: 0;">${domHex.toUpperCase()}</span>
                </div>
            `;

            // 2. METRICS (Color Name, Full Code, Soil Organic, Soil Type, Scan Count, Near-Match)
            bodyHtml += `
                <div class="soil-metrics-container">
                    <!-- Row 1: Color Name & Full Code (2 cols) -->
                    <div class="soil-modal-grid-2">
                        <div class="soil-metric-card soil-card-color-name">
                            <div class="soil-metric-card-label">Color Name</div>
                            <div class="soil-metric-card-val truncate" title="${domName}">${domName}</div>
                        </div>
                        <div class="soil-metric-card soil-card-full-code">
                            <div class="soil-metric-card-label">Full Code</div>
                            <div class="soil-metric-card-val">${domCode}</div>
                        </div>
                    </div>

                    <!-- Row 2: Soil Organic & Soil Type (2 cols) -->
                    <div class="soil-modal-grid-2">
                        <div class="soil-metric-card soil-card-organic">
                            <div class="soil-metric-card-label">Soil Organic</div>
                            <div class="soil-metric-card-val">${domOrganic}</div>
                        </div>
                        <div class="soil-metric-card soil-card-soil-type">
                            <div class="soil-metric-card-label">Soil Type</div>
                            <div class="soil-metric-card-val truncate" title="${soilType}">${soilType}</div>
                        </div>
                    </div>

                    <!-- Row 3: Scan Count & Near-Match (2 cols) -->
                    <div class="soil-modal-grid-2">
                        <div class="soil-metric-card soil-card-scan-count">
                            <div class="soil-metric-card-label">Scan Count</div>
                            <div class="soil-metric-card-val">${scanCount}</div>
                        </div>
                        <div class="soil-metric-card soil-card-confidence">
                            <div class="soil-metric-card-label">Near-Match</div>
                            <div class="soil-metric-card-val" style="color: #10b981; font-weight: 800;">${domConf}</div>
                        </div>
                    </div>
                </div>
            `;

            // 5. SCAN DATE & SCANNED BY
            bodyHtml += `
                <div class="soil-modal-grid-2">
                    <div class="soil-metric-card">
                        <div class="soil-metric-card-label">Scan Date</div>
                        <div class="soil-metric-card-val">${recordedDate}</div>
                    </div>
                    <div class="soil-metric-card">
                        <div class="soil-metric-card-label">Scanned By</div>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; flex-wrap: wrap;">
                            <span class="soil-metric-card-val">${scannedBy}</span>
                            <span class="soil-platform-badge">
                                ${scanPlatform}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="soil-metric-card" style="margin-bottom: 10px;">
                    <div class="soil-metric-card-label">Scan Note</div>
                    <div class="soil-metric-card-text" style="font-size: 13px; font-weight: 700; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
                        ${noteText}
                    </div>
                </div>

                <div class="soil-metric-card" style="margin-bottom: 12px;">
                    <div class="soil-metric-card-label">Description</div>
                    <div class="soil-metric-card-text" style="font-size: 13px; font-weight: 700; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
                        ${isMulti ? domDesc : desc}
                    </div>
                </div>
            `;

            if (isMulti) {
                // 5. SECONDARY DETECTED SOIL LAYERS ACCORDIONS (Screenshot 3)
                bodyHtml += `
                    <div style="margin-bottom: 12px; border-top: 1px dashed rgba(142, 182, 155, 0.25); padding-top: 12px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                `;

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
                    const subBox = normalizeBox(sub);

                    bodyHtml += `
                        <div class="layer-accordion-item">
                            <button
                                type="button"
                                class="layer-accordion-header"
                                onclick="toggleWebsiteLayerAccordion(${idx})"
                                style="width: 100%; padding: 11px 14px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; border: none; text-align: left; font-family: inherit; user-select: none;"
                            >
                                <div class="layer-accordion-header-title" style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13px;">
                                    <span id="wsLayerArrow_${idx}" class="layer-accordion-arrow" style="font-size: 11px;">▶</span>
                                    <span>${subCode} (Soil Percentage - ${subPct}%)</span>
                                </div>
                                <span style="font-size: 11px; font-weight: 800; color: #ffffff; background: ${subHex}; padding: 3px 10px; border-radius: 12px; font-family: monospace; text-shadow: none;">
                                    ${subHex.toUpperCase()}
                                </span>
                            </button>

                            <div id="wsLayerBody_${idx}" class="layer-accordion-body" style="display: none; padding: 14px;">
                                <!-- Swatch Banner with ROI Crop Box -->
                                <div class="soil-swatch-banner" style="background: ${subHex}; margin-bottom: 10px; min-height: 64px; padding: 12px 16px;">
                                    ${(() => {
                                        const cleanSubUrl = scanImgUrl ? scanImgUrl.replace(/_thumb\.jpg$/i, '_normalized.jpg') : '';
                                        const subAvatarUrl = cleanSubUrl || scanImgUrl || (sub && sub.image ? sub.image : '') || ((sub && sub.region_thumbnail) ? (sub.region_thumbnail.startsWith('data:') ? sub.region_thumbnail : 'data:image/png;base64,' + sub.region_thumbnail) : '');
                                        return subAvatarUrl ? `
                                            <div class="soil-swatch-avatar" style="width: 48px; height: 48px; min-width: 48px; position: relative; border-radius: 8px; overflow: hidden; background: rgba(0,0,0,0.18);">
                                                 <img src="${subAvatarUrl}" alt="Soil Sample" style="width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 8px;"
                                                      onerror="
                                                          this.style.display='none';
                                                          var b = this.parentElement.querySelector('.soil-bounding-box');
                                                          if(b) b.style.display='none';
                                                      " />
                                                 <div class="soil-bounding-box" style="position: absolute; left: ${subBox.left}%; top: ${subBox.top}%; width: ${subBox.width}%; height: ${subBox.height}%; border: 1.5px solid #4ade80; pointer-events: none; box-sizing: border-box; z-index: 5;"></div>
                                            </div>
                                        ` : '';
                                    })()}
                                    <div class="soil-swatch-center">
                                        <span class="soil-swatch-code" style="font-size: 20px;">${subCode}</span>
                                        <div class="soil-swatch-sub">Soil Percentage (${subPct}%)</div>
                                    </div>
                                    <span class="soil-swatch-hex-pill">${subHex.toUpperCase()}</span>
                                </div>

                                <!-- Sub-Layer Metrics (Color Name, Full Code, Soil Organic, Soil Type, Proportion, Near-Match) -->
                                <div class="soil-metrics-container">
                                    <!-- Row 1: Color Name & Full Code (2 cols) -->
                                    <div class="soil-modal-grid-2">
                                        <div class="soil-metric-card soil-card-color-name">
                                            <div class="soil-metric-card-label">Color Name</div>
                                            <div class="soil-metric-card-val truncate" title="${subName}">${subName}</div>
                                        </div>
                                        <div class="soil-metric-card soil-card-full-code">
                                            <div class="soil-metric-card-label">Full Code</div>
                                            <div class="soil-metric-card-val">${subCode}</div>
                                        </div>
                                    </div>

                                    <!-- Row 2: Soil Organic & Soil Type (2 cols) -->
                                    <div class="soil-modal-grid-2">
                                        <div class="soil-metric-card soil-card-organic">
                                            <div class="soil-metric-card-label">Soil Organic</div>
                                            <div class="soil-metric-card-val">${subOrganic}</div>
                                        </div>
                                        <div class="soil-metric-card soil-card-soil-type">
                                            <div class="soil-metric-card-label">Soil Type</div>
                                            <div class="soil-metric-card-val truncate" title="${subSoilType}">${subSoilType}</div>
                                        </div>
                                    </div>

                                    <!-- Row 3: Proportion & Near-Match (2 cols) -->
                                    <div class="soil-modal-grid-2">
                                        <div class="soil-metric-card soil-card-proportion">
                                            <div class="soil-metric-card-label">Proportion</div>
                                            <div class="soil-metric-card-val">${subPct}%</div>
                                        </div>
                                        <div class="soil-metric-card soil-card-confidence">
                                            <div class="soil-metric-card-label">Near-Match</div>
                                            <div class="soil-metric-card-val" style="color: #10b981; font-weight: 800;">${subConf}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 3: Scan Note -->
                                <div class="soil-metric-card" style="margin-bottom: 10px;">
                                    <div class="soil-metric-card-label">Scan Note</div>
                                    <div class="soil-metric-card-text" style="font-size: 13px; font-weight: 700; line-height: 1.5;">
                                        No scan note available for this record.
                                    </div>
                                </div>

                                <!-- Row 4: Description -->
                                <div class="soil-metric-card">
                                    <div class="soil-metric-card-label">Description</div>
                                    <div class="soil-metric-card-text" style="font-size: 13px; font-weight: 700; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
                                        ${subDesc}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                bodyHtml += `
                        </div>
                    </div>
                `;
            }

            // 6. SOIL NORMALIZATION: BEFORE / AFTER CARD (Matching Screenshot 1)
            // USER CONSTRAINT: "bat remove the Button Save Copy and Pin Location"
            // 6. SOIL NORMALIZATION: BEFORE / AFTER CARD (Matching Screenshot 1)
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

            bodyHtml += `
                <div class="soil-metric-card" style="margin-bottom: 12px; padding: 13px 16px 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div class="soil-metric-card-label" style="margin-bottom: 0;">
                            SOIL NORMALIZATION
                        </div>
                        <span style="font-size: 9.5px; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: rgba(16, 185, 129, 0.15); color: #10b981; text-transform: uppercase; letter-spacing: 0.04em;">
                            BEFORE / AFTER
                        </span>
                    </div>
                    <div class="soil-norm-container" style="position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #0c1a17; border: 1px solid rgba(142, 182, 155, 0.25);">
                        ${baImgUrl ? `
                            <img src="${baImgUrl}" alt="Soil Before & After Normalization" loading="lazy" style="width: 100%; height: auto; display: block; border-radius: 8px;"
                                 onerror="
                                     var p = this.parentElement;
                                     this.style.display = 'none';
                                     var o = p.querySelector('.soil-norm-overlays');
                                     if (o) o.style.display = 'none';
                                     var fb = p.querySelector('.soil-norm-missing-placeholder');
                                     if (fb) fb.style.display = 'flex';
                                 " />
                            <div class="soil-norm-overlays" style="position: absolute; inset: 0; pointer-events: none;">
                                ${overlayBoxesHtml}
                            </div>
                        ` : ''}
                        <div class="soil-norm-missing-placeholder" style="display: ${baImgUrl ? 'none' : 'flex'}; flex-direction: column; align-items: center; justify-content: center; padding: 28px 16px; background: rgba(12, 26, 23, 0.95); text-align: center; border-radius: 8px;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 8px;">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span style="font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.85); letter-spacing: 0.02em;">Image not available</span>
                            <span style="font-size: 10.5px; color: rgba(255,255,255,0.5); margin-top: 3px;">Normalization comparison image was not retained for this record</span>
                        </div>
                    </div>

                    <!-- Detected Soil Color Swatch Pills (Matching Screenshot 2 Capsule Design) -->
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px;">
                        ${swatchPillsHtml}
                    </div>

                    <!-- METADATA Line (High Contrast for Dark & Light Mode) -->
                    <div class="soil-normalization-meta-row">
                        <div><strong class="soil-normalization-meta-title">METADATA</strong></div>
                        <div class="soil-normalization-meta-info">CREATED: ${recordedDate} &nbsp;|&nbsp; DETECTED COLORS: ${regions.length}</div>
                    </div>
                </div>
            `;

            document.getElementById('wsModalBodyContent').innerHTML = bodyHtml;

            modal.classList.add('active');
            modal.style.setProperty('display', 'flex', 'important');
            document.body.style.overflow = 'hidden';
        };

        window.closeWebsiteSoilDetailModal = function() {
            const modal = document.getElementById('websiteSoilDetailModal');
            if (modal) {
                modal.classList.remove('active');
                modal.style.setProperty('display', 'none', 'important');
            }
            document.body.style.overflow = '';
        };

        // Force Leaflet to recalculate container size & render tiles instantly without blank canvas
        setTimeout(() => {
            map.invalidateSize();
        }, 200);

        window.addEventListener('resize', () => {
            map.invalidateSize();
        });
    </script>

    <!-- Website Soil Detail Modal (Matching Screenshots 1, 2, 3, 4 without Save Copy or Pin Location buttons) -->
    <div id="websiteSoilDetailModal" class="ws-soil-detail-backdrop" style="display: none !important;" onclick="if(event.target === this) closeWebsiteSoilDetailModal()">
        <div class="soil-detail-modal-container">
            <!-- Header (Screenshot 2 & 4) -->
            <div class="soil-detail-modal-header">
                <h3 id="wsModalHeaderTitle">Soil Record Details</h3>
                <x-close-button onclick="closeWebsiteSoilDetailModal()" title="Close details" ariaLabel="Close details" />
            </div>

            <!-- Scrollable Body -->
            <div id="wsModalBodyContent" class="soil-detail-modal-body">
                <!-- Content dynamically populated via viewWebsitePinDetails -->
            </div>

            <!-- Footer -->
            <div class="soil-detail-modal-footer">
                <button type="button" class="modal-cancel-btn" onclick="closeWebsiteSoilDetailModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

</x-website-layout>
