<div class="space-y-6">
    <style>
        @keyframes modalBackdropFadeIn {
            0% { opacity: 0; backdrop-filter: blur(0px); }
            100% { opacity: 1; backdrop-filter: blur(8px); }
        }
        @keyframes modalPopIn {
            0% { opacity: 0; transform: scale(0.92) translateY(14px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .masked-password-input {
            -webkit-text-security: disc !important;
        }
        .unmasked-password-input {
            -webkit-text-security: none !important;
        }
        .modal-animated-backdrop {
            position: fixed !important;
            inset: 0 !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            min-width: 100% !important;
            height: 100vh !important;
            height: 100dvh !important;
            min-height: 100% !important;
            margin: 0 !important;
            z-index: 99999 !important;
            box-sizing: border-box !important;
            background: rgba(0, 0, 0, 0.45) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            overscroll-behavior: contain !important;
            animation: modalBackdropFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .modal-animated-card {
            animation: modalPopIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Student Card Styles (matching uploaded design with clean avatar, no cover, no rainbow) */
        .student-profile-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 28px;
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05), 0 2px 6px -1px rgba(0,0,0,0.02);
            padding: 24px 20px 20px 20px;
            position: relative;
            transition: border-color 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-sizing: border-box;
        }
        .student-profile-card:hover {
            border-color: #cbd5e1;
        }

        .student-avatar-wrap {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0 auto;
            border: 3px solid #ffffff;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            background: #22c55e;
            overflow: hidden;
            flex-shrink: 0;
        }

        .student-stats-box {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 20px;
            padding: 14px 16px;
            margin: 16px 0 14px 0;
            width: 100%;
            box-sizing: border-box;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            position: relative;
        }
        .student-stats-box::before {
            content: '';
            position: absolute;
            top: 18%;
            bottom: 18%;
            left: 50%;
            width: 1px;
            background: #e2e8f0;
        }

        .card-action-bar {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            width: 100% !important;
            padding-top: 4px !important;
            box-sizing: border-box !important;
        }
        .card-action-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            height: 36px !important;
            min-height: 36px !important;
            max-height: 36px !important;
            padding: 0 8px !important;
            border-radius: 11px !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            white-space: nowrap !important;
            border: 1px solid transparent !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-decoration: none !important;
            flex: 1 1 0 !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }
        .card-action-btn:hover {
            box-shadow: 0 2px 6px rgba(0,0,0,0.04) !important;
        }
        /* Card Action Buttons: Soil Scan, Pin Map, Trash (Default Gray, Color on Hover) */
        .card-action-btn.soil-btn,
        .card-action-btn.pin-btn,
        .card-action-trash-btn {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #64748b !important;
        }
        .card-action-btn.soil-btn:hover {
            background: #f0fdf4 !important;
            border-color: #bbf7d0 !important;
            color: #15803d !important;
            box-shadow: 0 2px 6px rgba(34,197,94,0.12) !important;
        }
        .card-action-btn.pin-btn:hover {
            background: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #1d4ed8 !important;
            box-shadow: 0 2px 6px rgba(37,99,235,0.12) !important;
        }
        .card-action-trash-btn {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            max-width: 38px !important;
            min-height: 38px !important;
            max-height: 38px !important;
            border-radius: 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            flex-shrink: 0 !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-sizing: border-box !important;
            padding: 0 !important;
        }
        .card-action-trash-btn svg {
            stroke: #64748b !important;
            transition: stroke 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .card-action-trash-btn:hover {
            background: #fef2f2 !important;
            border-color: #fecaca !important;
            color: #ef4444 !important;
            box-shadow: 0 2px 6px rgba(239,68,68,0.14) !important;
        }
        .card-action-trash-btn:hover svg {
            stroke: #dc2626 !important;
        }

        @media (max-width: 360px) {
            .card-action-btn {
                font-size: 10.5px !important;
                padding: 0 5px !important;
                gap: 3px !important;
            }
        }

        /* Table Action Buttons: Icon-Only (36x36 desktop, 34x34 mobile), Default Gray, Color on Hover */
        .table-action-soil-btn,
        .table-action-pin-btn,
        .table-action-trash-btn {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            max-width: 36px !important;
            min-height: 36px !important;
            max-height: 36px !important;
            border-radius: 11px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            flex-shrink: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #64748b !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-decoration: none !important;
        }
        .table-action-soil-btn svg,
        .table-action-pin-btn svg,
        .table-action-trash-btn svg {
            width: 17px !important;
            height: 17px !important;
            stroke: #64748b !important;
            transition: stroke 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            flex-shrink: 0 !important;
        }
        .table-action-soil-btn:hover {
            background: #f0fdf4 !important;
            border-color: #bbf7d0 !important;
            color: #15803d !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 8px rgba(34,197,94,0.14) !important;
        }
        .table-action-soil-btn:hover svg {
            stroke: #15803d !important;
        }
        .table-action-pin-btn:hover {
            background: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #1d4ed8 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 8px rgba(37,99,235,0.14) !important;
        }
        .table-action-pin-btn:hover svg {
            stroke: #1d4ed8 !important;
        }
        .table-action-trash-btn:hover {
            background: #fef2f2 !important;
            border-color: #fecaca !important;
            color: #ef4444 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 8px rgba(239,68,68,0.18) !important;
        }
        .table-action-trash-btn:hover svg {
            stroke: #dc2626 !important;
        }

        /* Sticky Columns & Responsive Column Dropping in Student Table */
        .student-table-container {
            width: 100% !important;
            overflow-x: auto !important;
            overflow-y: visible !important;
            scrollbar-width: none !important; /* Firefox */
            -ms-overflow-style: none !important;  /* IE/Edge */
        }
        .student-table-container::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .student-table {
            width: 100% !important;
            min-width: 0 !important;
        }

        .col-student-id {
            display: none !important;
        }
        .student-id-subtext {
            display: inline !important;
        }

        /* Default (< 1024px): ID column is dropped, Name is sticky at left: 0 */
        .student-table th.col-sticky-name,
        .student-table td.col-sticky-name {
            position: sticky !important;
            left: 0 !important;
            min-width: 180px !important;
            white-space: nowrap !important;
            box-shadow: 4px 0 8px -2px rgba(0, 0, 0, 0.05) !important;
            z-index: 11 !important;
            background-color: #ffffff !important;
        }
        .student-table th.col-sticky-name {
            z-index: 21 !important;
            background-color: #F1FBF1 !important;
        }
        .student-table th.col-sticky-action,
        .student-table td.col-sticky-action {
            position: sticky !important;
            right: 0 !important;
            width: 144px !important;
            min-width: 144px !important;
            max-width: 144px !important;
            white-space: nowrap !important;
            box-shadow: none !important;
            padding-left: 12px !important;
            padding-right: 12px !important;
            text-align: center !important;
        }
        .student-table td.col-sticky-action > div,
        .student-table td.col-sticky-action .flex {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 6px !important;
            width: 100% !important;
        }
        .student-table th.col-sticky-action {
            z-index: 20 !important;
            background-color: #F1FBF1 !important;
            text-align: center !important;
        }
        .student-table td.col-sticky-action {
            z-index: 10 !important;
            background-color: #ffffff !important;
        }
        .student-table tbody tr:hover td.col-sticky-id,
        .student-table tbody tr:hover td.col-sticky-name,
        .student-table tbody tr:hover td.col-sticky-action {
            background-color: #f7fdf7 !important;
        }
        .dark .student-table th.col-sticky-id,
        .dark .student-table th.col-sticky-name,
        .dark .student-table th.col-sticky-action {
            background-color: #1e293b !important;
        }
        .dark .student-table td.col-sticky-id,
        .dark .student-table td.col-sticky-name,
        .dark .student-table td.col-sticky-action {
            background-color: #0f172a !important;
        }

        /* Responsive Column Dropping Order:
           1. SCANS & PIN MAP: drop first when space tightens below 1024px (reached by Action)
           2. EMAIL: compressed to 10 letters between 768px-899px, and drops when it reaches 10 letters (< 768px)
           3. SECTION: stays visible after Email drops, and only drops when reached by Action (< 550px)
        */
        @media (max-width: 1023px) {
            .col-scans,
            .col-pin-map {
                display: none !important;
            }
        }

        /* Between 768px and 899px: Email stays visible, constrained to 10 letters */
        @media (min-width: 768px) and (max-width: 899px) {
            .email-cell-content {
                max-width: 105px !important; /* ~10 characters */
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                white-space: nowrap !important;
            }
        }

        /* Drop Email when it reaches only 10 letters of space (< 768px) */
        @media (max-width: 767px) {
            .col-email {
                display: none !important;
            }

            /* MOBILE SIDE: Natural static flow without WebKit sticky glitches */
            .student-table th.col-sticky-action,
            .student-table td.col-sticky-action {
                position: static !important;
                left: auto !important;
                right: auto !important;
                box-shadow: none !important;
                width: 134px !important;
                min-width: 134px !important;
                max-width: 134px !important;
                padding-left: 12px !important;
                padding-right: 12px !important;
                text-align: center !important;
            }
            .student-table th.col-sticky-action {
                text-align: center !important;
            }
            .student-table td.col-sticky-action > div,
            .student-table td.col-sticky-action .flex {
                gap: 4px !important;
                justify-content: center !important;
                align-items: center !important;
                width: 100% !important;
            }
            .student-table .table-action-soil-btn,
            .student-table .table-action-pin-btn,
            .student-table .table-action-trash-btn {
                width: 34px !important;
                height: 34px !important;
                min-width: 34px !important;
                max-width: 34px !important;
                min-height: 34px !important;
                max-height: 34px !important;
                border-radius: 10px !important;
            }
            .student-table .table-action-soil-btn svg,
            .student-table .table-action-pin-btn svg,
            .student-table .table-action-trash-btn svg {
                width: 16px !important;
                height: 16px !important;
            }
            .student-table th.col-sticky-name,
            .student-table td.col-sticky-name {
                position: static !important;
                left: auto !important;
                right: auto !important;
                box-shadow: none !important;
                min-width: 135px !important;
            }
        }

        /* Desktop & Tablet (≥ 680px and < 1024px): When ID is hidden, Name is first visible column -> 20px left padding */
        @media (min-width: 680px) and (max-width: 1023px) {
            .student-table th.col-sticky-name,
            .student-table td.col-sticky-name {
                padding-left: 20px !important;
            }
        }

        /* Mobile (< 680px): Name is first visible column, table fits screen */
        @media (max-width: 679px) {
            .student-table-container {
                overflow-x: hidden !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .student-table {
                table-layout: fixed !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
            }
            .student-table th.col-sticky-name,
            .student-table td.col-sticky-name {
                padding-left: 12px !important;
                padding-right: 6px !important;
                width: auto !important;
                overflow: hidden !important;
            }
            .student-table th.col-sticky-action,
            .student-table td.col-sticky-action {
                width: 124px !important;
                min-width: 124px !important;
                max-width: 124px !important;
                padding-left: 4px !important;
                padding-right: 8px !important;
            }
        }

        /* Section stays visible, and only drops if reached by Action (< 550px) */
        @media (max-width: 549px) {
            .col-section {
                display: none !important;
            }
        }

        /* Desktop / Big screens (>= 1024px): ID column is visible at left: 0, Name is at left: 115px */
        @media (min-width: 1024px) {
            .col-student-id {
                display: table-cell !important;
            }
            .student-id-subtext {
                display: none !important;
            }
            .student-table th.col-sticky-id,
            .student-table td.col-sticky-id {
                position: sticky !important;
                left: 0 !important;
                width: 115px !important;
                min-width: 115px !important;
                max-width: 115px !important;
                white-space: nowrap !important;
                padding-left: 20px !important;
            }
            .student-table th.col-sticky-id {
                z-index: 22 !important;
                background-color: #F1FBF1 !important;
            }
            .student-table td.col-sticky-id {
                z-index: 12 !important;
                background-color: #ffffff !important;
            }
            .student-table th.col-sticky-name,
            .student-table td.col-sticky-name {
                left: 115px !important;
            }
        }

        @media (min-width: 1280px) {
            .student-table {
                min-width: 1050px !important;
            }
        }

        /* Record Popups (Soil Scans & Pin Maps) - Modern Responsive & Slim Scrollbar (Matching Soil Scan History Pop up) */
        .record-popup-card {
            background: #ffffff;
            border-radius: 28px;
            width: 100%;
            max-width: 680px;
            height: 75vh;
            max-height: 75vh;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
            border: 1px solid #f1f5f9;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            margin: auto;
            position: relative;
        }
        .record-popup-header {
            padding: 20px 26px 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            flex-shrink: 0;
        }
        .record-popup-body {
            padding: 18px 24px 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            flex: 1 1 auto;
            max-height: 480px;
            box-sizing: border-box;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
            scrollbar-gutter: stable;
            -webkit-overflow-scrolling: touch;
        }
        .record-popup-body.scan-popup-body,
        .record-popup-body.pin-popup-body {
            max-height: none;
        }
        .record-popup-body::-webkit-scrollbar {
            width: 6px;
            display: block !important;
        }
        .record-popup-body::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
            margin: 4px 0;
        }
        .record-popup-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .record-popup-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .record-popup-footer {
            padding: 16px 24px 20px;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 100%;
            box-sizing: border-box;
        }

        .pin-modal-card,
        .scan-modal-card {
            background: #ffffff;
            border: 1.5px solid #f1f5f9;
            border-radius: 18px;
            padding: 14px 18px;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }
        .pin-modal-card:hover,
        .scan-modal-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }
        .pin-modal-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;
            border-radius: 10px;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            flex-shrink: 0;
        }

        @media (max-width: 540px) {
            .record-popup-backdrop {
                padding: 16px 14px !important;
            }
            .record-popup-card {
                border-radius: 24px !important;
                height: 75vh !important;
                max-height: 75vh !important;
                margin: auto !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35) !important;
            }
            .record-popup-header {
                padding: 16px 20px 14px !important;
            }
            .record-popup-header h3 {
                font-size: 17.5px !important;
            }
            .record-popup-body {
                padding: 14px 18px 16px !important;
                gap: 10px !important;
                flex: 1 1 auto !important;
                max-height: none !important;
            }
            .record-popup-footer {
                padding: 12px 18px 14px !important;
                background: #fafafa !important;
            }
            .pin-modal-card,
            .scan-modal-card {
                padding: 12px 13px !important;
                border-radius: 14px !important;
            }
            .pin-modal-icon {
                width: 32px !important;
                height: 32px !important;
                min-width: 32px !important;
            }
        }

    </style>

    {{-- PAGE HEADER & ONLINE STATUS BADGE --}}
    <div class="ui-reveal student-page-header" style="--i:0; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
        <div>
            <h1 class="ui-reveal-text" style="--i:0; font-size:32px; font-weight:800; color:#0f172a; margin:0; line-height:1.2; letter-spacing:-0.02em;">
                Student Records
            </h1>
            <p class="ui-reveal-text" style="--i:0; font-size:13.5px; font-weight:500; color:#94a3b8; margin:4px 0 0 0; line-height:1.4;">
                <span class="ui-reveal-num" style="--i:0" x-data x-init="animateCount($el, {{ (int) $totalRegistered }}, { delay: 400 })">{{ $totalRegistered }}</span> registered students — <span class="ui-reveal-num" style="--i:0" x-data x-init="animateCount($el, {{ (int) $onlineCount }}, { delay: 400 })">{{ $onlineCount }}</span> currently online
            </p>
        </div>

        {{-- RIGHT ACTIONS: ADD STUDENT BUTTON + ONLINE STATUS PILL --}}
        <div class="student-header-actions ui-reveal-text" style="--i:0; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <button
                type="button"
                wire:click="openAddStudentModal"
                style="background: #22c55e !important; color: #ffffff !important; font-weight: 700; font-size: 13.5px; border: none; padding: 12px 24px; border-radius: 16px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;"
                onmouseover="this.style.background='#16a34a';"
                onmouseout="this.style.background='#22c55e';"
                title="Add Student"
            >
                <span style="font-size:18px; line-height:1; font-weight:700;">+</span>
                <span>Add Student</span>
            </button>

            {{-- SECTION RECORDS BUTTON --}}
            <button
                type="button"
                wire:click="openSectionRecordsModal"
                style="background: #ffffff; color: #0f172a; font-weight: 700; font-size: 13.5px; border: 1.5px solid #e2e8f0; padding: 12px 20px; border-radius: 16px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;"
                onmouseover="this.style.background='#f0fdf4'; this.style.borderColor='#86efac'; this.style.color='#15803d';"
                onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e2e8f0'; this.style.color='#0f172a';"
                title="Save as Copy - All Records Per Section"
            >
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                <span>Save as Copy</span>
            </button>

            {{-- ONLINE / OFFLINE STATUS PILL --}}
            <div style="background:#ffffff; border:1px solid #f1f5f9; border-radius:9999px; padding:4px 6px; box-shadow:0 2px 8px rgba(0,0,0,0.04); display:inline-flex; align-items:center; gap:8px; flex-shrink:0;">
                {{-- GREEN ONLINE PILL --}}
                <span style="background:#dcfce7; color:#22c55e; border-radius:9999px; padding:4px 14px; font-size:12.5px; font-weight:700; display:inline-flex; align-items:center; white-space:nowrap;">
                    <span class="ui-reveal-num" style="--i:0" x-data x-init="animateCount($el, {{ (int) $onlineCount }}, { suffix: ' Online', delay: 400 })">{{ $onlineCount }} Online</span>
                </span>

                {{-- VERTICAL DIVIDER --}}
                <span style="width:1px; height:14px; background:#cbd5e1; display:inline-block;"></span>

                {{-- GRAY OFFLINE TEXT --}}
                <span style="color:#64748b; font-size:12.5px; font-weight:600; padding-right:12px; display:inline-flex; align-items:center; white-space:nowrap;">
                    <span class="ui-reveal-num" style="--i:0" x-data x-init="animateCount($el, {{ (int) $offlineCount }}, { suffix: ' Offline', delay: 400 })">{{ $offlineCount }} Offline</span>
                </span>
            </div>
        </div>
    </div>

    {{-- SUCCESS / NOTIFICATION FLASH BANNER --}}
    @if (session()->has('message'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(34, 197, 94, 0.08);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 28px; height: 28px; border-radius: 50%; background: #dcfce7; display: flex; align-items: center; justify-content: center; color: #16a34a; flex-shrink: 0;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span style="font-size: 13.5px; font-weight: 700; color: #15803d;">{{ session('message') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" style="background: none; border: none; color: #86efac; cursor: pointer; padding: 4px; display: flex; align-items: center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- SEARCH AND FILTER CARD --}}
    <div class="ui-reveal" style="--i:1; background: #ffffff; border-radius: 18px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.03); padding: 14px 20px; display: flex; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        {{-- SEARCH INPUT WITH AUTO-SUGGESTIONS --}}
        <div style="position: relative; flex: 1 1 320px; min-width: 290px;" x-data="{ open: true }" @click.outside="open = false">
            <svg style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #94a3b8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input
                wire:key="student-search-input-{{ $showEditModal || $showSuspendModal || $showDeleteModal || $showSoilScansModal || $showPinMapsModal || $showAddStudentModal || $showSectionRecordsModal ? 'modal-open' : 'modal-closed' }}"
                wire:model.live="search"
                @focus="open = true"
                @input="open = true"
                @keydown.escape="$wire.set('search', ''); $el.value = ''; open = false;"
                type="text"
                name="agrico_student_search_filter"
                autocomplete="off"
                placeholder="Search student ID, name, email..."
                style="width: 100%; border: 1px solid #e2e8f0; border-radius: 16px; background: #ffffff; padding: 10px 44px 10px 46px; font-size: 14px; font-weight: 500; color: #1e293b; outline: none; transition: all 0.2s ease; box-sizing: border-box;"
                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3px rgba(68,175,53,0.15)';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
            />
            @if($search)
                <x-search-clear-button wireModel="search" />
            @endif

            {{-- AUTO SUGGESTION DROPDOWN --}}
            @if(!empty($searchSuggestions) && count($searchSuggestions) > 0)
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    style="position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 50; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05); max-height: 300px; overflow-y: auto; padding: 6px 0;"
                >
                    <div style="padding: 6px 14px 4px 14px; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">
                        Suggestions
                    </div>
                    @foreach($searchSuggestions as $u)
                        @php
                            $uName = $u->name ?: 'Student';
                            $uEmail = $u->email ?: '';
                            $uStudentId = $u->student_id ?: $u->formatted_id;
                            $uInitial = strtoupper(substr($uName, 0, 1));
                            $suggestVal = $u->student_id ?: $uName;
                        @endphp
                        <div
                            wire:click="selectSuggestion('{{ addslashes($suggestVal) }}')"
                            @click="open = false"
                            style="display: flex; align-items: center; gap: 10px; padding: 8px 14px; cursor: pointer; transition: background 0.15s ease;"
                            onmouseover="this.style.background='#f8fafc';"
                            onmouseout="this.style.background='transparent';"
                        >
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0;">
                                {{ $uInitial }}
                            </div>
                            <div style="min-width: 0; flex: 1;">
                                <div style="font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $uName }}
                                    @if(!empty($u->student_id))
                                        <span style="font-size: 12px; font-weight: 600; color: #64748b; font-family: monospace; margin-left: 6px;">{{ $u->student_id }}</span>
                                    @endif
                                </div>
                                <div style="font-size: 12px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $uEmail }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- STATUS FILTER DROPDOWN --}}
        <div style="position: relative; flex-shrink: 0;">
            <select
                wire:model.live="statusFilter"
                style="border: 1px solid #e2e8f0; border-radius: 14px; background: #ffffff; padding: 9px 34px 9px 14px; font-size: 13.5px; font-weight: 600; color: #334155; outline: none; appearance: none; cursor: pointer; transition: all 0.2s ease;"
                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3px rgba(68,175,53,0.15)';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
                title="Filter by Status"
            >
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Suspended">Suspended</option>
            </select>
            <div style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- CONNECTIVITY FILTER DROPDOWN --}}
        <div style="position: relative; flex-shrink: 0;">
            <select
                wire:model.live="connectivityFilter"
                style="border: 1px solid #e2e8f0; border-radius: 14px; background: #ffffff; padding: 9px 34px 9px 14px; font-size: 13.5px; font-weight: 600; color: #334155; outline: none; appearance: none; cursor: pointer; transition: all 0.2s ease;"
                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3px rgba(68,175,53,0.15)';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
            >
                <option value="">All Connectivity</option>
                <option value="Online">Online</option>
                <option value="Offline">Offline</option>
            </select>
            <div style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- SECTION FILTER DROPDOWN --}}
        <div style="position: relative; flex-shrink: 0;">
            <select
                wire:model.live="sectionFilter"
                style="border: 1px solid #e2e8f0; border-radius: 14px; background: #ffffff; padding: 9px 34px 9px 14px; font-size: 13.5px; font-weight: 600; color: #334155; outline: none; appearance: none; cursor: pointer; transition: all 0.2s ease;"
                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3px rgba(68,175,53,0.15)';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
                title="Filter by Section"
            >
                <option value="">All Sections</option>
                @foreach($availableSections as $section)
                    <option value="{{ $section }}">{{ $section }}</option>
                @endforeach
            </select>
            <div style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        {{-- ADMIN FILTER DROPDOWN (SUPER ADMIN ONLY) --}}
        @if($isSuperAdmin)
        <div style="position: relative; flex-shrink: 0;">
            <select
                wire:model.live="adminFilter"
                style="border: 1px solid #e2e8f0; border-radius: 14px; background: #ffffff; padding: 9px 34px 9px 14px; font-size: 13.5px; font-weight: 600; color: #334155; outline: none; appearance: none; cursor: pointer; transition: all 0.2s ease;"
                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3px rgba(68,175,53,0.15)';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
                title="Filter by Admin"
            >
                @php
                    $formatAdminName = function(?string $name) {
                        if (empty($name)) return '';
                        $parts = preg_split('/\s+/', trim($name));
                        return (count($parts) >= 2) ? $parts[0] . ' ' . strtoupper(substr($parts[1], 0, 1)) . '.' : $name;
                    };
                @endphp

                {{-- 1. Super Admin at the top --}}
                <option value="{{ $loggedInAdminId }}">{{ $formatAdminName($loggedInAdminName) }} (Super Admin)</option>

                {{-- 2. All Admins second --}}
                <option value="">All Admins</option>

                {{-- 3. Other Admins (excluding Super Admin to avoid duplicate) --}}
                @foreach($availableAdmins as $admin)
                    @php
                        $aId = $admin->Admin_ID ?? $admin->admin_id;
                        $fmtId = \App\Models\Admin::formatId($aId);
                    @endphp
                    @if($fmtId !== $loggedInAdminId && $aId !== $loggedInAdminId)
                        <option value="{{ $aId }}">{{ $formatAdminName($admin->Full_Name ?? $admin->full_name) }}</option>
                    @endif
                @endforeach
            </select>
            <div style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>
        @endif

        {{-- VIEW MODE TOGGLE & RESULTS COUNTER --}}
        <div style="display: inline-flex; align-items: center; gap: 10px; flex-shrink: 0;">
            <div style="background: #f1f5f9; border-radius: 12px; padding: 3px; display: inline-flex; align-items: center; gap: 2px; flex-shrink: 0;">
                <button
                    type="button"
                    wire:click="setViewMode('grid')"
                    style="padding: 6px 14px; border-radius: 10px; border: none; background: {{ $viewMode === 'grid' ? '#ffffff' : 'transparent' }}; color: {{ $viewMode === 'grid' ? '#22c55e' : '#64748b' }}; box-shadow: {{ $viewMode === 'grid' ? '0 1px 3px rgba(0,0,0,0.08)' : 'none' }}; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; transition: all 0.15s ease;"
                    title="Student Cards Grid View"
                >
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                    <span>Cards</span>
                </button>
                <button
                    type="button"
                    wire:click="setViewMode('table')"
                    style="padding: 6px 14px; border-radius: 10px; border: none; background: {{ $viewMode === 'table' ? '#ffffff' : 'transparent' }}; color: {{ $viewMode === 'table' ? '#22c55e' : '#64748b' }}; box-shadow: {{ $viewMode === 'table' ? '0 1px 3px rgba(0,0,0,0.08)' : 'none' }}; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; transition: all 0.15s ease;"
                    title="Student Table View"
                >
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                    <span>Table</span>
                </button>
            </div>

            <div class="ui-reveal-num" style="--i:1; font-size: 13px; font-weight: 600; color: #94a3b8; flex-shrink: 0; white-space: nowrap;" x-data x-init="animateCount($el, {{ (int) $students->total() }}, { suffix: ' results', delay: 600 })">
                {{ $students->total() }} results
            </div>
        </div>
    </div>

    {{-- 1. GRID CARDS VIEW (MATCHING USER-REQUESTED DESIGN) --}}
    @if($viewMode === 'grid')
        <div class="ui-reveal" style="--i:2; display: grid; grid-template-columns: repeat(auto-fill, minmax(285px, 1fr)); gap: 20px; width: 100%; box-sizing: border-box;">
            @forelse($students as $user)
            @php
                $isOnline = (bool) ($user->is_online ?? false);
                $statusLower = strtolower(trim($user->status ?? ''));
                $isSuspended = in_array($statusLower, ['suspended', 'inactive', 'disapproved']);
                $initial = strtoupper(substr($user->name ?: 'S', 0, 1));
            @endphp
            <div wire:key="student-card-{{ $user->id }}" class="student-profile-card">
                
                {{-- CARD TOP HEADER BAR (ONLINE / OFFLINE & SUSPENDED BADGE) --}}
                <div style="width: 100%; display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; min-height: 24px;">
                    {{-- ONLINE / OFFLINE INDICATOR --}}
                    <div style="display: inline-flex; align-items: center; gap: 5px; background: #f8fafc; border: 1px solid #f1f5f9; padding: 3px 9px; border-radius: 9999px;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $isOnline ? '#22c55e' : '#cbd5e1' }}; display: inline-block;"></span>
                        <span style="font-size: 11px; font-weight: 600; color: {{ $isOnline ? '#16a34a' : '#64748b' }};">
                            {{ $isOnline ? 'Online' : 'Offline' }}
                        </span>
                    </div>

                    {{-- SUSPENDED BADGE ON TOP RIGHT --}}
                    @if($isSuspended)
                        <div
                            wire:click="openViewSuspendModal('{{ $user->id }}')"
                            style="display: inline-flex; align-items: center; gap: 5px; background: #fef2f2; border: 1px solid #fecaca; padding: 3px 9px; border-radius: 9999px; cursor: pointer; transition: all 0.2s ease;"
                            onmouseover="this.style.background='#fee2e2';"
                            onmouseout="this.style.background='#fef2f2';"
                            title="Student Suspended: Click to view details & reason"
                        >
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                            <span style="font-size: 11px; font-weight: 700; color: #ef4444; text-transform: uppercase; letter-spacing: 0.02em;">
                                Suspended
                            </span>
                        </div>
                    @endif
                </div>

                {{-- PROFILE PICTURE (NO COVER PHOTO, NO RAINBOW RING - CLEAN & CRISP) --}}
                <div class="student-avatar-wrap">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <span style="font-size: 34px; font-weight: 800; color: #ffffff; user-select: none;">
                            {{ $initial }}
                        </span>
                    @endif
                </div>

                {{-- STUDENT FULL NAME --}}
                <h3 style="font-size: 17.5px; font-weight: 800; color: #0f172a; margin: 12px 0 0 0; line-height: 1.3; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $user->name }}">
                    {{ $user->name }}
                </h3>

                {{-- STUDENT ID, SECTION & EMAIL ADDRESS (REPLACING THE BIO TEXT IN REFERENCE IMAGE) --}}
                <div style="margin-top: 6px; display: flex; flex-direction: column; align-items: center; gap: 4px; width: 100%;">
                    @if(!empty($user->student_id) || !empty($user->section))
                        <div style="display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 13px; font-weight: 700; color: #6b7280; flex-wrap: wrap;">
                            @if(!empty($user->student_id))
                                <span style="font-family: monospace;">{{ $user->student_id }}</span>
                            @endif
                            @if(!empty($user->student_id) && !empty($user->section))
                                <span style="color: #cbd5e1; font-weight: 400;">&middot;</span>
                            @endif
                            @if(!empty($user->section))
                                <span>Section {{ $user->section }}</span>
                            @endif
                        </div>
                    @endif

                    <span style="font-size: 12.5px; color: #64748b; font-weight: 500; font-family: monospace; max-width: 95%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $user->email }}">
                        {{ $user->email }}
                    </span>
                </div>

                {{-- RECESSED STATS BOX (SCAN COUNT & SOIL PIN MAP - REPLACING LIKES, POSTS, VIEWS) --}}
                <div class="student-stats-box">
                    {{-- SCAN COUNT --}}
                    <div>
                        <div class="ui-reveal-num" style="--i:2; font-size: 20px; font-weight: 800; color: #0f172a; line-height: 1.1;" x-data x-init="animateCount($el, {{ (int) ($user->soil_scans_count ?? 0) }}, { delay: 1000 })">
                            {{ $user->soil_scans_count ?? 0 }}
                        </div>
                        <div style="font-size: 11.5px; font-weight: 600; color: #64748b; margin-top: 3px;">
                            Scan Count
                        </div>
                    </div>

                    {{-- SOIL PIN MAP --}}
                    <div>
                        <div class="ui-reveal-num" style="--i:2; font-size: 20px; font-weight: 800; color: #0f172a; line-height: 1.1;" x-data x-init="animateCount($el, {{ (int) ($user->location_mappings_count ?? 0) }}, { delay: 1000 })">
                            {{ $user->location_mappings_count ?? 0 }}
                        </div>
                        <div style="font-size: 11.5px; font-weight: 600; color: #64748b; margin-top: 3px;">
                            Soil Pin Map
                        </div>
                    </div>
                </div>

                {{-- BOTTOM ICONS: VIEW FOR SOIL RECORDS & PIN MAP RECORDS + REMOVE STUDENT BUTTON --}}
                <div class="card-action-bar">
                    {{-- 1. VIEW SOIL SCANS BUTTON --}}
                    <button
                        wire:click="openStudentSoilScansModal('{{ $user->id }}')"
                        type="button"
                        class="card-action-btn soil-btn"
                        title="View Soil Scans for {{ $user->name }}"
                    >
                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Soil Scan</span>
                    </button>

                    {{-- 2. VIEW PIN MAP RECORDS BUTTON --}}
                    <button
                        wire:click="openStudentPinMapsModal('{{ $user->id }}')"
                        type="button"
                        class="card-action-btn pin-btn"
                        title="View Pin Map for {{ $user->name }}"
                    >
                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Pin Map</span>
                    </button>

                    {{-- 3. REMOVE STUDENT BUTTON (REMOVES FROM STUDENT RECORDS ONLY) --}}
                    <button
                        wire:click="openRemoveStudentModal('{{ $user->id }}')"
                        type="button"
                        class="agrico-delete-btn"
                        title="Remove from Student Records"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; padding: 64px 24px; text-align: center; background: #ffffff; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;">
                    <div style="width: 56px; height: 56px; border-radius: 18px; background: #f0fdf4; border: 1px solid #dcfce7; display: flex; align-items: center; justify-content: center; color: #22c55e; box-shadow: 0 4px 14px rgba(22,163,74,0.12);">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    <div style="font-size: 16px; font-weight: 800; color: #0f172a; margin-top: 4px;">
                        No student records found
                    </div>
                    <div style="font-size: 13px; font-weight: 500; color: #64748b; max-width: 380px; line-height: 1.4;">
                        @if($search || $statusFilter || $connectivityFilter || $sectionFilter || $selectedDate)
                            No student records match your active search or filter criteria.<br>Try adjusting or clearing your filters.
                        @else
                            No active student records found. Approve pending student requests or add students to display them here.
                        @endif
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        {{-- PAGINATION FOOTER (FOR GRID VIEW) --}}
        <div style="background:#ffffff; border-radius:24px; border:1px solid #f1f5f9; padding:14px 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; box-shadow:0 4px 20px -2px rgba(0,0,0,0.05); margin-top:24px;">
            {{-- LEFT SIDE: STUDENTS PER PAGE & RANGE --}}
            <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#64748b; flex-wrap:wrap;">
                <span style="white-space:nowrap; font-weight:500;">Students per page:</span>
                <select
                    wire:model.live="perPage"
                    style="border:1px solid #e2e8f0; background:#fff; border-radius:8px; padding:4px 8px; font-size:13px; color:#334155; font-weight:600; outline:none; cursor:pointer;"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="12">12</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
                <span style="color:#94a3b8; font-size:13px; margin-left:4px; white-space:nowrap; font-weight:500;">
                    {{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }} of {{ $students->total() }}
                </span>
            </div>

            {{-- RIGHT SIDE: PREV, PAGE NUMBERS, NEXT BUTTONS --}}
            <div style="display:flex; align-items:center; gap:6px; flex-wrap:nowrap;">
                @if ($students->hasPages())
                    @if ($students->onFirstPage())
                        <button disabled style="padding:5px 12px; border-radius:8px; border:1px solid #f1f5f9; background:#f8fafc; color:#cbd5e1; font-size:12.5px; font-weight:600; cursor:not-allowed;">Prev</button>
                    @else
                        <button wire:click="previousPage" style="padding:5px 12px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; color:#475569; font-size:12.5px; font-weight:600; cursor:pointer; transition:all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569';">Prev</button>
                    @endif

                    @php
                        $currentPage = $students->currentPage();
                        $lastPage = $students->lastPage();

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
                        <button wire:click="gotoPage({{ max(1, $startPage - 1) }})" style="min-width:24px; height:32px; background:transparent; border:none; color:#64748b; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; padding:0 4px; transition:color 0.15s ease;" onmouseover="this.style.color='#22c55e';" onmouseout="this.style.color='#64748b';" title="Page {{ max(1, $startPage - 1) }}">...</button>
                    @endif

                    @foreach ($students->getUrlRange($startPage, $endPage) as $page => $url)
                        @if ($page == $currentPage)
                            <button wire:click="gotoPage({{ $page }})" style="width:32px; height:32px; border-radius:8px; background:#22c55e !important; color:#fff !important; font-size:13px; font-weight:700; border:none; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(34,197,94,0.35);">{{ $page }}</button>
                        @else
                            <button wire:click="gotoPage({{ $page }})" style="width:32px; height:32px; border-radius:8px; background:#fff; border:1px solid #e2e8f0; color:#334155; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; transition:all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#334155';">{{ $page }}</button>
                        @endif
                    @endforeach

                    @if ($endPage < $lastPage)
                        <button wire:click="gotoPage({{ min($lastPage, $endPage + 1) }})" style="min-width:24px; height:32px; background:transparent; border:none; color:#64748b; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; padding:0 4px; transition:color 0.15s ease;" onmouseover="this.style.color='#22c55e';" onmouseout="this.style.color='#64748b';" title="Page {{ min($lastPage, $endPage + 1) }}">...</button>
                    @endif

                    @if ($students->hasMorePages())
                        <button wire:click="nextPage" style="padding:5px 12px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; color:#475569; font-size:12.5px; font-weight:600; cursor:pointer; transition:all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569';">Next</button>
                    @else
                        <button disabled style="padding:5px 12px; border-radius:8px; border:1px solid #f1f5f9; background:#f8fafc; color:#cbd5e1; font-size:12.5px; font-weight:600; cursor:not-allowed;">Next</button>
                    @endif
                @else
                    <button disabled style="padding:5px 12px; border-radius:8px; border:1px solid #f1f5f9; background:#f8fafc; color:#cbd5e1; font-size:12.5px; font-weight:600; cursor:not-allowed;">Prev</button>
                    <button style="width:32px; height:32px; border-radius:8px; background:#22c55e !important; color:#fff !important; font-size:13px; font-weight:700; border:none; cursor:default; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(34,197,94,0.35);">1</button>
                    <button disabled style="padding:5px 12px; border-radius:8px; border:1px solid #f1f5f9; background:#f8fafc; color:#cbd5e1; font-size:12.5px; font-weight:600; cursor:not-allowed;">Next</button>
                @endif
            </div>
        </div>
    @endif

    {{-- 2. TABLE VIEW (ACCESSIBLE VIA VIEW SWITCHER) --}}
    @if($viewMode === 'table')
    <div class="ui-reveal bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden" style="--i:2">
        {{-- CARD HEADER --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
            <h2 class="ui-reveal-text text-base font-bold text-gray-900 dark:text-slate-100" style="--i:2">
                Student Records (<span class="ui-reveal-num" style="--i:2" x-data x-init="animateCount($el, {{ (int) ($students->total() ?? 0) }}, { delay: 800 })">{{ $students->total() ?? 0 }}</span>)
            </h2>
        </div>

        <div class="overflow-x-auto student-table-container no-scrollbar">
            <table class="w-full student-table">
                <thead>
                    <tr class="bg-[#F1FBF1] dark:bg-slate-800/80 border-b border-gray-100 dark:border-slate-800">
                        {{-- STUDENT ID --}}
                        <th
                            wire:click="sortBy('student_id')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-sticky-id col-student-id"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">STUDENT ID</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'student_id')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- STUDENT NAME --}}
                        <th
                            wire:click="sortBy('name')"
                            class="px-4 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-sticky-name"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">STUDENT NAME</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'name')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- SECTION --}}
                        <th
                            wire:click="sortBy('section')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-section"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">SECTION</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'section')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- EMAIL --}}
                        <th
                            wire:click="sortBy('email')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-email"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">EMAIL</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'email')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- SCANS --}}
                        <th
                            wire:click="sortBy('soil_scans_count')"
                            class="px-5 py-4 text-center text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-scans"
                        >
                            <div class="flex items-center justify-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">SCANS</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'soil_scans_count')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- PIN MAP --}}
                        <th
                            wire:click="sortBy('location_mappings_count')"
                            class="px-5 py-4 text-center text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-pin-map"
                        >
                            <div class="flex items-center justify-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">PIN MAP</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'location_mappings_count')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- ACTIONS --}}
                        <th class="px-3 py-4 text-center text-xs font-bold text-gray-500 whitespace-nowrap col-sticky-action">
                            ACTIONS
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $user)
                    @php
                        $isOnline = (bool) ($user->is_online ?? false);
                        $scans = $user->soil_scans_count ?? 0;
                        $statusLower = strtolower(trim($user->status ?? ''));
                        $isSuspended = in_array($statusLower, ['suspended', 'inactive', 'disapproved']);
                    @endphp
                    <tr wire:key="student-table-row-{{ $user->id }}" class="border-b border-gray-100 hover:bg-green-50/40 transition">
                        {{-- STUDENT ID --}}
                        <td class="px-5 py-5 whitespace-nowrap text-sm font-semibold font-mono text-gray-500 col-sticky-id col-student-id">
                            {{ $user->student_id ?: '—' }}
                        </td>

                        {{-- FULL NAME --}}
                        <td class="px-4 py-5 whitespace-nowrap col-sticky-name">
                            <div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span style="font-weight:700; font-size:14px; color:#475569; white-space:nowrap;" title="{{ $user->name }}">
                                        {{ $user->name }}
                                    </span>
                                    @if($isSuspended)
                                        <span
                                            wire:click="openViewSuspendModal('{{ $user->id }}')"
                                            style="display:inline-flex; align-items:center; gap:4px; background:#fef2f2; border:1px solid #fecaca; padding:2px 7px; border-radius:9999px; cursor:pointer; font-size:10px; font-weight:700; color:#ef4444; text-transform:uppercase;"
                                            title="Suspended: Click to view details"
                                        >
                                            <span style="width:5px; height:5px; border-radius:50%; background:#ef4444; display:inline-block;"></span>
                                            Suspended
                                        </span>
                                    @endif
                                </div>
                                <div style="display:flex; align-items:center; gap:6px; font-size:12px; color:#94a3b8; font-weight:500; margin-top:2px;">
                                    @if(!empty($user->student_id))
                                        <span class="student-id-subtext" style="font-family:monospace; font-weight:600; color:#64748b;">{{ $user->student_id }} </span>
                                    @endif
                                    <span>{{ $isOnline ? 'Online now' : 'Offline' }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- SECTION --}}
                        <td class="px-5 py-5 whitespace-nowrap text-sm font-semibold text-gray-500 col-section">
                            {{ $user->section ?: '—' }}
                        </td>

                        {{-- EMAIL --}}
                        <td class="px-5 py-5 text-sm text-gray-500 font-bold whitespace-nowrap font-mono col-email">
                            <div class="email-cell-content" title="{{ $user->email }}">
                                {{ $user->email }}
                            </div>
                        </td>

                        {{-- SCANS --}}
                        <td class="px-5 py-5 whitespace-nowrap text-center text-sm font-bold text-gray-500 col-scans">
                            <span class="ui-reveal-num" style="--i:2" x-data x-init="animateCount($el, {{ (int) $scans }}, { delay: 1000 })">{{ $scans }}</span>
                        </td>

                        {{-- PIN MAP --}}
                        <td class="px-5 py-5 whitespace-nowrap text-center text-sm font-bold text-gray-500 col-pin-map">
                            <span class="ui-reveal-num" style="--i:2" x-data x-init="animateCount($el, {{ (int) ($user->location_mappings_count ?? 0) }}, { delay: 1000 })">{{ $user->location_mappings_count ?? 0 }}</span>
                        </td>

                        {{-- ACTIONS --}}
                        <td class="px-3 py-4 whitespace-nowrap text-center col-sticky-action">
                            <div style="display:flex; justify-content:center; align-items:center; gap:6px;">
                                {{-- 1. VIEW SOIL SCANS BUTTON --}}
                                <button
                                    wire:click="openStudentSoilScansModal('{{ $user->id }}')"
                                    type="button"
                                    class="table-action-soil-btn"
                                    title="View Soil Scans for {{ $user->name }}"
                                >
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M3 7V5a2 2 0 0 1 2-2h2"/>
                                        <path d="M17 3h2a2 2 0 0 1 2 2v2"/>
                                        <path d="M21 17v2a2 2 0 0 1-2 2h-2"/>
                                        <path d="M7 21H5a2 2 0 0 1-2-2v-2"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>

                                {{-- 2. VIEW PIN MAP RECORDS BUTTON --}}
                                <button
                                    wire:click="openStudentPinMapsModal('{{ $user->id }}')"
                                    type="button"
                                    class="table-action-pin-btn"
                                    title="View Pin Map for {{ $user->name }}"
                                >
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                </button>

                                {{-- 3. REMOVE STUDENT BUTTON (REMOVES FROM STUDENT RECORDS ONLY) --}}
                                <button
                                    wire:click="openRemoveStudentModal('{{ $user->id }}')"
                                    type="button"
                                    class="agrico-delete-btn"
                                    title="Remove from Student Records"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="100%" style="padding: 56px 20px; text-align: center; border-bottom: none;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;">
                                <div style="width: 56px; height: 56px; border-radius: 18px; background: #f0fdf4; border: 1px solid #dcfce7; display: flex; align-items: center; justify-content: center; color: #22c55e; box-shadow: 0 4px 14px rgba(22,163,74,0.12);">
                                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                    </svg>
                                </div>
                                <div style="font-size: 16px; font-weight: 800; color: #0f172a; margin-top: 4px;">
                                    No student records found
                                </div>
                                <div style="font-size: 13px; font-weight: 500; color: #64748b; max-width: 380px; line-height: 1.4;">
                                    @if($search || $statusFilter || $connectivityFilter || $sectionFilter || $selectedDate)
                                        No student records match your active search or filter criteria.<br>Try adjusting or clearing your filters.
                                    @else
                                        No active student records found. Approve pending student requests or add students to display them here.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION FOOTER (INSIDE TABLE CARD - EXACTLY MATCHING MANAGE USERS) --}}
        <div style="padding: 14px 20px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: #fff;">
            {{-- LEFT SIDE: STUDENTS PER PAGE & RANGE --}}
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; flex-wrap: wrap;">
                <span style="white-space: nowrap; font-weight: 500;">Students per page:</span>
                <select
                    wire:model.live="perPage"
                    style="border: 1px solid #e2e8f0; background: #fff; border-radius: 8px; padding: 4px 8px; font-size: 13px; color: #334155; font-weight: 600; outline: none; cursor: pointer;"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="12">12</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
                <span style="color: #94a3b8; font-size: 13px; margin-left: 4px; white-space: nowrap; font-weight: 500;">
                    {{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }} of {{ $students->total() }}
                </span>
            </div>

            {{-- RIGHT SIDE: PREV, PAGE NUMBERS, NEXT BUTTONS (MATCHING MANAGE USERS) --}}
            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: nowrap;">
                @if ($students->hasPages())
                    @if ($students->onFirstPage())
                        <button disabled style="padding: 5px 12px; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; color: #cbd5e1; font-size: 12.5px; font-weight: 600; cursor: not-allowed;">Prev</button>
                    @else
                        <button wire:click="previousPage" style="padding: 5px 12px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569';">Prev</button>
                    @endif

                    @php
                        $currentPage = $students->currentPage();
                        $lastPage = $students->lastPage();

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
                        <button wire:click="gotoPage({{ max(1, $startPage - 1) }})" style="min-width: 24px; height: 32px; background: transparent; border: none; color: #64748b; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; transition: color 0.15s ease;" onmouseover="this.style.color='#22c55e';" onmouseout="this.style.color='#64748b';" title="Page {{ max(1, $startPage - 1) }}">...</button>
                    @endif

                    @foreach ($students->getUrlRange($startPage, $endPage) as $page => $url)
                        @if ($page == $currentPage)
                            <button wire:click="gotoPage({{ $page }})" style="width: 32px; height: 32px; border-radius: 8px; background: #22c55e !important; color: #fff !important; font-size: 13px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(34,197,94,0.35);">{{ $page }}</button>
                        @else
                            <button wire:click="gotoPage({{ $page }})" style="width: 32px; height: 32px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; color: #334155; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#334155';">{{ $page }}</button>
                        @endif
                    @endforeach

                    @if ($endPage < $lastPage)
                        <button wire:click="gotoPage({{ min($lastPage, $endPage + 1) }})" style="min-width: 24px; height: 32px; background: transparent; border: none; color: #64748b; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; transition: color 0.15s ease;" onmouseover="this.style.color='#22c55e';" onmouseout="this.style.color='#64748b';" title="Page {{ min($lastPage, $endPage + 1) }}">...</button>
                    @endif

                    @if ($students->hasMorePages())
                        <button wire:click="nextPage" style="padding: 5px 12px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569';">Next</button>
                    @else
                        <button disabled style="padding: 5px 12px; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; color: #cbd5e1; font-size: 12.5px; font-weight: 600; cursor: not-allowed;">Next</button>
                    @endif
                @else
                    <button disabled style="padding: 5px 12px; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; color: #cbd5e1; font-size: 12.5px; font-weight: 600; cursor: not-allowed;">Prev</button>
                    <button style="width: 32px; height: 32px; border-radius: 8px; background: #22c55e !important; color: #fff !important; font-size: 13px; font-weight: 700; border: none; cursor: default; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(34,197,94,0.35);">1</button>
                    <button disabled style="padding: 5px 12px; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; color: #cbd5e1; font-size: 12.5px; font-weight: 600; cursor: not-allowed;">Next</button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- POP-UP MODAL 1: VIEW SOIL RECORDS (POP-UP RECORDS)       --}}
    {{-- ======================================================== --}}
    @if($showSoilScansModal && $selectedStudentForScans)
    <div
        class="record-popup-backdrop fixed inset-0 z-50 flex items-center justify-center"
        style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.65); backdrop-filter:blur(6px); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px;"
        wire:click.self="closeStudentSoilScansModal"
    >
        <div class="modal-animated-card record-popup-card">
            
            {{-- MODAL HEADER --}}
            <div class="record-popup-header">
                <div style="min-width:0; flex:1; padding-right:10px;">
                    <h3 style="font-size:17.5px; font-weight:800; color:#0f172a; margin:0;">
                        Soil Scan Records
                    </h3>
                    <p style="font-size:12.5px; color:#64748b; font-weight:500; margin:3px 0 0 0; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <span>Student: <strong style="color:#1e293b;">{{ $selectedStudentForScans->name }}</strong></span>
                        @if(!empty($selectedStudentForScans->student_id))
                            <span style="font-family:monospace; font-weight:700;">ID: {{ $selectedStudentForScans->student_id }}</span>
                        @endif
                    </p>
                </div>

                <x-close-button wire:click="closeStudentSoilScansModal" />
            </div>

            {{-- MODAL BODY (SCROLLABLE LIST: 3 VISIBLE FIRST) --}}
            <div class="record-popup-body scan-popup-body">
                @forelse($studentScans as $scan)
                @php
                    $soil = $scan->soil;
                    $hexColor = $scan->effective_hex_color;
                    $soilName = $scan->effective_soil_name;
                    $scanDate = $scan->scan_date ? $scan->scan_date->format('M d, Y h:i A') : ($scan->created_at ? $scan->created_at->format('M d, Y h:i A') : 'N/A');
                    $soilDescription = $scan->effective_description ?: ($soil?->effective_description ?: '');
                @endphp
                <div class="scan-modal-card">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                        <div style="display:flex; align-items:flex-start; gap:10px; min-width:0; flex:1;">
                            {{-- COLOR SWATCH --}}
                            <div style="width:36px; height:36px; min-width:36px; border-radius:10px; background:{{ $hexColor }}; border:2px solid #ffffff; box-shadow:0 3px 8px rgba(0,0,0,0.15); flex-shrink:0; margin-top:2px;"></div>

                            <div style="min-width:0; flex:1;">
                                {{-- SOIL NAME & FULL CODE --}}
                                <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                    <h4 style="font-size:14px; font-weight:800; color:#0f172a; margin:0; line-height:1.3; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $soilName }}">
                                        {{ $soilName }}
                                    </h4>
                                    @if(!empty($soil?->full_code))
                                        <span style="color:#6b7280; font-size:12px; font-weight:700;">
                                            {{ $soil->full_code }}
                                        </span>
                                    @endif
                                </div>

                                {{-- DATE / TIME --}}
                                <div style="font-size:11.5px; color:#64748b; margin-top:2px; font-weight:500; white-space:nowrap;">
                                    {{ $scanDate }}
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: ID + THUMBNAIL --}}
                        @if($scan->scan_image_url)
                            <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                                <div style="width:32px; height:32px; border-radius:8px; overflow:hidden; border:1px solid #e2e8f0; flex-shrink:0;">
                                    <img src="{{ $scan->scan_image_url }}" alt="Scan thumbnail" style="width:100%; height:100%; object-fit:cover;" onerror="this.parentElement.parentElement.style.display='none';">
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- DESCRIPTION & SCAN NOTE (IF AVAILABLE) --}}
                    @if(!empty($soilDescription) || !empty($scan->scan_note))
                        <div style="margin-top:8px; padding-top:8px; border-top:1px dashed #f1f5f9; display:flex; flex-direction:column; gap:3px;">
                            @if(!empty($soilDescription))
                                <div style="font-size:12px; color:#475569; line-height:1.4;" class="truncate" title="{{ $soilDescription }}">
                                    {{ $soilDescription }}
                                </div>
                            @endif
                            @if(!empty($scan->scan_note))
                                <div style="font-size:11.5px; color:#64748b; font-style:italic; line-height:1.4;" class="truncate" title="{{ $scan->scan_note }}">
                                    "{{ $scan->scan_note }}"
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                @empty
                <div style="padding: 56px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: auto;">
                    <div style="width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(145deg, #f0fdf4 0%, #dcfce7 100%); border: 1.5px solid #bbf7d0; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #16a34a; box-shadow: 0 8px 24px -4px rgba(34, 197, 94, 0.2);">
                        <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M3 7V5a2 2 0 0 1 2-2h2"/>
                            <path d="M17 3h2a2 2 0 0 1 2 2v2"/>
                            <path d="M21 17v2a2 2 0 0 1-2 2h-2"/>
                            <path d="M7 21H5a2 2 0 0 1-2-2v-2"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <h4 style="font-size: 16.5px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; letter-spacing: -0.015em;">No Soil Scans Found</h4>
                    <p style="font-size: 13px; font-weight: 500; color: #64748b; margin: 0; line-height: 1.5; max-width: 300px;">
                        This student has not performed any soil scans yet.
                    </p>
                    <span style="margin-top: 14px; padding: 4px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 11.5px; font-weight: 600; color: #94a3b8; display: inline-flex; align-items: center; gap: 5px;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #cbd5e1;"></span>
                        <span>0 Records Logged</span>
                    </span>
                </div>
                @endforelse
            </div>

            {{-- MODAL FOOTER --}}
            <div class="record-popup-footer" style="display: flex; gap: 12px; width: 100%; box-sizing: border-box;">
                <button
                    type="button"
                    wire:click="closeStudentSoilScansModal"
                    class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                    title="Cancel"
                >
                    Cancel
                </button>
                <a
                    href="{{ route('soil.manage') }}"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center flex items-center justify-center whitespace-nowrap min-h-[44px]"
                    style="background: #22c55e !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); text-decoration: none; border: none;"
                    onmouseover="this.style.background='#16a34a'"
                    onmouseout="this.style.background='#22c55e'"
                    title="Manage Soil"
                >
                    Manage Soil
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- POP-UP MODAL 2: VIEW PIN MAP RECORDS (POP-UP RECORDS)    --}}
    {{-- ======================================================== --}}
    @if($showPinMapsModal && $selectedStudentForPins)
    <div
        class="record-popup-backdrop fixed inset-0 z-50 flex items-center justify-center"
        style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.65); backdrop-filter:blur(6px); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px;"
        wire:click.self="closeStudentPinMapsModal"
    >
        <div class="modal-animated-card record-popup-card">
            
            {{-- MODAL HEADER (NO COUNT BADGE) --}}
            <div class="record-popup-header">
                <div style="min-width:0; flex:1; padding-right:10px;">
                    <h3 style="font-size:17.5px; font-weight:800; color:#0f172a; margin:0;">
                        Soil Pin Map Records
                    </h3>
                    <p style="font-size:12.5px; color:#64748b; font-weight:500; margin:3px 0 0 0; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <span>Student: <strong style="color:#1e293b;">{{ $selectedStudentForPins->name }}</strong></span>
                        @if(!empty($selectedStudentForPins->student_id))
                            <span style="font-family:monospace; font-weight:700;">ID: {{ $selectedStudentForPins->student_id }}</span>
                        @endif
                    </p>
                </div>

                <x-close-button wire:click="closeStudentPinMapsModal" />
            </div>

            {{-- MODAL BODY (SCROLLABLE LIST: 3 VISIBLE FIRST) --}}
            <div class="record-popup-body pin-popup-body">
                @forelse($studentPins as $pin)
                @php
                    $pinDate = $pin->timestamp ? \Carbon\Carbon::parse($pin->timestamp)->format('M d, Y h:i A') : ($pin->created_at ? $pin->created_at->format('M d, Y h:i A') : 'N/A');
                    $soilName = $pin->soil?->effective_soil_name ?? ($pin->soilScan?->effective_soil_name ?? 'Soil Specimen');
                    $hexColor = $pin->soil?->effective_hex_color ?? ($pin->soilScan?->effective_hex_color ?? '#22c55e');
                @endphp
                <div class="pin-modal-card">
                    {{-- TOP ROW: PIN ICON + BARANGAY + COORD + ACTIVE BADGE --}}
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                        <div style="display:flex; align-items:center; gap:10px; min-width:0; flex:1;">
                            <div class="pin-modal-icon">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 2a8 8 0 00-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 00-8-8z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>

                            <div style="min-width:0; flex:1;">
                                <h4 style="font-size:14px; font-weight:800; color:#0f172a; margin:0; line-height:1.3; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $pin->barangay ?: 'Unknown Barangay' }}">
                                    {{ $pin->barangay ?: 'Unknown Barangay' }}
                                </h4>
                                <span style="background:#f1f5f9; color:#475569; font-family:monospace; font-size:10.5px; font-weight:700; padding:1px 7px; border-radius:6px; display:inline-block; margin-top:2px;">
                                    {{ number_format($pin->latitude, 5) }}, {{ number_format($pin->longitude, 5) }}
                                </span>
                            </div>
                        </div>

                        {{-- STATUS BADGE --}}
                        <span style="background:#dcfce7; color:#15803d; font-size:11px; font-weight:700; padding:3px 9px; border-radius:9999px; flex-shrink:0;">
                            {{ ucfirst($pin->status ?: 'Active') }}
                        </span>
                    </div>

                    {{-- BOTTOM ROW: DATE & TIME + SOIL SPECIMEN NAME --}}
                    <div style="font-size:11.5px; color:#64748b; margin-top:8px; padding-top:8px; border-top:1px dashed #f1f5f9; display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap;">
                        <span style="white-space:nowrap; font-weight:500;">
                            {{ $pinDate }}
                        </span>
                        <span style="display:inline-flex; align-items:center; gap:5px; font-weight:600; color:#334155; max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $soilName }}">
                            <span style="width:8px; height:8px; border-radius:50%; background:{{ $hexColor }}; display:inline-block; flex-shrink:0; border:1px solid rgba(0,0,0,0.12);"></span>
                            <span>{{ $soilName }}</span>
                        </span>
                    </div>
                </div>
                @empty
                <div style="padding: 56px 20px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: auto;">
                    <div style="width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(145deg, #eff6ff 0%, #dbeafe 100%); border: 1.5px solid #bfdbfe; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #2563eb; box-shadow: 0 8px 24px -4px rgba(37, 99, 235, 0.2);">
                        <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <h4 style="font-size: 16.5px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; letter-spacing: -0.015em;">No Pin Map Records Found</h4>
                    <p style="font-size: 13px; font-weight: 500; color: #64748b; margin: 0; line-height: 1.5; max-width: 320px;">
                        This student has not submitted any soil location pin map entries yet.
                    </p>
                    <span style="margin-top: 14px; padding: 4px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 11.5px; font-weight: 600; color: #94a3b8; display: inline-flex; align-items: center; gap: 5px;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #cbd5e1;"></span>
                        <span>0 Pins Mapped</span>
                    </span>
                </div>
                @endforelse
            </div>

            {{-- MODAL FOOTER --}}
            <div class="record-popup-footer" style="display: flex; gap: 12px; width: 100%; box-sizing: border-box;">
                <button
                    type="button"
                    wire:click="closeStudentPinMapsModal"
                    class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                    title="Cancel"
                >
                    Cancel
                </button>
                <a
                    href="{{ route('map.manage') }}"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center flex items-center justify-center whitespace-nowrap min-h-[44px]"
                    style="background: #22c55e !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); text-decoration: none; border: none;"
                    onmouseover="this.style.background='#16a34a'"
                    onmouseout="this.style.background='#22c55e'"
                    title="Manage Map"
                >
                    Manage Map
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- EDIT STUDENT MODAL (2-STEP VERIFICATION)                 --}}
    {{-- ======================================================== --}}
    @if($showEditModal)
    <div
        x-data
        class="fixed inset-0 z-50 flex items-center justify-center modal-animated-backdrop"
        style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; min-width: 100%; height: 100vh; height: 100dvh; min-height: 100%; margin: 0; box-sizing: border-box; background: rgba(0,0,0,0.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999;"
        wire:key="student-edit-modal-wrapper-{{ $isVerified ? 'verified' : 'unverified' }}"
    >
        @if(!$isVerified)
            {{-- STEP 1: ADMIN VERIFICATION REQUIRED MODAL --}}
            <form
                wire:submit.prevent="verifyAdminPassword"
                wire:key="student-edit-modal-step-1"
                class="modal-animated-card"
                style="background:#ffffff; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); width:100%; max-width:440px; margin:0 16px; overflow:hidden; position:relative;"
            >
                <div style="padding:28px 28px 12px 28px; display:flex; align-items:flex-start; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:14px;">
                        <div style="width:44px; height:44px; border-radius:14px; background:#fef3c7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="22" height="22" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0; line-height:1.2;">
                                Admin Verification Required
                            </h3>
                            <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:3px 0 0 0;">
                                Security authorization step
                            </p>
                        </div>
                    </div>

                    <x-close-button wire:click="closeEditModal" />
                </div>

                <div style="padding:12px 28px 20px 28px; display:flex; flex-direction:column; gap:16px;">
                    <p style="font-size:13px; color:#64748b; font-weight:400; line-height:1.5; margin:0;">
                        To protect student credentials, please enter your <strong style="color:#1e293b; font-weight:700;">admin password</strong> to authorize editing account details for <strong style="color:#0f172a; font-weight:700;">Student</strong>.
                    </p>

                    <div x-data="{ showPass: false }">
                        <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">
                            Your Admin Password
                        </label>
                        @php $hasAdminError = $errors->has('adminPassword') || !empty($adminPasswordError); @endphp
                        <div style="position:relative; width:100%;">
                            <input
                                wire:model="adminPassword"
                                type="text"
                                autocomplete="off"
                                data-lpignore="true"
                                data-form-type="other"
                                name="agrico_student_admin_sec_pin"
                                placeholder="Enter your password..."
                                :class="showPass ? 'unmasked-password-input' : 'masked-password-input'"
                                style="width:100%; height:44px; box-sizing:border-box; border:1.5px solid {{ $hasAdminError ? '#ef4444' : '#cbd5e1' }}; border-radius:12px; padding:0 42px 0 14px; font-size:13.5px; color:#0f172a; background:#ffffff; outline:none; transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                            />
                            <button
                                type="button"
                                @click="showPass = !showPass"
                                style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px;"
                                :title="showPass ? 'Hide Password' : 'Show Password'"
                            >
                                <svg x-show="!showPass" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                <svg x-show="showPass" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('adminPassword') <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:6px; display:block;">{{ $message }}</span> @enderror
                        @if($adminPasswordError) <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:6px; display:block;">{{ $adminPasswordError }}</span> @endif
                    </div>
                </div>

                <div style="padding:16px 24px 20px 24px; display:flex; gap:12px; background:#ffffff; border-top:1px solid #f1f5f9; width:100%; box-sizing:border-box; align-items:center;">
                    <button
                        type="button"
                        wire:click="closeEditModal"
                        class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center whitespace-nowrap min-h-[44px]"
                        style="background: #f59e0b !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35); border: none;"
                        onmouseover="this.style.background='#d97706'"
                        onmouseout="this.style.background='#f59e0b'"
                        title="Authorize & Continue"
                    >
                        Authorize & Continue
                    </button>
                </div>
            </form>

        @else
            {{-- STEP 2: EDIT STUDENT DETAILS FORM MODAL --}}
            <form
                wire:submit.prevent="saveUser"
                wire:key="student-edit-modal-step-2"
                class="modal-animated-card"
                style="background:#ffffff; border-radius:28px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.3); border:1px solid #f1f5f9; width:100%; max-width:680px; height:75vh; max-height:75vh; margin:auto; display:flex; flex-direction:column; overflow:hidden; position:relative;"
            >
                <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 26px 16px; border-bottom:1px solid #f1f5f9; flex-shrink:0; background:#ffffff;">
                    <div>
                        <h3 style="font-size:17.5px; font-weight:800; color:#0f172a; margin:0; letter-spacing:-0.01em;">Edit Student Details</h3>
                        <p style="font-size:12px; color:#64748b; margin:2px 0 0 0;">Update student record and academic information</p>
                    </div>
                    <x-close-button wire:click="closeEditModal" />
                </div>

                <div style="padding:18px 24px 20px; display:flex; flex-direction:column; gap:14px; overflow-y:auto; flex:1 1 auto; box-sizing:border-box; scrollbar-width:thin; scrollbar-color:#cbd5e1 #f1f5f9;">
                    {{-- CIRCULAR PROFILE PHOTO AVATAR PICKER --}}
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <label style="position: relative; cursor: pointer; display: inline-block;">
                            <div style="width: 86px; height: 86px; border-radius: 50%; background: #22c55e; color: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 4px 16px rgba(34,197,94,0.35); border: none;">
                                @if($editPhoto)
                                    <img src="{{ $editPhoto->temporaryUrl() }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                @elseif($editProfilePhotoUrl)
                                    <img src="{{ $editProfilePhotoUrl }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                @else
                                    <span style="font-size: 36px; font-weight: 800; color: #ffffff;">{{ $editUserInitial }}</span>
                                @endif
                            </div>
                            <div style="position: absolute; bottom: 0; right: 0; background: #0f172a; color: #ffffff; border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            </div>
                            <input type="file" wire:model="editPhoto" accept="image/*" style="display: none;" />
                        </label>
                        <span style="font-size: 11.5px; font-weight: 600; color: #64748b; margin-top: 6px;">Click avatar to change picture</span>
                        @error('editPhoto') <span style="color:#ef4444; font-size:11.5px; font-weight:600; margin-top:2px;">{{ $message }}</span> @enderror
                    </div>

                    {{-- FULL NAME --}}
                    <div>
                        <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Full Name</label>
                        <input wire:model="editName" type="text" style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 14px; font-size:13.5px; color:#0f172a; outline:none;" />
                        @error('editName') <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    {{-- STUDENT ID NUMBER & SECTION --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Student ID Number</label>
                            <input wire:model="editStudentId" type="text" placeholder="e.g. 2024-00123" style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 14px; font-size:13.5px; font-family:monospace; color:#0f172a; outline:none;" />
                            @error('editStudentId') <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Section</label>
                            <input wire:model="editSection" type="text" placeholder="e.g. 4-A" style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 14px; font-size:13.5px; color:#0f172a; outline:none;" />
                            @error('editSection') <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- EMAIL ADDRESS --}}
                    <div>
                        <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Email Address</label>
                        <input wire:model="editEmail" type="email" style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 14px; font-size:13.5px; color:#0f172a; outline:none;" />
                        @error('editEmail') <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    {{-- STATUS --}}
                    <div>
                        <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Account Status</label>
                        <select wire:model="editStatus" style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 14px; font-size:13.5px; color:#0f172a; outline:none; background:#ffffff; cursor:pointer;">
                            <option value="Active">Active</option>
                            <option value="Suspended">Suspended</option>
                        </select>
                        @error('editStatus') <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    {{-- NEW PASSWORD (OPTIONAL) & CONFIRM --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div x-data="{ showNewPass: false }">
                            <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">New Password (Optional)</label>
                            <div style="position:relative; width:100%;">
                                <input wire:model="editPassword" type="text" autocomplete="off" placeholder="Leave blank to keep" :class="showNewPass ? 'unmasked-password-input' : 'masked-password-input'" style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 36px 0 14px; font-size:13px; color:#0f172a; outline:none;" />
                                <button type="button" @click="showNewPass = !showNewPass" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; padding:2px;">
                                    <svg x-show="!showNewPass" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                    <svg x-show="showNewPass" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            @error('editPassword') <span style="color:#ef4444; font-size:11.5px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                        </div>

                        <div x-data="{ showConfirmPass: false }">
                            <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Confirm Password</label>
                            <div style="position:relative; width:100%;">
                                <input wire:model="editPasswordConfirmation" type="text" autocomplete="off" placeholder="Repeat new password" :class="showConfirmPass ? 'unmasked-password-input' : 'masked-password-input'" style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 36px 0 14px; font-size:13px; color:#0f172a; outline:none;" />
                                <button type="button" @click="showConfirmPass = !showConfirmPass" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; padding:2px;">
                                    <svg x-show="!showConfirmPass" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                    <svg x-show="showConfirmPass" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                            @error('editPasswordConfirmation') <span style="color:#ef4444; font-size:11.5px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div style="padding: 16px 24px 20px; border-top: 1px solid #f1f5f9; background: #ffffff; display: flex; flex-direction: column; gap: 10px; flex-shrink: 0; width: 100%; box-sizing: border-box;">
                    <div style="display: flex; gap: 12px; width: 100%; box-sizing: border-box; align-items: center;">
                        <button
                            type="button"
                            wire:click="closeEditModal"
                            class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                            title="Cancel"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer flex items-center justify-center text-center whitespace-nowrap min-h-[44px]"
                            style="background: #22c55e !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); border: none;"
                            onmouseover="this.style.background='#16a34a'"
                            onmouseout="this.style.background='#22c55e'"
                            title="Save Changes"
                        >
                            Save Changes
                        </button>
                    </div>
                    <div style="display: flex; justify-content: flex-start;">
                        <button type="button" wire:click="goBackToVerification" style="background: none; border: none; font-size: 12.5px; font-weight: 600; color: #64748b; cursor: pointer; padding: 2px 0;">&larr; Re-verify password</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- SUSPEND MODAL                                            --}}
    {{-- ======================================================== --}}
    @if($showSuspendModal && $suspendTargetUser)
    <div style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px;" wire:click.self="closeSuspendModal">
        <div class="modal-animated-card" style="background:#ffffff; border-radius:24px; width:100%; max-width:440px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); border:1px solid #f1f5f9; overflow:hidden;">
            <div style="padding:24px 28px 12px 28px; display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:40px; height:40px; border-radius:12px; background:#fee2e2; display:flex; align-items:center; justify-content:center; color:#ef4444;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0;">Suspend Student Account</h3>
                        <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:2px 0 0 0;">{{ $suspendTargetUser->name }}</p>
                    </div>
                </div>
                <x-close-button wire:click="closeSuspendModal" />
            </div>

            <div style="padding:14px 28px 20px 28px;">
                <p style="font-size:13.5px; color:#475569; line-height:1.5; margin:0 0 14px 0;">
                    Select how long you want to suspend <strong style="color:#0f172a;">{{ $suspendTargetUser->name }}</strong>. While suspended, they will be unable to log in or submit scans.
                </p>

                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:700; color:#475569; display:block; margin-bottom:6px;">Suspension Reason</label>
                    <input type="text" wire:model="suspendReason" placeholder="e.g. Inappropriate behavior, System abuse" style="width:100%; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:10px; padding:10px 12px; font-size:13px; color:#0f172a; outline:none;" />
                    @error('suspendReason') <span style="color:#ef4444; font-size:11.5px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label style="font-size:12px; font-weight:700; color:#475569; display:block; margin-bottom:6px;">Suspension Duration</label>
                    <select wire:model="suspendDurationDays" style="width:100%; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:10px; padding:10px 12px; font-size:13px; color:#0f172a; outline:none; background:#ffffff;">
                        <option value="1">1 Day</option>
                        <option value="3">3 Days</option>
                        <option value="7">7 Days (1 Week)</option>
                        <option value="14">14 Days (2 Weeks)</option>
                        <option value="30">30 Days (1 Month)</option>
                    </select>
                </div>
            </div>

            <div style="padding:16px 24px 20px 24px; display:flex; gap:12px; background:#ffffff; border-top:1px solid #f1f5f9; width:100%; box-sizing:border-box; align-items:center;">
                <button
                    type="button"
                    wire:click="closeSuspendModal"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="confirmSuspend"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center whitespace-nowrap min-h-[44px]"
                    style="background: #ef4444 !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35); border: none;"
                    onmouseover="this.style.background='#dc2626'"
                    onmouseout="this.style.background='#ef4444'"
                >
                    Confirm Suspension
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- VIEW SUSPENSION MODAL (WITH TIMER & UNSUSPEND)           --}}
    {{-- ======================================================== --}}
    @if($showViewSuspendModal && $viewSuspendTargetUser)
    <div style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px;" wire:click.self="closeViewSuspendModal">
        <div class="modal-animated-card" style="background:#ffffff; border-radius:24px; width:100%; max-width:440px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); border:1px solid #f1f5f9; overflow:hidden;">
            <div style="padding:22px 28px 12px 28px; display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:40px; height:40px; border-radius:12px; background:#fee2e2; display:flex; align-items:center; justify-content:center; color:#ef4444;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0;">Suspension Details</h3>
                        <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:2px 0 0 0;">{{ $viewSuspendTargetUser->name }}</p>
                    </div>
                </div>
                <x-close-button wire:click="closeViewSuspendModal" />
            </div>

            <div style="padding:14px 28px 20px 28px; display:flex; flex-direction:column; gap:14px;">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:14px; display:flex; flex-direction:column; gap:8px;">
                    <div>
                        <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:2px;">Reason</span>
                        <div style="font-size:13.5px; font-weight:700; color:#0f172a;">{{ $viewSuspendTargetUser->suspend_reason ?: 'Administrative Suspension' }}</div>
                    </div>
                    <div style="height:1px; background:#e2e8f0; margin:4px 0;"></div>
                    <div>
                        <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:2px;">Suspended By</span>
                        <div style="font-size:13px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:6px;">
                            <svg width="15" height="15" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>{{ $viewSuspendTargetUser->suspended_by_admin_detail ?: 'System (SuperAdmin)' }}</span>
                        </div>
                    </div>
                    <div style="height:1px; background:#e2e8f0; margin:4px 0;"></div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div>
                            <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:2px;">Suspended Until</span>
                            <div style="font-size:13px; font-weight:700; color:#d97706;">{{ $viewSuspendTargetUser->suspended_until?->format('M d, Y') ?? 'Manual' }}</div>
                        </div>
                        <div>
                            <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:2px;">Time Remaining</span>
                            <div style="font-size:13px; font-weight:700; color:#22c55e; font-family:monospace;">{{ $viewSuspendTargetUser->suspension_countdown }}</div>
                        </div>
                    </div>
                </div>

                @php
                    $hasPendingReactivation = \App\Models\UserRequest::where('user_email', $viewSuspendTargetUser->email)
                        ->where('type', 'Account Reactivation Request')
                        ->where('status', 'Pending')
                        ->first();
                    $isSuperAdmin = $this->isSuperAdminActor();
                @endphp

                @if($hasPendingReactivation)
                    <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:10px 14px; display:flex; align-items:center; gap:10px;">
                        <div style="font-size:18px;">⏳</div>
                        <div>
                            <div style="font-size:12px; color:#b45309; font-weight:700;">Reactivation Request Pending</div>
                            <div style="font-size:11px; color:#92400e;">Request {{ $hasPendingReactivation->request_code }} was submitted to {{ $hasPendingReactivation->admin_id }} for approval.</div>
                        </div>
                    </div>
                @endif
            </div>

            <div style="padding:16px 24px 20px 24px; display:flex; justify-content:space-between; align-items:center; gap:12px; background:#ffffff; border-top:1px solid #f1f5f9; width:100%; box-sizing:border-box;">
                <button
                    type="button"
                    wire:click="closeViewSuspendModal"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                >
                    Close
                </button>
                @if($hasPendingReactivation)
                    <button type="button" disabled class="flex-1 py-3 px-3 sm:px-4 rounded-full bg-gray-400 text-white font-bold text-sm opacity-75 cursor-not-allowed whitespace-nowrap min-h-[44px]">Reactivation Pending</button>
                @else
                    <button
                        type="button"
                        wire:click="cancelSuspensionFromView"
                        class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center whitespace-nowrap min-h-[44px]"
                        style="background: #22c55e !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); border: none;"
                        onmouseover="this.style.background='#16a34a'"
                        onmouseout="this.style.background='#22c55e'"
                        title="{{ $isSuperAdmin ? 'Reactivate student account immediately' : 'Submit reactivation request to ' . ($viewSuspendTargetUser->suspended_by_admin_detail ?: 'SuperAdmin') . ' for approval' }}"
                    >
                        Reactivate Account
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- DELETE MODAL                                             --}}
    {{-- ======================================================== --}}
    @if($showDeleteModal && $deleteTargetUser)
    <div style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px;" wire:click.self="closeDeleteModal">
        <div class="modal-animated-card" style="background:#ffffff; border-radius:24px; width:100%; max-width:440px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); border:1px solid #f1f5f9; overflow:hidden;">
            <div style="padding:24px 28px 12px 28px; display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0;">Schedule Deletion</h3>
                        <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:2px 0 0 0;">5-day grace period</p>
                    </div>
                </div>
                <x-close-button wire:click="closeDeleteModal" />
            </div>

            <div style="padding:14px 28px 20px 28px;">
                <p style="font-size:13.5px; color:#475569; line-height:1.5; margin:0;">
                    Are you sure you want to schedule deletion of student <strong style="color:#0f172a;">{{ $deleteTargetUser->name }}</strong>? The account will be suspended and permanently deleted in 5 days.
                </p>
            </div>

            <div style="padding:16px 24px 20px 24px; display:flex; gap:12px; background:#ffffff; border-top:1px solid #f1f5f9; width:100%; box-sizing:border-box; align-items:center;">
                <button
                    type="button"
                    wire:click="closeDeleteModal"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="confirmDelete"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center whitespace-nowrap min-h-[44px]"
                    style="background: #ef4444 !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35); border: none;"
                    onmouseover="this.style.background='#dc2626'"
                    onmouseout="this.style.background='#ef4444'"
                >
                    Schedule Deletion
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- CANCEL DELETION MODAL                                    --}}
    {{-- ======================================================== --}}
    @if($showCancelDeletionModal && $cancelTargetUser)
    <div style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px;" wire:click.self="closeCancelDeletionModal">
        <div class="modal-animated-card" style="background:#ffffff; border-radius:24px; width:100%; max-width:440px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); border:1px solid #f1f5f9; overflow:hidden;">
            <div style="padding:24px 28px 12px 28px; display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:40px; height:40px; border-radius:12px; background:#dcfce7; display:flex; align-items:center; justify-content:center; color:#22c55e;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0;">Cancel Deletion</h3>
                        <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:2px 0 0 0;">Restore student account</p>
                    </div>
                </div>
                <x-close-button wire:click="closeCancelDeletionModal" />
            </div>

            <div style="padding:14px 28px 20px 28px;">
                <p style="font-size:13.5px; color:#475569; line-height:1.5; margin:0;">
                    Cancel the scheduled deletion countdown and restore <strong style="color:#0f172a;">{{ $cancelTargetUser->name }}</strong>?
                </p>
            </div>

            <div style="padding:16px 24px 20px 24px; display:flex; gap:12px; background:#ffffff; border-top:1px solid #f1f5f9; width:100%; box-sizing:border-box; align-items:center;">
                <button
                    type="button"
                    wire:click="closeCancelDeletionModal"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="confirmCancelDeletion"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center whitespace-nowrap min-h-[44px]"
                    style="background: #22c55e !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); border: none;"
                    onmouseover="this.style.background='#16a34a'"
                    onmouseout="this.style.background='#22c55e'"
                >
                    Restore Student
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- REMOVE STUDENT MODAL (REMOVES FROM STUDENT RECORDS ONLY)  --}}
    {{-- ======================================================== --}}
    @if($showRemoveStudentModal && $removeTargetUser)
    <div style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px;" wire:click.self="closeRemoveStudentModal">
        <div class="modal-animated-card" style="background:#ffffff; border-radius:24px; width:100%; max-width:460px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); border:1px solid #f1f5f9; overflow:hidden;">
            <div style="padding:24px 28px 12px 28px; display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0;">Remove from Student Records</h3>
                        <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:2px 0 0 0;">Student Records Only</p>
                    </div>
                </div>
                <x-close-button wire:click="closeRemoveStudentModal" />
            </div>

            <div style="padding:14px 28px 20px 28px;">
                <p style="font-size:13.5px; color:#475569; line-height:1.5; margin:0;">
                    Are you sure you want to remove <strong style="color:#0f172a;">{{ $removeTargetUser->name }}</strong>
                    @if(!empty($removeTargetUser->student_id))
                        <span style="font-family:monospace; font-weight:700;">({{ $removeTargetUser->student_id }})</span>
                    @endif
                    from Student Records?
                </p>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px 14px; margin-top:14px; font-size:12.5px; color:#64748b; line-height:1.4;">
                    ℹ️ <strong>Note:</strong> This will only remove their entry from the <em>Student Records</em> page. Their account will <strong>not</strong> be removed from <em>Manage Users</em>.
                </div>
            </div>

            <div style="padding:16px 28px 24px 28px; display:flex; gap:12px; background:#ffffff; border-top:1px solid #f1f5f9;">
                <button
                    type="button"
                    wire:click="closeRemoveStudentModal"
                    class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="confirmRemoveStudent"
                    class="flex-1 py-3 rounded-full bg-red-500 hover:bg-red-600 text-white font-bold text-sm shadow-md shadow-red-500/35 transition cursor-pointer"
                    style="box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);"
                >
                    Remove Student
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ADD STUDENT MODAL --}}
    @include('admin.livewire.student.partials.add-student-modal')

    {{-- SECTION RECORDS MODAL --}}
    @include('admin.livewire.student.partials.section-records-modal')

    {{-- SOIL RECORD DETAILS MODAL --}}
    <livewire:admin.soil.soil-detail-modal />
</div>
