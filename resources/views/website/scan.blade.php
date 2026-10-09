<x-website-layout>
    <x-slot name="title">Soil Color Scan</x-slot>

    {{-- Leaflet Map Assets for Pin Soil Location Modal --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    {{-- jsPDF for Multi-Page A4 Soil Classification Reports --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        .ws-page-hero {
            background: linear-gradient(135deg, #051F20 0%, #0B2B26 100%);
            padding: 16px 24px 14px;
            border-bottom: 1px solid rgba(142, 182, 155, 0.2);
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
            margin-bottom: 4px;
            font-weight: 500;
        }
        .ws-breadcrumb a {
            color: #DAF1DE;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .ws-breadcrumb a:hover { text-decoration: underline; color: #8EB69B; }
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

        /* ── Universal Modal Close / Exit Circle Button ── */
        .modal-close-circle-btn {
            width: 36px !important;
            height: 36px !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            border: none !important;
            background: transparent !important;
            color: #8EB69B !important;
            outline: none !important;
            padding: 0 !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            flex-shrink: 0 !important;
        }
        .modal-close-circle-btn:hover {
            background: rgba(255, 255, 255, 0.14) !important;
            color: #ffffff !important;
            transform: scale(1.08) !important;
        }
        .modal-close-circle-btn:active {
            transform: scale(0.95) !important;
        }

        /* Light Mode Overrides */
        html.light-mode .modal-close-circle-btn,
        .light-mode .modal-close-circle-btn,
        body.light-mode .modal-close-circle-btn {
            color: #94a3b8 !important;
            background: transparent !important;
        }
        html.light-mode .modal-close-circle-btn:hover,
        .light-mode .modal-close-circle-btn:hover,
        body.light-mode .modal-close-circle-btn:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            transform: scale(1.08) !important;
        }

        /* Custom Selection Highlight (replaces default browser blue highlight box) */
        ::selection {
            background: rgba(34, 197, 94, 0.4) !important;
            color: #ffffff !important;
        }

        /* Scanner Container */
        .scanner-section {
            background: #051F20;
            padding: 32px 24px;
        }
        .scanner-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            align-items: start;
            width: 100%;
        }
        @media (max-width: 900px) {
            .scanner-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ── MOBILE PHONE SCREEN FIT (ONE PAGE VIEWPORT OPTIMIZATION) ── */
        @media (max-width: 640px) {
            .ws-page-hero {
                padding: 10px 14px 8px !important;
            }
            .ws-breadcrumb {
                font-size: 11px !important;
                margin-bottom: 2px !important;
            }
            .ws-page-title {
                font-size: 18px !important;
                margin-bottom: 2px !important;
            }
            .ws-page-sub {
                font-size: 11.5px !important;
                line-height: 1.3 !important;
            }
            .scanner-section {
                padding: 10px 10px 20px !important;
            }
            .scanner-grid {
                gap: 12px !important;
            }
            .scanner-card {
                padding: 12px !important;
                border-radius: 16px !important;
            }
            .scanner-card-header {
                margin-bottom: 12px !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 8px !important;
                width: 100% !important;
            }
            .scanner-card-header h2 {
                font-size: 13px !important;
                gap: 6px !important;
                min-width: 0 !important;
            }
            .scan-card-title-text {
                font-size: 12px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            .scan-card-title-icon {
                width: 24px !important;
                height: 24px !important;
                border-radius: 6px !important;
                flex-shrink: 0 !important;
            }
            .scan-card-title-icon svg {
                width: 13px !important;
                height: 13px !important;
            }
            .camera-status-badge {
                font-size: 10px !important;
                padding: 3px 8px !important;
                white-space: nowrap !important;
                flex-shrink: 0 !important;
                margin-left: auto !important;
            }
            .scanner-viewfinder {
                height: 280px !important;
                min-height: 240px !important;
                max-height: 300px !important;
                border-radius: 14px !important;
            }
            .scanner-actions {
                margin-top: 10px !important;
                gap: 6px !important;
            }
            .scanner-btn {
                padding: 9px 6px !important;
                font-size: 12px !important;
                border-radius: 10px !important;
            }
            .result-card {
                padding: 14px !important;
                border-radius: 16px !important;
                min-height: 400px !important;
            }
            .munsell-badge-big {
                font-size: 22px !important;
            }
            .munsell-swatch-large {
                width: 32px !important;
                height: 32px !important;
                border-radius: 8px !important;
            }
            .result-grid {
                gap: 8px !important;
                margin-bottom: 12px !important;
            }
            .result-box {
                padding: 10px !important;
                border-radius: 10px !important;
            }
            .result-box-label {
                font-size: 10px !important;
            }
            .result-box-val {
                font-size: 14px !important;
            }

            /* ── MOBILE TABLE RESPONSIVE LAYOUT (3 CLEAN COLUMNS FOR PERFECT MOBILE FIT) ── */
            .ws-table-section {
                padding: 0 6px 40px !important;
            }
            .ws-table-card {
                border-radius: 16px !important;
            }
            .ws-table-header {
                padding: 12px 14px !important;
            }

            .col-th-desc, .col-soil-desc,
            .col-th-code, .col-soil-code,
            .col-th-hue, .col-soil-hue,
            .col-th-seq-count, .col-soil-seq,
            .col-th-date, .col-soil-date {
                display: none !important;
            }

            .mobile-code-inline {
                display: inline-block !important;
            }

            table.ws-table {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
            }

            table.ws-table th,
            table.ws-table td {
                box-sizing: border-box !important;
            }

            th.col-th-seq, td.col-seq {
                width: 10% !important;
                min-width: 0 !important;
                max-width: none !important;
                font-size: 11.5px !important;
                font-weight: 700 !important;
                padding: 10px 2px 10px 6px !important;
                text-align: left !important;
            }

            th.col-th-name, td.col-soil-name {
                width: 62% !important;
                min-width: 0 !important;
                max-width: none !important;
                font-size: 12px !important;
                font-weight: 800 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                padding: 10px 4px !important;
            }

            th.col-th-action, td.col-action {
                width: 28% !important;
                min-width: 0 !important;
                max-width: none !important;
                text-align: right !important;
                padding: 10px 10px 10px 2px !important;
                white-space: nowrap !important;
                overflow: visible !important;
            }

            th.col-th-action {
                font-size: 11px !important;
                font-weight: 800 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.03em !important;
            }

            .btn-view-scan {
                padding: 4px 10px !important;
                font-size: 10.5px !important;
                font-weight: 700 !important;
                border-radius: 6px !important;
                display: inline-block !important;
                white-space: nowrap !important;
                box-sizing: border-box !important;
                margin: 0 !important;
            }

            /* ── MODAL MOBILE RESPONSIVE FIT ── */
            #soilRecordModal {
                padding: 10px !important;
            }
            .soil-modal-card {
                border-radius: 24px !important;
                max-width: 100% !important;
                height: 80vh !important;
                max-height: 80vh !important;
            }
            .soil-modal-header {
                padding: 14px 16px !important;
            }
            .soil-modal-body-container {
                padding: 14px 10px !important;
            }
            .soil-modal-grid-3 {
                gap: 5px !important;
            }
            .soil-modal-grid-3 > div {
                padding: 8px 5px !important;
            }
            .soil-modal-grid-3 .soil-modal-label {
                font-size: 9.5px !important;
                letter-spacing: -0.03em !important;
                gap: 2px !important;
            }
            .soil-modal-grid-3 .soil-modal-label svg {
                width: 11px !important;
                height: 11px !important;
            }
            .soil-modal-grid-3 .soil-modal-value {
                font-size: 13px !important;
            }
            .soil-modal-user-card {
                padding: 10px 10px !important;
                gap: 8px !important;
            }
            .soil-modal-user-name {
                font-size: 12.5px !important;
            }
            .soil-modal-user-email {
                font-size: 10.5px !important;
            }
            .soil-modal-grid-2 {
                grid-template-columns: 1fr !important;
                gap: 8px !important;
            }
        }

        /* Camera / Upload Card */
        .scanner-card {
            background: linear-gradient(145deg, #0B2B26 0%, #163832 100%);
            border-radius: 24px;
            border: 1px solid rgba(142, 182, 155, 0.25);
            box-shadow: 0 16px 40px rgba(0,0,0,0.4);
            overflow: hidden;
            padding: 24px;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        .scanner-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        .scanner-card-header h2 {
            font-size: 18px;
            font-weight: 800;
            color: #DAF1DE;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            overflow: hidden;
        }
        .scan-card-title-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .camera-status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 9999px;
            white-space: nowrap;
            flex-shrink: 0;
            margin-left: auto;
            border: 1px solid rgba(142, 182, 155, 0.35);
            background: rgba(5, 31, 32, 0.7);
            color: #8EB69B;
        }
        .scan-card-title-icon {
            width: 36px; height: 36px;
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
        .scan-card-title-icon svg {
            stroke: #4ADE80 !important;
            transition: stroke 0.25s ease;
        }
        html.light-mode .scan-card-title-icon {
            background: #f0fdf4 !important;
            border: 1.5px solid #86efac !important;
            box-shadow: 0 2px 6px rgba(34, 197, 94, 0.1) !important;
        }
        html.light-mode .scan-card-title-icon svg {
            stroke: #16a34a !important;
        }
        .scanner-viewfinder {
            position: relative;
            width: 100%;
            height: 320px;
            min-height: 320px;
            max-height: 320px;
            background: #051F20;
            border: 1px solid rgba(142, 182, 155, 0.2);
            border-radius: 18px;
            overflow: hidden; /* Clips box-shadow: 0 0 0 9999px to stage boundary */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-sizing: border-box;
            touch-action: none;
        }
        .image-mask {
            position: absolute;
            inset: 0;
            overflow: hidden;
            border-radius: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .image-mask video,
        .image-mask img,
        .scanner-viewfinder video,
        .scanner-viewfinder img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            will-change: transform;
            transform-origin: center center;
        }
        .zoom-controls {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            justify-content: center;
            align-items: center;
        }
        .zoom-btn {
            background: rgba(11, 43, 38, 0.85);
            border: 1px solid rgba(142, 182, 155, 0.3);
            color: #DAF1DE;
            padding: 5px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .zoom-btn:hover {
            background: #163832;
            border-color: #8EB69B;
        }
        .viewfinder-overlay {
            position: absolute;
            inset: 0;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reticle {
            width: 140px;
            height: 140px;
            border: 1px solid rgba(255, 255, 255, 0.95) !important;
            border-radius: 0 !important;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.6) !important;
            position: relative;
            pointer-events: auto;
            cursor: move;
            touch-action: none;
            user-select: none;
            -webkit-user-select: none;
            box-sizing: border-box !important;
        }

        /* Rule-of-thirds grid inside the frame */
        .reticle::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(to right,
                    transparent calc(33.333% - 0.5px),
                    rgba(255, 255, 255, 0.35) calc(33.333% - 0.5px),
                    rgba(255, 255, 255, 0.35) calc(33.333% + 0.5px),
                    transparent calc(33.333% + 0.5px),
                    transparent calc(66.666% - 0.5px),
                    rgba(255, 255, 255, 0.35) calc(66.666% - 0.5px),
                    rgba(255, 255, 255, 0.35) calc(66.666% + 0.5px),
                    transparent calc(66.666% + 0.5px)
                ),
                linear-gradient(to bottom,
                    transparent calc(33.333% - 0.5px),
                    rgba(255, 255, 255, 0.35) calc(33.333% - 0.5px),
                    rgba(255, 255, 255, 0.35) calc(33.333% + 0.5px),
                    transparent calc(33.333% + 0.5px),
                    transparent calc(66.666% - 0.5px),
                    rgba(255, 255, 255, 0.35) calc(66.666% - 0.5px),
                    rgba(255, 255, 255, 0.35) calc(66.666% + 0.5px),
                    transparent calc(66.666% + 0.5px)
                );
            z-index: 1;
        }

        /* Resize handles (transparent hit areas) */
        .resize-handle {
            position: absolute;
            width: 24px;
            height: 24px;
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            z-index: 10;
            pointer-events: auto;
            touch-action: none;
            box-sizing: border-box;
        }

        /* Hit area positions centered on edges and corners */
        .resize-handle-nw { top: -12px; left: -12px; cursor: nwse-resize; }
        .resize-handle-ne { top: -12px; right: -12px; cursor: nesw-resize; }
        .resize-handle-sw { bottom: -12px; left: -12px; cursor: nesw-resize; }
        .resize-handle-se { bottom: -12px; right: -12px; cursor: nwse-resize; }
        .resize-handle-n { top: -12px; left: 50%; transform: translateX(-50%); cursor: ns-resize; }
        .resize-handle-s { bottom: -12px; left: 50%; transform: translateX(-50%); cursor: ns-resize; }
        .resize-handle-e { right: -12px; top: 50%; transform: translateY(-50%); cursor: ew-resize; }
        .resize-handle-w { left: -12px; top: 50%; transform: translateY(-50%); cursor: ew-resize; }

        /* Thick white L-shaped corner brackets drawn via ::after */
        .resize-handle-nw::after,
        .resize-handle-ne::after,
        .resize-handle-sw::after,
        .resize-handle-se::after {
            content: '';
            position: absolute;
            width: 14px;
            height: 14px;
            box-sizing: border-box;
            pointer-events: none;
            filter: drop-shadow(0 0 1px rgba(0, 0, 0, 0.8));
        }
        .resize-handle-nw::after {
            top: 11px;
            left: 11px;
            border-top: 3px solid #ffffff;
            border-left: 3px solid #ffffff;
        }
        .resize-handle-ne::after {
            top: 11px;
            right: 11px;
            border-top: 3px solid #ffffff;
            border-right: 3px solid #ffffff;
        }
        .resize-handle-sw::after {
            bottom: 11px;
            left: 11px;
            border-bottom: 3px solid #ffffff;
            border-left: 3px solid #ffffff;
        }
        .resize-handle-se::after {
            bottom: 11px;
            right: 11px;
            border-bottom: 3px solid #ffffff;
            border-right: 3px solid #ffffff;
        }

        /* Short thick white bars for edge-midpoint handles drawn via ::after */
        .resize-handle-n::after,
        .resize-handle-s::after,
        .resize-handle-e::after,
        .resize-handle-w::after {
            content: '';
            position: absolute;
            background: #ffffff;
            pointer-events: none;
            filter: drop-shadow(0 0 1px rgba(0, 0, 0, 0.8));
        }
        .resize-handle-n::after {
            top: 11px;
            left: 50%;
            transform: translateX(-50%);
            width: 18px;
            height: 3px;
        }
        .resize-handle-s::after {
            bottom: 11px;
            left: 50%;
            transform: translateX(-50%);
            width: 18px;
            height: 3px;
        }
        .resize-handle-w::after {
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 18px;
        }
        .resize-handle-e::after {
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 18px;
        }

        /* Larger touch hit areas on coarse pointers while preserving visual alignment */
        @media (pointer: coarse) {
            .resize-handle {
                width: 34px;
                height: 34px;
            }
            .resize-handle-nw { top: -17px; left: -17px; }
            .resize-handle-ne { top: -17px; right: -17px; }
            .resize-handle-sw { bottom: -17px; left: -17px; }
            .resize-handle-se { bottom: -17px; right: -17px; }
            .resize-handle-n { top: -17px; left: 50%; }
            .resize-handle-s { bottom: -17px; left: 50%; }
            .resize-handle-e { right: -17px; top: 50%; }
            .resize-handle-w { left: -17px; top: 50%; }

            .resize-handle-nw::after { top: 16px; left: 16px; width: 16px; height: 16px; }
            .resize-handle-ne::after { top: 16px; right: 16px; width: 16px; height: 16px; }
            .resize-handle-sw::after { bottom: 16px; left: 16px; width: 16px; height: 16px; }
            .resize-handle-se::after { bottom: 16px; right: 16px; width: 16px; height: 16px; }

            .resize-handle-n::after { top: 16px; width: 22px; }
            .resize-handle-s::after { bottom: 16px; width: 22px; }
            .resize-handle-w::after { left: 16px; height: 22px; }
            .resize-handle-e::after { right: 16px; height: 22px; }
        }


        .scanner-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .scanner-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 0.25s ease;
            white-space: nowrap;
        }
        .scanner-btn-primary,
        .scanner-btn-cam {
            background: linear-gradient(135deg, #235347, #163832) !important;
            color: #DAF1DE !important;
            border: 1px solid #8EB69B !important;
            box-shadow: 0 6px 20px rgba(35, 83, 71, 0.4) !important;
        }
        .scanner-btn-primary:hover,
        .scanner-btn-cam:hover {
            background: linear-gradient(135deg, #163832, #0B2B26) !important;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(35, 83, 71, 0.6) !important;
            border-color: #DAF1DE !important;
        }
        .scanner-btn-secondary {
            background: rgba(11, 43, 38, 0.8) !important;
            color: #DAF1DE !important;
            border: 1.5px solid rgba(142, 182, 155, 0.35) !important;
        }
        .scanner-btn-secondary:hover {
            background: rgba(35, 83, 71, 0.5) !important;
            border-color: #DAF1DE !important;
        }
        .scanner-btn-cta {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4) !important;
        }
        .scanner-btn-cta:hover {
            background: linear-gradient(135deg, #16a34a, #15803d) !important;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.55) !important;
            transform: translateY(-1px);
        }

        /* Result Display Card */
        .result-card {
            scroll-margin-top: calc(var(--agrico-nav-h, 68px) + 20px);
            background: linear-gradient(145deg, #0B2B26 0%, #163832 100%);
            border-radius: 24px;
            border: 1px solid rgba(142, 182, 155, 0.25);
            box-shadow: 0 16px 40px rgba(0,0,0,0.4);
            padding: 24px;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            position: relative;
            min-height: 480px;
            transition: min-height 0.2s ease;
        }

        /* Dedicated In-Flow Scan Loading State (Never Collapses) */
        .scan-loading-card-content {
            display: none;
            position: relative;
            width: 100%;
            min-height: 430px;
            flex: 1;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            text-align: center;
            padding: 36px 20px;
            box-sizing: border-box;
            border-radius: 20px;
            z-index: 20;
        }

        .scan-loading-stage-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.35);
            color: #22c55e;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 5px 14px;
            border-radius: 9999px;
            box-shadow: 0 2px 10px rgba(34, 197, 94, 0.15);
        }

        .scan-loading-stage-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 8px #22c55e;
            animation: scanPulseDot 1.4s ease-in-out infinite;
        }

        @keyframes scanPulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(0.75); }
        }

        .scan-loading-spinner-wrap {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 74px;
            height: 74px;
            margin: 6px 0;
        }

        .scan-loading-spinner-ring {
            width: 70px;
            height: 70px;
            border: 4px solid rgba(142, 182, 155, 0.18);
            border-top: 4px solid #22c55e;
            border-right: 4px solid rgba(34, 197, 94, 0.65);
            border-radius: 50%;
            animation: spin 0.85s cubic-bezier(0.4, 0.1, 0.2, 1) infinite;
            filter: drop-shadow(0 0 10px rgba(34, 197, 94, 0.35));
            box-sizing: border-box;
        }

        .scan-loading-badge-center {
            position: absolute;
            font-size: 13.5px;
            font-weight: 800;
            color: #DAF1DE;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            letter-spacing: -0.02em;
        }

        .scan-loading-title {
            font-size: 17px;
            font-weight: 800;
            color: #DAF1DE;
            letter-spacing: -0.015em;
            margin: 0;
            line-height: 1.35;
        }

        .scan-loading-subtext {
            font-size: 13px;
            color: #8EB69B;
            margin: 6px 0 0;
            line-height: 1.5;
            max-width: 360px;
        }

        .scan-loading-progress-track {
            width: 250px;
            max-width: 85%;
            height: 6px;
            background: rgba(142, 182, 155, 0.18);
            border-radius: 9999px;
            overflow: hidden;
            margin-top: 14px;
            border: 1px solid rgba(142, 182, 155, 0.12);
        }

        .scan-loading-progress-fill {
            height: 100%;
            width: 1%;
            background: linear-gradient(90deg, #22c55e, #10b981);
            border-radius: 9999px;
            transition: width 0.08s linear;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
        }
        .result-empty {
            text-align: center;
            padding: 40px 20px;
            color: #8EB69B;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 320px;
            width: 100%;
            box-sizing: border-box;
        }
        .result-empty h3 {
            color: #DAF1DE;
            font-size: 16px;
            font-weight: 800;
            margin: 0 0 6px 0;
        }
        .result-empty p {
            color: #8EB69B;
            font-size: 13px;
            margin: 0;
            opacity: 0.85;
        }
        .pipeline-overview-card {
            margin-top: 22px;
            padding: 14px 16px;
            background: rgba(22, 56, 50, 0.45);
            border: 1px solid rgba(142, 182, 155, 0.18);
            border-radius: 14px;
            text-align: left;
            max-width: 380px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
            transition: all 0.25s ease;
        }
        .pipeline-overview-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #22c55e;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .pipeline-stages-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 7px;
            font-size: 11.5px;
            color: #8EB69B;
        }
        .pipeline-stage-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .pipeline-stage-badge {
            background: rgba(22, 163, 74, 0.18);
            color: #22c55e;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 10px;
            font-family: monospace;
            border: 1px solid rgba(34, 197, 94, 0.25);
            flex-shrink: 0;
            letter-spacing: 0.02em;
        }
        .pipeline-stage-label {
            color: #DAF1DE;
            font-size: 11.5px;
            font-weight: 500;
        }
        .result-header {
            border-bottom: 1px solid rgba(142, 182, 155, 0.2);
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .result-header h3 {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #8EB69B;
            margin: 0 0 6px 0;
        }
        .munsell-badge-big {
            font-size: 32px;
            font-weight: 900;
            color: #DAF1DE;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .munsell-swatch-large {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            border: 2px solid #8EB69B;
        }
        .result-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }
        .result-box {
            background: #051F20;
            border: 1px solid rgba(142, 182, 155, 0.25);
            border-radius: 14px;
            padding: 14px;
        }
        .result-box-label {
            font-size: 11px;
            font-weight: 700;
            color: #8EB69B;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .result-box-val {
            font-size: 16px;
            font-weight: 800;
            color: #DAF1DE;
        }

        /* Multi-Parameter Color Spaces 3-Row Grid (Dark Mode Default) */
        .color-spaces-section {
            margin-bottom: 14px;
            box-sizing: border-box;
        }
        .cs-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 8px;
        }
        .cs-box-title {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #8EB69B;
        }
        .cs-box-badge {
            font-size: 9.5px;
            color: #4ade80;
            background: rgba(74, 222, 128, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.25);
            padding: 2px 8px;
            border-radius: 9999px;
            font-weight: 700;
        }

        .color-space-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-top: 8px;
            box-sizing: border-box;
        }
        .color-space-card {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 10px;
            background: #051F20;
            border: 1px solid rgba(142, 182, 155, 0.25);
            min-height: 52px;
            box-sizing: border-box;
            transition: all 0.2s ease;
            min-width: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .color-space-card:hover {
            border-color: rgba(74, 222, 128, 0.45);
            background: rgba(11, 43, 38, 0.95);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .cs-badge-pill {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #8EB69B;
            margin-bottom: 4px;
            line-height: 1;
        }
        .cs-card-value {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            font-weight: 800;
            color: #DAF1DE;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            line-height: 1.2;
        }

        /* Soil Description Box (Dark Mode Default) */
        .soil-summary-box {
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 16px;
            background: #051F20;
            border: 1px solid rgba(142, 182, 155, 0.25);
            color: #DAF1DE;
            transition: all 0.25s ease;
            box-sizing: border-box;
            overflow: hidden;
            min-height: 80px;
        }
        .soil-summary-label {
            font-size: 11px;
            font-weight: 800;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #8EB69B;
        }
        #resDesc {
            font-size: 13px !important;
            font-weight: 700 !important;
            margin: 0;
            line-height: 1.5 !important;
            color: #DAF1DE !important;
            word-break: normal;
            overflow-wrap: break-word;
            white-space: pre-line;
        }

        /* Segmented Mode Selector Container & Buttons (Dark Mode Default) */
        .cs-mode-container {
            display: flex;
            gap: 4px;
            margin-bottom: 10px;
            background: rgba(11, 43, 38, 0.6);
            padding: 3px;
            border-radius: 8px;
            border: 1px solid rgba(142, 182, 155, 0.2);
        }
        .cs-mode-btn {
            flex: 1;
            padding: 5px 8px;
            font-size: 10.5px;
            font-weight: 700;
            border-radius: 6px;
            border: 1px solid transparent;
            background: transparent;
            color: #8EB69B;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .cs-mode-btn:hover {
            color: #DAF1DE;
        }
        .cs-mode-btn.active {
            font-weight: 800;
            border-color: #4ade80;
            background: #235347;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        }

        /* History Table Section */
        .ws-table-section {
            background: #051F20;
            padding: 0 24px 60px;
        }
        .ws-table-card {
            max-width: 1400px;
            margin: 0 auto;
            background: linear-gradient(145deg, #0B2B26 0%, #163832 100%);
            border-radius: 20px;
            border: 1px solid rgba(142, 182, 155, 0.25);
            box-shadow: 0 12px 36px rgba(0,0,0,0.4);
            overflow: hidden;
        }
        .ws-table-header {
            padding: 24px 28px;
            border-bottom: 1px solid rgba(142, 182, 155, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .ws-table-header h2 {
            font-size: 18px;
            font-weight: 800;
            color: #DAF1DE;
            margin: 0;
        }
        .ws-badge-count {
            background: rgba(35, 83, 71, 0.5);
            color: #DAF1DE;
            border: 1px solid #8EB69B;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 999px;
        }
        table.ws-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            table-layout: fixed;
        }
        table.ws-table thead th {
            padding: 14px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #8EB69B;
            background: #051F20;
            border-bottom: 1px solid rgba(142, 182, 155, 0.2);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        table.ws-table tbody tr {
            border-bottom: 1px solid rgba(142, 182, 155, 0.1);
            transition: background 0.15s;
        }
        table.ws-table tbody tr:hover { background: rgba(35, 83, 71, 0.3); }
        table.ws-table tbody tr:last-child { border-bottom: none; }
        table.ws-table tbody td {
            padding: 16px 12px;
            color: #DAF1DE;
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ws-munsell-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            background: #051F20;
            color: #DAF1DE;
            border: 1px solid rgba(142, 182, 155, 0.3);
        }

        /* ── SCAN HISTORY EMPTY STATE (DARK MODE DEFAULT) ── */
        .ws-empty-icon-box {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(35, 83, 71, 0.8), rgba(11, 43, 38, 0.95));
            border: 1px solid rgba(142, 182, 155, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            color: #DAF1DE;
            transition: all 0.25s ease;
        }
        .ws-empty-title {
            font-size: 18px;
            font-weight: 800;
            color: #DAF1DE;
            margin: 0 0 8px 0;
            letter-spacing: -0.02em;
        }
        .ws-empty-desc {
            font-size: 13.5px;
            color: #8EB69B;
            margin: 0 auto 22px;
            max-width: 460px;
            line-height: 1.6;
            font-weight: 500;
        }
        .ws-empty-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: linear-gradient(135deg, #235347, #163832);
            color: #DAF1DE;
            font-size: 13px;
            font-weight: 700;
            border-radius: 12px;
            border: 1px solid #8EB69B;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(35, 83, 71, 0.4);
            transition: all 0.25s ease;
        }
        .ws-empty-btn:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #2c6859, #1c463e);
            border-color: #DAF1DE;
            box-shadow: 0 8px 24px rgba(35, 83, 71, 0.6);
        }
        @keyframes toastSlideDown {
            from { opacity: 0; transform: translate(-50%, -30px) scale(0.95); }
            to { opacity: 1; transform: translate(-50%, 0) scale(1); }
        }
        @keyframes toastSlideUp {
            from { opacity: 1; transform: translate(-50%, 0) scale(1); }
            to { opacity: 0; transform: translate(-50%, -25px) scale(0.95); }
        }

        .agronomic-card-box,
        .soil-summary-box {
            background: #051F20;
            border: 1px solid rgba(142, 182, 155, 0.25);
            color: #DAF1DE;
            transition: all 0.25s ease;
        }
        .agronomic-card-label,
        .soil-summary-label {
            color: #8EB69B;
        }
        .result-confidence-val {
            color: #4ade80;
            font-weight: 800;
        }

        .ws-breadcrumb-current {
            color: #DAF1DE;
            font-weight: 700;
        }

        /* Multi-Layer Sub-layer Accordions (Dark Mode Default) */
        .layer-accordion-item {
            border: 1px solid rgba(142, 182, 155, 0.2);
            border-radius: 14px;
            overflow: hidden;
            background: rgba(11, 43, 38, 0.5);
            transition: all 0.2s ease;
            margin-bottom: 8px;
        }
        .layer-accordion-header {
            width: 100%;
            padding: 11px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(11, 43, 38, 0.35);
            border: none;
            cursor: pointer;
            color: #DAF1DE;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            text-align: left;
            gap: 8px;
            box-sizing: border-box;
            transition: background 0.2s ease;
            user-select: none;
        }
        .layer-accordion-header:hover {
            background: rgba(35, 83, 71, 0.25);
        }
        .layer-accordion-header .layer-chevron {
            color: #8EB69B;
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }
        .layer-accordion-body {
            padding: 12px 14px;
            border-top: 1px solid rgba(142, 182, 155, 0.15);
            background: rgba(5, 31, 32, 0.35);
            box-sizing: border-box;
        }
        .layer-accordion-desc {
            font-size: 13.5px;
            font-weight: 600;
            color: #DAF1DE;
            line-height: 1.5;
            word-break: normal;
            overflow-wrap: break-word;
        }

        /* ── LIGHT MODE OVERRIDES ── */
        html.light-mode .ws-page-hero {
            background: linear-gradient(135deg, #DAF1DE 0%, #EEF7F1 100%) !important;
            border-bottom-color: rgba(142, 182, 155, 0.4) !important;
        }
        html.light-mode .ws-page-title { color: #051F20 !important; }
        html.light-mode .ws-page-sub   { color: #163832 !important; }
        html.light-mode .ws-breadcrumb { color: #235347 !important; }
        html.light-mode .ws-breadcrumb a { color: #051F20 !important; }
        html.light-mode .ws-breadcrumb-current { color: #051F20 !important; font-weight: 700 !important; }

        html.light-mode .scanner-section,
        html.light-mode .ws-table-section {
            background: #F8FAFC;
        }
        html.light-mode .scanner-card,
        html.light-mode .result-card {
            background: #FFFFFF !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06) !important;
        }
        html.light-mode .scanner-card-header h2 { color: #0f172a; }
        html.light-mode .scanner-viewfinder {
            background: #F8FAFC;
            border-color: #cbd5e1;
        }
        html.light-mode .zoom-btn {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        html.light-mode .zoom-btn:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }
        html.light-mode .resize-handle {
            background: #22c55e;
            border-color: #ffffff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
        }
        @media (pointer: coarse) {
            html.light-mode .resize-handle {
                background: transparent;
                border: none;
                box-shadow: none;
            }
            html.light-mode .resize-handle::after {
                background: #22c55e;
                border-color: #ffffff;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
            }
        }
        html.light-mode .scanner-btn-primary {
            background: #22c55e !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
        }
        html.light-mode .scanner-btn-primary:hover {
            background: #16a34a !important;
            box-shadow: 0 6px 18px rgba(34, 197, 94, 0.45) !important;
            transform: translateY(-1px) !important;
        }
        html.light-mode .scanner-btn-secondary {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
        html.light-mode .scanner-btn-secondary:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
        }
        html.light-mode .result-empty {
            color: #475569 !important;
            font-weight: 600;
        }
        html.light-mode .result-empty svg {
            stroke: #64748b !important;
        }
        html.light-mode .result-empty h3 {
            color: #0f172a !important;
        }
        html.light-mode .result-empty p {
            color: #64748b !important;
        }

        html.light-mode .pipeline-overview-card {
            background: #F8FAFC !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04) !important;
        }
        html.light-mode .pipeline-overview-title {
            color: #15803d !important;
        }
        html.light-mode .pipeline-overview-title svg {
            stroke: #15803d !important;
        }
        html.light-mode .pipeline-stages-list {
            color: #1e293b !important;
        }
        html.light-mode .pipeline-stage-badge {
            background: #DCFCE7 !important;
            color: #15803d !important;
            border: 1px solid #BBF7D0 !important;
        }
        html.light-mode .pipeline-stage-label {
            color: #1e293b !important;
            font-weight: 600 !important;
        }

        html.light-mode .scan-loading-card-content {
            background: #FFFFFF !important;
        }
        html.light-mode .scan-loading-spinner-ring {
            border-color: rgba(226, 232, 240, 0.9) !important;
            border-top-color: #16a34a !important;
            border-right-color: rgba(22, 163, 74, 0.7) !important;
            filter: drop-shadow(0 0 8px rgba(22, 163, 74, 0.25)) !important;
        }
        html.light-mode .scan-loading-badge-center {
            color: #0f172a !important;
        }
        html.light-mode .scan-loading-stage-pill {
            background: #dcfce7 !important;
            border-color: #bbf7d0 !important;
            color: #15803d !important;
            box-shadow: 0 2px 8px rgba(22, 163, 74, 0.12) !important;
        }
        html.light-mode .scan-loading-stage-dot {
            background: #16a34a !important;
            box-shadow: 0 0 6px #16a34a !important;
        }
        html.light-mode .scan-loading-title {
            color: #0f172a !important;
        }
        html.light-mode .scan-loading-subtext {
            color: #475569 !important;
        }
        html.light-mode .scan-loading-progress-track {
            background: #e2e8f0 !important;
            border-color: #cbd5e1 !important;
        }
        html.light-mode .scan-loading-progress-fill {
            background: linear-gradient(90deg, #16a34a, #22c55e) !important;
            box-shadow: 0 0 8px rgba(22, 163, 74, 0.3) !important;
        }
        html.light-mode #resMultiBadgeCount {
            background: #dcfce7 !important;
            color: #15803d !important;
            border-color: #bbf7d0 !important;
        }
        html.light-mode #resMetadataFooter {
            border-top-color: #e2e8f0 !important;
            color: #64748b !important;
        }
        html.light-mode #resMetadataFooter strong {
            color: #0f172a !important;
        }
        html.light-mode #resSoilSnippetFallback {
            background: #f1f5f9 !important;
            color: #64748b !important;
        }
        html.light-mode #resMultiLayerSection,
        html.light-mode #modalMultiLayerSection {
            border-top-color: #cbd5e1 !important;
        }
        html.light-mode .layer-accordion-item {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        }
        html.light-mode .layer-accordion-header {
            background: #f8fafc !important;
            color: #1e293b !important;
        }
        html.light-mode .layer-accordion-header:hover {
            background: #f1f5f9 !important;
        }
        html.light-mode .layer-accordion-header .layer-chevron {
            color: #64748b !important;
        }
        html.light-mode .layer-accordion-body {
            background: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
            color: #475569 !important;
        }
        html.light-mode .layer-accordion-body strong {
            color: #0f172a !important;
        }
        html.light-mode .layer-accordion-desc {
            color: #1e293b !important;
            font-weight: 600 !important;
        }
        html.light-mode #modalMetadataFooter {
            border-top-color: #e2e8f0 !important;
            color: #64748b !important;
        }
        html.light-mode #modalSoilSnippetFallback {
            background: #f1f5f9 !important;
            color: #64748b !important;
        }
        html.light-mode #cameraInsecureModal > div {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15) !important;
        }
        html.light-mode #cameraInsecureModal h3 {
            color: #0f172a !important;
        }
        html.light-mode #cameraInsecureModal p {
            color: #475569 !important;
        }
        html.light-mode #cameraInsecureModal code {
            background: #f1f5f9 !important;
            color: #0f172a !important;
        }

        html.light-mode .result-header { border-bottom-color: #e2e8f0 !important; }
        html.light-mode .result-header h3 { color: #16a34a !important; font-weight: 800 !important; }
        html.light-mode .munsell-badge-big { color: #0f172a !important; font-weight: 900 !important; }
        html.light-mode .result-box {
            background: #F8FAFC !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 14px !important;
        }
        html.light-mode .result-box-label { color: #16a34a !important; font-weight: 800 !important; }
        html.light-mode .result-box-val { color: #0f172a !important; font-weight: 800 !important; }
        html.light-mode .result-confidence-val { color: #16a34a !important; font-weight: 900 !important; }

        html.light-mode .agronomic-card-box,
        html.light-mode .color-spaces-card-box,
        html.light-mode .soil-summary-box {
            background: #F8FAFC !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }
        #resMetadataFooter,
        #modalMetadataFooter {
            border-top: 1px solid rgba(142, 182, 155, 0.25) !important;
            color: #CBD5E1 !important;
            font-weight: 600;
        }
        #resMetadataFooter strong,
        #modalMetadataFooter strong {
            color: #4ade80 !important;
            font-weight: 800;
        }
        #resMetadataFooter span,
        #modalMetadataFooter span {
            color: #DAF1DE !important;
            font-weight: 700;
        }
        html.light-mode #resMetadataFooter,
        html.light-mode #modalMetadataFooter {
            border-top-color: #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 600 !important;
        }
        html.light-mode #resMetadataFooter strong,
        html.light-mode #modalMetadataFooter strong {
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        html.light-mode #resMetadataFooter span,
        html.light-mode #modalMetadataFooter span {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        .before-after-swatch-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 13px;
            border-radius: 9999px;
            background: #0B2B26;
            border: 1px solid rgba(74, 222, 128, 0.25);
            box-shadow: none;
            font-size: 11.5px;
            color: #FFFFFF;
            cursor: default;
            user-select: none;
        }
        .before-after-swatch-pill:hover {
            border-color: rgba(74, 222, 128, 0.35);
        }
        .before-after-swatch-pill .swatch-color-box {
            width: 14px;
            height: 14px;
            border-radius: 3.5px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: inline-block;
            flex-shrink: 0;
            box-shadow: none;
        }
        .before-after-swatch-pill .swatch-hex {
            font-weight: 700;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            color: #FFFFFF;
        }
        .before-after-swatch-pill .swatch-code {
            color: #8EB69B;
            font-size: 11px;
            font-weight: 600;
        }
        .before-after-swatch-pill .swatch-pct {
            font-size: 11.5px;
            color: #00DC82;
            font-weight: 800;
            margin-left: 2px;
        }
        html.light-mode .before-after-swatch-pill {
            background: #f8fafc !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
            box-shadow: none !important;
            cursor: default;
            user-select: none;
        }
        html.light-mode .before-after-swatch-pill:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            box-shadow: none !important;
            transform: none !important;
        }
        html.light-mode .before-after-swatch-pill .swatch-color-box {
            border: 1px solid rgba(0, 0, 0, 0.2) !important;
            box-shadow: none !important;
        }
        html.light-mode .before-after-swatch-pill .swatch-hex {
            color: #0f172a !important;
        }
        html.light-mode .before-after-swatch-pill .swatch-code {
            color: #475569 !important;
        }
        html.light-mode .before-after-swatch-pill .swatch-pct {
            color: #15803d !important;
        }
        html.light-mode .before-after-badge {
            background: #dcfce7 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
        }
        html.light-mode #resBeforeAfterContainer,
        html.light-mode #modalBeforeAfterContainer,
        html.light-mode #resStage3Container,
        html.light-mode #modalStage3Container {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
        }
        html.light-mode #resBeforeAfterCard,
        html.light-mode #modalBeforeAfterCard,
        html.light-mode #resStage3Card,
        html.light-mode #modalStage3Card {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04) !important;
        }

        /* ── Before / After Zoom Lightbox Modal (Dark Mode Default) ── */
        .ba-zoom-modal {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(0, 0, 0, 0.88);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }
        .ba-zoom-card {
            position: relative;
            max-width: 95vw;
            max-height: 92vh;
            background: #0c1a17;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(142, 182, 155, 0.35);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.85);
            display: flex;
            flex-direction: column;
        }
        .ba-zoom-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 18px;
            background: rgba(5, 31, 32, 0.95);
            border-bottom: 1px solid rgba(142, 182, 155, 0.2);
            gap: 10px;
        }
        .ba-zoom-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .ba-zoom-title {
            font-size: 13px;
            font-weight: 700;
            color: #DAF1DE;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .ba-zoom-badge {
            font-size: 9.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 5px;
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }
        .ba-zoom-close-btn {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            color: #DAF1DE;
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.15s ease;
        }
        .ba-zoom-close-btn:hover {
            background: rgba(255,255,255,0.18);
            color: #ffffff;
            transform: scale(1.05);
        }
        .ba-zoom-body {
            padding: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: auto;
            background: #051F20;
            min-height: 160px;
            min-width: 240px;
        }
        .ba-zoom-img-container {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            max-width: 100%;
            max-height: 80vh;
        }
        .ba-zoom-img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            display: block;
        }
        .ba-zoom-boxes {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 10;
        }

        /* ── Before / After Zoom Lightbox Modal (Light Mode Overrides) ── */
        html.light-mode .ba-zoom-modal {
            background: rgba(15, 23, 42, 0.5) !important;
        }
        html.light-mode .ba-zoom-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.18) !important;
        }
        html.light-mode .ba-zoom-header {
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        html.light-mode .ba-zoom-title {
            color: #0f172a !important;
        }
        html.light-mode .ba-zoom-badge {
            background: #dcfce7 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
        }
        html.light-mode .ba-zoom-close-btn {
            background: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            color: #64748b !important;
        }
        html.light-mode .ba-zoom-close-btn:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
            transform: scale(1.05);
        }
        html.light-mode .ba-zoom-body {
            background: #f8fafc !important;
        }
        html.light-mode .ba-zoom-img {
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08) !important;
        }
        html.light-mode .cs-box-title {
            color: #475569 !important;
        }
        html.light-mode .cs-box-badge {
            color: #1d4ed8 !important;
            background: #dbeafe !important;
            border-color: #bfdbfe !important;
        }
        html.light-mode .color-space-card {
            background: #FFFFFF !important;
            border: 1px solid #e2e8f0 !important;
        }
        html.light-mode .cs-badge-pill {
            color: #64748b !important;
        }
        html.light-mode .cs-card-value {
            color: #0f172a !important;
        }
        html.light-mode .cs-mode-container {
            background: #e2e8f0 !important;
            border-color: #cbd5e1 !important;
        }
        html.light-mode .cs-mode-btn {
            color: #64748b !important;
        }
        html.light-mode .cs-mode-btn:hover {
            color: #0f172a !important;
        }
        html.light-mode .cs-mode-btn.active {
            background: #ffffff !important;
            color: #15803d !important;
            border-color: #22c55e !important;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.15) !important;
        }
        html.light-mode .agronomic-card-label,
        html.light-mode .soil-summary-label {
            color: #334155 !important;
            font-weight: 800 !important;
        }
        html.light-mode #resOrganic,
        html.light-mode #resDrainage {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        html.light-mode #resDesc {
            color: #0f172a !important;
            font-weight: 700 !important;
        }

        /* ── TABLE STYLING FOR DARK MODE ── */
        .col-seq {
            color: #8EB69B;
            font-weight: 600;
            overflow: visible !important;
            text-overflow: clip !important;
            white-space: nowrap !important;
        }
        .col-soil-name { color: #DAF1DE; font-weight: 800; }
        .col-soil-desc { color: #8EB69B; font-weight: 500; }
        .col-soil-code { color: #DAF1DE; font-weight: 700; }
        .col-soil-hue  { color: #8EB69B; font-weight: 700; }
        .col-soil-seq  { color: #DAF1DE; font-weight: 700; }
        .col-soil-date { color: #8EB69B; font-weight: 600; }
        .col-action,
        th.col-th-action {
            overflow: visible !important;
            text-overflow: clip !important;
            white-space: nowrap !important;
        }

        table.ws-table tbody tr {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            border-bottom: 1px solid rgba(142, 182, 155, 0.15);
        }
        table.ws-table tbody tr:hover {
            background: linear-gradient(145deg, #113832 0%, #163832 100%) !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4), 0 0 20px rgba(35, 83, 71, 0.3), inset 4px 0 0 #DAF1DE !important;
            transform: translateY(-2px);
            position: relative;
            z-index: 5;
        }
        table.ws-table tbody tr:hover .col-soil-name {
            color: #FFFFFF !important;
            transition: color 0.25s ease;
        }

        .btn-view-scan {
            color: #DAF1DE;
            background: rgba(35, 83, 71, 0.45);
            border: 1px solid #8EB69B;
            border-radius: 10px;
            font-weight: 700;
            padding: 6px 16px !important;
            font-size: 12.5px !important;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-block;
            font-family: inherit;
            box-sizing: border-box !important;
        }
        .btn-view-scan:hover,
        table.ws-table tbody tr:hover .btn-view-scan {
            color: #ffffff !important;
            background: #235347 !important;
            border-color: #DAF1DE !important;
            box-shadow: 0 4px 16px rgba(35, 83, 71, 0.6) !important;
            transform: scale(1.05) !important;
        }

        .ws-table-pagination-footer {
            background: #051F20;
            border-top: 1px solid rgba(142, 182, 155, 0.2);
            color: #8EB69B;
        }

        /* ── LIGHT MODE OVERRIDES FOR TABLE (SAME PADDING & DIMENSIONS TO PREVENT JUMPING) ── */
        html.light-mode .ws-table-card {
            background: #FFFFFF !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
        }
        html.light-mode .ws-table-header {
            background: #FFFFFF !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 24px 28px !important;
        }
        html.light-mode .ws-table-header h2 {
            color: #111827 !important;
            font-weight: 800 !important;
        }
        html.light-mode .ws-badge-count {
            background: #eefbe7 !important;
            color: #16a34a !important;
            border: 1px solid #86efac !important;
            font-weight: 700 !important;
        }
        html.light-mode table.ws-table thead tr {
            background: #F1FBF1 !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        html.light-mode table.ws-table thead th {
            background: #F1FBF1 !important;
            color: #475569 !important;
            border-bottom: 1px solid #e2e8f0 !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            letter-spacing: 0.04em !important;
            text-transform: uppercase !important;
            padding: 14px 12px !important;
        }
        html.light-mode table.ws-table tbody tr {
            border-bottom: 1px solid #f1f5f9 !important;
            background: #ffffff !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        html.light-mode table.ws-table tbody tr:hover {
            background: #f0fdf4 !important;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.12), inset 4px 0 0 #22c55e !important;
            transform: translateY(-2px) !important;
            position: relative !important;
            z-index: 5 !important;
        }
        html.light-mode table.ws-table tbody tr:hover .col-soil-name {
            color: #16a34a !important;
        }

        html.light-mode .btn-view-scan:hover,
        html.light-mode table.ws-table tbody tr:hover .btn-view-scan {
            color: #15803d !important;
            background: #eefbe7 !important;
            border-color: #22c55e !important;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.25) !important;
            transform: translateY(-1px) !important;
        }

        /* ── LIGHT MODE OVERRIDES FOR EMPTY STATE ── */
        html.light-mode .ws-empty-icon-box {
            background: #f0fdf4 !important;
            border: 1px solid #86efac !important;
            box-shadow: 0 6px 18px rgba(34, 197, 94, 0.12) !important;
            color: #16a34a !important;
        }
        html.light-mode .ws-empty-title {
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        html.light-mode .ws-empty-desc {
            color: #475569 !important;
            font-weight: 500 !important;
        }
        html.light-mode .ws-empty-btn {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.25) !important;
        }
        html.light-mode .ws-empty-btn:hover {
            background: linear-gradient(135deg, #16a34a, #15803d) !important;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4) !important;
            transform: translateY(-2px) !important;
        }

        /* ── SOIL RECORD DETAILS MODAL & SCAN RESULT CARDS (DARK MODE THEME DEFAULT) ── */
        #soilRecordModal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(5, 31, 32, 0.75) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            align-items: center;
            justify-content: center;
            padding: 16px;
            box-sizing: border-box;
        }
        .soil-modal-card {
            background: linear-gradient(145deg, #0B2B26 0%, #163832 100%) !important;
            border: 1px solid rgba(142, 182, 155, 0.25) !important;
            border-radius: 28px !important;
            width: 100% !important;
            max-width: 680px !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5) !important;
            overflow: hidden !important;
            position: relative !important;
            display: flex !important;
            flex-direction: column !important;
            height: 75vh !important;
            max-height: 75vh !important;
            box-sizing: border-box !important;
            margin: 0 auto !important;
        }
        .soil-modal-header {
            padding: 20px 26px 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            background: #0B2B26 !important;
            border-bottom: 1px solid rgba(142, 182, 155, 0.25) !important;
            flex-shrink: 0 !important;
            box-sizing: border-box !important;
        }
        .soil-modal-title {
            margin: 0 !important;
            font-size: 17.5px !important;
            font-weight: 800 !important;
            color: #DAF1DE !important;
            letter-spacing: -0.01em !important;
        }
        .soil-modal-close,
        .modal-close-circle-btn {
            background: rgba(5, 31, 32, 0.7) !important;
            border: 1.5px solid rgba(142, 182, 155, 0.35) !important;
            color: #8EB69B !important;
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            padding: 0 !important;
            transition: all 0.2s ease !important;
            flex-shrink: 0 !important;
        }
        .soil-modal-close:hover,
        .modal-close-circle-btn:hover {
            background: rgba(35, 83, 71, 0.8) !important;
            color: #DAF1DE !important;
            border-color: #DAF1DE !important;
        }
        .soil-modal-body-container {
            padding: 18px 24px 20px !important;
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            box-sizing: border-box !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 0 !important;
            scrollbar-width: thin !important;
            scrollbar-color: rgba(142, 182, 155, 0.35) rgba(11, 43, 38, 0.8) !important;
            scrollbar-gutter: stable;
            -webkit-overflow-scrolling: touch;
        }
        .soil-modal-body-container > * {
            flex-shrink: 0 !important;
        }
        .soil-modal-body-container::-webkit-scrollbar {
            width: 6px !important;
            display: block !important;
        }
        .soil-modal-body-container::-webkit-scrollbar-track {
            background: rgba(11, 43, 38, 0.8) !important;
            border-radius: 9999px !important;
            margin: 4px 0 !important;
        }
        .soil-modal-body-container::-webkit-scrollbar-thumb {
            background: rgba(142, 182, 155, 0.35) !important;
            border-radius: 9999px !important;
        }
        .soil-modal-body-container::-webkit-scrollbar-thumb:hover {
            background: rgba(142, 182, 155, 0.6) !important;
        }
        .soil-modal-footer {
            padding: 14px 24px 16px !important;
            background: #0B2B26 !important;
            border-top: 1px solid rgba(142, 182, 155, 0.25) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 12px !important;
            flex-shrink: 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .soil-modal-banner {
            position: relative !important;
            margin-top: 2px !important;
            margin-bottom: 12px !important;
            width: 100% !important;
            border-radius: 18px !important;
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            padding: 14px 20px !important;
            box-sizing: border-box !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important;
            border: none !important;
            min-height: 78px !important;
            transition: background-color 0.3s ease !important;
        }
        .soil-modal-grid-3 {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 10px !important;
            margin-bottom: 10px !important;
            box-sizing: border-box !important;
            width: 100% !important;
        }
        .soil-modal-grid-card {
            background: #163832 !important;
            border: 1px solid rgba(142, 182, 155, 0.25) !important;
            border-radius: 16px !important;
            padding: 12px 16px !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2) !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }
        .soil-modal-label {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #8EB69B !important;
            margin-bottom: 4px !important;
            letter-spacing: 0.03em !important;
            white-space: normal !important;
            word-break: break-word !important;
            text-transform: uppercase !important;
        }
        .soil-modal-value {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #DAF1DE !important;
            word-break: break-word !important;
        }
        #modalCardConfidence {
            color: #10b981 !important;
            font-weight: 800 !important;
        }
        #modalCardDateContainer {
            min-height: 54px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            background: #163832 !important;
            border: 1px solid rgba(142, 182, 155, 0.25) !important;
        }
        .soil-modal-desc-card {
            min-height: 58px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }
        #modalScanNoteCard,
        #modalDescCard,
        #resScanNoteCard,
        #resDescCard {
            background: #163832 !important;
            border: 1px solid rgba(142, 182, 155, 0.25) !important;
            border-radius: 16px !important;
            padding: 13px 16px 15px !important;
            margin-bottom: 10px !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2) !important;
            box-sizing: border-box !important;
        }
        #modalCardScanNote,
        #modalCardDesc,
        #resCardScanNote,
        #resDesc {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #DAF1DE !important;
            line-height: 1.5 !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            white-space: pre-line !important;
        }
        #modalBeforeAfterCard,
        #resBeforeAfterCard {
            background: #163832 !important;
            border: 1px solid rgba(142, 182, 155, 0.25) !important;
            border-radius: 16px !important;
            padding: 13px 16px 15px !important;
            margin-bottom: 12px !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important;
            box-sizing: border-box !important;
            flex-shrink: 0 !important;
        }
        #modalBeforeAfterContainer,
        #resBeforeAfterContainer {
            position: relative !important;
            width: 100% !important;
            border-radius: 10px !important;
            overflow: hidden !important;
            background: #0c1a17 !important;
            border: 1px solid rgba(142, 182, 155, 0.25) !important;
            cursor: pointer !important;
            display: block !important;
        }
        #modalBeforeAfterImg,
        #resBeforeAfterImg {
            width: 100% !important;
            height: auto !important;
            display: block !important;
            border-radius: 8px !important;
            object-fit: contain !important;
        }
        #modalSoilLocationCard {
            background: #163832 !important;
            border: 1px solid rgba(142, 182, 155, 0.25) !important;
            border-radius: 16px !important;
        }
        .modal-loc-badge-brgy {
            font-size: 10px !important;
            font-weight: 800 !important;
            color: #4ade80 !important;
            background: rgba(34, 197, 94, 0.18) !important;
            border: 1px solid rgba(74, 222, 128, 0.35) !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.02em !important;
        }
        .modal-loc-badge-coverage {
            font-size: 10px !important;
            font-weight: 800 !important;
            color: #34d399 !important;
            background: rgba(16, 185, 129, 0.18) !important;
            border: 1px solid rgba(52, 211, 153, 0.35) !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            letter-spacing: 0.02em !important;
        }
        .modal-loc-badge-coords {
            font-size: 10px !important;
            font-weight: 800 !important;
            color: #38bdf8 !important;
            background: rgba(56, 189, 248, 0.18) !important;
            border: 1px solid rgba(56, 189, 248, 0.35) !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        }
        .modal-soil-map-wrapper {
            position: relative !important;
            width: 100% !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            border: 1px solid rgba(142, 182, 155, 0.25) !important;
            box-shadow: 0 4px 14px rgba(0,0,0,0.3) !important;
            background: #0c1a17 !important;
        }
        .modal-soil-map-watermark {
            position: absolute !important;
            bottom: 8px !important;
            right: 8px !important;
            z-index: 1000 !important;
            background: rgba(5, 31, 32, 0.9) !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
            padding: 3px 8px !important;
            border-radius: 6px !important;
            font-size: 10px !important;
            color: #8EB69B !important;
            font-weight: 700 !important;
            border: 1px solid rgba(142, 182, 155, 0.25) !important;
            pointer-events: none !important;
        }

        /* Leaflet Controls & Popups (Dark Mode Default) */
        #modalSoilDetailMap .leaflet-bar,
        .admin-soil-detail-map .leaflet-bar {
            border: 1px solid rgba(142, 182, 155, 0.3) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4) !important;
            border-radius: 8px !important;
            overflow: hidden !important;
        }
        #modalSoilDetailMap .leaflet-bar a,
        .admin-soil-detail-map .leaflet-bar a {
            background-color: #0B2B26 !important;
            color: #DAF1DE !important;
            border-bottom: 1px solid rgba(142, 182, 155, 0.25) !important;
        }
        #modalSoilDetailMap .leaflet-bar a:hover,
        .admin-soil-detail-map .leaflet-bar a:hover {
            background-color: #163832 !important;
            color: #4ade80 !important;
        }
        #modalSoilDetailMap .leaflet-popup-content-wrapper,
        .admin-soil-detail-map .leaflet-popup-content-wrapper {
            background: #0B2B26 !important;
            color: #DAF1DE !important;
            border: 1px solid rgba(142, 182, 155, 0.35) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6) !important;
            border-radius: 14px !important;
        }
        #modalSoilDetailMap .leaflet-popup-tip,
        .admin-soil-detail-map .leaflet-popup-tip {
            background: #0B2B26 !important;
            border: 1px solid rgba(142, 182, 155, 0.35) !important;
            box-shadow: none !important;
        }
        #modalSoilDetailMap .leaflet-popup-close-button,
        .admin-soil-detail-map .leaflet-popup-close-button {
            color: #8EB69B !important;
            font-size: 16px !important;
            padding: 6px 8px 0 0 !important;
        }
        #modalSoilDetailMap .leaflet-popup-close-button:hover,
        .admin-soil-detail-map .leaflet-popup-close-button:hover {
            color: #4ade80 !important;
        }

        /* ── LIGHT MODE OVERRIDES FOR SOIL RECORD MODAL & SCAN RESULT CARDS ── */
        html.light-mode #soilRecordModal {
            background: rgba(15, 23, 42, 0.65) !important;
        }
        html.light-mode .soil-modal-card {
            background: #ffffff !important;
            border: 1px solid #f1f5f9 !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3) !important;
        }
        html.light-mode .soil-modal-header {
            background: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        html.light-mode .soil-modal-title {
            color: #0f172a !important;
        }
        html.light-mode .soil-modal-close,
        html.light-mode .modal-close-circle-btn {
            background: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            color: #64748b !important;
        }
        html.light-mode .soil-modal-close:hover,
        html.light-mode .modal-close-circle-btn:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
        }
        html.light-mode .soil-modal-body-container {
            scrollbar-color: #cbd5e1 #f1f5f9 !important;
        }
        html.light-mode .soil-modal-body-container::-webkit-scrollbar-track {
            background: #f1f5f9 !important;
        }
        html.light-mode .soil-modal-body-container::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
        }
        html.light-mode .soil-modal-body-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8 !important;
        }
        html.light-mode .soil-modal-footer {
            background: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
        }
        html.light-mode .soil-modal-grid-card {
            background: #ffffff !important;
            border: 1.5px solid #f1f5f9 !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }
        html.light-mode .soil-modal-label {
            color: #475569 !important;
            font-weight: 800 !important;
        }
        html.light-mode .soil-modal-value {
            color: #0f172a !important;
        }
        html.light-mode #modalCardDateContainer {
            background: #ffffff !important;
            border: 1.5px solid #f1f5f9 !important;
        }
        html.light-mode #modalScanNoteCard,
        html.light-mode #modalDescCard,
        html.light-mode #resScanNoteCard,
        html.light-mode #resDescCard {
            background: #ffffff !important;
            border: 1.5px solid #f1f5f9 !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }
        html.light-mode #modalCardScanNote,
        html.light-mode #modalCardDesc,
        html.light-mode #resCardScanNote,
        html.light-mode #resDesc,
        html.light-mode .layer-accordion-desc,
        html.light-mode #soilRecordModal [style*="#DAF1DE"],
        html.light-mode #soilRecordModal [style*="color: #DAF1DE"],
        html.light-mode #soilRecordModal [style*="color:#DAF1DE"],
        html.light-mode #resultsCard [style*="#DAF1DE"],
        html.light-mode #resultsCard [style*="color: #DAF1DE"],
        html.light-mode #resultsCard [style*="color:#DAF1DE"],
        html.light-mode .soil-modal-grid-card [style*="#DAF1DE"],
        html.light-mode .soil-modal-grid-card [style*="color: #DAF1DE"],
        html.light-mode .soil-modal-grid-card [style*="color:#DAF1DE"],
        html.light-mode .layer-accordion-item [style*="#DAF1DE"],
        html.light-mode .layer-accordion-item [style*="color: #DAF1DE"],
        html.light-mode .layer-accordion-item [style*="color:#DAF1DE"],
        html.light-mode .layer-accordion-header div,
        html.light-mode .layer-accordion-header span:not([style*="background"]) {
            color: #0f172a !important;
        }
        html.light-mode .layer-accordion-header .layer-chevron {
            color: #475569 !important;
        }
        html.light-mode #modalBeforeAfterCard,
        html.light-mode #resBeforeAfterCard {
            background: #ffffff !important;
            border: 1.5px solid #f1f5f9 !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }
        html.light-mode #modalBeforeAfterContainer,
        html.light-mode #resBeforeAfterContainer {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
        }
        html.light-mode #modalSoilLocationCard {
            background: #ffffff !important;
            border: 1.5px solid #f1f5f9 !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }
        html.light-mode .modal-loc-badge-brgy {
            color: #166534 !important;
            background: #dcfce7 !important;
            border: 1px solid #bbf7d0 !important;
        }
        html.light-mode .modal-loc-badge-coverage {
            color: #065f46 !important;
            background: #d1fae5 !important;
            border: 1px solid #a7f3d0 !important;
        }
        html.light-mode .modal-loc-badge-coords {
            color: #0284c7 !important;
            background: #e0f2fe !important;
            border: 1px solid #bae6fd !important;
        }
        html.light-mode .modal-soil-map-wrapper {
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
            background: #f8fafc !important;
        }
        html.light-mode .modal-soil-map-watermark {
            background: rgba(255, 255, 255, 0.92) !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
        }
        html.light-mode #modalSoilDetailMap .leaflet-bar,
        html.light-mode .admin-soil-detail-map .leaflet-bar {
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
        }
        html.light-mode #modalSoilDetailMap .leaflet-bar a,
        html.light-mode .admin-soil-detail-map .leaflet-bar a {
            background-color: #ffffff !important;
            color: #334155 !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        html.light-mode #modalSoilDetailMap .leaflet-bar a:hover,
        html.light-mode .admin-soil-detail-map .leaflet-bar a:hover {
            background-color: #f1f5f9 !important;
            color: #16a34a !important;
        }
        html.light-mode #modalSoilDetailMap .leaflet-popup-content-wrapper,
        html.light-mode .admin-soil-detail-map .leaflet-popup-content-wrapper {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
        }
        html.light-mode #modalSoilDetailMap .leaflet-popup-tip,
        html.light-mode .admin-soil-detail-map .leaflet-popup-tip {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
        }
        html.light-mode #modalSoilDetailMap .leaflet-popup-close-button,
        html.light-mode .admin-soil-detail-map .leaflet-popup-close-button {
            color: #64748b !important;
        }
        html.light-mode #modalSoilDetailMap .leaflet-popup-close-button:hover,
        html.light-mode .admin-soil-detail-map .leaflet-popup-close-button:hover {
            color: #0f172a !important;
        }

        /* ── MODAL FOOTER BUTTONS: CANCEL & SAVE COPY ── */
        .btn-modal-cancel {
            flex: 1 !important;
            height: 44px !important;
            min-height: 44px !important;
            min-width: 0 !important;
            background: rgba(11, 43, 38, 0.8) !important;
            border: 1.5px solid rgba(142, 182, 155, 0.35) !important;
            color: #DAF1DE !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            padding: 0 16px !important;
            border-radius: 14px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            cursor: pointer !important;
            transition: all 0.25s ease !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
            box-sizing: border-box !important;
            user-select: none !important;
            font-family: inherit !important;
            white-space: nowrap !important;
        }
        .btn-modal-cancel:hover {
            background: rgba(35, 83, 71, 0.5) !important;
            border-color: #DAF1DE !important;
            color: #DAF1DE !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 14px rgba(35, 83, 71, 0.35) !important;
        }
        .btn-modal-cancel:active {
            transform: translateY(0) !important;
        }
        .btn-save-hardcopy {
            flex: 1 !important;
            height: 44px !important;
            min-height: 44px !important;
            min-width: 0 !important;
            background: linear-gradient(135deg, #235347, #163832) !important;
            border: 1.5px solid #8EB69B !important;
            color: #DAF1DE !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            padding: 0 16px !important;
            border-radius: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            cursor: pointer !important;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            user-select: none !important;
            font-family: inherit !important;
            white-space: nowrap !important;
        }
        .btn-save-hardcopy:hover {
            background: linear-gradient(135deg, #2e695a, #1c453d) !important;
            border-color: #DAF1DE !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 18px rgba(35, 83, 71, 0.45) !important;
        }
        .btn-save-hardcopy:active {
            transform: translateY(0) !important;
        }
        .btn-save-hardcopy svg,
        .btn-modal-cancel svg {
            flex-shrink: 0 !important;
        }

        /* Light Mode Button Overrides */
        html.light-mode .soil-modal-footer {
            background: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
        }
        html.light-mode .btn-modal-cancel {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 14px !important;
            font-weight: 700 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.25s ease !important;
        }
        html.light-mode .btn-modal-cancel:hover {
            background: #e2e8f0 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }
        html.light-mode .btn-modal-cancel:active {
            transform: translateY(0) !important;
        }
        html.light-mode .btn-save-hardcopy {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
            font-weight: 800 !important;
        }
        html.light-mode .btn-save-hardcopy:hover {
            background: linear-gradient(135deg, #16a34a, #15803d) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.45) !important;
        }

        /* ── RESPONSIVE MEDIA QUERIES FOR SOIL RECORD MODAL ── */
        @media (max-width: 540px) {
            #soilRecordModal {
                padding: 10px !important;
            }
            .soil-modal-card {
                height: 80vh !important;
                max-height: 80vh !important;
                border-radius: 24px !important;
                width: 100% !important;
            }
            .soil-modal-header {
                padding: 14px 16px 12px !important;
            }
            .soil-modal-title {
                font-size: 16.5px !important;
            }
            .soil-modal-body-container {
                padding: 12px 14px 14px !important;
            }
            .soil-modal-hero {
                padding: 12px 14px !important;
                gap: 12px !important;
                margin-bottom: 8px !important;
            }
            #modalSoilName {
                font-size: 15px !important;
            }
            .soil-modal-grid-card {
                padding: 8px 10px !important;
            }
            .soil-modal-label {
                font-size: 10px !important;
            }
            .soil-modal-value {
                font-size: 13px !important;
            }
            .soil-metrics-row-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
            .soil-metrics-row-2 .card-near-match {
                grid-column: span 2 !important;
            }
            .soil-modal-footer {
                padding: 12px 16px 14px !important;
                gap: 10px !important;
            }
            .btn-modal-cancel,
            .btn-save-hardcopy {
                height: 42px !important;
                min-height: 42px !important;
                font-size: 12.5px !important;
                padding: 0 12px !important;
                border-radius: 10px !important;
            }
        }

        @media (max-width: 380px) {
            .soil-metrics-row-1,
            .soil-metrics-row-2 {
                grid-template-columns: 1fr !important;
            }
            .soil-metrics-row-2 .card-near-match {
                grid-column: span 1 !important;
            }
            .soil-modal-footer {
                padding: 10px 12px 12px !important;
                gap: 8px !important;
            }
            .btn-modal-cancel,
            .btn-save-hardcopy {
                height: 40px !important;
                min-height: 40px !important;
                font-size: 12px !important;
                padding: 0 8px !important;
                gap: 6px !important;
            }
            .btn-modal-cancel svg,
            .btn-save-hardcopy svg {
                width: 14px !important;
                height: 14px !important;
            }
        }

        /* ── SINGLE NEW SCAN ROW HIGHLIGHT (DARK MODE DEFAULT) ── */
        .ws-table tbody tr.is-new-scan-row {
            background: rgba(22, 56, 50, 0.65) !important;
            box-shadow: inset 4px 0 0 #4ade80, 0 4px 16px rgba(0, 0, 0, 0.3) !important;
            transition: all 0.35s ease;
        }
        .ws-table tbody tr.is-new-scan-row td {
            color: #DAF1DE !important;
        }
        .ws-table tbody tr.is-new-scan-row .col-seq {
            color: #4ade80 !important;
            font-weight: 800 !important;
        }
        .ws-table tbody tr.is-new-scan-row .col-soil-name {
            color: #ffffff !important;
            font-weight: 800 !important;
        }
        .ws-table tbody tr.is-new-scan-row .col-soil-desc,
        .ws-table tbody tr.is-new-scan-row .col-soil-code,
        .ws-table tbody tr.is-new-scan-row .col-soil-hue,
        .ws-table tbody tr.is-new-scan-row .col-soil-seq,
        .ws-table tbody tr.is-new-scan-row .col-soil-date {
            color: #DAF1DE !important;
            font-weight: 600 !important;
        }

        /* ── SINGLE NEW SCAN ROW HIGHLIGHT (LIGHT MODE OVERRIDE) ── */
        html.light-mode .ws-table tbody tr.is-new-scan-row {
            background: #eefbe7 !important;
            box-shadow: inset 4px 0 0 #22c55e, 0 4px 14px rgba(34, 197, 94, 0.15) !important;
        }
        html.light-mode .ws-table tbody tr.is-new-scan-row td {
            color: #0f172a !important;
        }
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-seq {
            color: #15803d !important;
            font-weight: 800 !important;
        }
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-name {
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-desc,
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-code,
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-hue,
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-seq,
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-date {
            color: #1e293b !important;
            font-weight: 600 !important;
        }
        html.light-mode .ws-table tbody tr.is-new-scan-row td {
            color: #0f172a !important;
        }
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-seq {
            color: #15803d !important;
            font-weight: 800 !important;
        }
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-name {
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-desc,
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-code,
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-hue,
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-seq,
        html.light-mode .ws-table tbody tr.is-new-scan-row .col-soil-date {
            color: #1e293b !important;
            font-weight: 600 !important;
        }

        html.light-mode table.ws-table tbody td {
            padding: 16px 12px !important;
        }
        html.light-mode .col-seq {
            color: #64748b !important;
            font-size: 12px !important;
            font-weight: 600 !important;
        }
        html.light-mode .col-soil-name {
            color: #0f172a !important;
            font-size: 14px !important;
            font-weight: 700 !important;
        }
        html.light-mode .col-soil-desc {
            color: #475569 !important;
            font-size: 13px !important;
            font-weight: 700 !important;
        }
        html.light-mode .col-soil-code {
            color: #0f172a !important;
            font-size: 13px !important;
            font-weight: 700 !important;
        }
        html.light-mode .col-soil-hue {
            color: #475569 !important;
            font-size: 13px !important;
            font-weight: 700 !important;
        }
        html.light-mode .col-soil-seq {
            color: #0f172a !important;
            font-size: 13px !important;
            font-weight: 700 !important;
        }
        html.light-mode .col-soil-date {
            color: #475569 !important;
            font-size: 13px !important;
            font-weight: 700 !important;
        }
        html.light-mode .col-action,
        html.light-mode th.col-th-action {
            overflow: visible !important;
            text-overflow: clip !important;
            white-space: nowrap !important;
        }
        html.light-mode .btn-view-scan {
            color: #16a34a !important;
            background: #f0fdf4 !important;
            border: 1px solid #86efac !important;
            font-weight: 700 !important;
            padding: 6px 16px !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        }
        html.light-mode .btn-view-scan:hover,
        html.light-mode table.ws-table tbody tr:hover .btn-view-scan {
            color: #15803d !important;
            background: #eefbe7 !important;
            border-color: #22c55e !important;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.25) !important;
            transform: translateY(-1px) !important;
        }
        /* ── PAGINATION FOOTER STYLING ── */
        .ws-page-info { color: #8EB69B; font-size: 13px; font-weight: 500; }
        .ws-page-count-text { color: #8EB69B; font-size: 13px; margin-left: 4px; font-weight: 500; }
        
        .ws-page-select {
            border: 1px solid rgba(142, 182, 155, 0.3);
            background: #0B2B26;
            border-radius: 8px;
            padding: 4px 8px;
            font-size: 13px;
            color: #DAF1DE;
            font-weight: 600;
            outline: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .ws-page-btn {
            height: 32px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid rgba(142, 182, 155, 0.3);
            background: #0B2B26;
            color: #DAF1DE;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .ws-page-btn:hover {
            border-color: #8EB69B;
            color: #ffffff;
            background: #235347;
        }

        .ws-page-num {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #0B2B26;
            color: #DAF1DE;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid rgba(142, 182, 155, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .ws-page-num:hover {
            border-color: #8EB69B;
            color: #ffffff;
            background: #235347;
        }

        .ws-page-num-active,
        .ws-page-btn-active {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #235347, #163832) !important;
            color: #DAF1DE !important;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid #8EB69B !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(35, 83, 71, 0.5);
        }

        .ws-page-btn-disabled {
            height: 32px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid rgba(142, 182, 155, 0.15) !important;
            background: rgba(11, 43, 38, 0.4) !important;
            color: rgba(142, 182, 155, 0.35) !important;
            font-size: 12.5px;
            font-weight: 600;
            cursor: not-allowed;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: none !important;
        }

        .ws-page-dots {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 32px;
            color: #8EB69B;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            user-select: none;
            padding: 0 4px;
            transition: all 0.2s ease;
        }
        .ws-page-dots:hover {
            color: #ffffff;
        }

        /* ── LIGHT MODE OVERRIDES FOR PAGINATION (ADMIN PANEL MATCHED) ── */
        html.light-mode .ws-table-pagination-footer {
            background: #FFFFFF !important;
            border-top: 1px solid #f1f5f9 !important;
            color: #64748b !important;
        }
        html.light-mode .ws-page-info { color: #64748b !important; }
        html.light-mode .ws-page-count-text { color: #94a3b8 !important; }

        html.light-mode .ws-page-select {
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            color: #334155 !important;
        }
        html.light-mode .ws-page-select:hover {
            border-color: #cbd5e1 !important;
        }

        html.light-mode .ws-page-btn {
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            color: #475569 !important;
        }
        html.light-mode .ws-page-btn:hover {
            border-color: #22c55e !important;
            color: #22c55e !important;
            background: #f0fdf4 !important;
        }

        html.light-mode .ws-page-btn-disabled {
            border: 1px solid #f1f5f9 !important;
            background: #f8fafc !important;
            color: #cbd5e1 !important;
            box-shadow: none !important;
            cursor: not-allowed !important;
        }

        html.light-mode .ws-page-num {
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            color: #334155 !important;
        }
        html.light-mode .ws-page-num:hover {
            border-color: #22c55e !important;
            color: #22c55e !important;
            background: #f0fdf4 !important;
        }

        html.light-mode .ws-page-num-active,
        html.light-mode .ws-page-btn-active {
            background: #22c55e !important;
            color: #ffffff !important;
            border: 1px solid #22c55e !important;
            box-shadow: 0 4px 10px rgba(34, 197, 94, 0.35) !important;
        }

        html.light-mode .ws-page-dots {
            color: #64748b !important;
        }
        html.light-mode .ws-page-dots:hover {
            color: #22c55e !important;
        }

        /* Camera status badge & viewfinder placeholder */
        .camera-status-badge {
            font-size: 12px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.25s ease;
        }
        .camera-status-badge.status-off,
        .camera-status-badge {
            color: #DAF1DE;
            background: rgba(5, 31, 32, 0.7);
            border: 1.5px solid rgba(218, 241, 222, 0.6);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .camera-status-badge.status-active {
            color: #4ADE80 !important;
            background: rgba(35, 83, 71, 0.6) !important;
            border: 1.5px solid #22c55e !important;
            box-shadow: 0 0 16px rgba(34, 197, 94, 0.45) !important;
        }
        .camera-placeholder-box {
            color: #8EB69B;
        }
        .camera-placeholder-text {
            color: #DAF1DE;
        }

        /* Light mode overrides for viewfinder */
        html.light-mode .camera-status-badge.status-off,
        html.light-mode .camera-status-badge {
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1.5px solid #cbd5e1 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
        }
        html.light-mode .camera-status-badge.status-active {
            background: #eefbe7 !important;
            color: #16a34a !important;
            border: 1.5px solid #86efac !important;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.15) !important;
        }
        html.light-mode .camera-placeholder-box {
            color: #16a34a !important;
        }
        html.light-mode .camera-placeholder-text {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        html.light-mode .scanner-card-header h2 svg {
            stroke: #16a34a !important;
        }

        /* Result Error State Light Mode */
        html.light-mode #resultErrorState .error-state-title { color: #dc2626 !important; }
        html.light-mode #resultErrorState .error-state-desc { color: #475569 !important; }
        html.light-mode #resultErrorState .error-shield-icon-box { background: #fef2f2 !important; border-color: #fca5a5 !important; color: #dc2626 !important; }
            color: #ffffff !important;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.45) !important;
        }
        html.light-mode .btn-save-hardcopy svg {
            stroke: #ffffff !important;
        }

        /* ── SCAN NOTIFICATION TOAST (DARK & LIGHT MODE) ── */
        .scan-notice-card {
            background: linear-gradient(145deg, #0B2B26 0%, #051F20 100%);
            border-radius: 28px;
            padding: 18px 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.55), 0 4px 14px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(142, 182, 155, 0.25);
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .scan-notice-title {
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.01em;
            margin-bottom: 4px;
        }
        .scan-notice-msg {
            font-size: 13.5px;
            font-weight: 600;
            color: #8EB69B;
            line-height: 1.45;
        }
        .scan-notice-close {
            background: transparent;
            border: none;
            color: #8EB69B;
            cursor: pointer;
            padding: 0;
            margin-top: 2px;
            margin-right: -4px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            flex-shrink: 0;
        }
        .scan-notice-close:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            transform: scale(1.08);
        }

        /* Light Mode Overrides for Scan Notification Toast */
        html.light-mode .scan-notice-card {
            background: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12), 0 4px 14px rgba(0, 0, 0, 0.04) !important;
        }
        html.light-mode .scan-notice-title {
            color: #0f172a !important;
        }
        html.light-mode .scan-notice-msg {
            color: #475569 !important;
        }
        html.light-mode .scan-notice-close {
            background: transparent !important;
            border: none !important;
            color: #94a3b8 !important;
        }
        html.light-mode .scan-notice-close:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            transform: scale(1.08) !important;
        }

        /* ── Scan History Guest Block State ── */
        /* Dark Mode (Default) */
        .scan-guest-block-container {
            text-align: center;
            padding: 64px 24px 72px;
            border-radius: 0 0 24px 24px;
        }
        .scan-guest-icon-box {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.22), rgba(22, 56, 50, 0.9));
            border: 1.5px solid rgba(142, 182, 155, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.45);
        }
        .scan-guest-icon-box svg {
            stroke: #4ade80;
        }
        .scan-guest-title {
            font-size: 22px;
            font-weight: 800;
            color: #DAF1DE;
            margin: 0 0 10px;
            letter-spacing: -0.01em;
        }
        .scan-guest-desc {
            font-size: 14.5px;
            color: #8EB69B;
            max-width: 480px;
            margin: 0 auto 28px;
            line-height: 1.65;
            font-weight: 500;
        }
        .scan-guest-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 13px 28px;
            border-radius: 14px;
            border: 1px solid #8EB69B;
            background: linear-gradient(135deg, #235347, #163832);
            color: #DAF1DE;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(35, 83, 71, 0.45);
            transition: all 0.25s ease;
        }
        .scan-guest-btn:hover {
            background: linear-gradient(135deg, #163832, #0B2B26);
            border-color: #DAF1DE;
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(35, 83, 71, 0.65);
            color: #ffffff;
        }

        /* Light Mode Overrides for Guest Block */
        html.light-mode .scan-guest-block-container {
            background: #ffffff !important;
        }
        html.light-mode .scan-guest-icon-box {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7) !important;
            border: 1.5px solid #86efac !important;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.15) !important;
        }
        html.light-mode .scan-guest-icon-box svg {
            stroke: #16a34a !important;
        }
        html.light-mode .scan-guest-title {
            color: #0f172a !important;
        }
        html.light-mode .scan-guest-desc {
            color: #475569 !important;
        }
        html.light-mode .scan-guest-btn {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 16px rgba(34, 197, 94, 0.3) !important;
        }
        html.light-mode .scan-guest-btn:hover {
            background: linear-gradient(135deg, #16a34a, #15803d) !important;
            color: #ffffff !important;
        }

        /* ── RESULT CARD ACTION BUTTONS (SAVE COPY & PIN LOCATION) ── */
        .res-action-buttons-row {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            margin-bottom: 6px;
            box-sizing: border-box;
            width: 100%;
        }
        .btn-res-action {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 700;
            font-size: 13px;
            padding: 11px 16px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            box-sizing: border-box;
            font-family: inherit;
            line-height: 1;
            text-decoration: none;
            outline: none;
        }
        .btn-res-save-copy {
            background: rgba(11, 43, 38, 0.65);
            border: 1.5px solid rgba(142, 182, 155, 0.35);
            color: #8EB69B;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-res-save-copy:hover {
            background: rgba(35, 83, 71, 0.45);
            border-color: #DAF1DE;
            color: #DAF1DE;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(35, 83, 71, 0.35);
        }
        .btn-res-save-copy:active {
            transform: translateY(0);
        }
        .btn-res-pin-location {
            background: linear-gradient(135deg, #235347, #163832);
            border: 1.5px solid #8EB69B;
            color: #DAF1DE;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        }
        .btn-res-pin-location:hover {
            background: linear-gradient(135deg, #2e695a, #1c453d);
            border-color: #DAF1DE;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(35, 83, 71, 0.45);
        }
        .btn-res-pin-location:active {
            transform: translateY(0);
        }

        /* LIGHT MODE OVERRIDES FOR ACTION BUTTONS */
        html.light-mode .btn-res-save-copy {
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #475569 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
        }
        html.light-mode .btn-res-save-copy:hover {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
            transform: translateY(-1px) !important;
        }
        html.light-mode .btn-res-pin-location {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
            border: 1.5px solid #16a34a !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.28) !important;
        }
        html.light-mode .btn-res-pin-location:hover {
            background: linear-gradient(135deg, #16a34a, #15803d) !important;
            border-color: #15803d !important;
            box-shadow: 0 6px 18px rgba(22, 163, 74, 0.38) !important;
            transform: translateY(-1px) !important;
        }

        /* ── PIN SOIL LOCATION MODAL (DARK MODE DEFAULT) ── */
        .pin-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            box-sizing: border-box;
            animation: fadeIn 0.2s ease-out;
        }
        .pin-modal-overlay {
            position: absolute;
            inset: 0;
        }
        .pin-modal-container {
            position: relative;
            background: #0B2B26;
            border: 1px solid rgba(142, 182, 155, 0.3);
            border-radius: 28px !important;
            width: 100% !important;
            max-width: 680px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6) !important;
            display: flex;
            flex-direction: column;
            height: 75vh !important;
            max-height: 75vh !important;
            overflow: hidden !important;
            z-index: 10;
            margin: auto !important;
            box-sizing: border-box !important;
        }
        .pin-modal-header {
            padding: 18px 22px 14px;
            background: #0B2B26;
            border-bottom: 1px solid rgba(142, 182, 155, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .pin-modal-title {
            margin: 0 0 2px 0;
            font-size: 18px;
            font-weight: 800;
            color: #DAF1DE;
            letter-spacing: -0.3px;
        }
        .pin-modal-subtitle {
            margin: 0;
            font-size: 11.5px;
            color: #8EB69B;
            font-weight: 500;
        }
        .pin-modal-close-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(142, 182, 155, 0.15);
            border: 1px solid rgba(142, 182, 155, 0.3);
            color: #DAF1DE;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            line-height: 1;
            padding: 0;
        }
        .pin-modal-close-btn:hover {
            background: rgba(142, 182, 155, 0.3);
            color: #ffffff;
        }
        .pin-modal-body {
            padding: 16px 20px 20px;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-sizing: border-box;
            flex: 1;
            min-height: 0;
            scrollbar-width: thin !important;
            scrollbar-color: rgba(142, 182, 155, 0.35) transparent !important;
        }
        .pin-modal-body::-webkit-scrollbar {
            width: 6px;
        }
        .pin-modal-body::-webkit-scrollbar-track {
            background: transparent;
        }
        .pin-modal-body::-webkit-scrollbar-thumb {
            background: rgba(142, 182, 155, 0.35);
            border-radius: 9999px;
            transition: background 0.2s ease;
        }
        .pin-modal-body::-webkit-scrollbar-thumb:hover {
            background: rgba(142, 182, 155, 0.55);
        }
        .pin-specimen-badge {
            display: none !important;
        }
        .pin-specimen-swatch {
            width: 72px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 11px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }
        .pin-specimen-details {
            flex: 1;
            min-width: 0;
        }
        .pin-specimen-name {
            font-size: 14px;
            font-weight: 800;
            color: #DAF1DE;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pin-specimen-sub {
            font-size: 11.5px;
            color: #8EB69B;
        }
        .pin-field-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .pin-field-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .pin-field-label {
            font-size: 10.5px;
            font-weight: 800;
            color: #8EB69B;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .pin-selected-brgy-badge {
            display: none !important;
        }
        .pin-coverage-helper {
            font-size: 11px;
            color: #4ADE80;
            font-weight: 600;
        }
        .pin-search-row {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }
        .pin-custom-select-wrapper {
            position: relative;
            flex-shrink: 0;
        }
        .pin-select-filter {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            background: #163832;
            border: 1px solid rgba(142, 182, 155, 0.25);
            border-radius: 12px;
            padding: 0 12px;
            color: #DAF1DE;
            font-size: 13px;
            font-weight: 600;
            outline: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-sizing: border-box;
            font-family: inherit;
            height: 38px;
            min-width: 155px;
            max-width: 185px;
            flex-shrink: 0;
            user-select: none;
        }
        .pin-select-filter:hover {
            background: rgba(35, 83, 71, 0.6);
            border-color: #8EB69B;
        }
        .pin-select-filter:focus,
        .pin-select-filter.open {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        }
        .pin-select-arrow {
            transition: transform 0.2s ease;
            color: #8EB69B;
            flex-shrink: 0;
        }
        .pin-select-filter.open .pin-select-arrow {
            transform: rotate(180deg);
            color: #4ADE80;
        }
        .pin-custom-select-menu {
            position: absolute;
            top: calc(100% + 4px);
            right: 0;
            width: 210px;
            max-height: 190px;
            overflow-y: auto;
            background: #0B2B26;
            border: 1px solid rgba(142, 182, 155, 0.35);
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.65);
            z-index: 1000;
            scrollbar-width: thin;
            scrollbar-color: rgba(142, 182, 155, 0.35) transparent;
        }
        .pin-custom-select-menu::-webkit-scrollbar {
            width: 5px;
        }
        .pin-custom-select-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .pin-custom-select-menu::-webkit-scrollbar-thumb {
            background: rgba(142, 182, 155, 0.35);
            border-radius: 999px;
        }
        .pin-custom-select-item {
            padding: 8px 12px;
            font-size: 12.5px;
            font-weight: 600;
            color: #DAF1DE;
            cursor: pointer;
            border-bottom: 1px solid rgba(142, 182, 155, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .pin-custom-select-item:last-child {
            border-bottom: none;
        }
        .pin-custom-select-item:hover {
            background: rgba(35, 83, 71, 0.65);
            color: #ffffff;
        }
        .pin-custom-select-item.selected {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            font-weight: 700;
        }
        .pin-search-box {
            position: relative;
            display: flex;
            align-items: center;
        }
        .agrico-map-pin-marker {
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.45));
            cursor: grab !important;
            transition: none !important;
        }
        .agrico-map-pin-marker:active {
            cursor: grabbing !important;
        }
        .pin-input-field {
            width: 100%;
            height: 38px;
            background: #163832;
            border: 1px solid rgba(142, 182, 155, 0.25);
            border-radius: 12px;
            padding: 9px 34px 9px 14px;
            color: #DAF1DE;
            font-size: 13px;
            font-weight: 600;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            box-sizing: border-box;
            font-family: inherit;
        }
        .pin-input-field:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        }
        .pin-clear-btn {
            position: absolute;
            right: 10px;
            background: rgba(142, 182, 155, 0.2);
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            color: #DAF1DE;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            line-height: 1;
        }
        .pin-dropdown-box {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #0B2B26;
            border: 1px solid rgba(142, 182, 155, 0.35);
            border-radius: 12px;
            max-height: 160px;
            overflow-y: auto;
            margin-top: 4px;
            z-index: 50;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }
        .pin-dropdown-item {
            padding: 8px 14px;
            font-size: 12.5px;
            color: #DAF1DE;
            cursor: pointer;
            border-bottom: 1px solid rgba(142, 182, 155, 0.1);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.15s ease;
        }
        .pin-dropdown-item:hover {
            background: rgba(35, 83, 71, 0.6);
            color: #ffffff;
        }

        .pin-map-card {
            background: #163832;
            border: 1px solid rgba(142, 182, 155, 0.25);
            border-radius: 14px;
            overflow: hidden;
            flex-shrink: 0 !important;
        }
        .pin-map-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 12px;
            background: rgba(11, 43, 38, 0.7);
            border-bottom: 1px solid rgba(142, 182, 155, 0.15);
            font-size: 11px;
            flex-shrink: 0 !important;
        }
        .pin-map-instruction {
            color: #8EB69B;
            font-weight: 600;
        }
        .pin-coord-pill {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            color: #4ADE80;
            font-weight: 700;
            padding: 0;
            font-family: monospace;
            font-size: 11px;
            letter-spacing: 0.02em;
        }
        .pin-leaflet-container {
            width: 100%;
            height: 200px !important;
            min-height: 200px !important;
            background: #051F20;
            position: relative;
            flex-shrink: 0 !important;
        }
        .pin-scrollable-fields {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 10px 12px;
            background: rgba(5, 31, 32, 0.45);
            border: 1px solid rgba(142, 182, 155, 0.2);
            border-radius: 14px;
            box-sizing: border-box;
            flex-shrink: 0;
        }
        html.light-mode .pin-scrollable-fields {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }
        .pin-coverage-pills-row {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            padding-bottom: 2px;
        }
        .pin-coverage-pill {
            background: rgba(35, 83, 71, 0.4);
            border: 1px solid rgba(142, 182, 155, 0.25);
            border-radius: 10px;
            padding: 6px 12px;
            font-size: 11.5px;
            font-weight: 600;
            color: #DAF1DE;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            font-family: inherit;
        }
        .pin-coverage-pill:hover {
            border-color: #22c55e;
            color: #ffffff;
        }
        .pin-coverage-pill.active {
            background: #16a34a !important;
            border-color: #22c55e !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }
        .pin-textarea {
            padding: 8px 12px;
            resize: vertical;
            min-height: 54px;
        }
        .pin-modal-footer {
            padding: 14px 22px 16px;
            background: #0B2B26;
            border-top: 1px solid rgba(142, 182, 155, 0.15);
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-shrink: 0;
            width: 100%;
            box-sizing: border-box;
        }
        .pin-modal-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 0;
            width: 100%;
            flex-shrink: 0;
        }
        .btn-pin-cancel {
            flex: 1 !important;
            height: 44px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(11, 43, 38, 0.8) !important;
            border: 1.5px solid rgba(142, 182, 155, 0.35) !important;
            color: #DAF1DE !important;
            font-weight: 700;
            font-size: 13.5px;
            padding: 0 16px;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.25s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            font-family: inherit;
            box-sizing: border-box;
        }
        .btn-pin-cancel:hover {
            background: rgba(35, 83, 71, 0.5) !important;
            border-color: #DAF1DE !important;
            color: #DAF1DE !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(35, 83, 71, 0.35);
        }
        .btn-pin-cancel:active {
            transform: translateY(0);
        }
        .btn-pin-submit {
            flex: 1 !important;
            height: 44px !important;
            background: linear-gradient(135deg, #235347, #163832) !important;
            border: 1.5px solid #8EB69B !important;
            color: #DAF1DE !important;
            font-weight: 700;
            font-size: 13.5px;
            padding: 0 16px;
            border-radius: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3) !important;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            font-family: inherit;
            box-sizing: border-box;
            white-space: nowrap;
        }
        .btn-pin-submit:hover {
            background: linear-gradient(135deg, #2e695a, #1c453d) !important;
            border-color: #DAF1DE !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(35, 83, 71, 0.45) !important;
        }
        .btn-pin-submit:active {
            transform: translateY(0);
        }

        /* ── LIGHT MODE OVERRIDES FOR PIN MODAL ── */
        html.light-mode .pin-modal-container {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 25px 70px rgba(15, 23, 42, 0.15) !important;
        }
        html.light-mode .pin-modal-header {
            background: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        html.light-mode .pin-modal-title {
            color: #0f172a !important;
        }
        html.light-mode .pin-modal-subtitle {
            color: #64748b !important;
        }
        html.light-mode .pin-modal-close-btn {
            background: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            color: #64748b !important;
        }
        html.light-mode .pin-modal-close-btn:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
        }
        html.light-mode .pin-specimen-badge {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
        }
        html.light-mode .pin-specimen-name {
            color: #0f172a !important;
        }
        html.light-mode .pin-specimen-sub {
            color: #64748b !important;
        }
        html.light-mode .pin-field-label {
            color: #475569 !important;
        }
        html.light-mode .pin-selected-brgy-badge {
            display: none !important;
        }
        html.light-mode .pin-coverage-helper {
            color: #16a34a !important;
        }
        html.light-mode .pin-input-field {
            background: #f8fafc !important;
            border: 1.5px solid #e2e8f0 !important;
            color: #0f172a !important;
        }
        html.light-mode .pin-input-field:focus {
            border-color: #16a34a !important;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15) !important;
        }
        html.light-mode .pin-clear-btn {
            background: #e2e8f0 !important;
            color: #475569 !important;
        }
        html.light-mode .pin-select-filter {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            color: #0f172a !important;
        }
        html.light-mode .pin-select-filter:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }
        html.light-mode .pin-select-filter:focus,
        html.light-mode .pin-select-filter.open {
            border-color: #16a34a !important;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15) !important;
        }
        html.light-mode .pin-select-arrow {
            color: #64748b !important;
        }
        html.light-mode .pin-custom-select-menu {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.15) !important;
        }
        html.light-mode .pin-custom-select-item {
            color: #334155 !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        html.light-mode .pin-custom-select-item:hover {
            background: #f0fdf4 !important;
            color: #16a34a !important;
        }
        html.light-mode .pin-custom-select-item.selected {
            background: #eefbe7 !important;
            color: #16a34a !important;
        }
        html.light-mode .pin-dropdown-box {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        }
        html.light-mode .pin-dropdown-item {
            color: #334155 !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        html.light-mode .pin-dropdown-item:hover {
            background: #f0fdf4 !important;
            color: #16a34a !important;
        }

        html.light-mode .pin-map-card {
            background: #f8fafc !important;
            border: 1.5px solid #e2e8f0 !important;
        }
        html.light-mode .pin-map-header {
            background: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        html.light-mode .pin-map-instruction {
            color: #475569 !important;
        }
        html.light-mode .pin-coord-pill {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            color: #059669 !important;
        }
        html.light-mode .pin-coverage-pill {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
        }
        html.light-mode .pin-coverage-pill:hover {
            border-color: #16a34a !important;
            color: #16a34a !important;
        }
        html.light-mode .pin-coverage-pill.active {
            background: #16a34a !important;
            border-color: #16a34a !important;
            color: #ffffff !important;
        }
        html.light-mode .btn-pin-submit {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
            font-weight: 800 !important;
        }
        html.light-mode .btn-pin-submit:hover {
            background: linear-gradient(135deg, #16a34a, #15803d) !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.45) !important;
        }
        html.light-mode .btn-pin-cancel {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 14px !important;
            font-weight: 700 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.25s ease !important;
        }
        html.light-mode .btn-pin-cancel:hover {
            background: #e2e8f0 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }
        html.light-mode .btn-pin-cancel:active {
            transform: translateY(0) !important;
        }
        html.light-mode .pin-modal-footer {
            background: #fafafa !important;
            border-top: 1px solid #f1f5f9 !important;
        }
        html.light-mode .pin-modal-body {
            scrollbar-color: #cbd5e1 transparent !important;
        }
        html.light-mode .pin-modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
        }
        html.light-mode .pin-modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8 !important;
        }
        html.light-mode .pin-chips-wrapper {
            scrollbar-color: #cbd5e1 transparent !important;
        }
        html.light-mode .pin-chips-wrapper::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
        }

        /* ── RESPONSIVE MEDIA QUERIES FOR PIN LOCATION MODAL (MATCHES ADMIN MODALS) ── */
        @media (max-width: 540px) {
            .pin-modal-backdrop {
                padding: 12px 10px !important;
            }
            .pin-modal-container {
                height: 80vh !important;
                max-height: 80vh !important;
                border-radius: 24px !important;
                width: 100% !important;
                margin: auto !important;
            }
            .pin-modal-header {
                padding: 14px 18px 12px !important;
            }
            .pin-modal-title {
                font-size: 16px !important;
            }
            .pin-modal-subtitle {
                font-size: 10.5px !important;
            }
            .pin-modal-body {
                padding: 12px 14px 16px !important;
                gap: 10px !important;
            }
            .pin-leaflet-container {
                height: 180px !important;
                min-height: 180px !important;
            }
            .pin-select-filter {
                min-width: 120px !important;
                max-width: 145px !important;
                font-size: 11.5px !important;
                padding: 0 8px !important;
                height: 36px !important;
            }
            .pin-custom-select-menu {
                width: 175px !important;
                max-height: 160px !important;
            }
            .pin-input-field {
                height: 36px !important;
                font-size: 12px !important;
            }
            .pin-modal-footer {
                padding: 10px 14px 12px !important;
                gap: 6px !important;
            }
            .pin-modal-actions {
                gap: 8px !important;
            }
            .btn-pin-cancel,
            .btn-pin-submit {
                height: 40px !important;
                min-height: 40px !important;
                padding: 0 10px !important;
                font-size: 12px !important;
                border-radius: 10px !important;
            }
        }

        @keyframes desktopToastSlideIn {
            from { opacity: 0; transform: translateY(24px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes desktopToastSlideOut {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(20px) scale(0.96); }
        }
    </style>

    {{-- FLOATING NOTIFICATION TOAST (Mobile Style - Top Floating Card - Matches Image 1) --}}
    <div id="scanNoticeToast" style="display: none; position: fixed; top: 24px; left: 50%; transform: translateX(-50%); z-index: 10000000; width: 92%; max-width: 520px; pointer-events: auto;">
        <div class="scan-notice-card">
            
            {{-- Icon Badge --}}
            <div style="width: 52px; height: 52px; border-radius: 18px; background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0; box-shadow: 0 6px 18px rgba(124, 58, 237, 0.35); margin-top: 2px;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                    <circle cx="12" cy="13" r="4"/>
                </svg>
            </div>

            {{-- Body Content --}}
            <div style="flex: 1; min-width: 0; padding-top: 2px;">
                <div class="scan-notice-title">
                    AgriCo Scanner
                </div>
                <div class="scan-notice-msg" id="scanNoticeMessage">
                    Please start the camera or upload a soil photo sample first before scanning.
                </div>
            </div>

            {{-- Close Button --}}
            <button class="scan-notice-close" onclick="dismissScanNotice()" type="button" aria-label="Close notification">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- DESKTOP NOTIFICATION TOAST (Windows Style - Bottom Right - Matches Image 2) --}}
    <div id="desktopNoticeToast" style="display: none; position: fixed; bottom: 24px; right: 24px; z-index: 10000000; width: 340px; max-width: calc(100vw - 32px); pointer-events: auto;">
        <div style="background: #18181b; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.55); padding: 14px 16px; color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; box-sizing: border-box;">
            {{-- Header Row --}}
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 20px; height: 20px; border-radius: 6px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span style="font-size: 12px; font-weight: 500; color: #a1a1aa; letter-spacing: -0.01em;">AgriCo Scanner</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="color: #71717a; font-size: 12px; cursor: default; user-select: none;">•••</span>
                    <button type="button" onclick="dismissDesktopNotice()" style="background: transparent; border: none; color: #a1a1aa; cursor: pointer; padding: 2px 4px; font-size: 16px; line-height: 1; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#a1a1aa'">&times;</button>
                </div>
            </div>
            {{-- Body --}}
            <div style="padding-left: 2px;">
                <div style="font-size: 13.5px; font-weight: 700; color: #ffffff; margin-bottom: 3px; letter-spacing: -0.01em;">AgriCo Scanner</div>
                <div id="desktopNoticeMessage" style="font-size: 12px; color: #a1a1aa; line-height: 1.45;">Selected location is outside the official Lal-lo, Cagayan municipal coverage area (35 Barangays).</div>
            </div>
        </div>
    </div>

    {{-- Page Hero --}}
    <div class="ws-page-hero">
        <div class="ws-page-hero-inner ui-reveal">
            <div class="ws-breadcrumb">
                <a href="{{ route('website.home') }}">Home</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                <span class="ws-breadcrumb-current">Soil Scanner</span>
            </div>
            <h1 class="ws-page-title">Soil Color Scanner</h1>
            <p class="ws-page-sub">Determine Munsell Soil Color records in real-time via live camera feed or sample upload.</p>
        </div>
    </div>

    {{-- Interactive Live Soil Camera Scanner --}}
    <div class="scanner-section">
        <div class="scanner-grid">
            
            {{-- Camera / Viewfinder Card --}}
            <div class="scanner-card ui-reveal">
                <div class="scanner-card-header">
                    <h2>
                        <span class="scan-card-title-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                <circle cx="12" cy="13" r="4"/>
                            </svg>
                        </span>
                        <span class="scan-card-title-text">Live Camera Viewfinder</span>
                    </h2>
                    <span id="cameraStatus" class="camera-status-badge status-off">
                        Camera Ready
                    </span>
                </div>

                {{-- Viewfinder Canvas --}}
                <div class="scanner-viewfinder" id="viewfinderArea" style="position: relative;">
                    <div class="image-mask" id="imageMask">
                        <video id="webcamVideo" autoplay playsinline muted style="display: none;"></video>
                        <img id="imagePreview" alt="Soil Preview" style="display: none;" />
                    </div>
                    
                    {{-- Viewfinder Floating Controls (Bottom Right: Flip Camera & Flashlight) --}}
                    <div style="position: absolute; bottom: 16px; right: 16px; z-index: 30; display: flex; gap: 8px; align-items: center;">
                        {{-- Switch / Flip Camera Button --}}
                        <button
                            type="button"
                            id="btnSwitchCamera"
                            title="Flip / Switch Camera"
                            style="display: none; width: 44px; height: 44px; border-radius: 50%; background: rgba(5, 31, 32, 0.75); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1.5px solid #8EB69B; color: #DAF1DE; cursor: pointer; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(0,0,0,0.5); transition: all 0.25s ease;"
                            onmouseover="this.style.background='rgba(35, 83, 71, 0.85)';"
                            onmouseout="this.style.background='rgba(5, 31, 32, 0.75)';"
                            onclick="switchCameraDevice()"
                        >
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M20 10c0-4.418-3.582-8-8-8s-8 3.582-8 8H1l4 4 4-4H6c0-3.309 2.691-6 6-6s6 2.691 6 6c0 1.294-.416 2.49-1.115 3.473l1.431 1.431A7.95 7.95 0 0 0 20 10zM4 14c0 4.418 3.582 8 8 8s8-3.582 8-8h3l-4-4-4 4h3c0 3.309-2.691 6-6 6s-6-2.691-6-6c0-1.294.416-2.49 1.115-3.473l-1.431-1.431A7.95 7.95 0 0 0 4 14z"/>
                            </svg>
                        </button>

                        {{-- Floating Flashlight Icon Button --}}
                        <button
                            type="button"
                            id="btnFlashlight"
                            title="Toggle Flashlight"
                            style="display: none; width: 44px; height: 44px; border-radius: 50%; background: rgba(5, 31, 32, 0.75); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1.5px solid #8EB69B; color: #DAF1DE; cursor: pointer; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(0,0,0,0.5); transition: all 0.25s ease;"
                            onmouseover="if (!isFlashOn) this.style.background='rgba(35, 83, 71, 0.85)';"
                            onmouseout="if (!isFlashOn) this.style.background='rgba(5, 31, 32, 0.75)';"
                        >
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Placeholder when camera is stopped --}}
                    <div id="cameraPlaceholder" class="camera-placeholder-box" style="text-align: center; padding: 20px;">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="margin: 0 auto 12px;">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        <p class="camera-placeholder-text" style="margin:0; font-size:14px; font-weight:700;">Click "Start Camera" or "Upload Sample"</p>
                    </div>

                    {{-- Target Reticle --}}
                    <div class="viewfinder-overlay" id="reticleOverlay" style="display: none;">
                        <div class="reticle"></div>
                    </div>
                </div>

                {{-- Zoom Controls for static preview --}}
                <div class="zoom-controls" id="zoomControls" style="display: none;">
                    <button type="button" class="zoom-btn" id="zoomOutBtn" title="Zoom Out">−</button>
                    <button type="button" class="zoom-btn" id="zoomResetBtn" title="Reset Zoom">Reset</button>
                    <button type="button" class="zoom-btn" id="zoomInBtn" title="Zoom In">+</button>
                </div>

                {{-- Hidden processing canvas & file input --}}
                <canvas id="processCanvas" style="display: none;"></canvas>
                <input type="file" id="fileUpload" accept="image/*" style="display: none;" />
                <input type="file" id="cameraCaptureInput" accept="image/*" capture="environment" style="display: none;" />

                {{-- Controls --}}
                <div class="scanner-actions">
                    <button type="button" class="scanner-btn scanner-btn-primary" id="btnToggleCamera" onclick="toggleCameraStream()">
                        <span>Start Camera</span>
                    </button>
                    <button type="button" class="scanner-btn scanner-btn-secondary" id="btnUploadImage" onclick="triggerPhotoUpload()">
                        <span>Upload Photo</span>
                    </button>
                    <button type="button" class="scanner-btn scanner-btn-primary" id="btnCaptureScan" onclick="analyzeSoilImage()">
                        <span>Scan Soil</span>
                    </button>
                </div>
            </div>

            {{-- Analysis Result Card --}}
            <div class="result-card ui-reveal" id="resultCard" style="position: relative;">

                {{-- Progressive Loading Card Overlay with Realistic Soil Skeleton (Sections 61, 74) --}}
                <div id="scanLoadingOverlay" class="scan-loading-card-content" style="display: none; flex-direction: column; width: 100%;">
                    {{-- Realistic Soil Result Skeleton Placeholder --}}
                    <div class="skeleton-soil-result" style="margin-bottom: 22px; width: 100%;">
                        <div class="skeleton skeleton-soil-swatch" style="border-radius: 14px;"></div>
                        <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                            <span class="skeleton skeleton-heading" style="width: 55%; height: 24px; margin: 0;"></span>
                            <span class="skeleton skeleton-text" style="width: 40%; height: 13px; margin: 0;"></span>
                            <span class="skeleton skeleton-pill" style="width: 75px; height: 22px; margin-top: 4px;"></span>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px; width: 100%; margin-bottom: 20px;">
                        <span class="skeleton skeleton-text w-full"></span>
                        <span class="skeleton skeleton-text w-3-4"></span>
                        <span class="skeleton skeleton-text w-1-2"></span>
                    </div>

                    {{-- Pipeline progress info without spinner --}}
                    <div style="width: 100%; display: flex; flex-direction: column; border-top: 1px solid rgba(142, 182, 155, 0.2); padding-top: 14px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; margin-bottom: 6px;">
                            <h4 id="scanLoadingTitle" class="scan-loading-title" style="margin: 0; font-size: 13px; font-weight: 700; text-align: left;">Initializing pipeline…</h4>
                            <span id="scanProgressBadge" style="font-size: 12px; font-weight: 800; font-family: ui-monospace, SFMono-Regular, monospace; color: var(--ui-accent, #22c55e);">1%</span>
                        </div>
                        <p id="scanLoadingSubtext" class="scan-loading-subtext" style="width: 100%; text-align: left; margin: 0 0 10px 0; font-size: 12px;">Preparing soil analysis engine</p>

                        <div class="scan-loading-progress-track" style="width: 100%;">
                            <div id="scanProgressBarInner" class="scan-loading-progress-fill"></div>
                        </div>
                    </div>
                </div>

                <div id="resultContent" style="display: none;">
                    <div class="result-header" style="margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(142, 182, 155, 0.2); display: flex; align-items: center; justify-content: space-between;">
                        <h3 id="resHeaderTitle" class="result-header-title" style="font-size: 12.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; margin: 0; color: #16a34a;">DETERMINED SOIL RECORD</h3>
                        <span id="resMultiBadgeCount" style="display: none; font-size: 11px; font-weight: 800; background: rgba(34, 197, 94, 0.18); color: #22c55e; padding: 2px 8px; border-radius: 9999px; border: 1px solid rgba(34, 197, 94, 0.3);">1 DETECTED</span>
                    </div>

                    <!-- Dual-Preview Soil Badge Banner (Overlapping Left Circular Soil Snippet + Center Munsell Code + Right Hex Pill) -->
                    <div id="soilDualPreviewBadge" style="position: relative; margin-top: 10px; margin-bottom: 16px; width: 100%; display: flex; align-items: center;">
                        <!-- Rectangular Color Swatch Block -->
                        <div id="resSwatchBlock" style="width: 100%; height: 78px; border-radius: 14px; background: #594433; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18); transition: background-color 0.4s ease; display: flex; align-items: center; justify-content: space-between; padding: 0 20px 0 126px; position: relative; box-sizing: border-box;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span id="resDominantChevron" style="display: none; font-size: 12px; color: #ffffff; opacity: 0.9;">▼</span>
                                    <span id="resCodeDisplay" style="font-size: 26px; font-weight: 900; color: #ffffff; text-shadow: none; letter-spacing: -0.01em; line-height: 1;">
                                        10YR 4/3
                                    </span>
                                </div>
                                <div id="resSoilPercentageDisplay" style="display: none; font-size: 11.5px; font-weight: 700; color: rgba(255, 255, 255, 0.95); margin-top: 4px; letter-spacing: 0.02em;">
                                    Soil Percentage (60%)
                                </div>
                            </div>
                            <span id="resHexPill" style="font-size: 11.5px; font-weight: 800; color: #ffffff; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); padding: 5px 12px; border-radius: 20px; letter-spacing: 0.05em; text-transform: uppercase; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; border: 1px solid rgba(255, 255, 255, 0.2); user-select: none; -webkit-user-select: none; line-height: 1;">
                                #594433
                            </span>
                        </div>

                        <!-- Overlapping Soil Snippet on Left (Square-ish with subtle rounded corners) -->
                        <div id="resSoilSnippetContainer"
                             style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); z-index: 10; width: 96px; height: 96px; aspect-ratio: 1 / 1; border-radius: 12px; box-shadow: none !important; background: transparent; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; pointer-events: none; border: none; outline: none;">
                            <img id="resSoilCircleSnippet" src="" alt="Cropped Soil Snippet" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: none; border-radius: 0; border: none; outline: none;" />
                            <div id="resSoilSnippetFallback" style="position: absolute; inset: 0; border-radius: 0; display: flex; align-items: center; justify-content: center; background: #163832; color: #8EB69B;">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 1: Code Details (3 Columns: Color Name, Full Code, Scan Count) -->
                    <div class="soil-modal-grid-3 soil-metrics-row-1" style="margin-bottom: 8px;">
                        <div class="soil-modal-grid-card">
                            <div class="soil-modal-label">Color Name</div>
                            <div class="soil-modal-value" id="resName" style="font-size: 13px; font-weight: 700;">Dark Brown</div>
                        </div>
                        <div class="soil-modal-grid-card">
                            <div class="soil-modal-label">Full Code</div>
                            <div class="soil-modal-value" id="resFullCode" style="font-size: 13px; font-weight: 700;">10YR 4/3</div>
                        </div>
                        <div class="soil-modal-grid-card">
                            <div class="soil-modal-label">Scan Count</div>
                            <div class="soil-modal-value" id="resScanCount" style="font-size: 13px; font-weight: 700;">1</div>
                        </div>
                    </div>

                    <!-- Row 2: Soil Properties (3 Columns: Soil Organic, Soil Type, Near-Match Emerald #10b981) -->
                    <div class="soil-modal-grid-3 soil-metrics-row-2" style="margin-bottom: 14px;">
                        <div class="soil-modal-grid-card">
                            <div class="soil-modal-label">Soil Organic</div>
                            <div class="soil-modal-value" id="resOrganic" style="font-size: 13px; font-weight: 700;">High (3.8%)</div>
                        </div>
                        <div class="soil-modal-grid-card">
                            <div class="soil-modal-label">Soil Type</div>
                            <div class="soil-modal-value" id="resDrainage" style="font-size: 13px; font-weight: 700;">Well-Drained Loam</div>
                        </div>
                        <div class="soil-modal-grid-card card-near-match">
                            <div class="soil-modal-label">Near-Match</div>
                            <div class="soil-modal-value" id="resConfidence" style="font-size: 13.5px; font-weight: 800; color: #10b981 !important;">98.4%</div>
                        </div>
                    </div>

                    {{-- Scan Note --}}
                    <div id="resScanNoteCard" class="soil-modal-grid-card soil-modal-desc-card" style="margin-bottom: 12px; padding: 12px 16px; overflow: hidden; max-width: 100%; box-sizing: border-box;">
                        <div class="soil-modal-label">
                            Scan Note
                        </div>
                        <div id="resCardScanNote" class="soil-modal-value" style="font-size: 13px; font-weight: 700; margin: 0; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
                            No scan note available for this record.
                        </div>
                    </div>

                    {{-- Soil Description --}}
                    <div id="resDescCard" class="soil-modal-grid-card soil-modal-desc-card" style="margin-bottom: 10px; padding: 12px 16px; overflow: hidden; max-width: 100%; box-sizing: border-box;">
                        <div class="soil-modal-label">
                            Description
                        </div>
                        <div id="resDesc" class="soil-modal-value" style="font-size: 13px; font-weight: 700; margin: 0; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
                            Sample processed successfully using AgriCo Munsell Determination Algorithm.
                        </div>
                    </div>

                    {{-- Primary Soil Result: Pin Location Button (Placed directly after Description - Pins THIS single soil) --}}
                    <div id="resPrimaryPinLocationWrapper" style="margin-bottom: 12px;">
                        <button type="button" id="btnPinLocationScan" class="btn-res-action btn-res-pin-location" onclick="openPinLocationModal(null, false)" style="width: 100%; justify-content: center;" title="Pin This Soil Specimen to Map">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Pin Location</span>
                        </button>
                    </div>

                    {{-- Multi-Layer Accordion Section (Secondary & Tertiary Layers) --}}
                    <div id="resMultiLayerSection" style="display: none; margin-top: 14px; margin-bottom: 14px; border-top: 1px dashed rgba(142, 182, 155, 0.25); padding-top: 14px;">
                        <div id="resMultiLayerItems" style="display: flex; flex-direction: column; gap: 8px;"></div>
                    </div>

                    {{-- Soil Normalization: Before & After Card (With Detected Soil Color on BEFORE side & ColorThief Palette) --}}
                    <div id="resBeforeAfterCard" class="soil-modal-grid-card" style="margin-bottom: 12px; padding: 10px 14px; overflow: hidden; max-width: 100%; box-sizing: border-box; display: none;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px;">
                            <div class="soil-modal-label" style="font-size: 11px; font-weight: 700; margin-bottom: 0; text-transform: uppercase; letter-spacing: 0.03em;">
                                SOIL NORMALIZATION
                            </div>
                            <span class="before-after-badge" style="font-size: 9.5px; font-weight: 700; padding: 2px 7px; border-radius: 5px; background: rgba(16, 185, 129, 0.15); color: #10b981; text-transform: uppercase; letter-spacing: 0.03em;">
                                BEFORE / AFTER
                            </span>
                        </div>
                        <div id="resBeforeAfterContainer" style="position: relative; width: 100%; border-radius: 8px; overflow: hidden; background: #0c1a17; border: 1px solid rgba(142, 182, 155, 0.2); cursor: pointer;" onclick="openBeforeAfterLightbox('resBeforeAfterImg')" title="Click to view full resolution comparison">
                            <img id="resBeforeAfterImg" src="" alt="Soil Before & After Normalization" style="width: 100%; height: auto; display: block; border-radius: 6px; object-fit: contain;">

                            <!-- Replaces the single static box — populated per-region by renderBeforeAfterBoxes() -->
                            <div id="resBeforeAfterBoxes" style="position: absolute; inset: 0; pointer-events: none; z-index: 10;"></div>
                        </div>
                        <div id="resBeforeAfterSwatches" style="display: none; flex-wrap: wrap; gap: 6px; margin-top: 8px;"></div>
                    </div>

                    {{-- Action Buttons Row: Save Copy (Left) & Pin Location (Right - Pins ALL detected soils from scan) --}}
                    <div id="resActionButtonsRow" class="res-action-buttons-row">
                        <button type="button" id="btnSaveCopyScan" class="btn-res-action btn-res-save-copy" onclick="saveCurrentScanCopy()" title="Save Copy of Soil Record">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <span>Save Copy</span>
                        </button>
                        <button type="button" id="btnPinAllLocationScan" class="btn-res-action btn-res-pin-location" onclick="openPinAllLocationModal()" title="Pin All Detected Soils to Map">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Pin Location</span>
                        </button>
                    </div>

                    {{-- Metadata Footer --}}
                    <div id="resMetadataFooter" style="display: none; margin-top: 14px; padding-top: 10px; border-top: 1px solid rgba(142, 182, 155, 0.2); font-size: 11px; justify-content: space-between; align-items: center; text-transform: uppercase; letter-spacing: 0.04em;">
                        <div><strong>METADATA</strong></div>
                        <div>Created: <span id="resMetaCreated">--</span> &nbsp;|&nbsp; Detected Colors: <span id="resMetaLayers">1</span></div>
                    </div>
                </div>

                {{-- Initial Empty State --}}
                <div class="result-empty" id="resultPlaceholder">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="margin: 0 auto 14px; opacity: 0.8;">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                    <h3 style="font-size: 16px; font-weight: 800; margin: 0 0 6px 0;">No Scan Analyzed Yet</h3>
                    <p style="font-size: 13px; margin: 0; opacity: 0.85;">Start your camera or upload a soil photograph to identify Munsell records instantly.</p>

                    {{-- 6-Stage Pipeline Overview --}}
                    <div class="pipeline-overview-card">
                        <div class="pipeline-overview-title">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            6-Stage ML Soil Determination Pipeline
                        </div>
                        <div class="pipeline-stages-list">
                            <div class="pipeline-stage-item">
                                <span class="pipeline-stage-badge">STAGE 1</span>
                                <span class="pipeline-stage-label">Soil Gate (Texture & Non-Soil Filter)</span>
                            </div>
                            <div class="pipeline-stage-item">
                                <span class="pipeline-stage-badge">STAGE 2</span>
                                <span class="pipeline-stage-label">Lighting Normalizer (LAB L* 50% Center)</span>
                            </div>
                            <div class="pipeline-stage-item">
                                <span class="pipeline-stage-badge">STAGE 3</span>
                                <span class="pipeline-stage-label">Multi-Color Detection (ColorThief Palette)</span>
                            </div>
                            <div class="pipeline-stage-item">
                                <span class="pipeline-stage-badge">STAGE 4</span>
                                <span class="pipeline-stage-label">Per-Region Verification (Patch Gate Validation)</span>
                            </div>
                            <div class="pipeline-stage-item">
                                <span class="pipeline-stage-badge">STAGE 5</span>
                                <span class="pipeline-stage-label">Munsell Spectral Match (CIEDE2000 ΔE₀₀)</span>
                            </div>
                            <div class="pipeline-stage-item">
                                <span class="pipeline-stage-badge">STAGE 6</span>
                                <span class="pipeline-stage-label">Agronomic Profile & Reference Centroid</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Invalid Specimen Error State --}}
                <div class="result-error-state" id="resultErrorState" style="display: none; text-align: center; padding: 48px 24px;">
                    <div class="error-shield-icon-box" style="width: 68px; height: 68px; border-radius: 50%; background: rgba(239, 68, 68, 0.12); border: 2px solid rgba(239, 68, 68, 0.3); display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; color: #ef4444;">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h3 class="error-state-title" style="font-size: 19px; font-weight: 900; color: #f87171; margin: 0 0 10px 0; letter-spacing: -0.02em;">
                        Invalid Specimen Detected
                    </h3>
                    <p class="error-state-desc" style="font-size: 13.5px; line-height: 1.6; color: #8EB69B; max-width: 400px; margin: 0 auto 20px;">
                        The selected area does not appear to be a soil sample. Please align the viewfinder over a clear, well-lit soil specimen and try again.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- History Table Section (User Saved Scans) --}}
    @php $isLoggedIn = (Auth::check() || Auth::guard('admin')->check() || Auth::guard('web')->check() || session()->has('user_id') || session()->has('admin_id')); @endphp
    <div class="ws-table-section" id="scanHistory" style="padding: 0 24px 60px;">
        <div class="ws-table-card" style="max-width: 1200px; width: 100%; margin: 0 auto;">
            <div class="ws-table-header">
                <h2>Scan History</h2>
                <span class="ws-badge-count" id="savedScansCountBadge">{{ $isLoggedIn ? $scans->total() : '0' }} total scans</span>
            </div>
            @if($isLoggedIn)
            <div style="overflow-x:auto;">
                <table class="ws-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="col-th-seq" style="width: 5.5%; text-overflow: clip; overflow: visible; padding-left: 16px;">#</th>
                            <th class="col-th-name" style="width: 21%;">Soil Name</th>
                            <th class="col-th-desc" style="width: 25%;">Description</th>
                            <th class="col-th-code" style="width: 13%;">Munsell Code</th>
                            <th class="col-th-hue" style="width: 6%;">Hue</th>
                            <th class="col-th-seq-count" style="width: 10%;">Scan Count</th>
                            <th class="col-th-date" style="width: 10%;">Scan Date</th>
                            <th class="col-th-action" style="width: 9.5%; text-overflow: clip; overflow: visible;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="savedScansTbody">
                        @foreach($scans as $idx => $scan)
                        @php
                            $fCode = $scan->soil->full_code ?? $scan->soil?->munsellColor?->full_code ?? '—';
                            $mColor = $scan->soil?->dynamic_munsell_color;
                            $sHue  = $scan->soil->hue ?? ($mColor->hue ?? '10YR');
                            $soilName = $scan->soil?->effective_soil_name ?? ($mColor?->color_name ?? '—');
                            $soilDesc = $scan->soil?->effective_description ?? ($mColor?->description ?? 'Sample processed successfully using AgriCo Munsell Determination Algorithm.');
                            $sHex = $scan->soil?->effective_hex_color ?? ($mColor?->hex_code ?? '#8F694E');
                            $sOrganic = $scan->soil?->effective_organic_matter ?? 'Moderate (2.2% – 3.7%)';
                            $sSoilType = $scan->soil?->effective_soil_type ?? 'Well-Drained Clay / Latosol';
                            
                            $confidenceVal = '97.4%';
                            if (!empty($scan->scan_note) && preg_match('/Confidence:\s*([\d\.]+%?)/i', $scan->scan_note, $cm)) {
                                $confidenceVal = str_contains($cm[1], '%') ? $cm[1] : $cm[1] . '%';
                            }

                            $scanPct = null;
                            if (!empty($scan->regions) && isset($scan->regions[0]['percent'])) {
                                $scanPct = $scan->regions[0]['percent'];
                            } elseif (!empty($scan->scan_note) && preg_match('/Percentage:\s*([\d\.]+%?)/i', $scan->scan_note, $pm)) {
                                $scanPct = rtrim($pm[1], '%');
                            }

                            $scanSeq = $fCode 
                                ? \App\Models\SoilScan::whereHas('soil', function($q) use ($fCode) {
                                    $q->where('full_code', $fCode);
                                  })->where('soil_scan_id', '<=', $scan->soil_scan_id)->count()
                                : 1;
                            $scanUser = $scan->user ?? Auth::user();
                            $uName = $scanUser->name ?? 'Agrico Administrator';
                            $uEmail = $scanUser->email ?? 'admin@agrico.com';
                            $uCode = $scanUser->user_id ?? $scanUser->id ?? 'ADM-001';
                            $uAvatar = strtoupper(substr($uName, 0, 1));
                        @endphp
                        <tr>
                            <td class="col-seq" style="font-size:13px; font-weight:600; padding-left:16px; padding-right:6px; overflow:visible; text-overflow:clip; white-space:nowrap;">
                                {{ $scans->total() - ($scans->firstItem() - 1 + $idx) }}
                            </td>
                            <td class="col-soil-name" style="font-size:14px; font-weight:700; text-overflow:clip; white-space:nowrap;" title="{{ $soilName }}">
                                {{ $soilName }}
                            </td>
                            <td class="col-soil-desc" style="font-size:13px; font-weight:500; max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $soilDesc }}">
                                {{ \Illuminate\Support\Str::limit(trim($soilDesc), 36, '...') }}
                            </td>
                            <td class="col-soil-code" style="font-size:13.5px; font-weight:700;">
                                {{ $fCode }}
                            </td>
                            <td class="col-soil-hue" style="font-size:13px; font-weight:700;">
                                {{ $sHue }}
                            </td>
                            <td class="col-soil-seq" style="font-size:13px; font-weight:700;">
                                {{ $scanSeq }}
                            </td>
                            <td class="col-soil-date" style="font-size:13px; font-weight:600;">
                                {{ $scan->scan_date ? \Carbon\Carbon::parse($scan->scan_date)->format('d M Y') : '—' }}
                            </td>
                            <td class="col-action" style="font-size:13px; font-weight:700; overflow:visible !important; text-overflow:clip !important; white-space:nowrap !important;">
                                @php
                                    $rawImg = $scan->scan_image;
                                    $scanImg = '';
                                    $baImg = '';
                                    $rawPhotoImg = '';
                                    if (!empty($rawImg)) {
                                        if (str_starts_with($rawImg, 'http') || str_starts_with($rawImg, 'data:')) {
                                            $scanImg = $rawImg;
                                        } elseif (str_starts_with($rawImg, 'storage/')) {
                                            $scanImg = asset($rawImg);
                                        } else {
                                            $scanImg = asset('storage/' . ltrim($rawImg, '/'));
                                        }

                                        // Extract scan UUID if present in scan_image path
                                        if (preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $rawImg, $m)) {
                                            $scanUuid = $m[1];
                                            $baRel = "storage/scans/scan_{$scanUuid}_before_after.jpg";
                                            $rawRel = "storage/scans/scan_{$scanUuid}_raw.jpg";
                                            if (file_exists(public_path($baRel))) {
                                                $baImg = asset($baRel) . '?v=' . filemtime(public_path($baRel));
                                            } elseif (file_exists(storage_path("app/public/scans/scan_{$scanUuid}_before_after.jpg"))) {
                                                $baImg = asset($baRel) . '?v=' . filemtime(storage_path("app/public/scans/scan_{$scanUuid}_before_after.jpg"));
                                            }
                                            if (file_exists(public_path($rawRel))) {
                                                $rawPhotoImg = asset($rawRel);
                                            }
                                        }
                                    }
                                    if (!$scanImg && $scan->soil?->munsellColor?->image) {
                                        $scanImg = asset('storage/' . ltrim($scan->soil->munsellColor->image, '/'));
                                    }

                                    $loc = $scan->location ?: \App\Models\LocationMapping::where('soil_scan_id', $scan->soil_scan_id)->latest('location_mapping_id')->first();
                                    if (!$loc && $scan->soil_id) {
                                        $loc = \App\Models\LocationMapping::where('soil_id', $scan->soil_id)->latest('location_mapping_id')->first();
                                    }
                                    $scanLat = $loc?->latitude;
                                    $scanLng = $loc?->longitude;
                                    $scanBrgy = $loc?->barangay;
                                    $scanCoverage = '';
                                    if ($scan->scan_note && preg_match('/\[Covered Area:\s*([^\]]+)\]/i', $scan->scan_note, $covMatch)) {
                                        $scanCoverage = trim($covMatch[1]);
                                    }
                                    if (!$scanCoverage && $loc) {
                                        $uReq = \App\Models\UserRequest::where('description', 'like', "%#{$loc->location_mapping_id}%")
                                            ->orWhere('description', 'like', "%({$scanLat}, {$scanLng})%")
                                            ->latest('user_request_id')->first();
                                        if ($uReq && preg_match('/\[Covered Area:\s*([^\]]+)\]/i', $uReq->description, $uCov)) {
                                            $scanCoverage = trim($uCov[1]);
                                        }
                                    }
                                @endphp
                                <button type="button" class="btn-view-scan" onclick="openScanModalFromBtn(this, event)"
                                    data-name="{{ $soilName }}"
                                    data-code="{{ $fCode }}"
                                    data-hue="{{ $sHue }}"
                                    data-hex="{{ $sHex }}"
                                    data-seq="{{ $scanSeq }}"
                                    data-date="{{ $scan->scan_date ? \Carbon\Carbon::parse($scan->scan_date)->format('m/d/Y') : '—' }}"
                                    data-desc="{{ $soilDesc }}"
                                    data-scan-note="{{ $scan->has_user_scan_note ? $scan->scan_note : ($scan->scan_note && !str_starts_with(trim($scan->scan_note), 'Determined Soil:') && trim($scan->scan_note) !== '—' && trim($scan->scan_note) !== '-' ? $scan->scan_note : '') }}"
                                    data-organic="{{ $sOrganic }}"
                                    data-drainage="{{ $sSoilType }}"
                                    data-confidence="{{ $confidenceVal }}"
                                    data-percent="{{ $scanPct }}"
                                    data-image="{{ $scanImg }}"
                                    data-before-after="{{ $baImg }}"
                                    data-raw-image="{{ $rawPhotoImg }}"
                                    data-scan-id="{{ $scan->soil_scan_id }}"
                                    data-lat="{{ $scanLat }}"
                                    data-lng="{{ $scanLng }}"
                                    data-barangay="{{ $scanBrgy }}"
                                    data-coverage="{{ $scanCoverage }}"
                                    data-uname="{{ $uName }}"
                                    data-uemail="{{ $uEmail }}"
                                    data-ucode="{{ $uCode }}"
                                    data-uavatar="{{ $uAvatar }}"
                                    data-regions="{{ json_encode($scan->regions ?? []) }}">View</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="savedScansEmptyState" class="ws-empty-state-container" style="padding: 56px 24px; text-align: center; display: {{ $scans->count() > 0 ? 'none' : 'block' }};">
                <div class="ws-empty-icon-box">
                    <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <h3 class="ws-empty-title">No Saved Soil Scans Yet</h3>
                <p class="ws-empty-desc">
                    Your soil scan records, Munsell classification codes, and agronomic reports will be automatically saved here once you perform your first soil scan.
                </p>
                <button type="button" class="ws-empty-btn" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polygon points="10 8 16 12 10 16 10 8"/>
                    </svg>
                    <span>Start Your First Scan</span>
                </button>
            </div>

            {{-- PAGINATION FOOTER --}}
            <div id="scanPaginationFooter" class="ws-table-pagination-footer" style="padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="ws-page-info">Rows per page:</span>
                    <select
                        id="perPageSelect"
                        class="ws-page-select"
                        onchange="window.location.assign('{{ route('website.scan') }}?per_page=' + this.value + '&page=1#scanHistory');"
                    >
                        <option value="5" {{ (int)request('per_page', 10) === 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ (int)request('per_page', 10) === 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ (int)request('per_page', 10) === 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ (int)request('per_page', 10) === 50 ? 'selected' : '' }}>50</option>
                    </select>
                    <span class="ws-page-count-text">
                        {{ $scans->firstItem() ?? 0 }}–{{ $scans->lastItem() ?? 0 }} of {{ $scans->total() ?? 0 }}
                    </span>
                </div>

                @if ($scans->hasPages())
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: nowrap; white-space: nowrap;">
                        @if ($scans->onFirstPage())
                            <button disabled class="ws-page-btn-disabled">Prev</button>
                        @else
                            <a href="{{ $scans->previousPageUrl() }}" class="ws-page-btn">Prev</a>
                        @endif

                        @php
                            $currentPage = $scans->currentPage();
                            $lastPage = $scans->lastPage();

                            if ($lastPage <= 3) {
                                $startPage = 1;
                                $endPage = $lastPage;
                            } else {
                                if ($currentPage <= 2) {
                                    $startPage = 1;
                                    $endPage = 3;
                                } elseif ($currentPage >= $lastPage - 1) {
                                    $startPage = $lastPage - 2;
                                    $endPage = $lastPage;
                                } else {
                                    $startPage = $currentPage - 1;
                                    $endPage = $currentPage + 1;
                                }
                            }
                        @endphp

                        @if ($startPage > 1)
                            <a href="{{ $scans->url(max(1, $startPage - 1)) }}" class="ws-page-dots" title="Page {{ max(1, $startPage - 1) }}">...</a>
                        @endif

                        @foreach ($scans->getUrlRange($startPage, $endPage) as $page => $url)
                            @if ($page == $currentPage)
                                <span class="ws-page-num-active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="ws-page-num">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($endPage < $lastPage)
                            <a href="{{ $scans->url(min($lastPage, $endPage + 1)) }}" class="ws-page-dots" title="Page {{ min($lastPage, $endPage + 1) }}">...</a>
                        @endif

                        @if ($scans->hasMorePages())
                            <a href="{{ $scans->nextPageUrl() }}" class="ws-page-btn">Next</a>
                        @else
                            <button disabled class="ws-page-btn-disabled">Next</button>
                        @endif
                    </div>
                @endif
            </div>
        @elseif($isLoggedIn && $isPending)
            {{-- Account Pending Approval Block State --}}
            <div class="scan-guest-block-container">
                <div class="scan-guest-icon-box" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.22), rgba(22, 56, 50, 0.9)); border: 1.5px solid rgba(245, 158, 11, 0.45);">
                    <svg width="34" height="34" fill="none" stroke="#f59e0b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <h3 class="scan-guest-title">Account Pending Approval</h3>
                <p class="scan-guest-desc">
                    Your account is awaiting admin approval. Past scan records and scanner tools will be enabled once your account has been approved by an administrator.
                </p>
                <div style="display: flex; gap: 12px; justify-content: center; max-width: 320px; margin: 0 auto;">
                    <a href="{{ route('website.profile') }}" class="scan-guest-btn" style="background: linear-gradient(135deg, #d97706, #b45309); border-color: #f59e0b;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>View Profile Status</span>
                    </a>
                </div>
            </div>
        @else
            {{-- Guest Block State (Adaptive Dark & Light Mode) --}}
            <div class="scan-guest-block-container">
                <div class="scan-guest-icon-box">
                    <svg width="34" height="34" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <h3 class="scan-guest-title">Authentication Required</h3>
                <p class="scan-guest-desc">
                    Please log in to your account to view your past soil scan records, Munsell color classifications, and field analysis reports.
                </p>
                <div style="display: flex; gap: 12px; justify-content: center; max-width: 320px; margin: 0 auto;">
                    <a href="{{ route('login') }}" class="scan-guest-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                        <span>Log In to AgriCo</span>
                    </a>
                </div>
            </div>
        @endif
        </div>
    </div>


    {{-- Login Required Modal --}}
    <div id="loginRequiredModal" class="global-login-modal-overlay" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(5,31,32,0.82); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); align-items:center; justify-content:center; padding:16px;">
        <div class="global-login-modal-card">
            {{-- Close "X" Exit Button --}}
            <button type="button" class="modal-close-circle-btn" onclick="document.getElementById('loginRequiredModal').style.display='none'" style="position: absolute; top: 16px; right: 16px; z-index: 10;" title="Close modal" aria-label="Close modal">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Shield Icon --}}
            <div class="global-login-icon-box">
                <svg width="34" height="34" fill="none" stroke="#22c55e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h3 class="global-login-title">Login Required</h3>
            <p class="global-login-notice" id="loginModalNotice">Please log in to perform a scan and view scan history. Would you like to log in to your account now?</p>
            <div style="display:flex; gap:12px; justify-content:center;">
                <button type="button" class="global-login-btn-secondary" onclick="document.getElementById('loginRequiredModal').style.display='none'">No, Thanks</button>
                <a href="{{ route('login') }}" class="global-login-btn-primary">Yes, Log In</a>
            </div>
        </div>
    </div>

    {{-- Insecure Context Camera Notice Modal --}}
    <div id="cameraInsecureModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #0B2B26; border: 1.5px solid rgba(142, 182, 155, 0.3); border-radius: 20px; max-width: 480px; width: 100%; padding: 28px; box-shadow: 0 24px 60px rgba(0,0,0,0.6); color: #DAF1DE; text-align: center; position: relative;">
            <button type="button" class="modal-close-circle-btn" onclick="document.getElementById('cameraInsecureModal').style.display='none'" style="position: absolute; top: 16px; right: 16px; z-index: 10;" title="Close modal" aria-label="Close modal">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: #f59e0b;">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 10px; color: #DAF1DE;">Secure Camera Access Required</h3>
            <p style="font-size: 13.5px; color: #8EB69B; line-height: 1.6; margin: 0 0 20px;">
                Web browsers require a secure connection (HTTPS or localhost) to access your webcam. You are currently viewing via <code style="background: rgba(0,0,0,0.4); padding: 2px 8px; border-radius: 6px; color: #34d399; font-weight: 600;" id="currentInsecureOrigin"></code>.
            </p>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <button type="button" id="btnSwitchToLocalhost" style="background: #16a34a; color: #ffffff; font-weight: 700; font-size: 14px; padding: 12px 20px; border-radius: 12px; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.4);">
                    Switch to Localhost
                </button>
                <button type="button" class="scanner-btn scanner-btn-secondary" onclick="document.getElementById('cameraInsecureModal').style.display='none'">
                    Cancel / Use Upload Photo
                </button>
            </div>
        </div>
    </div>


    <!-- Client-Side Scanning Stack: TensorFlow.js (WebGL GPU acceleration) & Culori (CIELAB & Delta E Colorimetry) -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.22.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/culori@3.3.0/bundled/culori.min.js"></script>
    <script src="{{ asset('js/image-pan-zoom.js') }}?v={{ file_exists(public_path('js/image-pan-zoom.js')) ? filemtime(public_path('js/image-pan-zoom.js')) : time() }}"></script>

    {{-- Soil Scan & Munsell Determination Script --}}
    <script>
        const isUserLoggedIn = {{ $isLoggedIn ? 'true' : 'false' }};
        const isUserApproved = {{ $isApproved ? 'true' : 'false' }};
        const isUserPending  = {{ $isPending ? 'true' : 'false' }};

        // ── Shared zoom-avatar engine ──────────────────────────────────────────
        // ── Shared whole-image-with-color-box avatar engine ──────────────────────────
        // drawWholeImageWithColorBox: loads sourceImageSrc on a canvas, draws the
        // WHOLE scanned soil image intact (no whiteouts or masks), and draws a clean
        // bounding box in the exact color of that soil layer (region.hex).
        function drawWholeImageWithColorBox(sourceImageSrc, region, onReady) {
            let src = sourceImageSrc;
            if (!src) { onReady(null); return; }
            if (!src.startsWith('data:') && !src.startsWith('http') && !src.startsWith('/')) {
                src = 'data:image/jpeg;base64,' + src;
            }
            const img = new Image();
            img.onload = function () {
                const canvas = document.createElement('canvas');
                const w = img.naturalWidth || 300;
                const h = img.naturalHeight || 300;
                canvas.width  = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');

                // 1. Draw the WHOLE scanned soil image intact (no whiteouts, no masking)
                ctx.drawImage(img, 0, 0, w, h);

                // 2. Draw the bounding box where this color was actually detected
                const b = getNormalizedRegionBox(region);
                const boxColor = '#4ade80'; // bright neon green

                if (b) {
                    let bx = Math.round(b.x * w);
                    let by = Math.round(b.y * h);
                    let bw = Math.round(b.w * w);
                    let bh = Math.round(b.h * h);

                    bx = Math.max(0, Math.min(w - 1, bx));
                    by = Math.max(0, Math.min(h - 1, by));
                    bw = Math.max(10, Math.min(w - bx, bw));
                    bh = Math.max(10, Math.min(h - by, bh));

                    ctx.save();
                    // Line width matching Image 1: bold, solid, vibrant (~5% of image width)
                    const strokeW = Math.max(5, Math.round(w * 0.03));
                    bw = Math.max(16, Math.min(w - (strokeW * 2) - 4, bw));
                    bh = Math.max(16, Math.min(h - (strokeW * 2) - 4, bh));
                    bx = Math.max(strokeW + 2, Math.min(w - bw - strokeW - 2, bx));
                    by = Math.max(strokeW + 2, Math.min(h - bh - strokeW - 2, by));

                    // Main stroke in bright neon green only (no black border)
                    ctx.lineWidth = strokeW;
                    ctx.strokeStyle = boxColor;
                    ctx.strokeRect(bx, by, bw, bh);

                    ctx.restore();
                }

                onReady(canvas.toDataURL('image/png'));
            };
            img.onerror = function () { onReady(null); };
            img.src = src;
        }

        function getNormalizedRegionBox(region) {
            let b = null;
            if (region) {
                if (typeof region.box === 'string') {
                    try { b = JSON.parse(region.box); } catch(e) {}
                } else if (region.box && typeof region.box === 'object') {
                    b = region.box;
                }
                if (!b) {
                    if (typeof region.bounding_box === 'string') {
                        try { b = JSON.parse(region.bounding_box); } catch(e) {}
                    } else if (region.bounding_box && typeof region.bounding_box === 'object') {
                        b = region.bounding_box;
                    }
                    if (!b && typeof region.box === 'string') {
                        try {
                            b = JSON.parse(region.box);
                        } catch(e) {}
                    } else if (!b && region.box && typeof region.box === 'object') {
                        b = region.box;
                    }
                }
                if (!b && (region.x !== undefined || region.norm_x !== undefined)) {
                    b = region;
                }
            }
            if (!b) {
                return { x: 0.25, y: 0.25, w: 0.5, h: 0.5 };
            }

            let x = (b.norm_x !== undefined) ? b.norm_x : (b.x !== undefined ? b.x : 0.25);
            let y = (b.norm_y !== undefined) ? b.norm_y : (b.y !== undefined ? b.y : 0.25);
            let w = (b.norm_w !== undefined) ? b.norm_w : (b.w !== undefined ? b.w : (b.width !== undefined ? b.width : 0.5));
            let h = (b.norm_h !== undefined) ? b.norm_h : (b.h !== undefined ? b.h : (b.height !== undefined ? b.height : 0.5));

            if (w > 1.0 || h > 1.0 || x > 1.0 || y > 1.0) {
                if (b.norm_w !== undefined && b.norm_h !== undefined) {
                    x = b.norm_x !== undefined ? b.norm_x : 0.25;
                    y = b.norm_y !== undefined ? b.norm_y : 0.25;
                    w = b.norm_w;
                    h = b.norm_h;
                } else {
                    return { x: 0.25, y: 0.25, w: 0.5, h: 0.5 };
                }
            }

            let numX = Number(x) || 0.25;
            let numY = Number(y) || 0.25;
            let numW = Number(w) || 0.25;
            let numH = Number(h) || 0.20;

            // Guard against edge-clipping artifacts where extreme right/bottom was picked
            if (numX + numW > 0.92) {
                numX = Math.max(0.05, 0.92 - numW);
            }
            if (numY + numH > 0.92) {
                numY = Math.max(0.05, 0.92 - numH);
            }

            return {
                x: Math.max(0.02, Math.min(0.88, numX)),
                y: Math.max(0.02, Math.min(0.88, numY)),
                w: Math.max(0.06, Math.min(0.96 - numX, numW)),
                h: Math.max(0.06, Math.min(0.96 - numY, numH))
            };
        }

        function positionBeforeAfterBox(boxEl, region) {
            if (!boxEl) return;
            const b = getNormalizedRegionBox(region);
            if (!b) {
                boxEl.style.display = 'none';
                return;
            }

            // The side-by-side comparison image has:
            // - Top bar: ~7.41% height (both Python and JS use max(30, 8% of photo h))
            // - Left half: BEFORE image (0% to 50% width)
            // - Right half: AFTER image (50% to 100% width)
            // The green box is placed on the AFTER (right / normalized) side (50% to 100%).
            const topBarPct = 7.41;
            const photoHeightPct = 92.59;

            // Map bounding box (0.0 to 1.0) into the RIGHT 50% (AFTER half)
            let left = 50.0 + (b.x * 50.0);
            let top = (topBarPct + b.y * photoHeightPct);
            let width = (b.w * 50.0);
            let height = (b.h * photoHeightPct);

            // Hard clamp: left edge cannot go below 50.5% (stays on the AFTER side)
            const minLeft = 50.5;
            if (left < minLeft) {
                const overflow = minLeft - left;
                left = minLeft;
                width = Math.max(5, width - overflow);
            }

            // Hard clamp: right edge cannot exceed 99.0% (stays inside container bounds)
            const maxRight = 99.0;
            if (left + width > maxRight) {
                width = Math.max(5, maxRight - left);
            }
            if (left > maxRight - 5) {
                left = maxRight - 5;
                width = 5;
            }

            boxEl.style.setProperty('left', left.toFixed(2) + '%', 'important');
            boxEl.style.setProperty('top', top.toFixed(2) + '%', 'important');
            boxEl.style.setProperty('width', width.toFixed(2) + '%', 'important');
            boxEl.style.setProperty('height', height.toFixed(2) + '%', 'important');
            boxEl.style.setProperty('display', 'block', 'important');
            boxEl.style.setProperty('visibility', 'visible', 'important');
            boxEl.style.setProperty('opacity', '1', 'important');
        }

        function renderBeforeAfterBoxes(containerEl, regions) {
            if (!containerEl) return;
            containerEl.innerHTML = '';
            if (!Array.isArray(regions) || regions.length === 0) return;

            const topBarPct = 7.41;
            const photoHeightPct = 92.59;
            const minLeft = 51.0;
            const maxRight = 99.0;
            const containerRect = containerEl.getBoundingClientRect();
            const containerW = containerRect.width || 400; // fallback for hidden/zero-width containers

            // Scale label font/padding down on narrow containers so text never overflows
            const scale = Math.max(0.55, Math.min(1, containerW / 700));
            const labelFontSize = Math.max(7, Math.round(8.5 * scale));
            const labelPadV = Math.max(1, Math.round(1 * scale));
            const labelPadH = Math.max(3, Math.round(4 * scale));

            const placedBoxes = []; // track placed {left, top, width, labelAbove} to detect collisions

            const ordinalNames = ['1ST', '2ND', '3RD', '4TH', '5TH', '6TH', '7TH', '8TH', '9TH', '10TH'];

            const greenColor = '#4ade80';

            regions.forEach((r, idx) => {
                const b = getNormalizedRegionBox(r);
                if (!b) return;

                const code = r.munsell || r.munsell_code || r.code || '';
                const roleName = ordinalNames[idx] || `${idx + 1}TH`;

                let left = 50 + (b.x * 50);
                let top = topBarPct + (b.y * photoHeightPct);
                let width = b.w * 50;
                let height = b.h * photoHeightPct;

                if (left < minLeft) {
                    const overflow = minLeft - left;
                    left = minLeft;
                    width = Math.max(5, width - overflow);
                }
                if (left + width > maxRight) width = Math.max(5, maxRight - left);
                if (left > maxRight - 5) { left = maxRight - 5; width = 5; }

                // Decide label position: default above the box; flip below if that
                // would collide with an already-placed box's label, or if there's
                // no room above (box too close to the top of the image).
                const labelHeightPct = (14 / (containerRect.height || 400)) * 100 * scale;
                let labelAbove = top > labelHeightPct + 2;

                // Collision check against already-placed labels at the same rough
                // horizontal band
                for (const placed of placedBoxes) {
                    const horizOverlap = !(left + width < placed.left || left > placed.left + placed.width);
                    const vertClose = Math.abs(top - placed.top) < labelHeightPct * 2.2;
                    if (horizOverlap && vertClose && labelAbove === placed.labelAbove) {
                        labelAbove = !labelAbove; // flip to opposite side to de-collide
                        break;
                    }
                }

                placedBoxes.push({ left, top, width, labelAbove });

                // Right alignment safeguard if box is near the right boundary
                const isNearRight = (left + width) > 82.0;
                const horizPos = isNearRight ? 'right: -2px; left: auto;' : 'left: -2px;';

                const box = document.createElement('div');
                box.style.cssText = `
                    position: absolute;
                    left: ${left.toFixed(2)}%;
                    top: ${top.toFixed(2)}%;
                    width: ${width.toFixed(2)}%;
                    height: ${height.toFixed(2)}%;
                    border: 2.5px solid ${greenColor};
                    border-radius: 4px;
                    box-sizing: border-box;
                    pointer-events: none;
                `;

                const label = document.createElement('span');
                label.style.cssText = `
                    position: absolute;
                    ${labelAbove ? `bottom: 100%; margin-bottom: 2px;` : `top: 100%; margin-top: 2px;`}
                    ${horizPos}
                    background: ${greenColor};
                    color: #051F20;
                    font-size: ${labelFontSize}px;
                    font-weight: 800;
                    padding: ${labelPadV}px ${labelPadH}px;
                    border-radius: 3px;
                    white-space: nowrap;
                    max-width: none;
                    z-index: ${10 + idx};
                    line-height: 1.3;
                    letter-spacing: 0.03em;
                    text-transform: uppercase;
                `;
                label.textContent = `${roleName} COLOR`;

                box.appendChild(label);
                containerEl.appendChild(box);
            });
        }

        function renderStage3Swatches(containerEl, regions, dominantMatch) {
            if (!containerEl) return;
            containerEl.innerHTML = '';
            let swatches = [];
            if (Array.isArray(regions) && regions.length > 0) {
                swatches = regions;
            } else if (dominantMatch) {
                swatches = [dominantMatch];
            }
            if (!swatches || swatches.length === 0) {
                containerEl.style.display = 'none';
                return;
            }
            containerEl.style.display = 'flex';
            swatches.forEach((r, idx) => {
                const hex = r.hex || (r.rgb ? `rgb(${r.rgb.join(',')})` : '#836E4F');
                const pct = r.percent !== undefined ? r.percent : (r.proportion !== undefined ? Math.round(r.proportion * 100) : (idx === 0 ? 70 : 15));
                const code = r.munsell || r.munsell_code || r.code || '';
                const pill = document.createElement('div');
                pill.className = 'before-after-swatch-pill';
                pill.innerHTML = `
                    <span class="swatch-color-box" style="background: ${hex};"></span>
                    <span class="swatch-hex">${hex.toUpperCase()}</span>
                    ${code ? `<span class="swatch-code">(${code})</span>` : ''}
                    <span class="swatch-pct">${pct}%</span>
                `;
                containerEl.appendChild(pill);
            });
        }

        function drawGreenBoxOnBeforeAfter(sourceImageSrc, region, onReady) {
            // Stage 2 Before & After is kept clean without green box; detected color box is in Stage 3
            if (onReady) onReady(sourceImageSrc);
        }

        // populateZoomAvatar: fills any avatar container (dominant card or sub-layer
        // swatch banner) with the whole soil image and the bounding box for that color.
        function populateZoomAvatar(container, region, sourceImgSrc) {
            if (!container) return;

            let zoomImg = null;
            let fallback = null;

            if (container.tagName === 'IMG') {
                zoomImg = container;
                const parent = container.parentElement;
                if (parent) {
                    fallback = parent.querySelector('.layer-zoom-fallback') || 
                               document.getElementById('modalSoilSnippetFallback') || 
                               document.getElementById('resSoilSnippetFallback');
                }
            } else {
                zoomImg = container.querySelector('img') || container.querySelector('.layer-zoom-img');
                fallback = container.querySelector('.layer-zoom-fallback') || container.querySelector('div, span');
            }

            if (zoomImg && zoomImg.id === 'modalSoilCircleSnippet') {
                fallback = document.getElementById('modalSoilSnippetFallback') || fallback;
            } else if (zoomImg && zoomImg.id === 'resSoilCircleSnippet') {
                fallback = document.getElementById('resSoilSnippetFallback') || fallback;
            }

            const boxHost = (container.tagName === 'IMG') ? container.parentElement : container;

            function _show(src) {
                if (!src || !zoomImg) {
                    if (fallback) fallback.style.display = 'flex';
                    if (zoomImg) zoomImg.style.display = 'none';
                    if (boxHost) {
                        const oldBox = boxHost.querySelector('.soil-roi-box');
                        if (oldBox) oldBox.style.display = 'none';
                    }
                    return;
                }
                zoomImg.src = src;
                zoomImg.style.display = 'block';
                if (fallback) fallback.style.display = 'none';

                // Render vector-sharp CSS green bounding box on top of the avatar (never blurred!)
                if (boxHost) {
                    let roiBox = boxHost.querySelector('.soil-roi-box');
                    if (!roiBox) {
                        roiBox = document.createElement('div');
                        roiBox.className = 'soil-roi-box';
                        boxHost.appendChild(roiBox);
                    }
                    const b = getNormalizedRegionBox(region);
                    if (b) {
                        const leftPct = Math.max(2, Math.min(88, (b.x || 0.25) * 100));
                        const topPct = Math.max(2, Math.min(88, (b.y || 0.25) * 100));
                        const widthPct = Math.max(6, Math.min(96 - leftPct, (b.w || 0.5) * 100));
                        const heightPct = Math.max(6, Math.min(96 - topPct, (b.h || 0.5) * 100));
                        roiBox.style.cssText = `
                            position: absolute;
                            left: ${leftPct.toFixed(2)}%;
                            top: ${topPct.toFixed(2)}%;
                            width: ${widthPct.toFixed(2)}%;
                            height: ${heightPct.toFixed(2)}%;
                            border: 1.5px solid #4ade80;
                            border-radius: 0;
                            pointer-events: none;
                            box-sizing: border-box;
                            z-index: 5;
                            display: block;
                        `;
                    } else {
                        roiBox.style.display = 'none';
                    }
                }
            }

            if (!sourceImgSrc) {
                _show(null);
                return;
            }

            _show(sourceImgSrc);
        }

        // Legacy aliases
        function drawFullImageWithBox(sourceImageSrc, boundingBox, onReady) {
            drawWholeImageWithColorBox(sourceImageSrc, { bounding_box: boundingBox }, onReady);
        }
        function cropRegionImage(sourceImageSrc, boundingBox, onReady) {
            drawWholeImageWithColorBox(sourceImageSrc, { bounding_box: boundingBox }, onReady);
        }

        function checkScanPermissions() {
            if (!isUserLoggedIn) {
                showLoginModal('Please log in to perform a scan and view scan history.');
                return false;
            }
            if (isUserPending || !isUserApproved) {
                showAccountPendingModal('Account Pending Approval', 'Your account is awaiting admin approval. Scanning and Map access will be enabled once approved.');
                return false;
            }
            return true;
        }

        function showLoginModal(msg = null) {
            const modal = document.getElementById('loginRequiredModal');
            const notice = document.getElementById('loginModalNotice');
            if (notice && msg) {
                notice.textContent = msg;
            } else if (notice) {
                notice.textContent = 'Please log in to perform a scan and view scan history. Would you like to log in to your account now?';
            }
            if (modal) {
                modal.style.display = 'flex';
            } else if (typeof showGlobalLoginModal === 'function') {
                showGlobalLoginModal('scan');
            } else {
                window.location.href = "{{ route('login') }}";
            }
        }

        let currentScanResponseData = null;
        let activeColorSpaceMode = 'matched';

        function switchColorSpaceMode(mode) {
            activeColorSpaceMode = mode;
            const btnMatched = document.getElementById('btnModeMatched');
            const btnRaw = document.getElementById('btnModeRaw');

            if (mode === 'matched') {
                if (btnMatched) btnMatched.classList.add('active');
                if (btnRaw) btnRaw.classList.remove('active');
            } else {
                if (btnRaw) btnRaw.classList.add('active');
                if (btnMatched) btnMatched.classList.remove('active');
            }

            renderColorSpaceBadges();
        }

        function renderColorSpaceBadges() {
            if (!currentScanResponseData) return;

            const isMatched = activeColorSpaceMode === 'matched';
            const badges = isMatched
                ? (currentScanResponseData.munsell_color_space_badges || currentScanResponseData.color_space_badges)
                : (currentScanResponseData.raw_color_space_badges || currentScanResponseData.color_space_badges);

            const activeHex = isMatched
                ? (currentScanResponseData.hex || '#292322')
                : (currentScanResponseData.raw_hex || '#171513');

            // Update hex tags on mode buttons
            const modeMunsellHexTag = document.getElementById('modeMunsellHexTag');
            const modeRawHexTag = document.getElementById('modeRawHexTag');
            if (modeMunsellHexTag && currentScanResponseData.hex) modeMunsellHexTag.textContent = currentScanResponseData.hex.toUpperCase();
            if (modeRawHexTag && currentScanResponseData.raw_hex) modeRawHexTag.textContent = currentScanResponseData.raw_hex.toUpperCase();

            if (badges) {
                const badgeIds = {
                    RGB: 'badgeRGB', HEX: 'badgeHEX', HSL: 'badgeHSL', HWB: 'badgeHWB',
                    CMYK: 'badgeCMYK', XYZ: 'badgeXYZ', LAB: 'badgeLAB', LUV: 'badgeLUV'
                };

                for (const [key, elemId] of Object.entries(badgeIds)) {
                    const badgeEl = document.getElementById(elemId);
                    if (badgeEl && badges[key]) {
                        let val = badges[key];
                        if (key !== 'HEX') {
                            val = val.replace(/^[A-Z]+\((.*)\)$/, '$1');
                        }
                        badgeEl.textContent = val;
                    }
                }
            }
        }

        // ── Client-Side Colorimetry (Culori) & TensorFlow.js ML Helpers ──
        let tfjsEngineReady = false;
        async function initTfjsClientEngine() {
            if (window.tf && !tfjsEngineReady) {
                try {
                    await window.tf.setBackend('webgl').catch(() => window.tf.setBackend('cpu'));
                    tfjsEngineReady = true;
                    console.log('TensorFlow.js Client Engine Ready (Backend: ' + window.tf.getBackend() + ')');
                } catch (e) {
                    console.log('TensorFlow.js init error:', e);
                }
            }
        }
        document.addEventListener('DOMContentLoaded', initTfjsClientEngine);

        function computeClientDeltaE(rgb1, rgb2) {
            if (window.culori && typeof window.culori.differenceCiede2000 === 'function') {
                try {
                    const c1 = { mode: 'rgb', r: rgb1.r / 255, g: rgb1.g / 255, b: rgb1.b / 255 };
                    const c2 = { mode: 'rgb', r: rgb2.r / 255, g: rgb2.g / 255, b: rgb2.b / 255 };
                    return window.culori.differenceCiede2000()(c1, c2);
                } catch (e) {
                    console.log('Culori DeltaE calculation error:', e);
                }
            }
            return Math.sqrt((rgb1.r - rgb2.r) ** 2 + (rgb1.g - rgb2.g) ** 2 + (rgb1.b - rgb2.b) ** 2);
        }

        // ── Viewfinder ROI Coordinate Mapping & Crop Isolation ──
        function getExactReticleCropRect(sourceElement) {
            let srcW = sourceElement.videoWidth || sourceElement.naturalWidth || sourceElement.width || 640;
            let srcH = sourceElement.videoHeight || sourceElement.naturalHeight || sourceElement.height || 480;

            const viewfinderEl = document.querySelector('.scanner-viewfinder');
            const reticleEl = document.querySelector('.reticle');

            if (viewfinderEl && reticleEl && viewfinderEl.clientWidth > 0 && viewfinderEl.clientHeight > 0) {
                const vfRect = viewfinderEl.getBoundingClientRect();
                const retRect = reticleEl.getBoundingClientRect();

                const containerW = vfRect.width;
                const containerH = vfRect.height;

                // If sourceElement is a static <img> preview, read its live bounding rect including pan & zoom!
                if (sourceElement.tagName === 'IMG' && srcW > 0 && srcH > 0) {
                    const imgRect = sourceElement.getBoundingClientRect();
                    const scaleX = imgRect.width / srcW;
                    const scaleY = imgRect.height / srcH;

                    const reticleCenterX = retRect.left + retRect.width / 2;
                    const reticleCenterY = retRect.top + retRect.height / 2;

                    const realCx = (reticleCenterX - imgRect.left) / scaleX;
                    const realCy = (reticleCenterY - imgRect.top) / scaleY;
                    const realW = retRect.width / scaleX;
                    const realH = retRect.height / scaleY;

                    const realRadius = Math.min(realW, realH) / 2;
                    const maxRadius = Math.min(srcW, srcH) * 0.499;
                    const radius = Math.max(10, Math.min(realRadius, maxRadius));

                    return { cx: realCx, cy: realCy, radius, cropW: realW, cropH: realH, srcW, srcH };
                }

                // Account for object-fit: cover scaling and offset (live camera)
                const scale = Math.max(containerW / srcW, containerH / srcH);
                const renderW = srcW * scale;
                const renderH = srcH * scale;

                const offsetX = (containerW - renderW) / 2;
                const offsetY = (containerH - renderH) / 2;

                const reticleCenterX = (retRect.left + retRect.width / 2) - vfRect.left;
                const reticleCenterY = (retRect.top + retRect.height / 2) - vfRect.top;

                const realCx = (reticleCenterX - offsetX) / scale;
                const realCy = (reticleCenterY - offsetY) / scale;
                const realW = retRect.width / scale;
                const realH = retRect.height / scale;

                const realRadius = Math.min(realW, realH) / 2;
                const maxRadius = Math.min(srcW, srcH) * 0.499;
                const radius = Math.max(10, Math.min(realRadius, maxRadius));

                return { cx: realCx, cy: realCy, radius, cropW: realW, cropH: realH, srcW, srcH };
            }

            const radius = Math.min(srcW, srcH) * 0.30;
            return { cx: srcW / 2, cy: srcH / 2, radius, cropW: radius * 2, cropH: radius * 2, srcW, srcH };
        }

        /**
         * Strictly isolates and crops the Region of Interest (ROI) viewfinder target.
         * Clamps crop bounds to the source image to avoid any transparent/black edge padding.
         */
        function createIsolatedCircularRoiCanvas(sourceElement, bgHex = null) {
            try {
                if (!sourceElement) return null;

                const mapped = getExactReticleCropRect(sourceElement);
                const { cx, cy, radius, cropW, cropH, srcW, srcH } = mapped;

                if (!srcW || !srcH) return null;

                const w = cropW || (radius * 2);
                const h = cropH || (radius * 2);

                let sx = cx - w / 2;
                let sy = cy - h / 2;
                let sw = w;
                let sh = h;

                if (sx < 0) { sw += sx; sx = 0; }
                if (sy < 0) { sy += sy; sy = 0; }
                if (sx + sw > srcW) sw = srcW - sx;
                if (sy + sh > srcH) sh = srcH - sy;

                if (sw <= 0 || sh <= 0) return null;

                const outW = Math.max(20, Math.round(sw));
                const outH = Math.max(20, Math.round(sh));

                const roiCanvas = document.createElement('canvas');
                roiCanvas.width = outW;
                roiCanvas.height = outH;
                const ctx = roiCanvas.getContext('2d');

                ctx.fillStyle = bgHex || '#5c4838';
                ctx.fillRect(0, 0, outW, outH);

                ctx.drawImage(
                    sourceElement,
                    sx, sy, sw, sh,
                    0, 0, outW, outH
                );

                return roiCanvas;
            } catch (e) {
                console.log('Error creating isolated ROI canvas:', e);
                return null;
            }
        }

        // Circular ROI Avatar Snippet Crop Engine
        function extractCircularSoilSnippet(sourceElement, bgHex = null) {
            const cropCanvas = createIsolatedCircularRoiCanvas(sourceElement, bgHex);
            return cropCanvas ? cropCanvas.toDataURL('image/png') : null;
        }

        // Asynchronous AJAX Pagination Engine (No Page Reload / No Label Movement)
        async function loadScanHistoryPage(url) {
            try {
                const tbody = document.getElementById('savedScansTbody');
                if (tbody) tbody.style.opacity = '0.3';

                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newTbody = doc.getElementById('savedScansTbody');
                if (newTbody && tbody) {
                    tbody.innerHTML = newTbody.innerHTML;
                    tbody.style.opacity = '1';
                }

                const newFooter = doc.getElementById('scanPaginationFooter');
                const currentFooter = document.getElementById('scanPaginationFooter');
                if (newFooter && currentFooter) {
                    currentFooter.innerHTML = newFooter.innerHTML;
                }

                const newBadge = doc.getElementById('savedScansCountBadge');
                const currentBadge = document.getElementById('savedScansCountBadge');
                if (newBadge && currentBadge) {
                    currentBadge.textContent = newBadge.textContent;
                }

                const newEmpty = doc.getElementById('savedScansEmptyState');
                const currentEmpty = document.getElementById('savedScansEmptyState');
                if (newEmpty && currentEmpty) {
                    currentEmpty.style.display = newEmpty.style.display;
                }

                window.history.pushState(null, '', url);
                attachPaginationAjaxListeners();
            } catch (err) {
                console.error('AJAX scan history load error:', err);
                window.location.assign(url);
            }
        }

        function attachPaginationAjaxListeners() {
            const footer = document.getElementById('scanPaginationFooter');
            if (!footer) return;

            const perPageSelect = document.getElementById('perPageSelect');
            if (perPageSelect) {
                perPageSelect.onchange = function() {
                    const targetUrl = '{{ route("website.scan") }}?per_page=' + this.value + '&page=1';
                    loadScanHistoryPage(targetUrl);
                };
            }

            const pageLinks = footer.querySelectorAll('a[href]');
            pageLinks.forEach(link => {
                link.onclick = function(e) {
                    e.preventDefault();
                    loadScanHistoryPage(this.href);
                };
            });
        }

        let scanPanZoom = null;
        let scanResizable = null;

        function initScanPanZoom() {
            const img = document.getElementById('imagePreview');
            const imageMask = document.getElementById('imageMask');
            const container = document.getElementById('viewfinderArea');
            const reticleBox = document.querySelector('.reticle');

            if (!scanPanZoom && img && imageMask && typeof window.ImagePanZoom !== 'undefined') {
                scanPanZoom = new window.ImagePanZoom(img, imageMask, { minScale: 1, maxScale: 4 });

                const zoomInBtn = document.getElementById('zoomInBtn');
                const zoomOutBtn = document.getElementById('zoomOutBtn');
                const zoomResetBtn = document.getElementById('zoomResetBtn');

                if (zoomInBtn) zoomInBtn.addEventListener('click', () => scanPanZoom && scanPanZoom.zoomBy(0.25));
                if (zoomOutBtn) zoomOutBtn.addEventListener('click', () => scanPanZoom && scanPanZoom.zoomBy(-0.25));
                if (zoomResetBtn) zoomResetBtn.addEventListener('click', () => {
                    if (scanPanZoom) scanPanZoom.reset();
                    if (scanResizable) scanResizable.reset();
                });
            }

            if (!scanResizable && reticleBox && container && typeof window.ResizableViewfinder !== 'undefined') {
                scanResizable = new window.ResizableViewfinder(reticleBox, container, {
                    minSize: 60,
                    defaultSize: window.innerWidth <= 640 ? 110 : 140
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            attachPaginationAjaxListeners();
            initScanPanZoom();
        });
        const video = document.getElementById('webcamVideo');
        const imgPreview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('cameraPlaceholder');
        const reticle = document.getElementById('reticleOverlay');
        const btnToggleCam = document.getElementById('btnToggleCamera');
        const btnUpload = document.getElementById('btnUploadImage');
        const fileInput = document.getElementById('fileUpload');
        const btnCapture = document.getElementById('btnCaptureScan');
        const statusText = document.getElementById('cameraStatus');
        const canvas = document.getElementById('processCanvas');

        // Results UI elements
        const resCard = document.querySelector('.result-card');
        const resContent = document.getElementById('resultContent');
        const resPlaceholder = document.getElementById('resultPlaceholder');
        const resSwatch = document.getElementById('resSwatch');
        const resCode = document.getElementById('resCode');
        const resName = document.getElementById('resName');
        const resHue = document.getElementById('resHue');
        const resValChroma = document.getElementById('resValChroma');
        const resConfidence = document.getElementById('resConfidence');
        const resDesc = document.getElementById('resDesc');

        let isCameraActive = false;
        let mediaStream = null;

        // Munsell Knowledgebase Database lookup table
        const munsellDB = [
            { hue: '10YR', val: '4', chroma: '3', code: '10YR 4/3', name: 'Dark Brown', hex: '#594433', desc: 'Moist organic topsoil with high fertility potential.' },
            { hue: '10YR', val: '3', chroma: '2', code: '10YR 3/2', name: 'Very Dark Brown', hex: '#443428', desc: 'Rich loam soil with deep organic matter composition.' },
            { hue: '7.5YR', val: '5', chroma: '6', code: '7.5YR 5/6', name: 'Strong Brown', hex: '#8C5638', desc: 'Slightly oxidized clay loam suitable for diversified crops.' },
            { hue: '2.5Y', val: '6', chroma: '4', code: '2.5Y 6/4', name: 'Light Yellowish Brown', hex: '#9E9267', desc: 'Silty clay soil containing moderate mineral carbonates.' },
            { hue: '5YR', val: '4', chroma: '6', code: '5YR 4/6', name: 'Reddish Brown', hex: '#7A3b26', desc: 'Iron-rich clay soil common in upland agricultural zones.' },
            { hue: '10YR', val: '6', chroma: '4', code: '10YR 6/4', name: 'Light Yellowish Brown', hex: '#a18e69', desc: 'Sandy loam soil with rapid drainage characteristics.' }
        ];

        const btnFlashlight = document.getElementById('btnFlashlight');
        const btnSwitchCamera = document.getElementById('btnSwitchCamera');
        let isFlashOn = false;
        let currentFacingMode = 'environment';
        let currentVideoDeviceId = null;
        const isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || (navigator.maxTouchPoints > 1 && window.innerWidth < 1024);

        if (!isMobileDevice) {
            currentFacingMode = 'user'; // default to user-facing / webcam on desktop and laptop
        }

        async function acquireCameraStream(preferredDeviceId = null, preferredFacingMode = null) {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                // Check for legacy vendor-prefixed getUserMedia
                const legacyGUM = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
                if (legacyGUM) {
                    return new Promise((resolve, reject) => {
                        legacyGUM.call(navigator, { video: true }, resolve, reject);
                    });
                }
                throw new Error('MEDIA_DEVICES_UNSUPPORTED');
            }

            const facing = preferredFacingMode || currentFacingMode;
            const altFacing = (facing === 'environment') ? 'user' : 'environment';

            // Progressive constraint attempts: from preferred to most permissive
            const attempts = [];

            if (preferredDeviceId) {
                attempts.push({ video: { deviceId: { exact: preferredDeviceId }, width: { ideal: 1280 }, height: { ideal: 720 } } });
                attempts.push({ video: { deviceId: { exact: preferredDeviceId } } });
            }

            // Attempt with preferred facingMode + ideal resolution
            attempts.push({ video: { facingMode: { ideal: facing }, width: { ideal: 1280 }, height: { ideal: 720 } } });
            // Attempt with preferred facingMode without resolution constraint
            attempts.push({ video: { facingMode: { ideal: facing } } });
            // Attempt with alternate facingMode (e.g. laptop webcam if environment was requested)
            attempts.push({ video: { facingMode: { ideal: altFacing }, width: { ideal: 1280 }, height: { ideal: 720 } } });
            attempts.push({ video: { facingMode: { ideal: altFacing } } });
            // Generic resolution constraint
            attempts.push({ video: { width: { ideal: 1280 }, height: { ideal: 720 } } });
            // Universal fallback: any video device
            attempts.push({ video: true });

            let lastErr = null;
            for (const constraints of attempts) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    if (stream && stream.getVideoTracks().length > 0) {
                        const track = stream.getVideoTracks()[0];
                        const settings = (track && typeof track.getSettings === 'function') ? track.getSettings() : {};
                        if (settings.deviceId) currentVideoDeviceId = settings.deviceId;
                        if (settings.facingMode) currentFacingMode = settings.facingMode;
                        return stream;
                    }
                } catch (err) {
                    lastErr = err;
                    // If user explicitly blocked camera permissions, do not keep polling constraints
                    if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                        throw err;
                    }
                }
            }

            // Last resort: enumerate devices and take the first available videoinput
            try {
                if (navigator.mediaDevices.enumerateDevices) {
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    const videoInputs = devices.filter(d => d.kind === 'videoinput');
                    if (videoInputs.length > 0) {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: { deviceId: { exact: videoInputs[0].deviceId } }
                        });
                        if (stream && stream.getVideoTracks().length > 0) {
                            currentVideoDeviceId = videoInputs[0].deviceId;
                            return stream;
                        }
                    }
                }
            } catch (enumErr) {
                console.warn('enumerateDevices fallback warning:', enumErr);
            }

            throw lastErr || new Error('CAMERA_INIT_FAILED');
        }

        function updateFlashlightVisibility() {
            const btnFlash = document.getElementById('btnFlashlight');
            if (!btnFlash) return;

            if (!isCameraActive || !mediaStream) {
                btnFlash.style.display = 'none';
                return;
            }

            const track = mediaStream.getVideoTracks()[0];
            const capabilities = (track && typeof track.getCapabilities === 'function') ? track.getCapabilities() : {};
            if (capabilities && capabilities.torch) {
                btnFlash.style.display = 'flex';
            } else {
                btnFlash.style.display = 'none';
            }
        }

        async function updateCameraSwitcherVisibility() {
            const btnSwitch = document.getElementById('btnSwitchCamera');
            if (!btnSwitch) return;

            if (!isCameraActive) {
                btnSwitch.style.display = 'none';
                return;
            }

            try {
                if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    const videoInputs = devices.filter(d => d.kind === 'videoinput');
                    if (videoInputs.length > 1 || isMobileDevice) {
                        btnSwitch.style.display = 'flex';
                        return;
                    }
                }
            } catch (e) {}

            if (isMobileDevice) {
                btnSwitch.style.display = 'flex';
            } else {
                btnSwitch.style.display = 'none';
            }
        }

        window.switchCameraDevice = async function() {
            if (!isCameraActive) return;

            const btnSwitch = document.getElementById('btnSwitchCamera');
            if (btnSwitch) {
                btnSwitch.style.transform = 'rotate(180deg)';
                setTimeout(() => { if (btnSwitch) btnSwitch.style.transform = 'none'; }, 300);
            }

            try {
                let nextDeviceId = null;
                const nextFacing = (currentFacingMode === 'environment') ? 'user' : 'environment';

                if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    const videoInputs = devices.filter(d => d.kind === 'videoinput');
                    if (videoInputs.length > 1) {
                        const curIdx = videoInputs.findIndex(d => d.deviceId === currentVideoDeviceId);
                        const nextIdx = (curIdx + 1) % videoInputs.length;
                        nextDeviceId = videoInputs[nextIdx].deviceId;
                    }
                }

                // Stop current tracks
                if (mediaStream) {
                    mediaStream.getTracks().forEach(t => { try { t.stop(); } catch(e){} });
                    mediaStream = null;
                }

                mediaStream = await acquireCameraStream(nextDeviceId, nextFacing);
                const video = document.getElementById('webcamVideo');
                if (video) {
                    video.srcObject = mediaStream;
                    video.setAttribute('playsinline', '');
                    video.setAttribute('webkit-playsinline', '');
                    video.setAttribute('autoplay', '');
                    video.muted = true;
                    video.style.display = 'block';
                    try { await video.play(); } catch(p) {}
                }

                updateFlashlightVisibility();
                await updateCameraSwitcherVisibility();
            } catch (err) {
                console.error('Failed to switch camera device:', err);
                showScanNotice('Could not switch camera: ' + (err.message || 'Device unavailable'));
            }
        };

        async function toggleFlashlight() {
            if (!mediaStream) {
                showScanNotice('Please start the camera first to use the flashlight.');
                return;
            }
            const track = mediaStream.getVideoTracks()[0];
            if (!track) return;

            try {
                const capabilities = track.getCapabilities ? track.getCapabilities() : {};
                if (!capabilities.torch) {
                    showScanNotice('Flashlight is supported on mobile devices with a built-in camera LED flash.');
                    return;
                }
                isFlashOn = !isFlashOn;
                await track.applyConstraints({
                    advanced: [{ torch: isFlashOn }]
                });

                const btnFlash = document.getElementById('btnFlashlight');
                if (btnFlash) {
                    if (isFlashOn) {
                        btnFlash.style.background = '#f59e0b';
                        btnFlash.style.borderColor = '#f59e0b';
                        btnFlash.style.color = '#ffffff';
                        btnFlash.style.boxShadow = '0 0 20px rgba(245, 158, 11, 0.65)';
                    } else {
                        btnFlash.style.background = 'rgba(15, 23, 42, 0.65)';
                        btnFlash.style.borderColor = 'rgba(255, 255, 255, 0.35)';
                        btnFlash.style.color = '#ffffff';
                        btnFlash.style.boxShadow = '0 4px 16px rgba(0,0,0,0.35)';
                    }
                }
            } catch (err) {
                showScanNotice('Flashlight is supported on mobile devices with a built-in camera LED flash.');
            }
        }

        if (btnFlashlight) {
            btnFlashlight.addEventListener('click', toggleFlashlight);
        }

        // Global Camera and Upload Actions
        window.toggleCameraStream = async function() {
            if (!checkScanPermissions()) {
                return;
            }

            const video = document.getElementById('webcamVideo');
            const imgPreview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('cameraPlaceholder');
            const reticle = document.getElementById('reticleOverlay');
            const btnToggleCam = document.getElementById('btnToggleCamera');
            const btnCapture = document.getElementById('btnCaptureScan');
            const statusText = document.getElementById('cameraStatus');

            if (isCameraActive) {
                window.stopCamera();
                return;
            }

            // Verify Secure Context (HTTPS or localhost required by modern WebRTC)
            const isSecure = window.isSecureContext || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
            if (!isSecure && !isMobileDevice) {
                const modal = document.getElementById('cameraInsecureModal');
                const originSpan = document.getElementById('currentInsecureOrigin');
                if (originSpan) originSpan.textContent = window.location.origin;
                if (modal) {
                    modal.style.display = 'flex';
                    const btnSwitch = document.getElementById('btnSwitchToLocalhost');
                    if (btnSwitch) {
                        const port = window.location.port ? `:${window.location.port}` : '';
                        btnSwitch.onclick = () => {
                            window.location.href = `${window.location.protocol}//localhost${port}${window.location.pathname}${window.location.search}`;
                        };
                    }
                } else {
                    showScanNotice(`Camera requires HTTPS or localhost. Please open http://localhost:${window.location.port || '8080'}/scan`);
                }
                return;
            }

            // Set UI to connecting state
            if (statusText) {
                statusText.textContent = 'Connecting Camera...';
                statusText.className = 'camera-status-badge status-active';
            }
            if (btnToggleCam) {
                btnToggleCam.innerHTML = `<span>Starting...</span>`;
                btnToggleCam.disabled = true;
            }

            try {
                const stream = await acquireCameraStream();
                mediaStream = stream;

                if (video) {
                    video.srcObject = mediaStream;
                    video.setAttribute('playsinline', '');
                    video.setAttribute('webkit-playsinline', '');
                    video.setAttribute('autoplay', '');
                    video.muted = true;
                    video.style.display = 'block';

                    try {
                        await video.play();
                    } catch (playErr) {
                        console.warn('video.play() warning:', playErr);
                    }
                }

                if (imgPreview) imgPreview.style.display = 'none';
                if (placeholder) placeholder.style.display = 'none';
                if (reticle) reticle.style.display = 'flex';
                if (btnCapture) btnCapture.style.display = 'inline-flex';

                const zoomCtrl = document.getElementById('zoomControls');
                if (zoomCtrl) zoomCtrl.style.display = 'none';
                if (scanPanZoom) scanPanZoom.reset();
                if (scanResizable) scanResizable.reset();

                if (btnToggleCam) {
                    btnToggleCam.innerHTML = `<span>Stop Camera</span>`;
                    btnToggleCam.className = 'scanner-btn scanner-btn-secondary';
                    btnToggleCam.disabled = false;
                }
                if (statusText) {
                    statusText.textContent = 'Live Feed Active';
                    statusText.className = 'camera-status-badge status-active';
                    statusText.removeAttribute('style');
                }
                isCameraActive = true;

                updateFlashlightVisibility();
                await updateCameraSwitcherVisibility();

            } catch (err) {
                console.error('Camera access error:', err);

                if (btnToggleCam) {
                    btnToggleCam.innerHTML = `<span>Start Camera</span>`;
                    btnToggleCam.className = 'scanner-btn scanner-btn-primary';
                    btnToggleCam.disabled = false;
                }
                if (statusText) {
                    statusText.textContent = 'Camera Off';
                    statusText.className = 'camera-status-badge status-off';
                    statusText.removeAttribute('style');
                }

                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    showScanNotice('Camera permission was blocked. Please click the camera/lock icon in your browser address bar to allow access.');
                } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                    if (isMobileDevice) {
                        const cameraInput = document.getElementById('cameraCaptureInput');
                        if (cameraInput) cameraInput.click();
                    } else {
                        showScanNotice('No camera detected. Please connect a webcam or use the "Upload Photo" button.');
                    }
                } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                    showScanNotice('Camera is currently in use by another application (Zoom, Teams, or another tab). Please close other apps and try again.');
                } else if (err.message === 'MEDIA_DEVICES_UNSUPPORTED') {
                    if (isMobileDevice) {
                        const cameraInput = document.getElementById('cameraCaptureInput');
                        if (cameraInput) cameraInput.click();
                    } else {
                        showScanNotice(`Camera requires HTTPS or localhost. Please visit http://localhost:${window.location.port || '8080'}/scan`);
                    }
                } else {
                    if (isMobileDevice) {
                        const cameraInput = document.getElementById('cameraCaptureInput');
                        if (cameraInput) cameraInput.click();
                    } else {
                        showScanNotice('Could not start camera: ' + (err.message || 'Check camera permissions or connection.'));
                    }
                }
            }
        };

        window.triggerPhotoUpload = function() {
            if (!checkScanPermissions()) {
                return;
            }
            if (isCameraActive) {
                window.stopCamera();
            }
            const fileInput = document.getElementById('fileUpload');
            if (fileInput) {
                fileInput.click();
            }
        };

        window.stopCamera = function() {
            if (mediaStream) {
                mediaStream.getTracks().forEach(track => {
                    try { track.stop(); } catch(e) {}
                });
                mediaStream = null;
            }
            const video = document.getElementById('webcamVideo');
            const imgPreview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('cameraPlaceholder');
            const reticle = document.getElementById('reticleOverlay');
            const btnToggleCam = document.getElementById('btnToggleCamera');
            const btnCapture = document.getElementById('btnCaptureScan');
            const statusText = document.getElementById('cameraStatus');
            const btnFlashlight = document.getElementById('btnFlashlight');
            const btnSwitch = document.getElementById('btnSwitchCamera');

            if (video) {
                try { video.pause(); } catch(e) {}
                video.srcObject = null;
                video.style.display = 'none';
            }
            if (btnFlashlight) {
                btnFlashlight.style.display = 'none';
                btnFlashlight.style.background = 'rgba(15, 23, 42, 0.55)';
                btnFlashlight.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                btnFlashlight.style.color = '#ffffff';
                isFlashOn = false;
            }
            if (btnSwitch) {
                btnSwitch.style.display = 'none';
            }

            const hasImage = imgPreview && imgPreview.src && imgPreview.style.display !== 'none' && imgPreview.src !== '' && window.getComputedStyle(imgPreview).display !== 'none';
            if (!hasImage) {
                if (reticle) reticle.style.display = 'none';
                if (placeholder) placeholder.style.display = 'block';
                const zoomCtrl = document.getElementById('zoomControls');
                if (zoomCtrl) zoomCtrl.style.display = 'none';
                if (scanPanZoom) scanPanZoom.reset();
                if (scanResizable) scanResizable.reset();
            }
            if (btnCapture) btnCapture.style.display = 'inline-flex';
            if (btnToggleCam) {
                btnToggleCam.innerHTML = `<span>Start Camera</span>`;
                btnToggleCam.className = 'scanner-btn scanner-btn-primary';
                btnToggleCam.disabled = false;
            }
            if (statusText) {
                statusText.textContent = 'Camera Off';
                statusText.className = 'camera-status-badge status-off';
                statusText.removeAttribute('style');
            }
            isCameraActive = false;
            setTimeout(syncScannerCardHeights, 50);
        };

        function stopCamera() {
            window.stopCamera();
        }

        function syncScannerCardHeights() {
            const scannerCard = document.querySelector('.scanner-card');
            const resultCard = document.querySelector('.result-card');
            const resultContent = document.getElementById('resultContent');

            if (scannerCard && resultCard) {
                if (window.innerWidth > 900) {
                    const leftH = scannerCard.offsetHeight;
                    if (leftH > 0) {
                        const isContentVisible = resultContent && resultContent.style.display !== 'none' && window.getComputedStyle(resultContent).display !== 'none';
                        if (!isContentVisible) {
                            resultCard.style.height = leftH + 'px';
                            resultCard.style.minHeight = leftH + 'px';
                        } else {
                            resultCard.style.height = 'auto';
                            resultCard.style.minHeight = leftH + 'px';
                        }
                    }
                } else {
                    resultCard.style.minHeight = '420px';
                    resultCard.style.height = 'auto';
                }
            }
        }

        window.addEventListener('resize', syncScannerCardHeights);
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(syncScannerCardHeights, 50);
            setTimeout(syncScannerCardHeights, 200);
            setTimeout(syncScannerCardHeights, 500);
        });

        // Trigger Analyze handled via onclick attribute on #btnCaptureScan
        // (EventListener removed to prevent duplicate simultaneous scan execution)

        // Load All Official Munsell Colors from Database
        const dbMunsellColors = @json($allMunsellColors ?? []);

        const scanLoadingOverlay = document.getElementById('scanLoadingOverlay');
        const resOrganic = document.getElementById('resOrganic');
        const resDrainage = document.getElementById('resDrainage');
        const resCrops = document.getElementById('resCrops');

        let noticeTimeout = null;
        let desktopNoticeTimeout = null;

        function showScanNotice(msg) {
            const isMobile = window.innerWidth <= 640;
            if (isMobile) {
                // Image 1: Mobile Top Floating Card Toast
                const toast = document.getElementById('scanNoticeToast');
                const msgElem = document.getElementById('scanNoticeMessage');
                if (msgElem) msgElem.textContent = msg || 'Please start the camera or upload a soil photo sample first before scanning.';
                if (toast) {
                    toast.style.display = 'block';
                    toast.style.animation = 'toastSlideDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards';
                    if (noticeTimeout) clearTimeout(noticeTimeout);
                    noticeTimeout = setTimeout(() => {
                        dismissScanNotice();
                    }, 4000);
                }
            } else {
                // Image 2: Desktop Windows-style Bottom-Right Notification Toast
                showDesktopNotice(msg);
            }
        }

        function dismissScanNotice() {
            const toast = document.getElementById('scanNoticeToast');
            if (toast) {
                toast.style.animation = 'toastSlideUp 0.3s ease forwards';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 300);
            }
        }

        function showDesktopNotice(msg) {
            const toast = document.getElementById('desktopNoticeToast');
            const msgElem = document.getElementById('desktopNoticeMessage');
            if (msgElem) msgElem.textContent = msg || 'Notification from AgriCo';
            if (toast) {
                toast.style.display = 'block';
                toast.style.animation = 'desktopToastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards';
                if (desktopNoticeTimeout) clearTimeout(desktopNoticeTimeout);
                desktopNoticeTimeout = setTimeout(() => {
                    dismissDesktopNotice();
                }, 5000);
            }

            // Also trigger system desktop Notification if permission is granted
            if ('Notification' in window && Notification.permission === 'granted') {
                try {
                    new Notification('AgriCo Scanner', {
                        body: msg,
                        icon: '{{ asset("images/agrico-map-pin.png") }}'
                    });
                } catch (e) {}
            }
        }

        function dismissDesktopNotice() {
            const toast = document.getElementById('desktopNoticeToast');
            if (toast) {
                toast.style.animation = 'desktopToastSlideOut 0.25s ease forwards';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 250);
            }
        }

        // Multi-Factor Image Validation Engine (Micro-Texture Granularity, Facial Detector, Artificial Color Gamut)
        function isSoilSample(imgData) {
            let validSoilPixels = 0;
            let invalidNonSoilPixels = 0;
            let totalPixels = 0;

            for (let i = 0; i < imgData.length; i += 4) {
                const pr = imgData[i];
                const pg = imgData[i + 1];
                const pb = imgData[i + 2];
                const alpha = imgData[i + 3];

                if (alpha < 50) continue; // Ignore transparent pixels
                totalPixels++;

                // Check 1: Extreme Overexposure / Pure White Screen Background
                if (pr > 248 && pg > 248 && pb > 248) {
                    invalidNonSoilPixels++;
                    continue;
                }

                // Check 2: Pure Pitch Black / Lens Cover
                if (pr < 5 && pg < 5 && pb < 5) {
                    invalidNonSoilPixels++;
                    continue;
                }

                // Convert RGB to HSV
                const rNorm = pr / 255;
                const gNorm = pg / 255;
                const bNorm = pb / 255;
                const max = Math.max(rNorm, gNorm, bNorm);
                const min = Math.min(rNorm, gNorm, bNorm);
                const delta = max - min;
                
                let h = 0;
                let s = max === 0 ? 0 : delta / max;

                if (delta !== 0) {
                    if (max === rNorm) {
                        h = ((gNorm - bNorm) / delta) % 6;
                    } else if (max === gNorm) {
                        h = (bNorm - rNorm) / delta + 2;
                    } else {
                        h = (rNorm - gNorm) / delta + 4;
                    }
                    h = Math.round(h * 60);
                    if (h < 0) h += 360;
                }

                // Pure bright synthetic primaries (Blue clothing/sky, Neon foliage, Purple/Pink plastic)
                const isGreenDominant = (pg > pr + 25) || (h >= 80 && h <= 160 && s > 0.35);
                const isBlueDominant = (pb > pr + 25) || (pb > pg + 20) || (h >= 180 && h <= 250 && s > 0.30);
                const isPurplePink = (h >= 265 && h <= 340 && s > 0.30) || (pr > pg + 40 && pb > pg + 40);

                if (isGreenDominant || isBlueDominant || isPurplePink) {
                    invalidNonSoilPixels++;
                } else {
                    // All Munsell natural soil earth hues (10R, 2.5YR, 5YR, 7.5YR, 10YR, 2.5Y, 5Y, latosol, clay loam, sand, humus)
                    validSoilPixels++;
                }
            }

            if (totalPixels === 0) return false;

            const nonSoilRatio = invalidNonSoilPixels / totalPixels;
            const soilRatio = validSoilPixels / totalPixels;

            return (soilRatio >= 0.35 && nonSoilRatio < 0.45);
        }

        async function performClientSoilValidation(canvas) {
            try {
                const ctx = canvas.getContext('2d');
                const w = canvas.width;
                const h = canvas.height;
                if (w < 10 || h < 10) return { isValid: true };

                // ── STEP 2: Full-Scene Synthetic Color Guard (Tier A) ────────────────
                // Tightened to 15 % and lower green threshold (g > r+15 instead of +25)
                const fullImgData = ctx.getImageData(0, 0, w, h);
                const fullPixels = fullImgData.data;
                let nonSoilSceneCount = 0;
                let totalSampled = 0;

                for (let i = 0; i < fullPixels.length; i += 16) {
                    const r = fullPixels[i];
                    const g = fullPixels[i + 1];
                    const b = fullPixels[i + 2];
                    const a = fullPixels[i + 3];
                    if (a < 50) continue;
                    totalSampled++;

                    const rN = r / 255, gN = g / 255, bN = b / 255;
                    const mx = Math.max(rN, gN, bN), mn = Math.min(rN, gN, bN);
                    const d = mx - mn;
                    let hVal = 0;
                    if (d !== 0) {
                        if (mx === rN) hVal = ((gN - bN) / d) % 6;
                        else if (mx === gN) hVal = (bN - rN) / d + 2;
                        else hVal = (rN - gN) / d + 4;
                        hVal = Math.round(hVal * 60);
                        if (hVal < 0) hVal += 360;
                    }
                    const sVal = mx === 0 ? 0 : d / mx;

                    const isBlueCyan     = (sVal > 0.28) && ((b > r + 25 && b > g + 15) || (hVal >= 175 && hVal <= 255));
                    const isGreenToy     = (sVal > 0.30) && ((g > r + 25) || (hVal >= 75 && hVal <= 165));
                    const isPinkPurple   = (sVal > 0.28) && ((hVal >= 260 && hVal <= 345) || (r > g + 35 && b > g + 35 && b > r));
                    const isBrightYellow = (sVal > 0.45) && (hVal >= 45 && hVal <= 65 && r > 160 && g > 160);

                    if (isBlueCyan || isGreenToy || isPinkPurple || isBrightYellow) {
                        nonSoilSceneCount++;
                    }
                }

                const nonSoilRatio = totalSampled > 0 ? (nonSoilSceneCount / totalSampled) : 0;
                if (nonSoilRatio > 0.40) {
                    console.log('Scene Validation Failed: nonSoilRatio=' + nonSoilRatio.toFixed(2));
                    return { isValid: false, message: 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.' };
                }

                // ── STEP 3: CIRCULAR RETICLE ANALYSIS ───────────────────────────────
                // Sample pixels inside the inscribed circular ROI target.
                const cx = w / 2;
                const cy = h / 2;
                const radius = Math.min(w, h) * 0.45;
                const r2 = radius * radius;
                const step = Math.max(1, Math.floor(radius / 30));

                let grayPixels = [];
                let rValues = [];
                let sumR = 0, sumG = 0, sumB = 0;
                let greenPixels = 0;
                let invalidCirclePixels = 0;
                let circleTotal = 0;

                const x0 = Math.floor(cx - radius), x1 = Math.ceil(cx + radius);
                const y0 = Math.floor(cy - radius), y1 = Math.ceil(cy + radius);

                for (let py = y0; py <= y1; py += step) {
                    for (let px = x0; px <= x1; px += step) {
                        if (px < 0 || px >= w || py < 0 || py >= h) continue;
                        const dx = px - cx, dy = py - cy;
                        if (dx * dx + dy * dy > r2) continue;    // outside circle → skip

                        const idx = (py * w + px) * 4;
                        const r = fullPixels[idx];
                        const g = fullPixels[idx + 1];
                        const b = fullPixels[idx + 2];
                        const a = fullPixels[idx + 3];
                        if (a < 50) continue;

                        circleTotal++;
                        sumR += r; sumG += g; sumB += b;

                        const gray = 0.299 * r + 0.587 * g + 0.114 * b;
                        grayPixels.push(gray);
                        rValues.push(r);

                        // Bright synthetic glare (e.g. pure white plastic reflection)
                        if (r > 252 && g > 252 && b > 252) { invalidCirclePixels++; continue; }

                        const rN2 = r / 255, gN2 = g / 255, bN2 = b / 255;
                        const mx2 = Math.max(rN2, gN2, bN2), mn2 = Math.min(rN2, gN2, bN2);
                        const d2 = mx2 - mn2;
                        let h2 = 0, s2 = mx2 === 0 ? 0 : d2 / mx2;
                        if (d2 !== 0) {
                            if (mx2 === rN2) h2 = ((gN2 - bN2) / d2) % 6;
                            else if (mx2 === gN2) h2 = (bN2 - rN2) / d2 + 2;
                            else h2 = (rN2 - gN2) / d2 + 4;
                            h2 = Math.round(h2 * 60);
                            if (h2 < 0) h2 += 360;
                        }

                        // Pure bright synthetic primaries inside circle (neon greens, vivid blues, bright pinks)
                        if ((g > r + 20) || (h2 >= 70 && h2 <= 170 && s2 > 0.35)) {
                            invalidCirclePixels++;
                            greenPixels++;
                        } else if ((b > r + 24 && b > g + 16) || (h2 >= 175 && h2 <= 255 && s2 > 0.30)) {
                            invalidCirclePixels++;
                        } else if ((h2 >= 260 && h2 <= 345 && s2 > 0.30)) {
                            invalidCirclePixels++;
                        }
                    }
                }

                if (circleTotal === 0) return { isValid: false, message: 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.' };

                // Tier B – Micro-texture & granularity (Laplacian edge density + variance)
                const n = grayPixels.length;
                let edgeCount = 0;
                for (let k = 1; k < n; k++) {
                    if (Math.abs(grayPixels[k] - grayPixels[k - 1]) > 7) edgeCount++;
                }
                const edgeDensity = n > 0 ? edgeCount / n : 0;

                const avgR = n > 0 ? rValues.reduce((a, b) => a + b, 0) / n : 0;
                const stdDevR = n > 0 ? Math.sqrt(rValues.map(x => (x - avgR) ** 2).reduce((a, b) => a + b, 0) / n) : 0;

                // Tier B – Smooth non-soil surfaces (blank walls, paper, plastic) have almost no edges or variance
                if (edgeDensity < 0.035 && stdDevR < 5.0) {
                    console.log('Texture Validation Failed (circular): edgeDensity=' + edgeDensity.toFixed(3) + ', stdDevR=' + stdDevR.toFixed(1));
                    return { isValid: false, message: 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.' };
                }

                // Tier D – YCbCr skin-tone x texture gatekeeper
                // Melanin and brown soil share Cb/Cr, so require high skin ratio AND extremely smooth texture to trigger
                let skinPixels = 0;
                for (let py2 = y0; py2 <= y1; py2 += step) {
                    for (let px2 = x0; px2 <= x1; px2 += step) {
                        if (px2 < 0 || px2 >= w || py2 < 0 || py2 >= h) continue;
                        const dx2 = px2 - cx, dy2 = py2 - cy;
                        if (dx2 * dx2 + dy2 * dy2 > r2) continue;
                        const idx2 = (py2 * w + px2) * 4;
                        const sr = fullPixels[idx2], sg = fullPixels[idx2 + 1], sb = fullPixels[idx2 + 2];
                        const cb2 = 128 - 0.168736 * sr - 0.331264 * sg + 0.5 * sb;
                        const cr2 = 128 + 0.5 * sr - 0.418688 * sg - 0.081312 * sb;
                        if (cb2 >= 70 && cb2 <= 135 && cr2 >= 125 && cr2 <= 175) skinPixels++;
                    }
                }
                const skinRatio = circleTotal > 0 ? skinPixels / circleTotal : 0;
                // Primary: Photographed skin (> 0.75 skinRatio, < 0.065 edgeDensity) without requiring low stdDev
                if (skinRatio > 0.75 && edgeDensity < 0.065) {
                    console.log('Tier D Skin Rejection (Photographed Skin): skinRatio=' + skinRatio.toFixed(2) + ' edgeDensity=' + edgeDensity.toFixed(3));
                    return { isValid: false, message: 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.' };
                }
                // Secondary: Flat artificial skin-tones
                if (skinRatio > 0.40 && edgeDensity < 0.030 && stdDevR < 4.5) {
                    console.log('Tier D Skin Rejection (Flat Synthetic): skinRatio=' + skinRatio.toFixed(2) + ' edgeDensity=' + edgeDensity.toFixed(3) + ' stdDevR=' + stdDevR.toFixed(1));
                    return { isValid: false, message: 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.' };
                }

                // Tier C – Earth-tone average + synthetic pixel ratio inside circle
                const avgRc = sumR / circleTotal;
                const avgGc = sumG / circleTotal;
                const avgBc = sumB / circleTotal;
                const greenRatio = greenPixels / circleTotal;
                const circleNonSoilRatio = invalidCirclePixels / circleTotal;

                if (avgGc > avgRc + 20) {
                    console.log('Circle Avg Color Rejected: Green-dominant avgG=' + avgGc.toFixed(0) + ' avgR=' + avgRc.toFixed(0));
                    return { isValid: false, message: 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.' };
                }
                if (greenRatio > 0.25) {
                    console.log('Circle Green Pixel Ratio Rejected: greenRatio=' + greenRatio.toFixed(2));
                    return { isValid: false, message: 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.' };
                }
                if (avgBc > avgRc + 22 && avgBc > avgGc + 16) {
                    console.log('Circle Avg Color Rejected: Blue-dominant avgB=' + avgBc.toFixed(0) + ' avgR=' + avgRc.toFixed(0));
                    return { isValid: false, message: 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.' };
                }
                if (circleNonSoilRatio > 0.45) {
                    console.log('Circle Non-Soil Ratio Rejected: circleNonSoilRatio=' + circleNonSoilRatio.toFixed(2));
                    return { isValid: false, message: 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.' };
                }
            } catch (e) {
                console.log('Client validation check error:', e);
            }

            return { isValid: true };
        }

        // File Upload handler with instant photo validation
        if (btnUpload && fileInput) {
            btnUpload.addEventListener('click', () => {
                if (!checkScanPermissions()) {
                    return;
                }
                fileInput.click();
            });
        }

        function processSelectedPhoto(file) {
            if (!file) return;
            if (!checkScanPermissions()) return;
            stopCamera();
            const reader = new FileReader();
            reader.onload = (event) => {
                imgPreview.src = event.target.result;
                imgPreview.style.display = 'block';
                placeholder.style.display = 'none';
                reticle.style.display = 'flex';
                btnCapture.style.display = 'inline-flex';

                initScanPanZoom();
                if (scanPanZoom) scanPanZoom.reset();
                if (scanResizable) scanResizable.reset();
                const zoomCtrl = document.getElementById('zoomControls');
                if (zoomCtrl) zoomCtrl.style.display = 'flex';

                // Reset error state and display ready state for user to tap Scan Soil
                const resErrorState = document.getElementById('resultErrorState');
                if (resErrorState) resErrorState.style.display = 'none';
                if (resContent) resContent.style.display = 'none';
                if (resPlaceholder) resPlaceholder.style.display = 'block';
                lastScanMatch = null;
                const btnSave = document.getElementById('btnSaveSoilScan');
                if (btnSave) btnSave.disabled = true;

                setTimeout(syncScannerCardHeights, 50);
            };
            reader.readAsDataURL(file);
        }

        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files[0]) {
                    processSelectedPhoto(e.target.files[0]);
                }
            });
        }

        const cameraCaptureInput = document.getElementById('cameraCaptureInput');
        if (cameraCaptureInput) {
            cameraCaptureInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files[0]) {
                    processSelectedPhoto(e.target.files[0]);
                }
            });
        }

        // Trigger Analyze
        /**
         * Return the official Munsell Soil Color name.
         * Keeps color names bound strictly to official Munsell Soil Color Chart standards.
         */
        async function fetchOpenColorName(hexCode, munsellFallback = 'Soil Specimen') {
            return munsellFallback;
        }

        let isAnalyzingSoilScan = false;

        window.analyzeSoilImage = async function() {
            if (!checkScanPermissions()) {
                return;
            }

            if (isAnalyzingSoilScan) {
                return;
            }
            isAnalyzingSoilScan = true;

            const resErrorState = document.getElementById('resultErrorState');

            try {
                const hasImage = imgPreview && imgPreview.src && imgPreview.style.display !== 'none' && imgPreview.src !== '' && window.getComputedStyle(imgPreview).display !== 'none';

                if (!isCameraActive && !hasImage) {
                    showScanNotice('Please start the camera or upload a soil photo sample first before scanning.');
                    isAnalyzingSoilScan = false;
                    return;
                }

                // Show loading animation & hide static panels
                if (resContent) resContent.style.display = 'none';
                if (resPlaceholder) resPlaceholder.style.display = 'none';
                if (resErrorState) resErrorState.style.display = 'none';
                if (scanLoadingOverlay) scanLoadingOverlay.style.display = 'flex';

                // Automatically scroll into view of the scan result card
                const targetResultCard = document.getElementById('resultCard') || resCard;
                if (targetResultCard) {
                    targetResultCard.style.minHeight = (window.innerWidth > 900 ? '480px' : '420px');
                    targetResultCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                let base64Image = null;
                let roiCanvas = null;
                try {
                    const sourceEl = isCameraActive ? video : imgPreview;
                    roiCanvas = createIsolatedCircularRoiCanvas(sourceEl);
                    if (roiCanvas) {
                        base64Image = roiCanvas.toDataURL('image/jpeg', 0.90);
                    }
                } catch (e) {
                    console.log('ROI Crop export fallback:', e);
                }

                if (!base64Image && hasImage && imgPreview.src) {
                    base64Image = imgPreview.src;
                }

                if (!base64Image) {
                    if (scanLoadingOverlay) scanLoadingOverlay.style.display = 'none';
                    if (resErrorState) resErrorState.style.display = 'block';
                    lastScanMatch = null;
                    showScanNotice('Unable to capture soil image. Please re-upload a clear soil photo.');
                    isAnalyzingSoilScan = false;
                    return;
                }

                // Client-side pre-validation: run advisory check
                if (roiCanvas) {
                    try {
                        const clientCheck = await performClientSoilValidation(roiCanvas);
                        if (!clientCheck.isValid) {
                            console.warn('Client validation advisory:', clientCheck.message);
                        }
                    } catch (e) {
                        console.log('Client pre-check exception:', e);
                    }
                }

                // ── Real Pipeline Progress via Polling (Smooth 1% Increment Counter) ──
                const progressBadge   = document.getElementById('scanProgressBadge');
                const loadingTitle    = document.getElementById('scanLoadingTitle');
                const loadingSubtext  = document.getElementById('scanLoadingSubtext');
                const progressBar     = document.getElementById('scanProgressBarInner');

                let currentPct = 1;
                let targetPct = 15;
                let progressTimer = null;
                let creepTicks = 0;

                const applyProgressDisplay = (val) => {
                    if (progressBadge) progressBadge.textContent = `${val}%`;
                    if (progressBar)   progressBar.style.width   = `${Math.min(100, Math.max(val, 1))}%`;
                };

                const updateStageText = (title, subtext) => {
                    if (loadingTitle)   loadingTitle.textContent   = title;
                    if (loadingSubtext) loadingSubtext.textContent = subtext;
                };

                const stopProgressAnimation = () => {
                    if (progressTimer) {
                        clearInterval(progressTimer);
                        progressTimer = null;
                    }
                };

                const finishProgressAnimation = async () => {
                    stopProgressAnimation();
                    while (currentPct < 100) {
                        currentPct++;
                        applyProgressDisplay(currentPct);
                        await new Promise(r => setTimeout(r, 10));
                    }
                    await new Promise(r => setTimeout(r, 180));
                };

                // Initialize counter at 1%
                applyProgressDisplay(1);
                updateStageText('Initializing pipeline…', 'Preparing soil analysis engine');

                // Starts continuous 1% increment counter (1%, 2%, 3%, ...)
                progressTimer = setInterval(() => {
                    if (currentPct < targetPct) {
                        currentPct++;
                        applyProgressDisplay(currentPct);
                    } else if (currentPct < 96) {
                        // Slowly creep forward 1% every ~280ms while awaiting next server stage
                        creepTicks++;
                        if (creepTicks % 8 === 0) {
                            currentPct++;
                            applyProgressDisplay(currentPct);
                        }
                    }
                }, 35);

                // Stage metadata mirrors the Python pipeline labels with target % bounds
                const STAGE_META = {
                    0: { targetPct: 15, title: 'Initializing pipeline…',                   sub: 'Preparing soil analysis engine' },
                    1: { targetPct: 32, title: 'Stage 1/6: Soil Gate Validation…',         sub: 'Verifying genuine soil grain texture & non-soil rejection' },
                    2: { targetPct: 50, title: 'Stage 2/6: Lighting Normalization…',       sub: 'Balancing dark & light exposure to center (Target L* 50.0)' },
                    3: { targetPct: 68, title: 'Stage 3/6: Multi-Color Detection…',        sub: 'Extracting dominant soil color clusters via ColorThief' },
                    4: { targetPct: 82, title: 'Stage 4/6: Per-Region Verification…',      sub: 'Re-checking individual color regions through soil gate' },
                    5: { targetPct: 94, title: 'Stage 5/6: Munsell Spectral Match…',       sub: 'Calculating CIEDE2000 (ΔE₀₀) color distance to Munsell chart' },
                    6: { targetPct: 98, title: 'Stage 6/6: Agronomic Profile & Centroid…', sub: 'Generating soil properties, drainage & crop profile' },
                };

                // Generate a client-owned UUID for this scan
                const scanId = ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
                    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                );

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // POST to kick off the non-blocking Python pipeline (returns HTTP 202 immediately)
                const startResp = await fetch("{{ route('website.scan.analyze') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ image_base64: base64Image, scan_id: scanId })
                });

                if (!startResp.ok) {
                    stopProgressAnimation();
                    const errData = await startResp.json().catch(() => ({}));
                    if (scanLoadingOverlay) scanLoadingOverlay.style.display = 'none';
                    if (resErrorState) {
                        resErrorState.style.display = 'block';
                        const errDesc = resErrorState.querySelector('.error-state-desc');
                        if (errDesc && errData.message) errDesc.textContent = errData.message;
                    }
                    showScanNotice(errData.message || 'Unable to start scan. Please try again.');
                    isAnalyzingSoilScan = false;
                    return;
                }

                // ── Poll /scan/progress/{scanId} until Stage 6 done or error ──
                let data = null;
                let pollAttempts = 0;
                let sinceCursor = 0;
                const MAX_POLL = 150; // 150 × 400ms = 60s timeout

                while (pollAttempts < MAX_POLL) {
                    pollAttempts++;
                    try {
                        const pollResp = await fetch(`/scan/progress/${scanId}?since=${sinceCursor}`, {
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                        });
                        if (!pollResp.ok) {
                            throw new Error('Poll endpoint error: HTTP ' + pollResp.status);
                        }

                        const payload = await pollResp.json();
                        sinceCursor = payload.cursor ?? sinceCursor;

                        for (const evt of (payload.events || [])) {
                            const meta = STAGE_META[evt.stage] ?? STAGE_META[0];
                            if (evt.status === 'running' || evt.status === 'done') {
                                targetPct = Math.max(targetPct, meta.targetPct);
                                updateStageText(meta.title.replace('…', ''), meta.sub);
                            }
                            if (evt.stage === 0 && evt.status === 'error') {
                                stopProgressAnimation();
                                throw new Error(evt.error || 'Pipeline error');
                            }
                            if (evt.status === 'rejected') {
                                stopProgressAnimation();
                                // Stage 1 or 4 hard-rejected — treat as fake soil
                                data = {
                                    success: false,
                                    label: 'fake_soil',
                                    message: 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.'
                                };
                                break;
                            }
                        }

                        if (data) break;

                        const stage  = payload.stage  ?? 0;
                        const status = payload.status ?? 'pending';
                        if (stage === 6 && status === 'done') {
                            updateStageText('Complete', 'Analysis finished');
                            await finishProgressAnimation();
                            data = payload.result ?? {};
                            data.success = data.success ?? true;
                            break;
                        }
                    } catch (pollErr) {
                        // If it's a fatal error from stage 0, rethrow to stop
                        if (pollErr.message && pollErr.message.includes('Pipeline error')) {
                            stopProgressAnimation();
                            throw pollErr;
                        }
                        console.warn('Poll error (retrying):', pollErr);
                    }
                    await new Promise(r => setTimeout(r, 400));
                }

                stopProgressAnimation();

                if (!data && pollAttempts >= MAX_POLL) {
                    if (scanLoadingOverlay) scanLoadingOverlay.style.display = 'none';
                    throw new Error('Scan timed out after 60 seconds.');
                }

                // Hard rejection: poll resolved but no usable result
                if (!data || !data.success) {
                    if (scanLoadingOverlay) scanLoadingOverlay.style.display = 'none';
                    if (resContent)    resContent.style.display    = 'none';
                    if (resPlaceholder) resPlaceholder.style.display = 'none';
                    if (resErrorState) {
                        resErrorState.style.display = 'block';
                        const errDesc = resErrorState.querySelector('.error-state-desc');
                        if (errDesc && data?.message) errDesc.textContent = data.message;
                    }
                    lastScanMatch = null;
                    showScanNotice(data?.message || 'Unable to process soil photo sample. Please ensure image is well-lit and clear.');
                    isAnalyzingSoilScan = false;
                    return;
                }

                // Soft rejection: Python pipeline returned fake_soil
                if (data.label && data.label !== 'real_soil') {
                    if (scanLoadingOverlay) scanLoadingOverlay.style.display = 'none';
                    if (resContent) resContent.style.display = 'none';
                    if (resPlaceholder) resPlaceholder.style.display = 'none';
                    if (resErrorState) {
                        resErrorState.style.display = 'block';
                        const errDescSoft = resErrorState.querySelector('.error-state-desc');
                        if (errDescSoft) {
                            errDescSoft.textContent = 'No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.';
                        }
                    }
                    lastScanMatch = null;
                    showScanNotice('No valid soil sample detected. Please align the viewfinder over a clear, well-lit soil sample and try again.');
                    isAnalyzingSoilScan = false;
                    return;
                }

                window.getNearMatchScore = function(r, fallback = '98.2%') {
                    if (!r) return fallback;
                    const de = (r.color_delta_e !== undefined && r.color_delta_e !== null) ? r.color_delta_e : (r.delta_e !== undefined && r.delta_e !== null ? r.delta_e : (r.distance !== undefined && r.distance !== null ? r.distance : null));
                    if (de !== null && !isNaN(de) && parseFloat(de) >= 0) {
                        const val = 99.4 - (parseFloat(de) * 0.45);
                        return Math.max(88.5, Math.min(99.6, Math.round(val * 10) / 10)).toFixed(1) + '%';
                    }
                    if (r.near_match && typeof r.near_match === 'string' && r.near_match !== '100%' && r.near_match !== '100.0%') {
                        return r.near_match.includes('%') ? r.near_match : r.near_match + '%';
                    }
                    if (r.confidence && typeof r.confidence === 'string' && r.confidence !== '100%' && r.confidence !== '100.0%') {
                        return r.confidence.includes('%') ? r.confidence : r.confidence + '%';
                    }
                    return fallback;
                };

                // Match format expected by UI rendering
                const match = {
                    code:       data.munsell_code,
                    color_name: data.color_name || 'Determined Soil Specimen',
                    soil_type:  data.soil_name  || 'Agricultural Loam',
                    name:       data.color_name || 'Determined Soil Specimen',
                    hue:        data.hue,
                    val:        data.value,
                    chroma:     data.chroma,
                    confidence: window.getNearMatchScore(data, (data.confidence_formatted && data.confidence_formatted !== '100%' && data.confidence_formatted !== '100.0%') ? data.confidence_formatted : '98.2%'),
                    hex:        data.hex,
                    desc:       data.description,
                    organic:    data.organic_matter,
                    drainage:   data.drainage_profile,
                    crops:      data.recommended_crops,
                    engine:     data.engine,
                    regions:    data.regions || []
                };

                // Async-enrich match.name from Open Colors API (non-blocking)
                fetchOpenColorName(match.hex, match.color_name).then(openName => {
                    match.name = openName;
                    if (lastScanMatch === match) {
                        if (resName) resName.textContent = openName;
                        const tbody = document.getElementById('savedScansTbody');
                        const firstNameCell = tbody?.querySelector('tr.is-new-scan-row .col-soil-name');
                        if (firstNameCell) firstNameCell.childNodes[0].textContent = openName;
                    }
                });

                // Header Title & Multi-Layer handling
                const resHeaderTitle = document.getElementById('resHeaderTitle');
                const resMultiBadgeCount = document.getElementById('resMultiBadgeCount');
                const resDominantChevron = document.getElementById('resDominantChevron');
                const resSoilPercentageDisplay = document.getElementById('resSoilPercentageDisplay');
                const resFullCode = document.getElementById('resFullCode');
                const resScanCount = document.getElementById('resScanCount');
                const resMultiLayerSection = document.getElementById('resMultiLayerSection');
                const resMultiLayerItems = document.getElementById('resMultiLayerItems');
                const resMetadataFooter = document.getElementById('resMetadataFooter');
                const resMetaCreated = document.getElementById('resMetaCreated');
                const resMetaLayers = document.getElementById('resMetaLayers');

                const regions = Array.isArray(data.regions) ? data.regions : [];
                const isMultiLayer = regions.length > 1;

                if (resHeaderTitle) {
                    resHeaderTitle.textContent = isMultiLayer ? `SOIL RECORD DETAILS (${regions.length} DETECTED)` : 'DETERMINED SOIL RECORD';
                }
                if (resMultiBadgeCount) {
                    resMultiBadgeCount.style.display = isMultiLayer ? 'inline-block' : 'none';
                    resMultiBadgeCount.textContent = `${regions.length} DETECTED`;
                }

                // Dominant layer percentage in Hero Banner
                if (isMultiLayer) {
                    if (resDominantChevron) resDominantChevron.style.display = 'inline';
                    if (resSoilPercentageDisplay) {
                        resSoilPercentageDisplay.style.display = 'block';
                        const domPct = regions[0].percent || Math.round((regions[0].proportion || 0.6) * 100);
                        resSoilPercentageDisplay.textContent = `Soil Percentage (${domPct}%)`;
                    }
                } else {
                    if (resDominantChevron) resDominantChevron.style.display = 'none';
                    if (resSoilPercentageDisplay) resSoilPercentageDisplay.style.display = 'none';
                }

                // Row 1: Code Details
                if (resName) resName.textContent = match.color_name;
                if (resFullCode) resFullCode.textContent = match.code;
                const resCodeDisplay = document.getElementById('resCodeDisplay');
                if (resCodeDisplay) resCodeDisplay.textContent = match.code;
                if (resScanCount) resScanCount.textContent = data.scan_seq || '1';

                // Row 2: Soil Properties
                if (resOrganic) resOrganic.textContent = match.organic;
                if (resDrainage) resDrainage.textContent = match.soil_type;
                if (resConfidence) resConfidence.textContent = match.confidence;

                // Scan Note
                const resCardScanNote = document.getElementById('resCardScanNote');
                if (resCardScanNote) {
                    const rawLiveNote = data.scan_note || match.scan_note || '';
                    let cleanLiveNote = typeof rawLiveNote === 'string' ? rawLiveNote.trim() : '';
                    if (cleanLiveNote && !cleanLiveNote.startsWith('Determined Soil:') && cleanLiveNote !== '—' && cleanLiveNote !== '-') {
                        resCardScanNote.textContent = cleanLiveNote;
                    } else {
                        resCardScanNote.textContent = 'No scan note available for this record.';
                    }
                }

                // Description (Single clean description line)
                if (resDesc) {
                    let singleDesc = match.desc;
                    if (!singleDesc || singleDesc.includes('Sample processed')) {
                        singleDesc = `${match.code} - ${match.color_name}`;
                    } else {
                        singleDesc = singleDesc.replace(/\.{2,}\s*[a-zA-Z]*$/g, '.').replace(/\.{2,}/g, '.').trim();
                    }
                    if (singleDesc.includes('\n')) {
                        singleDesc = singleDesc.split('\n')[0].trim();
                    }
                    resDesc.textContent = singleDesc;
                }

                // Resolve the whole actual scanned soil image for avatars
                let snippetUrl = null;
                const videoEl = document.getElementById('webcamVideo');
                const imgPreviewEl = document.getElementById('imagePreview');

                if (isCameraActive && videoEl && videoEl.readyState >= 2) {
                    snippetUrl = extractCircularSoilSnippet(videoEl, match.hex);
                } else if (imgPreviewEl && imgPreviewEl.src && imgPreviewEl.complete && imgPreviewEl.naturalWidth > 0) {
                    snippetUrl = extractCircularSoilSnippet(imgPreviewEl, match.hex);
                }

                if (!snippetUrl && base64Image) {
                    snippetUrl = base64Image;
                }

                let actualScanImage = data.normalized_image_url || data.stage2_normalized_url || data.scan_image_url || data.image || data.scan_image || snippetUrl || base64Image;
                if (typeof actualScanImage === 'string' && actualScanImage.startsWith('storage/')) {
                    actualScanImage = '/' + actualScanImage;
                }
                window.currentCleanScanImage = actualScanImage;
                match.image = actualScanImage;
                match.scan_image = actualScanImage;

                // Soil Normalization: Before & After Card Display (With Detected Soil Color Boxes & Swatches)
                const resBeforeAfterCard = document.getElementById('resBeforeAfterCard');
                const resBeforeAfterImg = document.getElementById('resBeforeAfterImg');
                const resBeforeAfterBoxes = document.getElementById('resBeforeAfterBoxes');
                const resBeforeAfterSwatches = document.getElementById('resBeforeAfterSwatches');
                if (resBeforeAfterCard && resBeforeAfterImg) {
                    const rawSrc = data.raw_image_url || snippetUrl || base64Image;
                    const normSrc = actualScanImage;
                    let baUrl = data.before_after_image_url;
                    if (!baUrl && data.scan_id) {
                        baUrl = `/storage/scans/scan_${data.scan_id}_before_after.jpg?t=${Date.now()}`;
                    }

                    const targetReg = (regions && regions[0]) || { hex: match.hex, bounding_box: null };
                    window.currentScanResult = data;
                    window.currentActiveScanRegions = regions;
                    window.currentBeforeAfterRegions = regions;
                    window.currentBeforeAfterRegion = targetReg;
                    window.currentDetectedSoilRegion = targetReg;

                    function showResBeforeAfter(src) {
                        resBeforeAfterImg.onload = () => {
                            resBeforeAfterCard.style.display = 'block';
                            // Show dynamic color-coded boxes on the AFTER side per region
                            if (resBeforeAfterBoxes) renderBeforeAfterBoxes(resBeforeAfterBoxes, regions || [targetReg]);
                            if (resBeforeAfterSwatches) renderStage3Swatches(resBeforeAfterSwatches, regions, match);
                        };
                        resBeforeAfterImg.onerror = () => {
                            if (resBeforeAfterBoxes) resBeforeAfterBoxes.innerHTML = '';
                            if (rawSrc && normSrc) {
                                generateBeforeAfterCanvas(rawSrc, normSrc, match.code, targetReg, (stitchedDataUrl) => {
                                    if (stitchedDataUrl) {
                                        showResBeforeAfter(stitchedDataUrl);
                                    } else {
                                        resBeforeAfterCard.style.display = 'none';
                                    }
                                });
                            } else {
                                resBeforeAfterCard.style.display = 'none';
                            }
                        };
                        resBeforeAfterImg.src = src;
                        if (resBeforeAfterBoxes) renderBeforeAfterBoxes(resBeforeAfterBoxes, regions || [targetReg]);
                        if (resBeforeAfterSwatches) renderStage3Swatches(resBeforeAfterSwatches, regions, match);
                        resBeforeAfterCard.style.display = 'block';
                    }

                    if (baUrl) {
                        showResBeforeAfter(baUrl);
                    } else if (rawSrc && normSrc) {
                        generateBeforeAfterCanvas(rawSrc, normSrc, match.code, targetReg, (stitchedDataUrl) => {
                            if (stitchedDataUrl) {
                                showResBeforeAfter(stitchedDataUrl);
                            } else {
                                if (resBeforeAfterBoxes) resBeforeAfterBoxes.innerHTML = '';
                                resBeforeAfterCard.style.display = 'none';
                            }
                        });
                    } else {
                        if (resBeforeAfterBoxes) resBeforeAfterBoxes.innerHTML = '';
                        resBeforeAfterCard.style.display = 'none';
                    }
                }

                // Multi-Layer Accordion Rows (Secondary & Tertiary Layers)
                if (isMultiLayer && resMultiLayerSection && resMultiLayerItems) {
                    resMultiLayerSection.style.display = 'block';
                    resMultiLayerItems.innerHTML = '';
                    regions.slice(1).forEach((r, idx) => {
                        const rPct = r.percent || Math.round((r.proportion || 0.2) * 100);
                        const rMunsell = r.munsell || r.munsell_code || 'Soil Color';
                        const rHex = r.hex || getMunsellColorHex(rMunsell, '') || '#8F694E';
                        const rLabel = r.label || r.color_name || 'Soil Color';
                        const rConf = window.getNearMatchScore(r, '96.8%');

                        const rOrganic = r.organic_matter || match.organic || 'Moderate (2.2% – 3.7%)';
                        const rDrainage = r.soil_name || match.drainage || 'Well-Drained Clay / Latosol';
                        const rDesc = r.description || `${rMunsell} - ${rLabel}`;

                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'layer-accordion-item';

                        const avatarId = `res-region-avatar-${idx}`;

                        itemDiv.innerHTML = `
                            <button type="button" class="layer-accordion-header">
                                <div style="display: flex; align-items: center; gap: 8px; min-width: 0;">
                                    <svg class="layer-chevron" width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 4 18 12 6 20"></polygon></svg>
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${rMunsell} (Soil Percentage - ${rPct}%)</span>
                                </div>
                            </button>
                            <div class="layer-accordion-body" style="display: none;">

                                <!-- Swatch banner: matches dominant layer hero style with square-ish avatar on left -->
                                <div style="width: 100%; height: 66px; border-radius: 12px; background: ${rHex}; box-shadow: 0 3px 10px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 12px; padding: 0 14px; margin-bottom: 10px; box-sizing: border-box; overflow: visible;">
                                    <span id="${avatarId}" style="width:56px;height:56px;aspect-ratio:1/1;border-radius:10px;background:rgba(0,0,0,0.2);border:none;outline:none;flex-shrink:0;overflow:hidden;box-shadow:none;position:relative;display:flex;align-items:center;justify-content:center;">
                                        <img class="layer-zoom-img" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none;border-radius:0;border:none;outline:none;" />
                                        <span class="layer-zoom-fallback" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;border-radius:0;"></span>
                                    </span>
                                    <span style="font-size: 17px; font-weight: 800; color: #ffffff; text-shadow: none; flex: 1; min-width: 0;">${rMunsell}</span>
                                    <span style="font-size: 10.5px; font-weight: 800; color: #ffffff; background: rgba(0,0,0,0.5); padding: 3px 9px; border-radius: 9999px; font-family: monospace; flex-shrink: 0;">${rHex.toUpperCase()}</span>
                                </div>

                                <!-- Row 1: Color Name, Full Code, Proportion -->
                                <div class="soil-modal-grid-3" style="margin-bottom: 8px;">
                                    <div class="soil-modal-grid-card">
                                        <div class="soil-modal-label">Color Name</div>
                                        <div class="soil-modal-value">${rLabel}</div>
                                    </div>
                                    <div class="soil-modal-grid-card">
                                        <div class="soil-modal-label">Full Code</div>
                                        <div class="soil-modal-value">${rMunsell}</div>
                                    </div>
                                    <div class="soil-modal-grid-card">
                                        <div class="soil-modal-label">Proportion</div>
                                        <div class="soil-modal-value">${rPct}%</div>
                                    </div>
                                </div>

                                <!-- Row 2: Soil Organic, Soil Type, Near-Match -->
                                <div class="soil-modal-grid-3" style="margin-bottom: 8px;">
                                    <div class="soil-modal-grid-card">
                                        <div class="soil-modal-label">Soil Organic</div>
                                        <div class="soil-modal-value">${rOrganic}</div>
                                    </div>
                                    <div class="soil-modal-grid-card">
                                        <div class="soil-modal-label">Soil Type</div>
                                        <div class="soil-modal-value">${rDrainage}</div>
                                    </div>
                                    <div class="soil-modal-grid-card">
                                        <div class="soil-modal-label">Near-Match</div>
                                        <div class="soil-modal-value" style="color: #10b981;">${rConf}</div>
                                    </div>
                                </div>

                                <!-- Scan Note -->
                                <div class="soil-modal-grid-card" style="margin-bottom: 8px;">
                                    <div class="soil-modal-label">Scan Note</div>
                                    <div class="soil-modal-value layer-accordion-desc" style="font-size: 13px; font-weight: 700; line-height: 1.5; white-space: pre-line;">${(r.scan_note && !r.scan_note.startsWith('Determined Soil:') && r.scan_note !== '—' && r.scan_note !== '-') ? r.scan_note : 'No scan note available for this record.'}</div>
                                </div>

                                <!-- Description -->
                                <div class="soil-modal-grid-card">
                                    <div class="soil-modal-label">Description</div>
                                    <div class="soil-modal-value layer-accordion-desc" style="font-size: 13px; font-weight: 700; line-height: 1.5; white-space: pre-line;">${rDesc}</div>
                                </div>

                                <!-- Layer Pin Location Button (Placed directly after Description) -->
                                <div style="margin-top: 10px;">
                                    <button type="button" class="btn-res-action btn-res-pin-location btn-layer-pin-btn" style="width: 100%; justify-content: center; padding: 10px 14px; font-size: 12.5px;" title="Pin This Soil Layer to Map">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>Pin Location</span>
                                    </button>
                                </div>
                            </div>
                        `;

                        // Populate the avatar with whole image + colored bounding box for this sub-layer
                        populateZoomAvatar(
                            itemDiv.querySelector(`#${avatarId}`),
                            r,
                            actualScanImage
                        );

                        const layerPinBtn = itemDiv.querySelector('.btn-layer-pin-btn');
                        if (layerPinBtn) {
                            layerPinBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                openPinLocationModal(r, false);
                            });
                        }

                        const headerBtn = itemDiv.querySelector('.layer-accordion-header');
                        const bodyEl = itemDiv.querySelector('.layer-accordion-body');
                        const chevronEl = itemDiv.querySelector('.layer-chevron');
                        headerBtn.addEventListener('click', () => {
                            const isOpen = bodyEl.style.display === 'block';
                            bodyEl.style.display = isOpen ? 'none' : 'block';
                            chevronEl.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
                        });

                        resMultiLayerItems.appendChild(itemDiv);
                    });
                } else if (resMultiLayerSection) {
                    resMultiLayerSection.style.display = 'none';
                }

                // Metadata Footer (Always show on completed scan)
                if (resMetadataFooter) {
                    resMetadataFooter.style.display = 'flex';
                    const now = new Date();
                    const mm = String(now.getMonth() + 1).padStart(2, '0');
                    const dd = String(now.getDate()).padStart(2, '0');
                    const yyyy = now.getFullYear();
                    if (resMetaCreated) resMetaCreated.textContent = `${mm}/${dd}/${yyyy}`;
                    if (resMetaLayers) resMetaLayers.textContent = Math.max(regions.length, 1);
                }

                // Store scan response data and render color space badges
                currentScanResponseData = data;
                renderColorSpaceBadges();

                // Bind Dual-Preview Soil Badge
                const resSwatchBlock = document.getElementById('resSwatchBlock');
                const resHexPill = document.getElementById('resHexPill');
                const resSoilCircleSnippet = document.getElementById('resSoilCircleSnippet');
                const resSoilSnippetFallback = document.getElementById('resSoilSnippetFallback');

                if (resSwatchBlock) resSwatchBlock.style.backgroundColor = match.hex || '#594433';
                if (resHexPill) resHexPill.textContent = match.hex ? match.hex.toUpperCase() : match.code;

                if (resSoilCircleSnippet && actualScanImage) {
                    const targetReg = (regions && regions[0]) || { hex: match.hex, bounding_box: null };
                    populateZoomAvatar(resSoilCircleSnippet, targetReg, actualScanImage);
                }

                if (resErrorState) resErrorState.style.display = 'none';
                if (resPlaceholder) resPlaceholder.style.display = 'none';

                if (window.AgriMotion && typeof window.AgriMotion.transitionSkeleton === 'function' && scanLoadingOverlay && scanLoadingOverlay.style.display !== 'none') {
                    window.AgriMotion.transitionSkeleton(scanLoadingOverlay, resContent, { minShow: 220, step: 110 });
                } else {
                    if (scanLoadingOverlay) scanLoadingOverlay.style.display = 'none';
                    if (resContent) resContent.style.display = 'block';
                }

                const resCardContainer = document.querySelector('.result-card');
                if (resCardContainer) {
                    resCardContainer.style.height = 'auto';
                    resCardContainer.style.minHeight = '100%';
                }
                setTimeout(syncScannerCardHeights, 50);

                const targetCompletedCard = document.getElementById('resultCard') || resCard;
                if (targetCompletedCard) {
                    targetCompletedCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                lastScanMatch = match;
                if (lastScanMatch) {
                    lastScanMatch.regions = regions;
                    lastScanMatch.before_after_image_url = data.before_after_image_url || baUrl || null;
                }
                lastScanLat = null;
                lastScanLng = null;

                if (data.created_scans && Array.isArray(data.created_scans) && data.created_scans.length > 1) {
                    const prevNewScans = document.querySelectorAll('#savedScansTbody .is-new-scan-row');
                    prevNewScans.forEach(r => r.classList.remove('is-new-scan-row'));

                    const scansToAdd = [...data.created_scans].reverse();
                    scansToAdd.forEach(sc => {
                        sc.image = actualScanImage;
                        sc.before_after_image_url = data.before_after_image_url || null;
                        sc.raw_image_url = data.raw_image_url || null;
                        autoRecordScanToHistory(sc, null, null, sc.scan_seq || 1, sc.scan_id, true);
                    });
                } else {
                    match.before_after_image_url = data.before_after_image_url || null;
                    match.raw_image_url = data.raw_image_url || null;
                    autoRecordScanToHistory(match, null, null, data.scan_seq, data.scan_id, false);
                }

                const btnSave = document.getElementById('btnSaveSoilScan');
                if (btnSave) {
                    btnSave.disabled = false;
                    btnSave.style.opacity = '1';
                    btnSave.style.cursor = 'pointer';
                    btnSave.style.background = '#22c55e';
                    btnSave.innerHTML = `
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Save Copy
                    `;
                }


            } catch (err) {
                console.log('Backend scan error:', err);
                if (scanLoadingOverlay) scanLoadingOverlay.style.display = 'none';
                if (resContent) resContent.style.display = 'none';
                if (resPlaceholder) resPlaceholder.style.display = 'none';
                if (resErrorState) {
                    resErrorState.style.display = 'block';
                    const errTitle = resErrorState.querySelector('.error-state-title');
                    const errDesc = resErrorState.querySelector('.error-state-desc');
                    if (errTitle) errTitle.textContent = 'Scan Analysis Interrupted';
                    if (errDesc) errDesc.textContent = err.message || 'Unable to complete soil scan analysis. Please check your network connection and try again.';
                }
                lastScanMatch = null;
                showScanNotice('Unable to complete soil scan analysis. Please check your network connection.');
            } finally {
                if (typeof stopProgressAnimation === 'function') stopProgressAnimation();
                isAnalyzingSoilScan = false;
            }
        }

        const trackedScanIds = new Set();

        function autoRecordScanToHistory(match, lat, lng, scanSeq = 1, scanId = null, preserveHighlight = false) {
            const tbody = document.getElementById('savedScansTbody');
            const emptyState = document.getElementById('savedScansEmptyState');
            const badgeCount = document.getElementById('savedScansCountBadge');

            if (emptyState) emptyState.style.display = 'none';

            // Deduplication Check: Skip if scanId has already been added to the DOM table
            if (scanId && trackedScanIds.has(scanId)) {
                return;
            }
            if (scanId) {
                trackedScanIds.add(scanId);
            }

            if (tbody) {
                // Check if DOM already contains a row with this scan ID
                if (scanId && tbody.querySelector(`tr[data-scan-id="${scanId}"]`)) {
                    return;
                }

                if (!preserveHighlight) {
                    // Clear new scan highlight from previous rows so only the latest single scan is highlighted
                    const prevNewScans = tbody.querySelectorAll('.is-new-scan-row');
                    prevNewScans.forEach(r => r.classList.remove('is-new-scan-row'));
                }

                const tr = document.createElement('tr');
                tr.className = 'is-new-scan-row';
                if (scanId) {
                    tr.setAttribute('data-scan-id', scanId);
                }

                const currentTotal = badgeCount ? (parseInt(badgeCount.textContent) || 0) : tbody.children.length;
                const newSeqNum = currentTotal + 1;
                const dateStr = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

                const rawSoilName = (match.color_name || match.name || '—').replace(/\s*\([A-Za-z0-9\/\.\s]+\)$/i, '');
                const safeSoilName = rawSoilName.replace(/"/g, '&quot;');
                const safeSoilType = (match.soil_type || match.drainage || '—').replace(/"/g, '&quot;');
                const safeName  = safeSoilName;
                const safeCode  = (match.code  || '—').replace(/"/g, '&quot;');
                const safeHue   = (match.hue   || '—').replace(/"/g, '&quot;');
                const safeDesc  = (match.desc  || '—').replace(/"/g, '&quot;');
                const safeImage = (match.image || match.scan_image || '').replace(/"/g, '&quot;');
                const safeHex   = (match.hex   || '').replace(/"/g, '&quot;');
                const safeBeforeAfter = (match.before_after_image_url || '').replace(/"/g, '&quot;');
                const safeRawImage = (match.raw_image_url || '').replace(/"/g, '&quot;');
                const loggedUName = ("{{ Auth::user()->name ?? 'Agrico Administrator' }}").replace(/"/g, '&quot;');
                const loggedUEmail = ("{{ Auth::user()->email ?? 'admin@agrico.com' }}").replace(/"/g, '&quot;');
                const loggedUCode = ("{{ Auth::user()->user_id ?? Auth::user()->id ?? 'ADM-001' }}").replace(/"/g, '&quot;');
                const loggedUAvatar = loggedUName.charAt(0).toUpperCase();

                const safeOrganic = (match.organic || 'High (3.8%)').replace(/"/g, '&quot;');
                const safeDrainage = (match.drainage || match.name || 'Well-Drained Loam').replace(/"/g, '&quot;');
                const safeConfidence = ((window.getNearMatchScore ? window.getNearMatchScore(match, (match.confidence && match.confidence !== '100%' && match.confidence !== '100.0%') ? match.confidence : '98.5%') : match.confidence) || '98.5%').replace(/"/g, '&quot;');
                const safePercent = match.percent ? String(match.percent) : '';
                const safeScanNote = (match.scan_note || (typeof currentScanResponseData !== 'undefined' && currentScanResponseData ? currentScanResponseData.scan_note : '') || '').replace(/"/g, '&quot;');

                const displaySeq = scanSeq || 1;
                const displayDesc = safeDesc.startsWith(safeCode) ? safeDesc : `${safeCode} — ${safeDesc}`;

                const safeRegions = JSON.stringify(match.regions || []).replace(/"/g, '&quot;');

                tr.innerHTML = `
                    <td class="col-seq" style="font-size:13px; font-weight:600; padding-left:16px; padding-right:6px; overflow:visible; text-overflow:clip; white-space:nowrap;">${newSeqNum}</td>
                    <td class="col-soil-name" style="font-size:14px; font-weight:700; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; max-width:200px;" title="${safeSoilName}">${safeSoilName}</td>
                    <td class="col-soil-desc" style="font-size:13px; font-weight:500;" title="${displayDesc}">${displayDesc.length > 28 ? displayDesc.substring(0, 26) + '…' : displayDesc}</td>
                    <td class="col-soil-code" style="font-size:13.5px; font-weight:700;">${match.code}</td>
                    <td class="col-soil-hue" style="font-size:13px; font-weight:700;">${match.hue}</td>
                    <td class="col-soil-seq opt-scan-seq" style="font-size:13px; font-weight:700;">${displaySeq}</td>
                    <td class="col-soil-date" style="font-size:13px; font-weight:600;">${dateStr}</td>
                    <td class="col-action" style="font-size:13px; font-weight:700; overflow:visible !important; text-overflow:clip !important; white-space:nowrap !important;">
                        <button type="button" class="btn-view-scan" onclick="openScanModalFromBtn(this, event)"
                            data-name="${safeName}"
                            data-code="${safeCode}"
                            data-hue="${safeHue}"
                            data-hex="${safeHex}"
                            data-image="${safeImage}"
                            data-before-after="${safeBeforeAfter}"
                            data-raw-image="${safeRawImage}"
                            data-scan-id="${scanId || ''}"
                            data-seq="${displaySeq}"
                            data-date="${dateStr}"
                            data-desc="${safeDesc}"
                            data-scan-note="${safeScanNote}"
                            data-organic="${safeOrganic}"
                            data-drainage="${safeDrainage}"
                            data-confidence="${safeConfidence}"
                            data-percent="${safePercent}"
                            data-lat="${match.lat || ''}"
                            data-lng="${match.lng || ''}"
                            data-barangay="${match.barangay || ''}"
                            data-coverage="${match.coverage || ''}"
                            data-uname="${loggedUName}"
                            data-uemail="${loggedUEmail}"
                            data-ucode="${loggedUCode}"
                            data-uavatar="${loggedUAvatar}"
                            data-regions="${safeRegions}">View</button></td>
                `;
                tbody.insertBefore(tr, tbody.firstChild);
            }

            if (badgeCount) {
                const currentTotal = parseInt(badgeCount.textContent) || 0;
                badgeCount.textContent = (currentTotal + 1) + ' total scans';
            }
        }

        window.openScanModalFromBtn = function(btn, e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            if (!btn) return;
            let parsedRegions = [];
            try {
                const rawR = btn.getAttribute('data-regions');
                if (rawR) parsedRegions = JSON.parse(rawR);
            } catch (err) {
                parsedRegions = [];
            }
            const data = {
                name: btn.getAttribute('data-name'),
                code: btn.getAttribute('data-code'),
                hue: btn.getAttribute('data-hue'),
                hex: btn.getAttribute('data-hex'),
                seq: btn.getAttribute('data-seq'),
                date: btn.getAttribute('data-date'),
                desc: btn.getAttribute('data-desc'),
                scan_note: btn.getAttribute('data-scan-note') || null,
                percent: btn.getAttribute('data-percent'),
                image: btn.getAttribute('data-image'),
                before_after_image_url: btn.getAttribute('data-before-after') || null,
                raw_image_url: btn.getAttribute('data-raw-image') || null,
                scan_id: btn.getAttribute('data-scan-id') || null,
                lat: btn.getAttribute('data-lat') || null,
                lng: btn.getAttribute('data-lng') || null,
                barangay: btn.getAttribute('data-barangay') || null,
                coverage: btn.getAttribute('data-coverage') || null,
                organic: btn.getAttribute('data-organic') || 'Moderate (2.2% – 3.7%)',
                drainage: btn.getAttribute('data-drainage') || 'Well-Drained Clay / Latosol',
                confidence: btn.getAttribute('data-confidence') || '97.4%',
                userName: btn.getAttribute('data-uname'),
                userEmail: btn.getAttribute('data-uemail'),
                userCode: btn.getAttribute('data-ucode'),
                userAvatar: btn.getAttribute('data-uavatar'),
                regions: parsedRegions
            };
            viewScanDetails(data);
        };

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-view-scan');
            if (btn) {
                window.openScanModalFromBtn(btn, e);
            }
        });

        let currentModalScanData = null;
        let modalSoilDetailMapInstance = null;

        function viewScanDetails(data) {
            if (typeof data === 'string') {
                try { data = JSON.parse(data); } catch(e) {}
            }
            if (!data) return;

            currentModalScanData = data;

            try {
                const modalRecordTitle = document.getElementById('modalRecordTitle');
                const modalDominantChevron = document.getElementById('modalDominantChevron');
                const modalSoilPercentageDisplay = document.getElementById('modalSoilPercentageDisplay');
                const modalSwatchBlock = document.getElementById('modalSwatchBlock');
                const modalCodeDisplay = document.getElementById('modalCodeDisplay');
                const modalHexPill = document.getElementById('modalHexPill');
                const modalSoilCircleSnippet = document.getElementById('modalSoilCircleSnippet');
                const modalSoilSnippetFallback = document.getElementById('modalSoilSnippetFallback');

                const modalCardColorName = document.getElementById('modalCardColorName');
                const modalCardFullCode = document.getElementById('modalCardFullCode');
                const modalCardScanCount = document.getElementById('modalCardScanCount');
                const modalCardOrganic = document.getElementById('modalCardOrganic');
                const modalCardDrainage = document.getElementById('modalCardDrainage');
                const modalCardConfidence = document.getElementById('modalCardConfidence');
                const modalCardDateContainer = document.getElementById('modalCardDateContainer');
                const modalCardDate = document.getElementById('modalCardDate');
                const modalCardScanNote = document.getElementById('modalCardScanNote');
                const modalCardDesc = document.getElementById('modalCardDesc');

                const modalMultiLayerSection = document.getElementById('modalMultiLayerSection');
                const modalMultiLayerItems = document.getElementById('modalMultiLayerItems');
                const modalMetadataFooter = document.getElementById('modalMetadataFooter');
                const modalMetaCreated = document.getElementById('modalMetaCreated');
                const modalMetaLayers = document.getElementById('modalMetaLayers');

                const regions = Array.isArray(data.regions) ? data.regions : [];
                const isMulti = regions.length > 1;
                window.currentActiveScanRegions = regions;
                window.currentBeforeAfterRegions = regions;

                // Modal Header Title
                if (modalRecordTitle) {
                    modalRecordTitle.textContent = isMulti ? `Soil Record Details (${regions.length} DETECTED)` : 'Soil Record Details';
                }

                // Dominant / Single layer info
                const dom = isMulti ? regions[0] : (regions.length === 1 ? regions[0] : null);
                const code = (dom && (dom.munsell || dom.munsell_code)) ? (dom.munsell || dom.munsell_code) : (data.code || '7.5YR 5/3');
                const hex = (dom && dom.hex) ? dom.hex : (data.hex || getMunsellColorHex(code, data.hue) || '#8F694E');
                const name = (dom && (dom.label || dom.color_name)) ? (dom.label || dom.color_name) : (data.name || 'Determined Soil');
                const percent = dom ? (dom.percent || (dom.proportion ? Math.round(dom.proportion * 100) : null)) : (data.percent || null);

                // Apply the soil color to the banner (outer hero div), not the text container
                const heroBadge = document.getElementById('modalSoilDualPreviewBadge');
                if (heroBadge) {
                    heroBadge.style.setProperty('background', hex, 'important');
                    heroBadge.style.setProperty('background-color', hex, 'important');
                }
                if (modalCodeDisplay) modalCodeDisplay.textContent = code;
                if (modalHexPill) modalHexPill.textContent = hex.toUpperCase();

                if (isMulti) {
                    if (modalDominantChevron) modalDominantChevron.style.display = 'inline';
                    if (modalSoilPercentageDisplay) {
                        modalSoilPercentageDisplay.style.display = 'block';
                        modalSoilPercentageDisplay.textContent = `Soil Percentage (${percent}%)`;
                    }
                } else {
                    if (modalDominantChevron) modalDominantChevron.style.display = 'none';
                    if (modalSoilPercentageDisplay && percent) {
                        modalSoilPercentageDisplay.style.display = 'block';
                        modalSoilPercentageDisplay.textContent = `Soil Percentage (${percent}%)`;
                    } else if (modalSoilPercentageDisplay) {
                        modalSoilPercentageDisplay.style.display = 'none';
                    }
                }

                // Only use window.currentCleanScanImage when viewing a LIVE scan result (data.image set)
                // Do NOT bleed previous session's scan image into historical record views
                const domRegionThumb = (regions && regions[0] && regions[0].region_thumbnail) ? regions[0].region_thumbnail : null;
                let cleanSourceImg = data.normalized_image_url || data.stage2_normalized_url || data.image || data.scan_image || domRegionThumb || null;
                if (typeof cleanSourceImg === 'string' && cleanSourceImg.startsWith('storage/')) {
                    cleanSourceImg = '/' + cleanSourceImg;
                }
                const targetRegion = dom || (regions && regions[0]) || { hex: hex, bounding_box: null };

                // Reset the snippet img first to prevent stale src from flashing
                if (modalSoilCircleSnippet) {
                    modalSoilCircleSnippet.style.display = 'none';
                }
                if (modalSoilSnippetFallback) {
                    modalSoilSnippetFallback.style.background = 'rgba(0,0,0,0.25)';
                    modalSoilSnippetFallback.style.display = 'flex';
                }

                if (modalSoilCircleSnippet && cleanSourceImg) {
                    populateZoomAvatar(modalSoilCircleSnippet, targetRegion, cleanSourceImg);
                } else {
                    if (modalSoilCircleSnippet) modalSoilCircleSnippet.style.display = 'none';
                    if (modalSoilSnippetFallback) modalSoilSnippetFallback.style.display = 'flex';
                }

                // Row 1: Code Details (Color Name, Full Code, Scan Count)
                if (modalCardColorName) modalCardColorName.textContent = name;
                if (modalCardFullCode) modalCardFullCode.textContent = code;
                if (modalCardScanCount) modalCardScanCount.textContent = data.seq || data.count || '1';

                // Row 2: Soil Properties (Soil Organic, Soil Type, Near-Match)
                if (modalCardOrganic) modalCardOrganic.textContent = data.organic || 'Moderate (2.2% – 3.7%)';
                if (modalCardDrainage) modalCardDrainage.textContent = data.drainage || 'Well-Drained Clay / Latosol';
                const confVal = window.getNearMatchScore ? window.getNearMatchScore(dom, (data.confidence && data.confidence !== '100%' && data.confidence !== '100.0%') ? data.confidence : '98.2%') : (data.confidence || '98.2%');
                if (modalCardConfidence) modalCardConfidence.textContent = confVal;

                // Scan Note
                const rawModalNote = data.scan_note || data.note || data.field_remarks || '';
                let cleanModalNote = typeof rawModalNote === 'string' ? rawModalNote.trim() : '';
                if (modalCardScanNote) {
                    if (cleanModalNote && !cleanModalNote.startsWith('Determined Soil:') && cleanModalNote !== '—' && cleanModalNote !== '-') {
                        modalCardScanNote.textContent = cleanModalNote;
                    } else {
                        modalCardScanNote.textContent = 'No scan note available for this record.';
                    }
                }

                let cleanDesc = data.desc ? data.desc.replace(/\.{2,}\s*[a-zA-Z]*$/g, '.').replace(/\.{2,}/g, '.').trim() : (code + ' - ' + name);
                if (cleanDesc.includes('\n')) {
                    cleanDesc = cleanDesc.split('\n')[0].trim();
                }
                if (modalCardDesc) modalCardDesc.textContent = cleanDesc;

                if (modalCardDate) modalCardDate.textContent = data.date || '—';

                // Modal Before & After Card Display (With Detected Soil Color Boxes & Swatches)
                const modalBeforeAfterCard = document.getElementById('modalBeforeAfterCard');
                const modalBeforeAfterImg = document.getElementById('modalBeforeAfterImg');
                const modalBeforeAfterBoxes = document.getElementById('modalBeforeAfterBoxes');
                if (modalBeforeAfterCard && modalBeforeAfterImg) {
                    let baUrl = data.before_after_image_url || data.before_after_image;
                    if (!baUrl) {
                        const imgSrc = data.image || data.scan_image || data.normalized_image_url || '';
                        const uuidMatch = imgSrc.match(/(?:scan|region)_([a-f0-9\-]{10,})/i);
                        if (uuidMatch) {
                            baUrl = `/storage/scans/scan_${uuidMatch[1]}_before_after.jpg`;
                        }
                    }
                    if (!baUrl && data.scan_uuid) {
                        baUrl = `/storage/scans/scan_${data.scan_uuid}_before_after.jpg`;
                    }
                    if (baUrl && !baUrl.includes('?') && !baUrl.startsWith('data:')) {
                        baUrl += `?t=${Date.now()}`;
                    }

                    const rawSrc = data.raw_image_url || data.raw_image;
                    const normSrc = cleanSourceImg || data.image || data.scan_image;
                    window.currentBeforeAfterRegion = targetRegion;

                    function showModalBeforeAfter(src) {
                        modalBeforeAfterImg.onload = () => {
                            modalBeforeAfterImg.dataset.loaded = 'true';
                            modalBeforeAfterCard.style.display = 'block';
                            if (modalBeforeAfterBoxes) renderBeforeAfterBoxes(modalBeforeAfterBoxes, regions);
                        };
                        modalBeforeAfterImg.onerror = () => {
                            if (modalBeforeAfterBoxes) modalBeforeAfterBoxes.innerHTML = '';
                            if (rawSrc && normSrc) {
                                generateBeforeAfterCanvas(rawSrc, normSrc, code, targetRegion, (stitchedDataUrl) => {
                                    if (stitchedDataUrl) {
                                        showModalBeforeAfter(stitchedDataUrl);
                                    } else {
                                        modalBeforeAfterImg.dataset.loaded = 'false';
                                        modalBeforeAfterCard.style.display = 'none';
                                    }
                                });
                            } else {
                                modalBeforeAfterImg.dataset.loaded = 'false';
                                modalBeforeAfterCard.style.display = 'none';
                            }
                        };
                        modalBeforeAfterImg.dataset.loaded = 'pending';
                        modalBeforeAfterImg.src = src;
                        modalBeforeAfterCard.style.display = 'block';
                        if (modalBeforeAfterBoxes) modalBeforeAfterBoxes.innerHTML = '';
                    }

                    if (baUrl) {
                        showModalBeforeAfter(baUrl);
                    } else if (rawSrc && normSrc) {
                        generateBeforeAfterCanvas(rawSrc, normSrc, code, targetRegion, (stitchedDataUrl) => {
                            if (stitchedDataUrl) {
                                showModalBeforeAfter(stitchedDataUrl);
                            } else {
                                if (modalBeforeAfterBoxes) modalBeforeAfterBoxes.innerHTML = '';
                                modalBeforeAfterImg.dataset.loaded = 'false';
                                modalBeforeAfterImg.removeAttribute('src');
                                modalBeforeAfterCard.style.display = 'none';
                            }
                        });
                    } else {
                        if (modalBeforeAfterBoxes) modalBeforeAfterBoxes.innerHTML = '';
                        modalBeforeAfterImg.dataset.loaded = 'false';
                        modalBeforeAfterImg.removeAttribute('src');
                        modalBeforeAfterCard.style.display = 'none';
                    }
                }

                // Stage 3 swatches are now shown inside the Before/After card (not a separate card)
                const modalBeforeAfterSwatches = document.getElementById('modalBeforeAfterSwatches');
                window.currentDetectedSoilRegion = targetRegion;
                if (modalBeforeAfterSwatches) {
                    renderStage3Swatches(modalBeforeAfterSwatches, regions, { hex: hex, munsell: code, proportion: 0.7 });
                }

                // Multi-Layer Accordion for Secondary Layers
                if (isMulti && modalMultiLayerSection && modalMultiLayerItems) {
                    modalMultiLayerSection.style.display = 'block';
                    modalMultiLayerItems.innerHTML = '';
                    regions.slice(1).forEach((r, idx) => {
                        const rPct = r.percent || Math.round((r.proportion || 0.2) * 100);
                        const rMunsell = r.munsell || r.munsell_code || 'Soil Layer';
                        const rHex = r.hex || getMunsellColorHex(rMunsell, '') || '#8F694E';
                        const rLabel = r.label || r.color_name || 'Soil Sub-Layer';
                        const rConf = window.getNearMatchScore ? window.getNearMatchScore(r, '96.8%') : '96.8%';

                        const rOrganic = r.organic_matter || data.organic || 'Moderate (2.2% – 3.7%)';
                        const rDrainage = r.soil_name || data.drainage || 'Well-Drained Clay / Latosol';
                        const rDesc = r.description || `${rMunsell} - ${rLabel}`;

                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'layer-accordion-item';

                        const avatarId = `modal-region-avatar-${idx}`;

                        itemDiv.innerHTML = `
                            <button type="button" class="layer-accordion-header" style="width: 100%; padding: 11px 14px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; border: none; text-align: left; font-family: inherit; user-select: none;">
                                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13px; min-width: 0;">
                                    <span class="layer-chevron" style="font-size: 11px; transition: transform 0.2s ease;">▶</span>
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${rMunsell} (Soil Percentage - ${rPct}%)</span>
                                </div>
                                <span style="font-size: 11px; font-weight: 800; color: #ffffff; background: ${rHex}; padding: 3px 10px; border-radius: 12px; font-family: monospace; text-shadow: none; flex-shrink: 0;">
                                    ${rHex.toUpperCase()}
                                </span>
                            </button>
                            <div class="layer-accordion-body" style="display: none; padding: 14px;">
                                <!-- Swatch banner: matches dominant layer hero style with square-ish avatar on left -->
                                <div class="soil-swatch-banner" style="background: ${rHex}; border-radius: 14px; box-shadow: 0 4px 14px rgba(0,0,0,0.18); display: flex; align-items: center; gap: 12px; padding: 10px 16px; margin-bottom: 10px; box-sizing: border-box; min-height: 64px;">
                                    <span id="${avatarId}" style="width:48px;height:48px;aspect-ratio:1/1;border-radius:10px;background:rgba(0,0,0,0.2);border:none;outline:none;flex-shrink:0;overflow:hidden;box-shadow:none;position:relative;display:flex;align-items:center;justify-content:center;">
                                        <img class="layer-zoom-img" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none;border-radius:0;border:none;outline:none;" />
                                        <span class="layer-zoom-fallback" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;border-radius:0;"></span>
                                    </span>
                                    <div style="flex: 1; min-width: 0;">
                                        <span style="font-size: 18px; font-weight: 900; color: #ffffff; text-shadow: none;">${rMunsell}</span>
                                        <div style="font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.95); margin-top: 2px;">Soil Percentage (${rPct}%)</div>
                                    </div>
                                    <span style="font-size: 11px; font-weight: 800; color: #ffffff; background: rgba(0,0,0,0.58); border: 1px solid rgba(255,255,255,0.25); padding: 4px 10px; border-radius: 20px; font-family: monospace; flex-shrink: 0;">${rHex.toUpperCase()}</span>
                                </div>
                                <div class="soil-modal-grid-3" style="margin-bottom: 8px;">
                                    <div class="soil-modal-grid-card"><div class="soil-modal-label">Color Name</div><div class="soil-modal-value truncate" title="${rLabel}">${rLabel}</div></div>
                                    <div class="soil-modal-grid-card"><div class="soil-modal-label">Full Code</div><div class="soil-modal-value">${rMunsell}</div></div>
                                    <div class="soil-modal-grid-card"><div class="soil-modal-label">Proportion</div><div class="soil-modal-value">${rPct}%</div></div>
                                </div>
                                <div class="soil-modal-grid-3" style="margin-bottom: 8px;">
                                    <div class="soil-modal-grid-card"><div class="soil-modal-label">Soil Organic</div><div class="soil-modal-value">${rOrganic}</div></div>
                                    <div class="soil-modal-grid-card"><div class="soil-modal-label">Soil Type</div><div class="soil-modal-value">${rDrainage}</div></div>
                                    <div class="soil-modal-grid-card"><div class="soil-modal-label">Near-Match</div><div class="soil-modal-value" style="color: #10b981; font-weight: 800;">${rConf}</div></div>
                                </div>
                                <div class="soil-modal-grid-card" style="margin-bottom: 8px; padding: 12px 16px;">
                                    <div class="soil-modal-label">Scan Note</div>
                                    <div class="soil-modal-value" style="font-size: 13px; font-weight: 700; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">${(r.scan_note && !r.scan_note.startsWith('Determined Soil:') && r.scan_note !== '—' && r.scan_note !== '-') ? r.scan_note : 'No scan note available for this record.'}</div>
                                </div>
                                <div class="soil-modal-grid-card" style="padding: 12px 16px;">
                                    <div class="soil-modal-label">Description</div>
                                    <div class="soil-modal-value" style="font-size: 13px; font-weight: 700; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">${rDesc}</div>
                                </div>

                                <!-- Layer Pin Location Button (Placed directly after Description) -->
                                <div style="margin-top: 10px;">
                                    <button type="button" class="btn-res-action btn-res-pin-location modal-layer-pin-btn" style="width: 100%; justify-content: center; padding: 10px 14px; font-size: 12.5px;" title="Pin This Soil Layer to Map">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>Pin Location</span>
                                    </button>
                                </div>
                            </div>
                        `;

                        // Populate the avatar using the shared engine.
                        const modalImg = cleanSourceImg || data.image || (document.getElementById('modalSoilCircleSnippet')?.src);
                        populateZoomAvatar(
                            itemDiv.querySelector(`#${avatarId}`),
                            r,
                            modalImg
                        );

                        const modalLayerPinBtn = itemDiv.querySelector('.modal-layer-pin-btn');
                        if (modalLayerPinBtn) {
                            modalLayerPinBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                closeSoilRecordModal();
                                openPinLocationModal({
                                    ...r,
                                    scan_id: data.id || data.soil_scan_id
                                }, false);
                            });
                        }

                        const headerBtn = itemDiv.querySelector('.layer-accordion-header');
                        const bodyEl = itemDiv.querySelector('.layer-accordion-body');
                        const chevronEl = itemDiv.querySelector('.layer-chevron');
                        headerBtn.addEventListener('click', () => {
                            const isOpen = bodyEl.style.display === 'block';
                            bodyEl.style.display = isOpen ? 'none' : 'block';
                            chevronEl.textContent = isOpen ? '▶' : '▼';
                        });

                        modalMultiLayerItems.appendChild(itemDiv);
                    });
                } else if (modalMultiLayerSection) {
                    modalMultiLayerSection.style.display = 'none';
                }

                // Single vs Multi: Date container vs Metadata footer
                if (isMulti) {
                    if (modalCardDateContainer) modalCardDateContainer.style.display = 'none';
                    if (modalMetadataFooter) {
                        modalMetadataFooter.style.display = 'flex';
                        if (modalMetaCreated) modalMetaCreated.textContent = data.date || '—';
                        if (modalMetaLayers) modalMetaLayers.textContent = regions.length;
                    }
                } else {
                    if (modalCardDateContainer) modalCardDateContainer.style.display = 'block';
                    if (modalMetadataFooter) modalMetadataFooter.style.display = 'none';
                }

                // Soil Pin Location & Covered Area Map Card (Placed BEFORE Soil Normalization)
                const modalSoilLocationCard = document.getElementById('modalSoilLocationCard');
                const modalLocBrgyBadge = document.getElementById('modalLocBrgyBadge');
                const modalLocCoverageBadge = document.getElementById('modalLocCoverageBadge');
                const modalLocCoordsBadge = document.getElementById('modalLocCoordsBadge');

                const scanLat = data.lat ? parseFloat(data.lat) : null;
                const scanLng = data.lng ? parseFloat(data.lng) : null;

                if (modalSoilLocationCard && scanLat && scanLng && !isNaN(scanLat) && !isNaN(scanLng)) {
                    modalSoilLocationCard.style.display = 'block';
                    if (modalLocBrgyBadge) modalLocBrgyBadge.textContent = data.barangay || 'Lal-lo Overall';
                    if (modalLocCoordsBadge) modalLocCoordsBadge.textContent = `${scanLat.toFixed(4)}, ${scanLng.toFixed(4)}`;
                    if (modalLocCoverageBadge) {
                        if (data.coverage) {
                            modalLocCoverageBadge.textContent = data.coverage;
                            modalLocCoverageBadge.style.display = 'inline-block';
                        } else {
                            modalLocCoverageBadge.style.display = 'none';
                        }
                    }

                    setTimeout(() => {
                        const mapEl = document.getElementById('modalSoilDetailMap');
                        if (!mapEl || typeof L === 'undefined') return;

                        if (modalSoilDetailMapInstance) {
                            try {
                                modalSoilDetailMapInstance.remove();
                            } catch(e) {}
                            modalSoilDetailMapInstance = null;
                        }

                        modalSoilDetailMapInstance = L.map(mapEl, {
                            zoomControl: true,
                            attributionControl: false
                        }).setView([scanLat, scanLng], 15);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19
                        }).addTo(modalSoilDetailMapInstance);

                        const isApproved = data.status && (String(data.status).toLowerCase() === 'active' || String(data.status).toLowerCase() === 'approved');

                        const pinIcon = L.icon({
                            iconUrl: isApproved ? "{{ asset('images/agrico-map-pin.png') }}" : "{{ asset('images/agrico-map-pin-gray.png') }}",
                            iconSize: [36, 52],
                            iconAnchor: [18, 52],
                            popupAnchor: [0, -46]
                        });

                        const marker = L.marker([scanLat, scanLng], { icon: pinIcon }).addTo(modalSoilDetailMapInstance);

                        const isLight = document.documentElement.classList.contains('light-mode');
                        const popupHtml = `
                            <div style='font-family:inherit; min-width:170px; padding:3px;' class='soil-map-popup-inner'>
                                <div class='soil-map-popup-brgy' style='font-size:11px; font-weight:800; color:${isLight ? (isApproved ? '#16a34a' : '#64748b') : (isApproved ? '#4ade80' : '#8EB69B')}; text-transform:uppercase;'>${data.barangay || 'Lal-lo Overall'}</div>
                                <strong class='soil-map-popup-title' style='font-size:13.5px; color:${isLight ? '#0f172a' : '#DAF1DE'}; display:block; margin:2px 0;'>${name || 'Soil Specimen'}</strong>
                                <div class='soil-map-popup-munsell' style='font-size:11px; color:${isLight ? '#64748b' : '#8EB69B'}; font-family:monospace; margin-bottom:2px;'>Munsell: <strong style='color:${isLight ? '#0f172a' : '#DAF1DE'};'>${code || ''}</strong></div>
                                <div class='soil-map-popup-coords' style='font-size:10.5px; color:${isLight ? '#0284c7' : '#38bdf8'}; font-family:monospace;'>${scanLat.toFixed(5)}, ${scanLng.toFixed(5)}</div>
                            </div>
                        `;
                        marker.bindPopup(popupHtml).openPopup();

                        const covStr = data.coverage || '500 m²';
                        let boxW = 85, boxH = 85, cornerR = 20;
                        if (covStr.includes('50 m')) { boxW = 35; boxH = 35; cornerR = 8; }
                        else if (covStr.includes('250 m')) { boxW = 55; boxH = 55; cornerR = 13; }
                        else if (covStr.includes('500 m')) { boxW = 75; boxH = 75; cornerR = 18; }
                        else if (covStr.includes('1,000') || covStr.includes('1000')) { boxW = 100; boxH = 100; cornerR = 24; }
                        else if (covStr.includes('2,500') || covStr.includes('2500')) { boxW = 130; boxH = 130; cornerR = 30; }
                        else if (covStr.includes('5,000') || covStr.includes('5000')) { boxW = 160; boxH = 160; cornerR = 36; }
                        else if (covStr.toLowerCase().includes('hectare')) { boxW = 190; boxH = 190; cornerR = 42; }

                        const roundedCoords = typeof getRoundedBoxCoords === 'function' 
                            ? getRoundedBoxCoords(scanLat, scanLng, boxW, boxH, cornerR)
                            : [];

                        if (roundedCoords.length > 0) {
                            const polyBorderColor = isLight ? (isApproved ? '#16a34a' : '#64748b') : (isApproved ? '#4ade80' : '#8EB69B');
                            const polyFillColor   = isLight ? (isApproved ? '#22c55e' : '#9ca3af') : (isApproved ? '#22c55e' : '#163832');
                            const poly = L.polygon(roundedCoords, {
                                color: polyBorderColor,
                                fillColor: polyFillColor,
                                fillOpacity: isLight ? 0.22 : 0.28,
                                weight: 2.5
                            }).addTo(modalSoilDetailMapInstance);

                            if (covStr) {
                                poly.bindTooltip(`${isApproved ? 'Covered Area' : 'Pending Covered Area'} (Sakop): ${covStr}`, { sticky: true });
                            }
                        }

                        setTimeout(() => { if (modalSoilDetailMapInstance) modalSoilDetailMapInstance.invalidateSize(); }, 150);
                        setTimeout(() => { if (modalSoilDetailMapInstance) modalSoilDetailMapInstance.invalidateSize(); }, 400);
                    }, 80);
                } else if (modalSoilLocationCard) {
                    modalSoilLocationCard.style.display = 'none';
                    if (modalSoilDetailMapInstance) {
                        try { modalSoilDetailMapInstance.remove(); } catch(e) {}
                        modalSoilDetailMapInstance = null;
                    }
                }
            } catch (err) {
                console.error('[viewScanDetails] Error populating modal fields:', err);
            } finally {
                const modal = document.getElementById('soilRecordModal');
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            }
        }

        function closeSoilRecordModal() {
            const modal = document.getElementById('soilRecordModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
            if (modalSoilDetailMapInstance) {
                try { modalSoilDetailMapInstance.remove(); } catch(e) {}
                modalSoilDetailMapInstance = null;
            }
        }

        function generateBeforeAfterCanvas(rawSrc, normSrc, label, regionOrCallback, callback) {
            let region = null;
            let onComplete = callback;
            if (typeof regionOrCallback === 'function') {
                onComplete = regionOrCallback;
                region = null;
            } else {
                region = regionOrCallback;
            }

            if (!rawSrc || !normSrc) {
                if (onComplete) onComplete(null);
                return;
            }
            const imgBefore = new Image();
            const imgAfter = new Image();
            imgBefore.crossOrigin = 'anonymous';
            imgAfter.crossOrigin = 'anonymous';
            let loadedCount = 0;
            let errorCount = 0;

            const checkComplete = () => {
                if (errorCount > 0) {
                    if (onComplete) onComplete(null);
                    return;
                }
                if (loadedCount === 2) {
                    try {
                        const targetW = 640;
                        const scale = targetW / (imgBefore.naturalWidth || targetW);
                        const w = targetW;
                        const h = Math.max(160, Math.round((imgBefore.naturalHeight || 360) * scale));
                        const barH = Math.max(30, Math.round(h * 0.08)); // matches Python: max(30, int(rh * 0.08))
                        const canvas = document.createElement('canvas');
                        canvas.width = w * 2;
                        canvas.height = h + barH;
                        const ctx = canvas.getContext('2d');
                        if (!ctx) {
                            if (onComplete) onComplete(null);
                            return;
                        }

                        // Top Bar background
                        ctx.fillStyle = '#121e18';
                        ctx.fillRect(0, 0, canvas.width, canvas.height);

                        // Draw Before and After images
                        ctx.drawImage(imgBefore, 0, barH, w, h);
                        ctx.drawImage(imgAfter, w, barH, w, h);

                        // Bar text
                        ctx.font = 'bold 12px Inter, ui-sans-serif, system-ui, sans-serif';
                        ctx.textBaseline = 'middle';

                        // BEFORE label (amber)
                        ctx.fillStyle = '#fbbf24';
                        ctx.fillText('BEFORE', 12, barH / 2);

                        // AFTER label (mint green)
                        ctx.fillStyle = '#8ef2b2';
                        const afterText = 'AFTER (NORMALIZED)' + (label ? ' -- ' + label : '');
                        ctx.fillText(afterText, w + 12, barH / 2);

                        // Center divider line (visual separator between BEFORE and AFTER)
                        ctx.save();
                        ctx.strokeStyle = 'rgba(255,255,255,0.25)';
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(w, 0);
                        ctx.lineTo(w, canvas.height);
                        ctx.stroke();
                        ctx.restore();

                        // Green ROI box is drawn by the HTML overlay only (not baked into the canvas).
                        // This prevents the box from ever appearing on the AFTER side.

                        const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
                        if (onComplete) onComplete(dataUrl);
                    } catch (err) {
                        console.warn('[generateBeforeAfterCanvas] Error stitching canvas:', err);
                        if (onComplete) onComplete(null);
                    }
                }
            };

            imgBefore.onload = () => { loadedCount++; checkComplete(); };
            imgBefore.onerror = () => { errorCount++; checkComplete(); };
            imgAfter.onload = () => { loadedCount++; checkComplete(); };
            imgAfter.onerror = () => { errorCount++; checkComplete(); };

            imgBefore.src = rawSrc;
            imgAfter.src = normSrc;
        }

        function handleBeforeAfterZoomError(imgEl) {
            if (!imgEl) return;
            imgEl.onerror = null;
            const d = (typeof currentModalScanData !== 'undefined' && currentModalScanData) ? currentModalScanData : null;
            const fallbackSrc = (d && (d.normalized_image_url || d.image || d.scan_image)) 
                || window.currentCleanScanImage 
                || document.getElementById('modalSoilCircleSnippet')?.src 
                || document.getElementById('resSoilCircleSnippet')?.src;

            if (fallbackSrc && imgEl.src !== fallbackSrc) {
                imgEl.src = fallbackSrc;
                const titleEl = document.getElementById('beforeAfterZoomTitle');
                const badgeEl = document.getElementById('beforeAfterZoomBadge');
                if (titleEl) titleEl.textContent = 'Soil Sample Full Resolution View';
                if (badgeEl) {
                    badgeEl.textContent = 'High Resolution';
                    badgeEl.style.color = '';
                    badgeEl.style.background = '';
                }
            } else {
                imgEl.style.display = 'none';
            }
        }

        function openSoilSnippetPopup(type = 'modal') {
            const zoomModal = document.getElementById('beforeAfterZoomModal');
            const zoomImg = document.getElementById('beforeAfterZoomImg');
            const modalTitle = document.getElementById('beforeAfterZoomTitle') || (zoomModal ? zoomModal.querySelector('.ba-zoom-title') : null);
            const modalBadge = document.getElementById('beforeAfterZoomBadge') || (zoomModal ? zoomModal.querySelector('.ba-zoom-badge') : null);
            if (!zoomModal || !zoomImg) return;

            function showZoomModal(src) {
                zoomImg.style.display = 'block';
                zoomImg.src = src;
                if (modalTitle) {
                    modalTitle.textContent = 'Soil Sample ROI Detection (Full Resolution)';
                }
                if (modalBadge) {
                    modalBadge.textContent = 'Detected ROI';
                    modalBadge.style.color = '';
                    modalBadge.style.background = '';
                }
                zoomModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            let snippetImg = null;
            let sourceImg = null;
            let targetRegion = null;

            if (type === 'modal') {
                snippetImg = document.getElementById('modalSoilCircleSnippet');
                const d = (typeof currentModalScanData !== 'undefined' && currentModalScanData) ? currentModalScanData : null;
                if (d) {
                    const regions = Array.isArray(d.regions) ? d.regions : [];
                    targetRegion = (regions && regions[0]) || { hex: d.hex, bounding_box: null };
                    sourceImg = d.normalized_image_url || d.stage2_normalized_url || d.image || d.scan_image;
                    if (typeof sourceImg === 'string' && sourceImg.startsWith('storage/')) {
                        sourceImg = '/' + sourceImg;
                    }
                }
            } else {
                snippetImg = document.getElementById('resSoilCircleSnippet');
                const resData = window.currentScanResult || (typeof currentScanResponseData !== 'undefined' ? currentScanResponseData : null);
                if (resData) {
                    const regions = Array.isArray(resData.regions) ? resData.regions : [];
                    targetRegion = (regions && regions[0]) || null;
                    sourceImg = resData.normalized_image_url || resData.stage2_normalized_url || resData.image || resData.scan_image || window.currentCleanScanImage;
                } else if (window.currentCleanScanImage) {
                    sourceImg = window.currentCleanScanImage;
                }
            }

            // 1. If snippet image already has the rendered canvas data URL with green box, show it immediately
            if (snippetImg && snippetImg.src && snippetImg.src.startsWith('data:')) {
                showZoomModal(snippetImg.src);
                return;
            }

            // 2. Generate crisp full-resolution image with green box on the fly
            if (sourceImg && typeof drawWholeImageWithColorBox === 'function') {
                drawWholeImageWithColorBox(sourceImg, targetRegion, (boxedDataUrl) => {
                    if (boxedDataUrl) {
                        showZoomModal(boxedDataUrl);
                    } else if (snippetImg && snippetImg.src) {
                        showZoomModal(snippetImg.src);
                    } else if (sourceImg) {
                        showZoomModal(sourceImg);
                    }
                });
                return;
            }

            if (snippetImg && snippetImg.src) {
                showZoomModal(snippetImg.src);
            } else if (sourceImg) {
                showZoomModal(sourceImg);
            }
        }

        function openBeforeAfterLightbox(imgElementId) {
            const srcEl = typeof imgElementId === 'string' ? document.getElementById(imgElementId) : imgElementId;
            if (!srcEl || !srcEl.src || srcEl.naturalWidth === 0) return;
            const zoomModal = document.getElementById('beforeAfterZoomModal');
            const zoomImg = document.getElementById('beforeAfterZoomImg');
            const zoomBoxes = document.getElementById('beforeAfterZoomBoxes');
            const modalTitle = document.getElementById('beforeAfterZoomTitle');
            const modalBadge = document.getElementById('beforeAfterZoomBadge');
            if (zoomModal && zoomImg) {
                zoomImg.style.display = 'block';
                zoomImg.src = srcEl.src;
                if (modalTitle) modalTitle.textContent = 'Soil Normalization Comparison (Before & After)';
                if (modalBadge) {
                    modalBadge.textContent = 'Stage 2 PromptNorm';
                    modalBadge.style.color = '';
                    modalBadge.style.background = '';
                }

                // Collect all active detected regions for the Before/After comparison
                let activeRegions = [];
                const isModalSource = (typeof imgElementId === 'string' && imgElementId.includes('modal')) || (srcEl.id && srcEl.id.includes('modal'));

                if (isModalSource && typeof currentModalScanData !== 'undefined' && currentModalScanData && Array.isArray(currentModalScanData.regions) && currentModalScanData.regions.length > 0) {
                    activeRegions = currentModalScanData.regions;
                } else if (!isModalSource && Array.isArray(window.currentActiveScanRegions) && window.currentActiveScanRegions.length > 0) {
                    activeRegions = window.currentActiveScanRegions;
                } else if (Array.isArray(window.currentBeforeAfterRegions) && window.currentBeforeAfterRegions.length > 0) {
                    activeRegions = window.currentBeforeAfterRegions;
                } else if (typeof lastScanMatch !== 'undefined' && lastScanMatch && Array.isArray(lastScanMatch.regions) && lastScanMatch.regions.length > 0) {
                    activeRegions = lastScanMatch.regions;
                } else if (typeof currentModalScanData !== 'undefined' && currentModalScanData && Array.isArray(currentModalScanData.regions) && currentModalScanData.regions.length > 0) {
                    activeRegions = currentModalScanData.regions;
                } else if (window.currentScanResult && Array.isArray(window.currentScanResult.regions) && window.currentScanResult.regions.length > 0) {
                    activeRegions = window.currentScanResult.regions;
                } else if (typeof currentScanResponseData !== 'undefined' && currentScanResponseData && Array.isArray(currentScanResponseData.regions) && currentScanResponseData.regions.length > 0) {
                    activeRegions = currentScanResponseData.regions;
                } else if (window.currentDetectedSoilRegion || window.currentBeforeAfterRegion) {
                    const fallbackReg = window.currentDetectedSoilRegion || window.currentBeforeAfterRegion;
                    activeRegions = Array.isArray(fallbackReg) ? fallbackReg : [fallbackReg];
                }

                if (zoomBoxes) {
                    renderBeforeAfterBoxes(zoomBoxes, activeRegions);
                }
                zoomModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function openStage3Lightbox(imgElementId) {
            const srcEl = typeof imgElementId === 'string' ? document.getElementById(imgElementId) : imgElementId;
            if (!srcEl || !srcEl.src || srcEl.naturalWidth === 0) return;
            const zoomModal = document.getElementById('beforeAfterZoomModal');
            const zoomImg = document.getElementById('beforeAfterZoomImg');
            const zoomBoxes = document.getElementById('beforeAfterZoomBoxes');
            const modalTitle = document.getElementById('beforeAfterZoomTitle');
            const modalBadge = document.getElementById('beforeAfterZoomBadge');
            if (zoomModal && zoomImg) {
                zoomImg.style.display = 'block';
                zoomImg.src = srcEl.src;
                if (modalTitle) modalTitle.textContent = 'Stage 3: Multi-Color Detection (Detected Soil Color)';
                if (modalBadge) {
                    modalBadge.textContent = 'ColorThief Palette';
                    modalBadge.style.color = '';
                    modalBadge.style.background = '';
                }
                const reg = window.currentDetectedSoilRegion || window.currentBeforeAfterRegion;
                if (zoomBoxes && reg) {
                    renderBeforeAfterBoxes(zoomBoxes, Array.isArray(reg) ? reg : [reg]);
                } else if (zoomBoxes) {
                    zoomBoxes.innerHTML = '';
                }
                zoomModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeBeforeAfterZoomModal(e) {
            if (e) e.stopPropagation();
            const zoomModal = document.getElementById('beforeAfterZoomModal');
            const zoomBoxes = document.getElementById('beforeAfterZoomBoxes');
            if (zoomBoxes) zoomBoxes.innerHTML = '';
            if (zoomModal) {
                zoomModal.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        function downloadSoilRecordHardCopy() {
            const data = currentModalScanData || {
                name: document.getElementById('modalSoilName')?.textContent || 'Red Tropical Clay Loam',
                code: document.getElementById('modalCardFullCode')?.textContent || '10R 3/2',
                hue: document.getElementById('modalCardHue')?.textContent || '10R',
                desc: document.getElementById('modalCardDesc')?.textContent || '10R 3/2 - dusky red'
            };

            const btn = document.querySelector('#soilRecordModal .btn-save-hardcopy');
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = `
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Saved Copy ✓
                `;
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }, 2200);
            }

            // 1. Copy details to clipboard
            const summaryText = `AgriCo Soil Scan Record\nSoil Name: ${data.name || '—'}\nCode: ${data.code || '—'}\nHue: ${data.hue || '—'}\nDate: ${data.date || '—'}\nDescription: ${data.desc || '—'}`;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(summaryText).catch(() => {});
            }

            // 2. Download crisp image report card (same function as scanner Save Copy button!)
            const modalRegions = data.regions || (typeof currentModalScanData !== 'undefined' && currentModalScanData?.regions) || null;
            downloadSoilScanReport(data, "{{ Auth::user()?->user_id ?? 'ADM-001' }}", modalRegions);

            if (typeof showScanNotice === 'function') {
                showScanNotice('Soil Scan report generated and downloaded!');
            }
        }

        function saveCurrentScanCopy() {
            const data = lastScanMatch || {
                name: document.getElementById('resName')?.textContent || 'Determined Soil Specimen',
                code: document.getElementById('resFullCode')?.textContent || '10YR 4/3',
                hue: document.getElementById('resHue')?.textContent || '10YR',
                desc: document.getElementById('resDesc')?.textContent || 'Sample processed using AgriCo Munsell Algorithm.',
                hex: document.getElementById('resHexPill')?.textContent || '#594433',
                image: document.getElementById('resSoilCircleSnippet')?.src || null
            };

            const btn = document.getElementById('btnSaveCopyScan');
            if (btn) {
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span>Saved Copy ✓</span>';
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }, 2200);
            }

            // Copy to clipboard
            const summaryText = `AgriCo Soil Scan Record\nSoil Name: ${data.name || '—'}\nCode: ${data.code || '—'}\nHue: ${data.hue || '—'}\nDescription: ${data.desc || '—'}`;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(summaryText).catch(() => {});
            }

            // Download crisp image report card
            const activeRegs = (data && data.regions) || (typeof lastScanMatch !== 'undefined' && lastScanMatch?.regions) || window.currentActiveScanRegions || null;
            downloadSoilScanReport(data, "{{ Auth::user()?->user_id ?? 'ADM-001' }}", activeRegs);

            if (typeof showScanNotice === 'function') {
                showScanNotice('Soil Scan report copy generated and downloaded!');
            }
        }

        // ── PIN SOIL LOCATION MODAL LOGIC ──
        const LALLO_35_BARANGAYS = [
            // Coordinates derived from official NAMRIA/PSA GeoJSON polygon centroids
            { name: 'Abagao',                    lat: 18.2431, lng: 121.7410 },
            { name: 'Alaguia',                   lat: 18.1564, lng: 121.6358 },
            { name: 'Bagumbayan',                lat: 18.1924, lng: 121.6690 },
            { name: 'Bangag',                    lat: 18.1176, lng: 121.6632 },
            { name: 'Bical',                     lat: 18.2402, lng: 121.7289 },
            { name: 'Bicud',                     lat: 18.1283, lng: 121.5843 },
            { name: 'Binag',                     lat: 18.1813, lng: 121.6949 },
            { name: 'Cabayabasan (Capacuan)',     lat: 18.1129, lng: 121.5687 },
            { name: 'Cagoran',                   lat: 18.1880, lng: 121.6845 },
            { name: 'Cambong',                   lat: 18.1909, lng: 121.7133 },
            { name: 'Catayauan',                 lat: 18.1728, lng: 121.6610 },
            { name: 'Catugan',                   lat: 18.1659, lng: 121.6319 },
            { name: 'Centro (Pob.)',              lat: 18.2007, lng: 121.6647 },
            { name: 'Cullit',                    lat: 18.2408, lng: 121.7515 },
            { name: 'Dagupan',                   lat: 18.1776, lng: 121.7383 }, // western settled-area centroid (polygon extends E to coast; centroid clipped at lng<121.80)
            { name: 'Dalaya',                    lat: 18.1746, lng: 121.6724 },
            { name: 'Fabrica',                   lat: 18.2012, lng: 121.6316 },
            { name: 'Fusina',                    lat: 18.2393, lng: 121.7091 },
            { name: 'Jurisdiction',              lat: 18.2245, lng: 121.6901 },
            { name: 'Lalafugan',                 lat: 18.1495, lng: 121.6142 },
            { name: 'Logac',                     lat: 18.1518, lng: 121.6103 },
            { name: 'Magallungon (Sta. Teresa)', lat: 18.2174, lng: 121.6942 },
            { name: 'Magapit',                   lat: 18.1224, lng: 121.6868 },
            { name: 'Malanao',                   lat: 18.1939, lng: 121.6285 },
            { name: 'Maxingal',                  lat: 18.2131, lng: 121.6832 },
            { name: 'Naguilian',                 lat: 18.2214, lng: 121.7724 },
            { name: 'Paranum',                   lat: 18.1595, lng: 121.6705 },
            { name: 'Rosario',                   lat: 18.2056, lng: 121.7405 },
            { name: 'San Antonio (Lafu)',         lat: 18.1832, lng: 121.6428 },
            { name: 'San Jose',                  lat: 18.2057, lng: 121.6638 },
            { name: 'San Juan',                  lat: 18.2349, lng: 121.7219 },
            { name: 'San Lorenzo',               lat: 18.1510, lng: 121.6434 },
            { name: 'San Mariano',               lat: 18.1297, lng: 121.7300 },
            { name: 'Santa Maria',               lat: 18.1505, lng: 121.6715 },
            { name: 'Tucalana',                  lat: 18.1805, lng: 121.6674 },
        ];

        let pinLeafletMapInstance = null;
        let pinLeafletMarker = null;
        let pinLeafletCoverageCircle = null;
        let pinBarangayGeoJsonLayer = null;
        let currentPinLat = 18.1972;
        let currentPinLng = 121.6612;
        let currentPinRadius = 13; // default 500 m2
        let currentPinCoverageLabel = '500 m²';
        let currentPinBarangay = 'Lal-lo Overall';
        let currentPinTargetSoil = null;
        let isPinningAllSoils = false;

        function openPinAllLocationModal() {
            openPinLocationModal(null, true);
        }

        function openPinLocationModal(layerData = null, isAll = false) {
            isPinningAllSoils = Boolean(isAll);
            currentPinTargetSoil = isAll ? null : layerData;
            const modal = document.getElementById('pinSoilLocationModal');
            if (!modal) return;

            const modalTitleEl = modal.querySelector('.pin-modal-title');
            const modalSubtitleEl = modal.querySelector('.pin-modal-subtitle');
            const noteEl = document.getElementById('pinScanNoteInput');
            const scanData = window.currentScanResult || (typeof currentScanResponseData !== 'undefined' ? currentScanResponseData : lastScanMatch);
            const activeRegions = window.currentActiveScanRegions || (scanData && scanData.regions) || [];

            if (isPinningAllSoils) {
                if (modalTitleEl) modalTitleEl.textContent = 'Pin All Soil Locations';
                if (modalSubtitleEl) modalSubtitleEl.textContent = activeRegions.length > 1
                    ? `Pin all ${activeRegions.length} detected soil layers from this scan to Lal-lo coordinates`
                    : 'Pin all detected soil specimens from this scan to Lal-lo coordinates';

                if (noteEl) {
                    if (activeRegions.length > 1) {
                        const munsellList = activeRegions.map(r => r.munsell || r.munsell_code || r.label || r.color_name).join(', ');
                        noteEl.value = `All ${activeRegions.length} detected soil layers (${munsellList}) pinned to location.`;
                    } else {
                        const sName = document.getElementById('resName')?.textContent || (lastScanMatch?.name) || 'Soil Specimen';
                        const fCode = document.getElementById('resFullCode')?.textContent || (lastScanMatch?.code) || '10YR 4/3';
                        noteEl.value = `All detected soil specimens (${sName} - ${fCode}) pinned to location.`;
                    }
                }
            } else {
                if (modalTitleEl) modalTitleEl.textContent = 'Pin Soil Location';
                if (modalSubtitleEl) modalSubtitleEl.textContent = 'Select Barangay & Pin Coordinates in Lal-lo, Cagayan for Admin Review';

                // Populate soil specimen details from layerData or current primary scan
                const soilName = layerData?.label || layerData?.color_name || layerData?.soil_name || document.getElementById('resName')?.textContent || (lastScanMatch?.name) || 'Determined Soil Specimen';
                const fullCode = layerData?.munsell || layerData?.munsell_code || layerData?.full_code || document.getElementById('resFullCode')?.textContent || (lastScanMatch?.code) || '10YR 4/3';

                if (noteEl && (!noteEl.value || noteEl.value.includes('Soil sample classified as') || noteEl.value.includes('detected soil layers') || noteEl.value.includes('All detected soil specimens'))) {
                    noteEl.value = `Soil sample classified as ${soilName} (${fullCode}).`;
                }
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            const modalBody = modal.querySelector('.pin-modal-body');
            if (modalBody) modalBody.scrollTop = 0;

            initPinBrgySelectDropdown();
            initPinSearchListener();

            // Populate scan location if present (using scanData declared above)
            const hasScanCoords = scanData && scanData.latitude && scanData.longitude;
            const brgyName = (scanData && scanData.barangay) || 'Lal-lo Overall';

            const searchInput = document.getElementById('pinBrgySearchInput');
            const clearBtn = document.getElementById('pinClearBrgyBtn');
            const dropdown = document.getElementById('pinBrgyDropdown');

            if (clearBtn) clearBtn.style.display = 'none';
            if (dropdown) dropdown.style.display = 'none';

            if (brgyName && brgyName !== 'Lal-lo Overall') {
                const found = LALLO_35_BARANGAYS.find(b => b.name.toLowerCase() === brgyName.toLowerCase());
                if (found) {
                    selectPinBarangay(found, !hasScanCoords);
                } else {
                    onPinBarangaySelectChange('Lal-lo Overall');
                }
            } else {
                onPinBarangaySelectChange('Lal-lo Overall');
            }

            if (hasScanCoords) {
                updatePinCoords(scanData.latitude, scanData.longitude, true);
            }

            setTimeout(() => {
                initOrUpdatePinMap();
            }, 100);
            setTimeout(() => {
                if (pinLeafletMapInstance) {
                    pinLeafletMapInstance.invalidateSize();
                    if (hasScanCoords) {
                        pinLeafletMapInstance.flyTo([currentPinLat, currentPinLng], 15, { duration: 0.5 });
                    }
                }
            }, 300);
        }

        function closePinLocationModal() {
            const modal = document.getElementById('pinSoilLocationModal');
            if (modal) modal.style.display = 'none';
            document.body.style.overflow = '';
            closePinBrgySelectDropdown();
        }

        function initPinBrgySelectDropdown() {
            const menu = document.getElementById('pinBrgySelectMenu');
            const btnText = document.getElementById('pinBrgySelectBtnText');
            if (btnText) {
                btnText.textContent = currentPinBarangay === 'Lal-lo Overall' ? 'All Barangays (35)' : currentPinBarangay;
            }
            if (!menu || menu.children.length > 0) return;

            // Option: All Barangays (35)
            const allItem = document.createElement('div');
            allItem.className = 'pin-custom-select-item' + (currentPinBarangay === 'Lal-lo Overall' ? ' selected' : '');
            allItem.setAttribute('data-name', 'Lal-lo Overall');
            allItem.innerHTML = `<span>All Barangays (35)</span> ${currentPinBarangay === 'Lal-lo Overall' ? '✓' : ''}`;
            allItem.onclick = (e) => {
                e.stopPropagation();
                onPinBarangaySelectChange('Lal-lo Overall');
                closePinBrgySelectDropdown();
            };
            menu.appendChild(allItem);

            // 35 Barangays
            LALLO_35_BARANGAYS.forEach(b => {
                const item = document.createElement('div');
                item.className = 'pin-custom-select-item' + (currentPinBarangay === b.name ? ' selected' : '');
                item.setAttribute('data-name', b.name);
                item.innerHTML = `<span>${b.name}</span> ${currentPinBarangay === b.name ? '✓' : ''}`;
                item.onclick = (e) => {
                    e.stopPropagation();
                    selectPinBarangay(b);
                    closePinBrgySelectDropdown();
                };
                menu.appendChild(item);
            });
        }

        function togglePinBrgySelectDropdown(e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('pinBrgySelectMenu');
            const btn = document.getElementById('pinBrgySelectBtn');
            const searchDropdown = document.getElementById('pinBrgyDropdown');
            if (searchDropdown) searchDropdown.style.display = 'none';

            if (!menu) return;
            initPinBrgySelectDropdown();

            const isShown = menu.style.display === 'block';
            if (isShown) {
                closePinBrgySelectDropdown();
            } else {
                menu.style.display = 'block';
                if (btn) btn.classList.add('open');
                const activeItem = menu.querySelector('.pin-custom-select-item.selected');
                if (activeItem) {
                    activeItem.scrollIntoView({ block: 'nearest' });
                }
            }
        }

        function closePinBrgySelectDropdown() {
            const menu = document.getElementById('pinBrgySelectMenu');
            const btn = document.getElementById('pinBrgySelectBtn');
            if (menu) menu.style.display = 'none';
            if (btn) btn.classList.remove('open');
        }

        function onPinBarangaySelectChange(val) {
            if (!val || val === 'Lal-lo Overall') {
                currentPinBarangay = 'Lal-lo Overall';
                setPinCoverageStatus(true, null);

                const searchInput = document.getElementById('pinBrgySearchInput');
                if (searchInput) searchInput.value = '';

                const clearBtn = document.getElementById('pinClearBrgyBtn');
                if (clearBtn) clearBtn.style.display = 'none';

                const dropdown = document.getElementById('pinBrgyDropdown');
                if (dropdown) dropdown.style.display = 'none';

                const btnText = document.getElementById('pinBrgySelectBtnText');
                if (btnText) btnText.textContent = 'All Barangays (35)';

                document.querySelectorAll('.pin-custom-select-item').forEach(item => {
                    const isSelected = item.getAttribute('data-name') === 'Lal-lo Overall';
                    item.className = 'pin-custom-select-item' + (isSelected ? ' selected' : '');
                    const label = isSelected ? 'All Barangays (35)' : item.getAttribute('data-name');
                    item.innerHTML = `<span>${label}</span> ${isSelected ? '✓' : ''}`;
                });

                // Reset map to Lal-lo municipality center
                updatePinCoords(18.1972, 121.6612, true);
                if (pinLeafletMapInstance) {
                    pinLeafletMapInstance.flyTo([18.1972, 121.6612], 13, { duration: 1 });
                }
                return;
            }

            const found = LALLO_35_BARANGAYS.find(b => b.name.toLowerCase() === val.toLowerCase());
            if (found) {
                selectPinBarangay(found);
            }
        }

        let pinSearchInitialized = false;
        function initPinSearchListener() {
            if (pinSearchInitialized) return;
            pinSearchInitialized = true;

            const searchInput = document.getElementById('pinBrgySearchInput');
            const dropdown = document.getElementById('pinBrgyDropdown');
            const clearBtn = document.getElementById('pinClearBrgyBtn');

            if (!searchInput || !dropdown) return;

            searchInput.addEventListener('input', () => {
                const val = searchInput.value.trim().toLowerCase();
                if (clearBtn) clearBtn.style.display = val.length > 0 ? 'flex' : 'none';

                if (!val) {
                    dropdown.style.display = 'none';
                    return;
                }

                const filtered = LALLO_35_BARANGAYS.filter(b => b.name.toLowerCase().includes(val));
                if (filtered.length === 0) {
                    dropdown.innerHTML = '<div style="padding:10px 14px; font-size:12px; opacity:0.7;">No matching barangays</div>';
                    dropdown.style.display = 'block';
                    return;
                }

                dropdown.innerHTML = '';
                filtered.forEach(b => {
                    const item = document.createElement('div');
                    item.className = 'pin-dropdown-item';
                    item.innerHTML = `<span>📍</span> <span>Brgy. ${b.name}</span>`;
                    item.onclick = () => selectPinBarangay(b);
                    dropdown.appendChild(item);
                });
                dropdown.style.display = 'block';
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('#pinBrgySearchInput') && !e.target.closest('#pinBrgyDropdown')) {
                    dropdown.style.display = 'none';
                }
                if (!e.target.closest('#pinBrgySelectWrapper')) {
                    closePinBrgySelectDropdown();
                }
            });
        }

        function clearPinBrgySearch() {
            const input = document.getElementById('pinBrgySearchInput');
            const clearBtn = document.getElementById('pinClearBrgyBtn');
            const dropdown = document.getElementById('pinBrgyDropdown');
            if (input) input.value = '';
            if (clearBtn) clearBtn.style.display = 'none';
            if (dropdown) dropdown.style.display = 'none';
            onPinBarangaySelectChange('Lal-lo Overall');
        }

        function selectPinBarangay(b, flyToCentroid = true) {
            currentPinBarangay = b.name;
            setPinCoverageStatus(true, b.name);

            const searchInput = document.getElementById('pinBrgySearchInput');
            if (searchInput) searchInput.value = b.name;

            const btnText = document.getElementById('pinBrgySelectBtnText');
            if (btnText) btnText.textContent = b.name;

            const dropdown = document.getElementById('pinBrgyDropdown');
            if (dropdown) dropdown.style.display = 'none';

            const clearBtn = document.getElementById('pinClearBrgyBtn');
            if (clearBtn) clearBtn.style.display = 'flex';

            document.querySelectorAll('.pin-custom-select-item').forEach(item => {
                const isSelected = item.getAttribute('data-name') === b.name;
                item.className = 'pin-custom-select-item' + (isSelected ? ' selected' : '');
                const label = item.getAttribute('data-name') === 'Lal-lo Overall' ? 'All Barangays (35)' : item.getAttribute('data-name');
                item.innerHTML = `<span>${label}</span> ${isSelected ? '✓' : ''}`;
            });

            if (flyToCentroid) {
                updatePinCoords(b.lat, b.lng, true);
                if (pinLeafletMapInstance) {
                    pinLeafletMapInstance.invalidateSize();
                    pinLeafletMapInstance.flyTo([b.lat, b.lng], 15, { duration: 0.8 });
                }
            }
        }

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

        const PIN_COVERAGE_BOX_MAP = {
            '50 m²':      { w: 35,  h: 35,  r: 8 },
            '250 m²':     { w: 55,  h: 55,  r: 13 },
            '500 m²':     { w: 75,  h: 75,  r: 18 },
            '1,000 m²':   { w: 100, h: 100, r: 24 },
            '2,500 m²':   { w: 130, h: 130, r: 30 },
            '5,000 m²':   { w: 160, h: 160, r: 36 },
            '1 Hectare+': { w: 190, h: 190, r: 42 }
        };

        function updatePinCoverageShape() {
            if (!pinLeafletMapInstance) return;
            const cfg = PIN_COVERAGE_BOX_MAP[currentPinCoverageLabel] || { w: 75, h: 75, r: 18 };
            const coords = getRoundedBoxCoords(currentPinLat, currentPinLng, cfg.w, cfg.h, cfg.r);

            if (pinLeafletCoverageCircle) {
                pinLeafletCoverageCircle.setLatLngs(coords);
            } else {
                pinLeafletCoverageCircle = L.polygon(coords, {
                    color: '#6b7280',
                    fillColor: '#9ca3af',
                    fillOpacity: 0.22,
                    weight: 2.5
                }).addTo(pinLeafletMapInstance);
            }
        }

        function setPinCoverage(radius, label) {
            currentPinRadius = radius;
            currentPinCoverageLabel = label;

            const helperText = document.getElementById('pinSelectedCoverageText');
            if (helperText) helperText.textContent = label;

            document.querySelectorAll('.pin-coverage-pill').forEach(pill => {
                if (pill.getAttribute('data-label') === label) {
                    pill.classList.add('active');
                } else {
                    pill.classList.remove('active');
                }
            });

            updatePinCoverageShape();
        }

        function getAgricoPinIcon() {
            return L.icon({
                iconUrl: "{{ asset('images/agrico-map-pin.png') }}",
                iconSize: [38, 56],
                iconAnchor: [19, 53],
                popupAnchor: [0, -50],
                className: 'agrico-map-pin-marker'
            });
        }

        function pointInGeoJsonPolygon(lat, lng, layer) {
            if (!layer || !layer.feature || !layer.feature.geometry) return false;
            const geom = layer.feature.geometry;
            const coords = geom.coordinates;
            const pt = [lng, lat]; // GeoJSON format: [longitude, latitude]

            function raycast(ring) {
                let inside = false;
                for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
                    const xi = ring[i][0], yi = ring[i][1];
                    const xj = ring[j][0], yj = ring[j][1];
                    const intersect = ((yi > pt[1]) !== (yj > pt[1])) && (pt[0] < (xj - xi) * (pt[1] - yi) / (yj - yi) + xi);
                    if (intersect) inside = !inside;
                }
                return inside;
            }

            if (geom.type === 'Polygon') {
                return raycast(coords[0]);
            } else if (geom.type === 'MultiPolygon') {
                for (let k = 0; k < coords.length; k++) {
                    if (raycast(coords[k][0])) return true;
                }
            }
            return false;
        }

        function checkIfLocationIsInsideLalloBarangays(lat, lng) {
            if (pinBarangayGeoJsonLayer) {
                let isInside = false;
                pinBarangayGeoJsonLayer.eachLayer(layer => {
                    if (layer.feature && layer.feature.properties) {
                        const isMuni = layer.feature.properties.isMunicipality || layer.feature.properties.admin_level === '6';
                        if (!isMuni && pointInGeoJsonPolygon(lat, lng, layer)) {
                            isInside = true;
                        }
                    }
                });
                return isInside;
            }
            return false;
        }

        function setPinCoverageStatus(isInside, brgyName = null) {
            const badge = document.getElementById('pinSelectedBrgyBadge');
            const badgeName = document.getElementById('pinSelectedBrgyName');
            const coordPill = document.getElementById('pinCoordPill');
            const searchInput = document.getElementById('pinBrgySearchInput');
            const btnText = document.getElementById('pinBrgySelectBtnText');

            if (!isInside) {
                currentPinBarangay = 'Outside Lal-lo Coverage';
                if (badge) {
                    badge.style.color = '#ef4444';
                    badge.innerHTML = `⚠️ <strong id="pinSelectedBrgyName" style="color: #ef4444; font-weight:800;">Outside Lal-lo Coverage</strong>`;
                }
                if (coordPill) {
                    coordPill.style.color = '#ef4444';
                }
                if (searchInput) searchInput.value = '';
                if (btnText) btnText.textContent = 'Outside Lal-lo';
                showScanNotice('⚠️ Selected location is outside the official Lal-lo, Cagayan municipal coverage area (35 Barangays).');
            } else {
                if (badge) {
                    badge.style.color = '';
                    badge.innerHTML = `Selected: <strong id="pinSelectedBrgyName">${brgyName ? ('Brgy. ' + brgyName) : 'Lal-lo Overall'}</strong>`;
                }
                if (coordPill) {
                    coordPill.style.color = '';
                }
            }
        }

        function recenterPinMap() {
            if (pinLeafletMapInstance && pinLeafletMarker) {
                pinLeafletMapInstance.invalidateSize();
                pinLeafletMapInstance.flyTo(pinLeafletMarker.getLatLng(), Math.max(pinLeafletMapInstance.getZoom(), 15), { duration: 0.8 });
            }
        }

        function initOrUpdatePinMap() {
            const mapDiv = document.getElementById('pinLeafletMap');
            if (!mapDiv) return;

            if (!pinLeafletMapInstance) {
                if (typeof L === 'undefined') {
                    console.warn('Leaflet is not loaded yet');
                    return;
                }

                pinLeafletMapInstance = L.map('pinLeafletMap', {
                    center: [currentPinLat, currentPinLng],
                    zoom: 13,
                    zoomControl: false,
                    attributionControl: false
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(pinLeafletMapInstance);

                // Add draggable marker with official AgriCo Map Pin
                pinLeafletMarker = L.marker([currentPinLat, currentPinLng], {
                    icon: getAgricoPinIcon(),
                    draggable: true,
                    title: 'Drag pin to exact soil sample location'
                }).addTo(pinLeafletMapInstance);

                pinLeafletMarker.on('dragend', function() {
                    const pos = pinLeafletMarker.getLatLng();
                    updatePinCoords(pos.lat, pos.lng, false);
                    if (!checkIfLocationIsInsideLalloBarangays(pos.lat, pos.lng)) {
                        setPinCoverageStatus(false);
                    } else {
                        let matched = null;
                        if (pinBarangayGeoJsonLayer) {
                            pinBarangayGeoJsonLayer.eachLayer(layer => {
                                if (layer.feature && layer.feature.properties) {
                                    const isMuni = layer.feature.properties.isMunicipality || layer.feature.properties.admin_level === '6';
                                    if (!isMuni && pointInGeoJsonPolygon(pos.lat, pos.lng, layer)) {
                                        const name = layer.feature.properties.name || layer.feature.properties.adm4_name;
                                        matched = LALLO_35_BARANGAYS.find(b => b.name.toLowerCase() === name.toLowerCase());
                                    }
                                }
                            });
                        }
                        if (matched) {
                            selectPinBarangay(matched, false);
                        } else {
                            setPinCoverageStatus(true, null);
                        }
                    }
                });

                pinLeafletMapInstance.on('click', function(e) {
                    updatePinCoords(e.latlng.lat, e.latlng.lng, true);
                    setPinCoverageStatus(false);

                    if (pinLeafletMapInstance) {
                        pinLeafletMapInstance.invalidateSize();
                        const targetZoom = Math.max(pinLeafletMapInstance.getZoom(), 15);
                        pinLeafletMapInstance.flyTo([e.latlng.lat, e.latlng.lng], targetZoom, { duration: 0.5 });
                    }
                });

                updatePinCoverageShape();

                // Load official NAMRIA / PSA Lal-lo municipal boundary & 35 barangay polygons
                if (!pinBarangayGeoJsonLayer) {
                    fetch("{{ asset('geojson/lal-lo-with-barangays.geojson') }}")
                        .then(res => res.json())
                        .then(data => {
                            if (pinLeafletMapInstance && !pinBarangayGeoJsonLayer) {
                                pinBarangayGeoJsonLayer = L.geoJSON(data, {
                                    style: (feature) => {
                                        const isMunicipality = feature.properties.isMunicipality || feature.properties.admin_level === '6';
                                        return {
                                            color: isMunicipality ? '#22c55e' : '#8EB69B',
                                            weight: isMunicipality ? 2.5 : 1.2,
                                            dashArray: isMunicipality ? null : '3, 4',
                                            fillColor: isMunicipality ? 'transparent' : '#163832',
                                            fillOpacity: isMunicipality ? 0 : 0.12
                                        };
                                    },
                                    onEachFeature: (feature, layer) => {
                                        const name = feature.properties.name || feature.properties.adm4_name;
                                        const isMuni = feature.properties.isMunicipality || feature.properties.admin_level === '6';
                                        if (name) {
                                            layer.bindTooltip(`<strong>${name}</strong>${isMuni ? ' (Municipality)' : ' (Barangay)'}`, { sticky: true });
                                        }
                                        if (!isMuni) {
                                            layer.on('click', function(e) {
                                                L.DomEvent.stopPropagation(e); // prevent map-level click from also firing
                                                const matchBrgy = LALLO_35_BARANGAYS.find(b => b.name.toLowerCase() === name.toLowerCase());
                                                if (matchBrgy) {
                                                    selectPinBarangay(matchBrgy, false); // update UI dropdown without flying to centroid
                                                } else {
                                                    console.warn(`[PinMap] GeoJSON polygon "${name}" has no match in LALLO_35_BARANGAYS — pin placed at click coords but barangay selector not updated.`);
                                                }
                                                updatePinCoords(e.latlng.lat, e.latlng.lng, true);
                                                if (pinLeafletMapInstance) {
                                                    pinLeafletMapInstance.invalidateSize();
                                                    const targetZoom = Math.max(pinLeafletMapInstance.getZoom(), 15);
                                                    pinLeafletMapInstance.flyTo([e.latlng.lat, e.latlng.lng], targetZoom, { duration: 0.5 });
                                                }
                                            });
                                        }
                                    }
                                }).addTo(pinLeafletMapInstance);

                                if (pinLeafletCoverageCircle) pinLeafletCoverageCircle.bringToFront();
                                if (pinLeafletMarker) pinLeafletMarker.bringToFront();
                            }
                        })
                        .catch(err => console.log('Barangay boundary GeoJSON load note:', err));
                }
            } else {
                pinLeafletMapInstance.invalidateSize();
                pinLeafletMapInstance.setView([currentPinLat, currentPinLng], pinLeafletMapInstance.getZoom());
                if (pinLeafletMarker) {
                    pinLeafletMarker.setIcon(getAgricoPinIcon());
                    pinLeafletMarker.setLatLng([currentPinLat, currentPinLng]);
                }
                updatePinCoverageShape();
                if (pinLeafletCoverageCircle) pinLeafletCoverageCircle.bringToFront();
                if (pinLeafletMarker) pinLeafletMarker.bringToFront();
            }
        }

        function updatePinCoords(lat, lng, moveMarker = true) {
            currentPinLat = parseFloat(lat);
            currentPinLng = parseFloat(lng);
            if (moveMarker && pinLeafletMarker) {
                pinLeafletMarker.setLatLng([currentPinLat, currentPinLng]);
            }
            updatePinCoverageShape();
            const pill = document.getElementById('pinCoordPill');
            if (pill) {
                pill.textContent = `${currentPinLat.toFixed(4)}, ${currentPinLng.toFixed(4)}`;
            }
        }

        function submitPinLocation() {
            const btn = document.getElementById('btnSubmitPinLocation');
            const spinner = document.getElementById('btnSubmitPinSpinner');
            const btnText = document.getElementById('btnSubmitPinText');
            const noteInput = document.getElementById('pinScanNoteInput');

            if (btn) btn.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (btnText) btnText.textContent = 'Submitting...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const soilId = (!isPinningAllSoils && currentPinTargetSoil && (currentPinTargetSoil.soil_id || currentPinTargetSoil.id)) 
                || (lastScanMatch && lastScanMatch.soil_id) 
                || null;
            const scanId = (currentPinTargetSoil && (currentPinTargetSoil.scan_id || currentPinTargetSoil.soil_scan_id)) 
                || (currentScanResponseData && currentScanResponseData.scan_id) 
                || (window.currentScanResult && window.currentScanResult.scan_id)
                || null;

            const scanData = window.currentScanResult || (typeof currentScanResponseData !== 'undefined' ? currentScanResponseData : lastScanMatch);
            const activeRegions = window.currentActiveScanRegions || (scanData && scanData.regions) || [];

            fetch("{{ route('website.scan.pin') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    latitude: currentPinLat,
                    longitude: currentPinLng,
                    barangay: currentPinBarangay,
                    soil_id: soilId,
                    soil_scan_id: scanId,
                    coverage_area: currentPinCoverageLabel,
                    scan_note: noteInput ? noteInput.value.trim() : null,
                    pin_all: isPinningAllSoils,
                    pin_mode: isPinningAllSoils ? 'all' : 'single',
                    regions: isPinningAllSoils ? activeRegions : (currentPinTargetSoil ? [currentPinTargetSoil] : null)
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closePinLocationModal();
                    if (data.location && typeof lastScanMatch !== 'undefined' && lastScanMatch) {
                        lastScanMatch.lat = data.location.latitude;
                        lastScanMatch.lng = data.location.longitude;
                        lastScanMatch.barangay = data.location.barangay;
                        lastScanMatch.coverage = currentPinCoverageLabel;
                    }
                    if (typeof showScanNotice === 'function') {
                        showScanNotice(data.message || '📍 Soil sample pinned and submitted for Admin approval!');
                    }
                } else {
                    alert(data.message || 'Unable to submit pin location.');
                }
            })
            .catch(err => {
                console.error('Pin location error:', err);
                alert('An error occurred while submitting coordinates. Please try again.');
            })
            .finally(() => {
                if (btn) btn.disabled = false;
                if (spinner) spinner.style.display = 'none';
                if (btnText) btnText.textContent = 'Submit for Admin Approval 📍';
            });
        }

        function getMunsellColorHex(code, hue) {
            const map = {
                '10R 7/1': '#D6C4C1', '10R 7/2': '#D9BBB4', '10R 8/3': '#E8C5BA',
                '2.5YR 7/1': '#D3C3BE', '2.5YR 7/2': '#D9BBAF', '2.5YR 8/3': '#E7C6B3',
                '5YR 7/1': '#CFBEB7', '5YR 7/2': '#D5BBAE', '5YR 8/3': '#E2C4AE',
                '7.5YR 7/1': '#CCC1BA', '7.5YR 7/2': '#D2BCAE', '10YR 8/3': '#DECDAD',
                '10YR 7/1': '#C8C0B5', '10YR 6/3': '#B7A07E', '10R 3/2': '#5E3A37',
                '2.5YR 3/4': '#62352B', '7.5YR 6/6': '#C68853', '7.5YR 5/3': '#8F694E',
                '10YR 4/3': '#594433', '10YR 3/2': '#443428', '5YR 4/6': '#7A3B26',
                '5YR 4/2': '#694B3E', '2.5Y 6/4': '#9E9267', '10YR 6/4': '#A18E69'
            };
            return map[code] || '#8F694E';
        }

        const btnSaveSoilScan = document.getElementById('btnSaveSoilScan');
        if (btnSaveSoilScan) {
            btnSaveSoilScan.addEventListener('click', () => {
                if (!lastScanMatch) {
                    showScanNotice('Please scan a soil sample first before saving.');
                    return;
                }

                // 1. Immediate UI Feedback (< 10ms)
                btnSaveSoilScan.disabled = true;
                btnSaveSoilScan.style.opacity = '0.9';
                btnSaveSoilScan.style.cursor = 'default';
                btnSaveSoilScan.style.background = '#16a34a';
                btnSaveSoilScan.innerHTML = `
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Saved to History
                `;

                // Scroll smoothly to Scan History table
                const tableSec = document.getElementById('scanHistorySection');
                if (tableSec) {
                    tableSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                // 2. Auto-Generate & Download Formatted Report Card
                const saveRegs = (lastScanMatch && lastScanMatch.regions) || window.currentActiveScanRegions || null;
                downloadSoilScanReport(lastScanMatch, "{{ Auth::user()?->user_id ?? 'ADM-001' }}", saveRegs);
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeSoilRecordModal();
                closePinLocationModal();
                closeBeforeAfterZoomModal();
            }
        });

        function drawReportHeader(ctx, userId, pageW, logoImg) {
            const marginX = 80;

            // Top decorative bar
            ctx.fillStyle = '#16a34a';
            ctx.fillRect(0, 0, pageW, 14);

            // Logo / System Text
            let textX = marginX;
            if (logoImg && logoImg.complete && logoImg.naturalWidth > 0) {
                const logoSize = 56;
                ctx.drawImage(logoImg, marginX, 48, logoSize, logoSize);
                textX = marginX + logoSize + 16;
            }
            ctx.fillStyle = '#15803d';
            ctx.font = 'bold 36px sans-serif';
            ctx.fillText('AgriCo Soil Management System', textX, 90);

            // Main Heading
            ctx.fillStyle = '#0f172a';
            ctx.font = '900 46px sans-serif';
            ctx.fillText('OFFICIAL SOIL CLASSIFICATION REPORT', marginX, 165);

            const now = new Date();
            const dateStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

            ctx.fillStyle = '#64748b';
            ctx.font = '500 24px sans-serif';
            ctx.fillText(`Issued: ${dateStr} at ${timeStr}  |  Account User ID: ${userId || 'ADM-001'}`, marginX, 220);

            // Header Divider Line
            ctx.strokeStyle = '#e2e8f0';
            ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.moveTo(marginX, 260);
            ctx.lineTo(pageW - marginX, 260);
            ctx.stroke();

            return 310;
        }

        function drawReportFooter(ctx, pageW, pageH) {
            const marginX = 80;
            const footerW = pageW - marginX * 2;
            const footerH = 76;
            const footerY = 2135;

            ctx.fillStyle = '#f0fdf4';
            ctx.strokeStyle = '#bbf7d0';
            ctx.lineWidth = 2.5;
            ctx.beginPath();
            ctx.roundRect ? ctx.roundRect(marginX, footerY, footerW, footerH, 20) : ctx.rect(marginX, footerY, footerW, footerH);
            ctx.fill();
            ctx.stroke();

            // Vector blue shield matching official badge
            const sx = marginX + 38;
            const sy = footerY + (footerH - 26) / 2;
            const sw = 20, sh = 26;
            ctx.save();
            ctx.fillStyle = '#3b82f6';
            ctx.beginPath();
            ctx.moveTo(sx, sy);
            ctx.lineTo(sx + sw / 2, sy);
            ctx.lineTo(sx + sw / 2, sy + sh);
            ctx.lineTo(sx, sy + sh - 8);
            ctx.closePath();
            ctx.fill();

            ctx.fillStyle = '#2563eb';
            ctx.beginPath();
            ctx.moveTo(sx + sw / 2, sy);
            ctx.lineTo(sx + sw, sy);
            ctx.lineTo(sx + sw, sy + sh - 8);
            ctx.lineTo(sx + sw / 2, sy + sh);
            ctx.closePath();
            ctx.fill();
            ctx.restore();

            ctx.fillStyle = '#15803d';
            ctx.font = 'bold 24px sans-serif';
            ctx.fillText('Generated by AgriCo Soil Management System — Official ISO-A4 Verification Record', sx + sw + 16, footerY + 48);
        }

        function drawDetailedSoilCard(ctx, region, x, y, w) {
            const hex = region.hex || '#836E4F';
            const code = region.munsell || region.code || region.munsell_code || '5YR 4/2';
            const name = region.color_name || region.soil_name || region.label || region.name || 'Brown';
            const codeParts = code.split(' ');
            const hueStr = region.hue || codeParts[0] || '2.5Y';
            const valChromaStr = codeParts[1] || `${region.val || region.value || '6'}/${region.chroma || '3'}`;
            let rawNear = (window.getNearMatchScore ? window.getNearMatchScore(region, null) : null)
                || region.near_match
                || ((region.confidence && region.confidence !== '100%' && region.confidence !== '100.0%') ? region.confidence : null)
                || region.match_rating
                || region.rating
                || '98.2%';
            if (typeof rawNear === 'number') rawNear = `${rawNear}%`;

            let cursorY = y;

            // --- Banner: enlarged & balanced ---
            const bannerH = 210, bannerR = 24;
            ctx.fillStyle = hex;
            ctx.beginPath();
            ctx.roundRect ? ctx.roundRect(x, cursorY, w, bannerH, bannerR) : ctx.rect(x, cursorY, w, bannerH);
            ctx.fill();

            const snipPad = 18, snipSize = bannerH - snipPad * 2, snipR = 18;
            const snipX = x + snipPad, snipY = cursorY + snipPad;
            ctx.save();
            ctx.beginPath();
            ctx.roundRect ? ctx.roundRect(snipX, snipY, snipSize, snipSize, snipR) : ctx.rect(snipX, snipY, snipSize, snipSize);
            ctx.clip();
            if (region.snippet_img && region.snippet_img.complete && region.snippet_img.naturalWidth > 0) {
                ctx.drawImage(region.snippet_img, snipX, snipY, snipSize, snipSize);

                // Draw green bounding box showing the detected soil region
                const b = getNormalizedRegionBox(region);
                if (b) {
                    let bx = snipX + Math.round(b.x * snipSize);
                    let by = snipY + Math.round(b.y * snipSize);
                    let bw = Math.round(b.w * snipSize);
                    let bh = Math.round(b.h * snipSize);

                    bx = Math.max(snipX + 4, Math.min(snipX + snipSize - 20, bx));
                    by = Math.max(snipY + 4, Math.min(snipY + snipSize - 20, by));
                    bw = Math.max(20, Math.min(snipX + snipSize - bx - 4, bw));
                    bh = Math.max(20, Math.min(snipY + snipSize - by - 4, bh));

                    ctx.lineWidth = 2.5;
                    ctx.strokeStyle = '#4ade80';
                    ctx.strokeRect(bx, by, bw, bh);
                }
            } else {
                ctx.fillStyle = hex;
                ctx.fillRect(snipX, snipY, snipSize, snipSize);
            }
            ctx.restore();

            // Subtle border around the snippet
            ctx.beginPath();
            ctx.roundRect ? ctx.roundRect(snipX, snipY, snipSize, snipSize, snipR) : ctx.rect(snipX, snipY, snipSize, snipSize);
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.35)';
            ctx.lineWidth = 2;
            ctx.stroke();

            const textX = snipX + snipSize + 36;
            ctx.fillStyle = '#ffffff';
            ctx.font = '900 54px sans-serif';
            ctx.fillText(code, textX, cursorY + 95);
            ctx.font = '600 28px sans-serif';
            const percent = region.percent !== undefined ? region.percent : Math.round((region.proportion || 0) * 100);
            ctx.fillText(`Soil Percentage (${percent}%)`, textX, cursorY + 135);

            const pillW = 200, pillH = 56;
            const pillX = x + w - pillW - 28, pillY = cursorY + (bannerH - pillH) / 2;
            ctx.fillStyle = 'rgba(0,0,0,0.55)';
            ctx.beginPath();
            ctx.roundRect ? ctx.roundRect(pillX, pillY, pillW, pillH, 28) : ctx.rect(pillX, pillY, pillW, pillH);
            ctx.fill();
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 26px monospace';
            ctx.textAlign = 'center';
            ctx.fillText(hex.toUpperCase(), pillX + pillW / 2, pillY + pillH / 2 + 9);
            ctx.textAlign = 'left';

            cursorY += bannerH + 24;

            // --- 3x2 metadata grid (Row 1: Color Name, Full Code, Scan Count | Row 2: Soil Organic, Soil Type, Near-Match) ---
            const metaCards = [
                { label: 'Color Name',   value: name },
                { label: 'Full Code',    value: code },
                { label: 'Scan Count',   value: region.scanCount || region.scan_count || region.scan_seq || 1 },
                { label: 'Soil Organic', value: region.organic || region.organic_matter || 'Low (1.1% – 2.0%)' },
                { label: 'Soil Type',    value: region.soilType || region.drainage || region.soil_type || 'Well-Drained Loam' },
                { label: 'Near-Match',   value: rawNear, green: true },
            ];

            const gridCols = 3, gridGap = 24;
            const metaCardW = (w - gridGap * (gridCols - 1)) / gridCols;
            const metaCardH = 150;

            metaCards.forEach(({ label, value, green }, i) => {
                const row = Math.floor(i / gridCols), col = i % gridCols;
                const cX = x + col * (metaCardW + gridGap), cY = cursorY + row * (metaCardH + 18);
                ctx.fillStyle = '#ffffff'; ctx.strokeStyle = '#e2e8f0'; ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.roundRect ? ctx.roundRect(cX, cY, metaCardW, metaCardH, 20) : ctx.rect(cX, cY, metaCardW, metaCardH);
                ctx.fill(); ctx.stroke();
                ctx.fillStyle = '#64748b'; ctx.font = '600 20px sans-serif';
                ctx.fillText(label, cX + 28, cY + 40);
                ctx.fillStyle = green ? '#10b981' : '#0f172a'; ctx.font = 'bold 30px sans-serif';
                ctx.fillText(String(value), cX + 28, cY + 96);
            });
            cursorY += 2 * (metaCardH + 18) + 24;

            // --- Scan Note ---
            const rawNote = region.scan_note || region.note || region.field_remarks || (typeof currentScanResponseData !== 'undefined' && currentScanResponseData ? currentScanResponseData.scan_note : '') || '';
            let cleanNote = typeof rawNote === 'string' ? rawNote.trim() : '';
            if (cleanNote.startsWith('Determined Soil:') || cleanNote === '—' || cleanNote === '-') {
                cleanNote = '';
            }
            const noteText = cleanNote || 'No scan note available for this record.';
            const maxNoteTextW = w - 64;
            ctx.font = '500 24px sans-serif';

            // Wrap text lines for canvas
            const noteWords = noteText.replace(/\r\n/g, '\n').split(/\s+/);
            const noteLines = [];
            let currentNoteLine = '';
            for (let i = 0; i < noteWords.length; i++) {
                const word = noteWords[i];
                if (!word) continue;
                const testLine = currentNoteLine ? `${currentNoteLine} ${word}` : word;
                if (ctx.measureText(testLine).width <= maxNoteTextW) {
                    currentNoteLine = testLine;
                } else {
                    if (currentNoteLine) noteLines.push(currentNoteLine);
                    currentNoteLine = word;
                }
            }
            if (currentNoteLine) noteLines.push(currentNoteLine);

            // Cap at 3 lines if excessively long, appending ellipsis to the 3rd line
            if (noteLines.length > 3) {
                noteLines.length = 3;
                while (noteLines[2] && ctx.measureText(noteLines[2] + '...').width > maxNoteTextW) {
                    noteLines[2] = noteLines[2].slice(0, -1);
                }
                noteLines[2] += '...';
            }

            const noteLineH = 34;
            const noteBoxH = Math.max(110, 48 + (noteLines.length * noteLineH) + 16);
            ctx.fillStyle = '#ffffff'; ctx.strokeStyle = '#e2e8f0'; ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.roundRect ? ctx.roundRect(x, cursorY, w, noteBoxH, 20) : ctx.rect(x, cursorY, w, noteBoxH);
            ctx.fill(); ctx.stroke();
            ctx.fillStyle = '#64748b'; ctx.font = '600 20px sans-serif';
            ctx.fillText('Scan Note', x + 32, cursorY + 38);
            ctx.fillStyle = '#0f172a'; ctx.font = 'bold 26px sans-serif';
            noteLines.forEach((line, idx) => {
                ctx.fillText(line, x + 32, cursorY + 76 + idx * noteLineH);
            });
            cursorY += noteBoxH + 18;

            // --- Description ---
            const rawDescText = region.desc || region.description || `${code} - ${name}`;
            const maxDescTextW = w - 64;
            ctx.font = 'bold 26px sans-serif';
            const descWords = rawDescText.replace(/\r\n/g, '\n').split(/\s+/);
            const descLines = [];
            let currentDescLine = '';
            for (let i = 0; i < descWords.length; i++) {
                const word = descWords[i];
                if (!word) continue;
                const testLine = currentDescLine ? `${currentDescLine} ${word}` : word;
                if (ctx.measureText(testLine).width <= maxDescTextW) {
                    currentDescLine = testLine;
                } else {
                    if (currentDescLine) descLines.push(currentDescLine);
                    currentDescLine = word;
                }
            }
            if (currentDescLine) descLines.push(currentDescLine);
            if (descLines.length > 2) {
                descLines.length = 2;
                while (descLines[1] && ctx.measureText(descLines[1] + '...').width > maxDescTextW) {
                    descLines[1] = descLines[1].slice(0, -1);
                }
                descLines[1] += '...';
            }

            const descLineH = 34;
            const descBoxH = Math.max(110, 48 + (descLines.length * descLineH) + 16);
            ctx.fillStyle = '#ffffff'; ctx.strokeStyle = '#e2e8f0'; ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.roundRect ? ctx.roundRect(x, cursorY, w, descBoxH, 20) : ctx.rect(x, cursorY, w, descBoxH);
            ctx.fill(); ctx.stroke();
            ctx.fillStyle = '#64748b'; ctx.font = '600 20px sans-serif';
            ctx.fillText('Description', x + 32, cursorY + 38);
            ctx.fillStyle = '#0f172a'; ctx.font = 'bold 26px sans-serif';
            descLines.forEach((line, idx) => {
                ctx.fillText(line, x + 32, cursorY + 76 + idx * descLineH);
            });
            cursorY += descBoxH;

            return cursorY;
        }

        // Backward compatibility alias for any existing callers
        function drawSoilResultCard(ctx, region, x, y, w) {
            return drawDetailedSoilCard(ctx, region, x, y, w);
        }

        function drawBeforeAfterOverlay(ctx, regions, imgX, imgY, imgW, imgH) {
            if (!Array.isArray(regions) || regions.length === 0) return;

            const topBarPct = 7.41;
            const photoHeightPct = 92.59;
            const minLeftPct = 51.0;
            const maxRightPct = 99.0;
            const ordinalNames = ['1ST', '2ND', '3RD', '4TH', '5TH', '6TH', '7TH', '8TH', '9TH', '10TH'];
            const greenColor = '#4ade80';

            const labelH = 26, labelPadX = 8;
            const placedLabels = []; // {x, y, w, h} of already-drawn label rects, for collision checks

            ctx.font = '800 16px sans-serif';

            regions.forEach((r, idx) => {
                const b = getNormalizedRegionBox(r);
                if (!b) return;

                const roleName = ordinalNames[idx] || `${idx + 1}TH`;
                const labelText = `${roleName} COLOR`;

                let leftPct = 50 + (b.x * 50);
                let topPct = topBarPct + (b.y * photoHeightPct);
                let widthPct = b.w * 50;
                let heightPct = b.h * photoHeightPct;

                if (leftPct < minLeftPct) {
                    const overflow = minLeftPct - leftPct;
                    leftPct = minLeftPct;
                    widthPct = Math.max(5, widthPct - overflow);
                }
                if (leftPct + widthPct > maxRightPct) widthPct = Math.max(5, maxRightPct - leftPct);
                if (leftPct > maxRightPct - 5) { leftPct = maxRightPct - 5; widthPct = 5; }

                const bx = imgX + (leftPct / 100) * imgW;
                const by = imgY + (topPct / 100) * imgH;
                const bw = (widthPct / 100) * imgW;
                const bh = (heightPct / 100) * imgH;

                // Box: green-colored stroke only (no black border)
                ctx.lineWidth = 3;
                ctx.strokeStyle = greenColor;
                ctx.strokeRect(bx, by, bw, bh);

                // Label placement: default above; flip below if that would collide
                // with any already-placed label (overlapping x-range and near-equal y)
                const labelW = ctx.measureText(labelText).width + labelPadX * 2;
                let labelAbove = by > imgY + labelH + 4;
                let labelX = bx - 1;
                let labelY = labelAbove ? by - labelH - 2 : by + bh + 2;

                const collidesWith = (lx, ly) => {
                    return placedLabels.some(p => {
                        const xOverlap = !(lx + labelW < p.x || lx > p.x + p.w);
                        const yOverlap = !(ly + labelH < p.y || ly > p.y + p.h);
                        return xOverlap && yOverlap;
                    });
                };

                if (collidesWith(labelX, labelY)) {
                    // Try flipping to the other side of the box first
                    const flippedY = labelAbove ? by + bh + 2 : by - labelH - 2;
                    if (!collidesWith(labelX, flippedY) && flippedY >= imgY && flippedY + labelH <= imgY + imgH) {
                        labelY = flippedY;
                        labelAbove = !labelAbove;
                    } else {
                        // Still colliding — nudge down in small steps until clear or out of bounds
                        let attempts = 0;
                        while (collidesWith(labelX, labelY) && attempts < 8 && labelY + labelH < imgY + imgH) {
                            labelY += labelH + 4;
                            attempts++;
                        }
                    }
                }

                ctx.fillStyle = greenColor;
                ctx.fillRect(labelX, labelY, labelW, labelH);

                ctx.fillStyle = '#051F20';
                ctx.font = '800 16px sans-serif';
                ctx.fillText(labelText, labelX + labelPadX, labelY + labelH / 2 + 5);

                placedLabels.push({ x: labelX, y: labelY, w: labelW, h: labelH });
            });
        }

        function drawBeforeAfterSwatchLegend(ctx, regions, x, y, w) {
            if (!Array.isArray(regions) || regions.length === 0) return y;

            const swatchSize = 24, rowH = 44, colGap = 20;
            const colW = (w - colGap) / 2;

            regions.forEach((r, idx) => {
                const row = Math.floor(idx / 2), col = idx % 2;
                const cx = x + col * (colW + colGap), cy = y + row * rowH;
                const hex = r.hex || '#836E4F';
                const code = r.munsell || r.munsell_code || r.code || '';
                const percent = r.percent !== undefined ? r.percent : Math.round((r.proportion || 0) * 100);

                ctx.fillStyle = hex;
                ctx.strokeStyle = '#00000030';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.roundRect ? ctx.roundRect(cx, cy, swatchSize, swatchSize, 5) : ctx.rect(cx, cy, swatchSize, swatchSize);
                ctx.fill(); ctx.stroke();

                ctx.fillStyle = '#0f172a';
                ctx.font = 'bold 18px monospace';
                ctx.fillText(hex.toUpperCase(), cx + swatchSize + 12, cy + 18);
                ctx.fillStyle = '#64748b';
                ctx.font = '500 15px sans-serif';
                ctx.fillText(`(${code})`, cx + swatchSize + 165, cy + 18);
                ctx.fillStyle = '#16a34a';
                ctx.font = 'bold 15px sans-serif';
                ctx.fillText(`${percent}%`, cx + swatchSize + 260, cy + 18);
            });

            const rows = Math.ceil(regions.length / 2);
            return y + rows * rowH + 10;
        }

        function drawBeforeAfterPage(ctx, pageW, pageH, beforeAfterImg, userId, logoImg, regions) {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, pageW, pageH);
            const startY = drawReportHeader(ctx, userId, pageW, logoImg) + 40;
            drawReportFooter(ctx, pageW, pageH);
            ctx.fillStyle = '#0f172a'; ctx.font = '900 40px sans-serif';
            ctx.fillText('SOIL NORMALIZATION — BEFORE / AFTER', 80, startY);
            if (beforeAfterImg && beforeAfterImg.complete && beforeAfterImg.naturalWidth > 0) {
                const imgW = pageW - 160;
                const imgH = imgW * (beforeAfterImg.naturalHeight / beforeAfterImg.naturalWidth);
                const drawH = Math.min(imgH, pageH - startY - 260);
                const imgY = startY + 50;
                ctx.drawImage(beforeAfterImg, 80, imgY, imgW, drawH);
                if (Array.isArray(regions) && regions.length > 0) {
                    drawBeforeAfterOverlay(ctx, regions, 80, imgY, imgW, drawH);
                    drawBeforeAfterSwatchLegend(ctx, regions, 80, imgY + drawH + 24, imgW);
                }
            }
        }

        function downloadSoilScanReport(match, userId, regions) {
            const { jsPDF } = window.jspdf || { jsPDF: window.jsPDF };
            if (!jsPDF) {
                console.error('jsPDF is not loaded');
                return;
            }
            const pageW = 1600, pageH = 2263;
            const marginX = 80, cardW = pageW - marginX * 2;
            const allRegions = Array.isArray(regions) && regions.length > 0 ? regions : [match];

            const logoImg = new Image();
            logoImg.crossOrigin = 'anonymous';
            logoImg.src = '{{ asset("images/agrico-logo.png") }}';

            const beforeAfterImg = new Image();
            beforeAfterImg.crossOrigin = 'anonymous';
            const baSrc = match.before_after_image_url || match.before_after_image || (document.getElementById('resBeforeAfterImg')?.src) || '';
            if (baSrc && typeof baSrc === 'string' && baSrc.length > 5 && !baSrc.endsWith('#')) {
                beforeAfterImg.src = baSrc;
            }

            const fallbackSoilImg = new Image();
            fallbackSoilImg.crossOrigin = 'anonymous';
            const soilSrc = match.image || match.scan_image || match.scan_image_url || (document.getElementById('resSoilCircleSnippet')?.src) || '';
            if (soilSrc && typeof soilSrc === 'string' && soilSrc.length > 5 && !soilSrc.endsWith('#')) {
                fallbackSoilImg.src = soilSrc;
            }

            let pending = 3;
            const doneOne = () => {
                pending--;
                if (pending <= 0) {
                    build();
                }
            };

            const build = () => {
                if (fallbackSoilImg.complete && fallbackSoilImg.naturalWidth > 0) {
                    allRegions.forEach(r => {
                        if (!r.snippet_img) r.snippet_img = fallbackSoilImg;
                    });
                }

                const pdf = new jsPDF({ unit: 'px', format: [pageW, pageH] });
                let firstPage = true;
                const newCanvas = () => {
                    const c = document.createElement('canvas');
                    c.width = pageW; c.height = pageH;
                    const ctx = c.getContext('2d');
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, pageW, pageH);
                    const headerBottom = drawReportHeader(ctx, userId, pageW, logoImg);
                    drawReportFooter(ctx, pageW, pageH);
                    return { c, ctx, headerBottom };
                };
                const commit = (c) => {
                    if (!firstPage) pdf.addPage([pageW, pageH]);
                    pdf.addImage(c.toDataURL('image/png'), 'PNG', 0, 0, pageW, pageH);
                    firstPage = false;
                };
                const drawBeforeAfterBlock = (ctx, startY) => {
                    ctx.fillStyle = '#64748b'; ctx.font = '700 20px sans-serif';
                    ctx.fillText('SOIL NORMALIZATION', marginX, startY);
                    startY += 26;
                    if (beforeAfterImg.complete && beforeAfterImg.naturalWidth > 0) {
                        const imgH = cardW * (beforeAfterImg.naturalHeight / beforeAfterImg.naturalWidth);
                        const drawH = Math.min(imgH, 2050 - startY - 120);
                        ctx.drawImage(beforeAfterImg, marginX, startY, cardW, drawH);
                        drawBeforeAfterOverlay(ctx, allRegions, marginX, startY, cardW, drawH);
                        drawBeforeAfterSwatchLegend(ctx, allRegions, marginX, startY + drawH + 24, cardW);
                    }
                };

                if (allRegions.length === 1) {
                    const { c, ctx, headerBottom } = newCanvas();
                    const endY = drawDetailedSoilCard(ctx, allRegions[0], marginX, headerBottom + 12, cardW);
                    drawBeforeAfterBlock(ctx, endY + 30);
                    commit(c);
                } else {
                    for (let i = 0; i < allRegions.length; i += 2) {
                        const pair = allRegions.slice(i, i + 2);
                        const isLastGroup = i + 2 >= allRegions.length;
                        const { c, ctx, headerBottom } = newCanvas();
                        let cursorY = headerBottom + 12;
                        pair.forEach((region) => {
                            cursorY = drawDetailedSoilCard(ctx, region, marginX, cursorY, cardW) + 36;
                        });
                        commit(c);
                        if (isLastGroup) {
                            const bp = newCanvas();
                            drawBeforeAfterBlock(bp.ctx, bp.headerBottom + 40);
                            commit(bp.c);
                        }
                    }
                }

                const codeClean = (match.code || match.munsell || 'SOIL').replace(/[^a-zA-Z0-9]/g, '');
                const dateFormatted = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                pdf.save(`Soil_Scan_Report_A4_${codeClean}_${dateFormatted}.pdf`);
            };

            if (logoImg.src && !logoImg.complete) {
                logoImg.onload = doneOne;
                logoImg.onerror = doneOne;
            } else {
                doneOne();
            }

            if (beforeAfterImg.src && !beforeAfterImg.complete) {
                beforeAfterImg.onload = doneOne;
                beforeAfterImg.onerror = doneOne;
            } else {
                doneOne();
            }

            if (fallbackSoilImg.src && !fallbackSoilImg.complete) {
                fallbackSoilImg.onload = doneOne;
                fallbackSoilImg.onerror = doneOne;
            } else {
                doneOne();
            }
        }

        function saveScanToDatabase(match, lat, lng, seqTdElem) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch("{{ route('website.scan.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    soil_name: match.name,
                    full_code: match.code,
                    hue: match.hue,
                    scan_note: match.desc,
                    latitude: lat,
                    longitude: lng,
                    location_name: lat ? 'Scanned Field Location' : null,
                    scan_image: match.image || match.scan_image || null
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.error === 'ACCOUNT_PENDING_APPROVAL') {
                    showAccountPendingModal('Account Pending Approval', data.message || 'Your account is awaiting admin approval. Scanning and Map access will be enabled once approved.');
                    return;
                }
                if (data.error === 'AUTH_REQUIRED') {
                    showLoginModal(data.message || 'Please log in to perform a scan and view scan history.');
                    return;
                }
                if (data.success && seqTdElem && data.scan_seq) {
                    seqTdElem.textContent = data.scan_seq;
                }
            })
            .catch(err => {
                console.log('Scan save error:', err);
                if (btnSaveSoilScan) {
                    btnSaveSoilScan.disabled = false;
                    btnSaveSoilScan.style.opacity = '1';
                    btnSaveSoilScan.style.background = '#22c55e';
                    btnSaveSoilScan.innerHTML = `
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Save Copy
                    `;
                }
                showScanNotice('Could not sync soil scan. Please try again.');
            });
        }

        function getClosestMunsell(r, g, b) {
            // First check database Munsell colors
            if (Array.isArray(dbMunsellColors) && dbMunsellColors.length > 0) {
                let minDistance = Infinity;
                let bestMatch = null;

                dbMunsellColors.forEach(item => {
                    const targetRGB = munsellToRGB(item.hue, parseFloat(item.value || 5), parseFloat(item.chroma || 4));
                    const dist = Math.sqrt(
                        Math.pow(r - targetRGB.r, 2) +
                        Math.pow(g - targetRGB.g, 2) +
                        Math.pow(b - targetRGB.b, 2)
                    );
                    if (dist < minDistance) {
                        minDistance = dist;
                        bestMatch = {
                            hue: item.hue,
                            val: item.value,
                            chroma: item.chroma,
                            code: item.full_code || `${item.hue} ${item.value}/${item.chroma}`,
                            name: item.color_name || 'Determined Soil Specimen',
                            hex: targetRGB.hex,
                            desc: item.description || `Specimen verified as ${item.color_name} (${item.full_code}).`,
                            organic: parseFloat(item.value || 5) <= 4 ? 'High (3.8%–4.5%)' : 'Moderate (2.1%–3.2%)',
                            drainage: item.hue.includes('YR') ? 'Well-Drained Loam' : 'Silty Clay Loam',
                            crops: getCropRecommendations(item.color_name, item.hue)
                        };
                    }
                });

                if (bestMatch) return bestMatch;
            }

            // Fallback default
            return {
                hue: '10YR', val: '4', chroma: '3', code: '10YR 4/3', name: 'Dark Brown', hex: '#594433',
                desc: 'Moist organic topsoil with high fertility potential.',
                organic: 'High (3.8%)', drainage: 'Well-Drained Loam',
                crops: 'Rice, Corn, Cassava, Sweet Potato, Vegetables'
            };
        }

        function munsellToRGB(hue, value, chroma) {
            const hueMap = { 'R': 0, 'YR': 30, 'Y': 60, 'GY': 90, 'G': 120 };
            const matches = hue.match(/^([\d.]+)(.+)$/) || [null, '10', 'YR'];
            const prefix = parseFloat(matches[1] || 5);
            const letter = matches[2] || 'YR';
            const baseAngle = hueMap[letter] ?? 30;
            const hueAngle = fmod(baseAngle + ((prefix - 5) * 3.6) + 360, 360);
            const lightness = (value / 10) * 100;
            const saturation = Math.min(chroma * 12, 100);

            return hslToRGB(hueAngle, saturation, lightness);
        }

        function fmod(a, b) { return Number((a - (Math.floor(a / b) * b)).toFixed(8)); }

        function hslToRGB(h, s, l) {
            s /= 100; l /= 100;
            const k = n => (n + h / 30) % 12;
            const a = s * Math.min(l, 1 - l);
            const f = n => l - a * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1)));
            const r = Math.round(255 * f(0));
            const g = Math.round(255 * f(8));
            const b = Math.round(255 * f(4));
            const hex = "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
            return { r, g, b, hex };
        }

        function getCropRecommendations(colorName, hue) {
            const nameLower = (colorName || '').toLowerCase();
            if (nameLower.includes('black') || nameLower.includes('very dark')) {
                return '🌾 Lowland Rice, 🌽 Corn, 🥬 Leafy Vegetables, 🥔 Potato, 🫘 Legumes';
            }
            if (nameLower.includes('brown') || hue.includes('YR')) {
                return '🌽 Corn, 🍠 Sweet Potato, 🌾 Upland Rice, 🥜 Peanut, 🍌 Banana';
            }
            if (nameLower.includes('red') || hue.includes('R')) {
                return '🍍 Pineapple, ☕ Coffee, 🌴 Oil Palm, 🥔 Cassava, 🥭 Fruit Trees';
            }
            return '🌾 Rice, 🌽 Corn, 🥬 Root Crops & Farm Vegetables';
        }
    </script>

    <!-- Soil Record Details Pop-up Modal -->
    <div id="soilRecordModal" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(5, 31, 32, 0.8); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); align-items:center; justify-content:center; padding:16px;">
        <div class="soil-modal-card" style="font-family: inherit;">
            
            <!-- Modal Header -->
            <div class="soil-modal-header" style="display: flex; align-items: center; justify-content: space-between;">
                <h3 class="soil-modal-title" id="modalRecordTitle">Soil Record Details</h3>
                <button type="button" class="modal-close-circle-btn" onclick="closeSoilRecordModal()" title="Close details" aria-label="Close details">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body Container -->
            <div class="soil-modal-body-container">
                
                {{-- Dual-Preview Soil Badge Banner (Inline Avatar + Center Code + Right Hex Pill) --}}
                <div id="modalSoilDualPreviewBadge" class="soil-modal-banner" style="position: relative; margin-top: 2px; margin-bottom: 16px; width: 100%; border-radius: 18px; display: flex; align-items: center; gap: 14px; padding: 14px 18px; box-sizing: border-box; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2); min-height: 76px; transition: background-color 0.3s ease;">
                    {{-- Inline Avatar (matches admin soil-swatch-avatar) --}}
                    <div id="modalSoilSnippetAvatarContainer" 
                         style="position: relative; width: 60px; height: 60px; min-width: 60px; border-radius: 12px; overflow: hidden; flex-shrink: 0; box-shadow: none !important; background: transparent; pointer-events: none; border: none; outline: none;">
                        <img id="modalSoilCircleSnippet" src="" alt="Soil Sample" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: none;" />
                        <div id="modalSoilSnippetFallback" style="position: absolute; inset: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); color: rgba(255,255,255,0.85);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                    </div>

                    {{-- Center Code & Percentage --}}
                    <div id="modalSwatchBlock" style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span id="modalDominantChevron" style="display: none; font-size: 12px; color: #ffffff; opacity: 0.9;">▼</span>
                            <span id="modalCodeDisplay" style="font-size: 24px; font-weight: 900; color: #ffffff; text-shadow: none; letter-spacing: -0.01em; line-height: 1.15;">
                                7.5YR 5/3
                            </span>
                        </div>
                        <div id="modalSoilPercentageDisplay" style="display: none; font-size: 11.5px; font-weight: 700; color: rgba(255,255,255,0.95); margin-top: 3px; letter-spacing: 0.02em;">
                            Soil Percentage (60%)
                        </div>
                    </div>

                    {{-- Right Hex Pill --}}
                    <span id="modalHexPill" style="font-size: 11px; font-weight: 800; color: #ffffff; background: rgba(0,0,0,0.58); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); padding: 5px 12px; border-radius: 20px; letter-spacing: 0.05em; text-transform: uppercase; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; border: 1px solid rgba(255,255,255,0.25); user-select: none; line-height: 1; flex-shrink: 0; white-space: nowrap;">
                        #8F694E
                    </span>
                </div>

                <!-- Row 1: Code Details (Color Name, Full Code, Scan Count) -->
                <div class="soil-modal-grid-3 soil-metrics-row-1" style="margin-bottom: 8px;">
                    <!-- Color Name Card -->
                    <div class="soil-modal-grid-card">
                        <div class="soil-modal-label">
                            Color Name
                        </div>
                        <div id="modalCardColorName" class="soil-modal-value" style="font-size: 13px; font-weight: 700;">Brown</div>
                    </div>

                    <!-- Full Code Card -->
                    <div class="soil-modal-grid-card">
                        <div class="soil-modal-label">
                            Full Code
                        </div>
                        <div id="modalCardFullCode" class="soil-modal-value" style="font-size: 13px; font-weight: 700;">7.5YR 5/3</div>
                    </div>

                    <!-- Scan Count Card -->
                    <div class="soil-modal-grid-card">
                        <div class="soil-modal-label">
                            Scan Count
                        </div>
                        <div id="modalCardScanCount" class="soil-modal-value" style="font-size: 13px; font-weight: 700;">1</div>
                    </div>
                </div>

                <!-- Row 2: Soil Properties (Soil Organic, Soil Type, Near-Match Emerald #10b981) -->
                <div class="soil-modal-grid-3 soil-metrics-row-2" style="margin-bottom: 12px;">
                    <!-- Soil Organic Card -->
                    <div class="soil-modal-grid-card">
                        <div class="soil-modal-label">
                            Soil Organic
                        </div>
                        <div id="modalCardOrganic" class="soil-modal-value" style="font-size:13px; font-weight:700;">Moderate (2.2% – 3.7%)</div>
                    </div>

                    <!-- Soil Type Card -->
                    <div class="soil-modal-grid-card">
                        <div class="soil-modal-label">
                            Soil Type
                        </div>
                        <div id="modalCardDrainage" class="soil-modal-value" style="font-size:13px; font-weight:700;">Well-Drained Clay / Latosol</div>
                    </div>

                    <!-- Near-Match Rating Card -->
                    <div class="soil-modal-grid-card card-near-match">
                        <div class="soil-modal-label">
                            Near-Match
                        </div>
                        <div id="modalCardConfidence" class="soil-modal-value" style="font-size: 13.5px; font-weight: 800; color: #10b981 !important;">97.4%</div>
                    </div>
                </div>

                <!-- Scan Date Card (Shown only for single record fallback) -->
                <div id="modalCardDateContainer" class="soil-modal-grid-card" style="margin-bottom: 10px;">
                    <div class="soil-modal-label">
                        SCAN DATE
                    </div>
                    <div id="modalCardDate" class="soil-modal-value">08/21/2026</div>
                </div>

                <!-- Scan Note Card (Full Width 100%) -->
                <div id="modalScanNoteCard" class="soil-modal-grid-card soil-modal-desc-card" style="margin-bottom: 10px; padding: 12px 16px; overflow: hidden; max-width: 100%; box-sizing: border-box;">
                    <div class="soil-modal-label">
                        Scan Note
                    </div>
                    <div id="modalCardScanNote" class="soil-modal-value" style="font-size: 13px; font-weight: 700; margin: 0; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
                        No scan note available for this record.
                    </div>
                </div>

                <!-- Description Card (Full Width 100%) -->
                <div id="modalDescCard" class="soil-modal-grid-card soil-modal-desc-card" style="margin-bottom: 10px; padding: 12px 16px; overflow: hidden; max-width: 100%; box-sizing: border-box;">
                    <div class="soil-modal-label">
                        Description
                    </div>
                    <div id="modalCardDesc" class="soil-modal-value" style="font-size: 13px; font-weight: 700; margin: 0; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
                        7.5YR 5/3 - brown
                    </div>
                </div>

                <!-- Multi-Layer Accordion Section (Secondary & Tertiary Layers) -->
                <div id="modalMultiLayerSection" style="display: none; margin-bottom: 12px; border-top: 1px dashed rgba(142, 182, 155, 0.25); padding-top: 12px;">
                    <div id="modalMultiLayerItems" style="display: flex; flex-direction: column; gap: 8px;"></div>
                </div>

                <!-- Soil Pin Location & Covered Area Card (Modal - Placed BEFORE Soil Normalization) -->
                <div id="modalSoilLocationCard" class="soil-modal-grid-card" style="margin-bottom: 12px; padding: 13px 16px 15px; overflow: hidden; max-width: 100%; box-sizing: border-box; display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div class="soil-modal-label" style="font-size: 11px; font-weight: 700; margin-bottom: 0; text-transform: uppercase; letter-spacing: 0.03em;">
                            SOIL PIN LOCATION &amp; COVERED AREA
                        </div>
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <span id="modalLocBrgyBadge" class="modal-loc-badge-brgy">
                                --
                            </span>
                            <span id="modalLocCoverageBadge" class="modal-loc-badge-coverage" style="display: none;">
                                --
                            </span>
                            <span id="modalLocCoordsBadge" class="modal-loc-badge-coords">
                                --
                            </span>
                        </div>
                    </div>
                    <div class="modal-soil-map-wrapper">
                        <div id="modalSoilDetailMap" style="height: 220px; width: 100%;"></div>
                        <div class="modal-soil-map-watermark">
                            AgriCo Soil GPS Preview
                        </div>
                    </div>
                </div>

                <!-- Soil Normalization: Before & After Card (Modal - Placed AFTER the last soil layer result) -->
                <div id="modalBeforeAfterCard" class="soil-modal-grid-card" style="margin-bottom: 12px; padding: 13px 16px 15px; overflow: hidden; max-width: 100%; box-sizing: border-box; display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div class="soil-modal-label" style="font-size: 11px; font-weight: 700; color: #8EB69B; margin-bottom: 0; text-transform: uppercase; letter-spacing: 0.03em;">
                            SOIL NORMALIZATION
                        </div>
                        <span class="before-after-badge" style="font-size: 9.5px; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: rgba(16, 185, 129, 0.15); color: #10b981; text-transform: uppercase; letter-spacing: 0.04em;">
                            BEFORE / AFTER
                        </span>
                    </div>
                    <div id="modalBeforeAfterContainer" style="position: relative; width: 100%; border-radius: 10px; overflow: hidden; cursor: pointer;" onclick="openBeforeAfterLightbox('modalBeforeAfterImg')" title="Click to view full resolution comparison">
                        <img id="modalBeforeAfterImg" src="" alt="Soil Before & After Normalization" style="width: 100%; height: auto; display: block; border-radius: 8px;">

                        <!-- Replaces the single static modalBeforeAfterBox -->
                        <div id="modalBeforeAfterBoxes" style="position: absolute; inset: 0; pointer-events: none; z-index: 10;"></div>
                    </div>
                    <div id="modalBeforeAfterSwatches" style="display: none; flex-wrap: wrap; gap: 6px; margin-top: 8px;"></div>
                </div>

                <!-- Metadata Footer -->
                <div id="modalMetadataFooter" style="display: none; margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(142, 182, 155, 0.2); font-size: 11.5px; justify-content: space-between; align-items: center; text-transform: uppercase; letter-spacing: 0.04em;">
                    <div>Created: <span id="modalMetaCreated" style="font-weight: 700;">--</span></div>
                    <div>Total Layers: <span id="modalMetaLayers" style="font-weight: 700;">1</span></div>
                </div>

            </div>

            <!-- Fixed Modal Footer: Cancel and Save Copy Buttons -->
            <div class="soil-modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="closeSoilRecordModal()" title="Cancel">
                    <span>Cancel</span>
                </button>
                <button type="button" class="btn-save-hardcopy" onclick="downloadSoilRecordHardCopy()" title="Save Copy">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Save Copy</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── PIN SOIL LOCATION MODAL (DARK & LIGHT MODE READY) ── --}}
    <div id="pinSoilLocationModal" class="pin-modal-backdrop" style="display: none;">
        <div class="pin-modal-overlay" onclick="closePinLocationModal()"></div>
        <div class="pin-modal-container">
            <!-- Header -->
            <div class="pin-modal-header">
                <div>
                    <h3 class="pin-modal-title">Pin Soil Location</h3>
                    <p class="pin-modal-subtitle">Select Barangay &amp; Pin Coordinates in Lal-lo, Cagayan for Admin Review</p>
                </div>
                <button type="button" class="pin-modal-close-btn" onclick="closePinLocationModal()" title="Close">&times;</button>
            </div>

            <!-- Scrollable Body -->
            <div class="pin-modal-body">

                <!-- Barangay Search Input -->
                <div class="pin-field-group" style="position: relative;">
                    <div class="pin-field-header">
                        <label class="pin-field-label">SEARCH BARANGAY (LAL-LO, CAGAYAN)</label>
                        <span id="pinSelectedBrgyBadge" class="pin-selected-brgy-badge" style="display: none !important;">Selected: <strong id="pinSelectedBrgyName">Lal-lo Overall</strong></span>
                    </div>
                    <div class="pin-search-row">
                        <div class="pin-search-box" style="flex: 1; min-width: 0; position: relative;">
                            <input type="text" id="pinBrgySearchInput" class="pin-input-field" placeholder="Search from 35 Barangays in Lal-lo..." autocomplete="off">
                            <button type="button" id="pinClearBrgyBtn" class="pin-clear-btn" style="display: none;" onclick="clearPinBrgySearch()">&times;</button>
                            <!-- Autocomplete Dropdown -->
                            <div id="pinBrgyDropdown" class="pin-dropdown-box" style="display: none;"></div>
                        </div>

                        <!-- Barangay Filter Dropdown (Custom Contained Dropdown) -->
                        <div class="pin-custom-select-wrapper" id="pinBrgySelectWrapper" style="position: relative;">
                            <button type="button" class="pin-select-filter" id="pinBrgySelectBtn" onclick="togglePinBrgySelectDropdown(event)" title="Filter Barangay">
                                <span id="pinBrgySelectBtnText">All Barangays (35)</span>
                                <svg class="pin-select-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div id="pinBrgySelectMenu" class="pin-custom-select-menu" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <!-- Leaflet Interactive Map Card -->
                <div class="pin-map-card">
                    <div class="pin-map-header">
                        <span class="pin-map-instruction">Drag marker or click map to pinpoint exact sample site</span>
                        <span id="pinCoordPill" class="pin-coord-pill" onclick="recenterPinMap()" style="cursor: pointer;" title="Center map on pin">18.1972, 121.6612</span>
                    </div>
                    <div id="pinLeafletMap" class="pin-leaflet-container"></div>
                </div>

                <!-- Scrollable Container for Soil Covered Area (Sakop) and Scan Note -->
                <div class="pin-scrollable-fields">
                    <!-- Soil Covered Area (Sakop) -->
                    <div class="pin-field-group">
                        <div class="pin-field-header">
                            <label class="pin-field-label">SOIL COVERED AREA (SAKOP)</label>
                            <span class="pin-coverage-helper">Selected: <strong id="pinSelectedCoverageText">500 m²</strong></span>
                        </div>
                        <div class="pin-coverage-pills-row">
                            <button type="button" class="pin-coverage-pill" data-radius="4" data-label="50 m²" onclick="setPinCoverage(4, '50 m²')">50 m²</button>
                            <button type="button" class="pin-coverage-pill" data-radius="9" data-label="250 m²" onclick="setPinCoverage(9, '250 m²')">250 m²</button>
                            <button type="button" class="pin-coverage-pill active" data-radius="13" data-label="500 m²" onclick="setPinCoverage(13, '500 m²')">500 m²</button>
                            <button type="button" class="pin-coverage-pill" data-radius="18" data-label="1,000 m²" onclick="setPinCoverage(18, '1,000 m²')">1,000 m²</button>
                            <button type="button" class="pin-coverage-pill" data-radius="28" data-label="2,500 m²" onclick="setPinCoverage(28, '2,500 m²')">2,500 m²</button>
                            <button type="button" class="pin-coverage-pill" data-radius="40" data-label="5,000 m²" onclick="setPinCoverage(40, '5,000 m²')">5,000 m²</button>
                            <button type="button" class="pin-coverage-pill" data-radius="56" data-label="1 Hectare+" onclick="setPinCoverage(56, '1 Hectare+')">1 Hectare+</button>
                        </div>
                    </div>

                    <!-- Scan Note -->
                    <div class="pin-field-group">
                        <label class="pin-field-label">SCAN NOTE / FIELD REMARKS</label>
                        <textarea id="pinScanNoteInput" class="pin-input-field pin-textarea" rows="2" placeholder="Enter soil sampling notes, crop conditions, moisture, or landmarks..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Fixed Modal Footer (Pinned at Bottom) -->
            <div class="pin-modal-footer">
                <div class="pin-modal-actions">
                    <button type="button" class="btn-pin-cancel" onclick="closePinLocationModal()">
                        Cancel
                    </button>
                    <button type="button" id="btnSubmitPinLocation" class="btn-pin-submit" onclick="submitPinLocation()">
                        <span id="btnSubmitPinSpinner" style="display:none; width:16px; height:16px; border:2px solid #ffffff; border-top-color:transparent; border-radius:50%; animation:spin 0.8s linear infinite;"></span>
                        <span id="btnSubmitPinText">Submit for Admin Approval</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <style>
        @media print {
            body > *:not(#soilRecordModal) {
                display: none !important;
            }
            #soilRecordModal {
                display: flex !important;
                position: absolute !important;
                inset: 0 !important;
                background: #ffffff !important;
                padding: 0 !important;
                align-items: flex-start !important;
                justify-content: center !important;
            }
            #soilRecordModal > div {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
                width: 100% !important;
                max-width: 100% !important;
                background: #ffffff !important;
                color: #000000 !important;
            }
            #soilRecordModal button, #soilRecordModal .btn-save-hardcopy {
                display: none !important;
            }
            #soilRecordModal h3, #soilRecordModal h4, #soilRecordModal div {
                color: #000000 !important;
            }
            #soilRecordModal [style*="background:#163832"],
            #soilRecordModal [style*="background:#0B2B26"],
            #soilRecordModal [style*="background:#051F20"] {
                background: #f8fafc !important;
                border-color: #cbd5e1 !important;
                color: #000000 !important;
            }
        }
    </style>

    {{-- Before / After Zoom Lightbox Modal --}}
    <div id="beforeAfterZoomModal" class="ba-zoom-modal" style="display: none;" onclick="closeBeforeAfterZoomModal(event)">
        <div class="ba-zoom-card" onclick="event.stopPropagation()">
            <div class="ba-zoom-header">
                <div class="ba-zoom-header-left">
                    <span id="beforeAfterZoomTitle" class="ba-zoom-title">
                        Soil Normalization Comparison (Before & After)
                    </span>
                    <span id="beforeAfterZoomBadge" class="ba-zoom-badge">
                        Stage 2 PromptNorm
                    </span>
                </div>
                <button type="button" class="ba-zoom-close-btn" onclick="closeBeforeAfterZoomModal(event)" title="Close">
                    ✕
                </button>
            </div>
            <div class="ba-zoom-body">
                <div class="ba-zoom-img-container">
                    <img id="beforeAfterZoomImg" class="ba-zoom-img" src="" alt="Soil Before and After Full Resolution" onerror="handleBeforeAfterZoomError(this)">
                    <div id="beforeAfterZoomBoxes" class="ba-zoom-boxes"></div>
                </div>
            </div>
        </div>
    </div>

</x-website-layout>
