<x-website-layout>
    <x-slot name="title">My Profile</x-slot>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Lock outer page scrollbar when modal popup is active */
        html.no-scroll, body.no-scroll {
            overflow: hidden !important;
            height: 100% !important;
            touch-action: none !important;
        }
        
        /* ==========================================================================
           EDIT INFORMATION MODAL DESIGN TOKENS (LIGHT & DARK MODE)
           ========================================================================== */
        :root {
            --pm-overlay: rgba(0, 0, 0, 0.65);
            --pm-bg: #0B2B26;
            --pm-border: rgba(142, 182, 155, 0.28);
            --pm-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 35px rgba(35, 83, 71, 0.25);
            --pm-header-bg: rgba(11, 43, 38, 0.98);
            --pm-footer-bg: rgba(11, 43, 38, 0.98);
            --pm-divider: rgba(142, 182, 155, 0.2);
            --pm-title: #DAF1DE;
            --pm-subtitle: #8EB69B;
            --pm-body-text: #DAF1DE;
            --pm-muted: #8EB69B;
            
            /* Tabs */
            --pm-tabs-track: rgba(16, 44, 38, 0.6);
            --pm-tabs-border: rgba(142, 182, 155, 0.2);
            --pm-tab-inactive-text: #8EB69B;
            --pm-tab-inactive-hover: #DAF1DE;
            --pm-tab-active-bg: #235347;
            --pm-tab-active-text: #FFFFFF;
            --pm-tab-active-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);

            /* Inputs */
            --pm-input-bg: rgba(5, 31, 32, 0.75);
            --pm-input-border: #475569;
            --pm-input-text: #DAF1DE;
            --pm-input-icon: #8EB69B;
            --pm-input-focus-border: #94A3B8;
            --pm-input-focus-ring: rgba(148, 163, 184, 0.22);
            --pm-input-locked-bg: rgba(5, 31, 32, 0.4);
            --pm-input-locked-border: rgba(142, 182, 155, 0.18);
            --pm-label-color: #94A3B8;
            --pm-label-bg: #0B2B26;
            --pm-label-active-text: #E2E8F0;

            /* Buttons */
            --pm-btn-primary-bg: linear-gradient(135deg, #235347, #163832);
            --pm-btn-primary-border: #8EB69B;
            --pm-btn-primary-text: #DAF1DE;
            --pm-btn-primary-shadow: 0 4px 14px rgba(35, 83, 71, 0.5);
            --pm-btn-primary-hover-bg: #235347;
            --pm-btn-primary-hover-border: #DAF1DE;
            --pm-btn-primary-hover-text: #FFFFFF;

            --pm-btn-secondary-bg: rgba(142, 182, 155, 0.12);
            --pm-btn-secondary-border: rgba(142, 182, 155, 0.25);
            --pm-btn-secondary-text: #8EB69B;
            --pm-btn-secondary-hover-bg: rgba(142, 182, 155, 0.22);
            --pm-btn-secondary-hover-text: #DAF1DE;

            /* Avatar & Preview */
            --pm-avatar-bg: linear-gradient(135deg, #235347, #163832);
            --pm-avatar-border: #8EB69B;
            --pm-avatar-text: #DAF1DE;
            --pm-avatar-shadow: 0 8px 24px rgba(35, 83, 71, 0.5);
            --pm-pencil-bg: #163832;
            --pm-pencil-border: #8EB69B;
            --pm-pencil-stroke: #DAF1DE;

            /* Security & Current Email Boxes */
            --pm-sec-box-bg: rgba(34, 197, 94, 0.08);
            --pm-sec-box-border: rgba(34, 197, 94, 0.28);
            --pm-sec-box-title: #DAF1DE;
            --pm-sec-box-text: #8EB69B;
            --pm-current-email-bg: rgba(16, 44, 38, 0.55);
            --pm-current-email-border: rgba(142, 182, 155, 0.25);
            --pm-current-email-label: #8EB69B;
            --pm-current-email-val: #FFFFFF;

            /* Badges & Meters */
            --pm-badge-inactive-bg: rgba(5, 31, 32, 0.7);
            --pm-badge-inactive-border: rgba(142, 182, 155, 0.25);
            --pm-badge-inactive-text: #8EB69B;
            --pm-meter-bg: rgba(142, 182, 155, 0.25);
            --pm-scrollbar-thumb: rgba(142, 182, 155, 0.35);
            --pm-scrollbar-track: rgba(5, 31, 32, 0.3);
        }

        html.light-mode,
        .light-mode,
        body.light-mode {
            --pm-overlay: rgba(15, 23, 42, 0.5);
            --pm-bg: #FFFFFF;
            --pm-border: #E2E8F0;
            --pm-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.15), 0 0 20px rgba(0, 0, 0, 0.04);
            --pm-header-bg: #F8FAFC;
            --pm-footer-bg: #F8FAFC;
            --pm-divider: #E2E8F0;
            --pm-title: #0F172A;
            --pm-subtitle: #64748B;
            --pm-body-text: #1E293B;
            --pm-muted: #64748B;
            
            /* Tabs */
            --pm-tabs-track: #F1F5F9;
            --pm-tabs-border: #E2E8F0;
            --pm-tab-inactive-text: #64748B;
            --pm-tab-inactive-hover: #0F172A;
            --pm-tab-active-bg: #FFFFFF;
            --pm-tab-active-text: #0F172A;
            --pm-tab-active-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);

            /* Inputs */
            --pm-input-bg: #F8FAFC;
            --pm-input-border: #CBD5E1;
            --pm-input-text: #0F172A;
            --pm-input-icon: #64748B;
            --pm-input-focus-border: #16A34A;
            --pm-input-focus-ring: rgba(22, 163, 74, 0.2);
            --pm-input-locked-bg: #F1F5F9;
            --pm-input-locked-border: #E2E8F0;
            --pm-label-color: #64748B;
            --pm-label-bg: #FFFFFF;
            --pm-label-active-text: #16A34A;

            /* Buttons */
            --pm-btn-primary-bg: linear-gradient(135deg, #16A34A, #15803D);
            --pm-btn-primary-border: #16A34A;
            --pm-btn-primary-text: #FFFFFF;
            --pm-btn-primary-shadow: 0 4px 14px rgba(22, 163, 74, 0.25);
            --pm-btn-primary-hover-bg: #15803D;
            --pm-btn-primary-hover-border: #15803D;
            --pm-btn-primary-hover-text: #FFFFFF;

            --pm-btn-secondary-bg: #F1F5F9;
            --pm-btn-secondary-border: #CBD5E1;
            --pm-btn-secondary-text: #475569;
            --pm-btn-secondary-hover-bg: #E2E8F0;
            --pm-btn-secondary-hover-text: #0F172A;

            /* Avatar & Preview */
            --pm-avatar-bg: linear-gradient(135deg, #DCFCE7, #BBF7D0);
            --pm-avatar-border: #86EFAC;
            --pm-avatar-text: #166534;
            --pm-avatar-shadow: 0 6px 18px rgba(22, 163, 74, 0.18);
            --pm-pencil-bg: #FFFFFF;
            --pm-pencil-border: #86EFAC;
            --pm-pencil-stroke: #166534;

            /* Security & Current Email Boxes */
            --pm-sec-box-bg: #F0FDF4;
            --pm-sec-box-border: #BBF7D0;
            --pm-sec-box-title: #166534;
            --pm-sec-box-text: #15803D;
            --pm-current-email-bg: #F8FAFC;
            --pm-current-email-border: #E2E8F0;
            --pm-current-email-label: #64748B;
            --pm-current-email-val: #0F172A;

            /* Badges & Meters */
            --pm-badge-inactive-bg: #F1F5F9;
            --pm-badge-inactive-border: #CBD5E1;
            --pm-badge-inactive-text: #64748B;
            --pm-meter-bg: #E2E8F0;
            --pm-scrollbar-thumb: #CBD5E1;
            --pm-scrollbar-track: #F1F5F9;
        }

        /* Autofill Neutralization (Chrome / Safari / Edge) */
        .floating-input:-webkit-autofill,
        .floating-input:-webkit-autofill:hover, 
        .floating-input:-webkit-autofill:focus, 
        .floating-input:-webkit-autofill:active {
            -webkit-text-fill-color: var(--pm-input-text) !important;
            -webkit-box-shadow: 0 0 0 1000px var(--pm-input-bg) inset !important;
            box-shadow: 0 0 0 1000px var(--pm-input-bg) inset !important;
            transition: background-color 5000s ease-in-out 0s !important;
        }

        /* Modal Overlay */
        .profile-modal-overlay {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            z-index: 99999;
            background: var(--pm-overlay);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 16px;
            box-sizing: border-box;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Consistent Responsive Card (Matching Soil Record Details max-width and styling) */
        .profile-modal-card {
            position: relative;
            z-index: 100000;
            width: 100% !important;
            max-width: 680px !important;
            height: auto !important;
            max-height: min(85vh, 680px) !important;
            background: var(--pm-bg);
            border-radius: 28px !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5) !important;
            border: 1px solid var(--pm-border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin: auto;
            box-sizing: border-box !important;
            transition: background-color 0.25s ease, border-color 0.25s ease;
        }

        /* Fixed Sticky Header */
        .profile-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 24px 14px 24px;
            border-bottom: 1px solid var(--pm-divider);
            flex-shrink: 0;
            background: var(--pm-header-bg);
            z-index: 10;
        }
        .profile-modal-title {
            font-size: 17px;
            font-weight: 800;
            color: var(--pm-title);
            margin: 0;
            letter-spacing: -0.3px;
        }
        .profile-modal-subtitle {
            font-size: 12px;
            color: var(--pm-subtitle);
            margin: 2px 0 0 0;
            font-weight: 500;
        }

        /* Fixed Tabs Bar */
        .profile-modal-tabs {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--pm-tabs-track);
            border: 1px solid var(--pm-tabs-border);
            border-radius: 14px;
            padding: 4px;
            margin: 12px 24px 0 24px;
            flex-shrink: 0;
        }
        .profile-modal-tab-btn {
            flex: 1;
            padding: 9px 12px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: var(--pm-tab-inactive-text);
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            white-space: nowrap;
            min-height: 40px;
        }
        .profile-modal-tab-btn:hover {
            color: var(--pm-tab-inactive-hover);
        }
        .profile-modal-tab-btn.active {
            background: var(--pm-tab-active-bg);
            color: var(--pm-tab-active-text);
            box-shadow: var(--pm-tab-active-shadow);
        }
        .tab-label-desktop { display: inline; }
        .tab-label-mobile { display: none; }

        /* Scrollable Middle Body (Standardized across all tabs) */
        .profile-modal-body {
            padding: 20px 24px;
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: var(--pm-scrollbar-thumb) transparent;
        }
        .profile-modal-body::-webkit-scrollbar { width: 6px; }
        .profile-modal-body::-webkit-scrollbar-thumb { background: var(--pm-scrollbar-thumb); border-radius: 999px; }
        .profile-modal-body::-webkit-scrollbar-track { background: var(--pm-scrollbar-track); border-radius: 999px; }

        /* Grid layout for Personal Info Tab */
        .profile-modal-grid {
            display: grid;
            grid-template-columns: 1fr 180px;
            gap: 24px;
            align-items: start;
        }

        .profile-modal-inputs {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .profile-modal-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 8px 0 0 12px;
            border-left: 1px solid var(--pm-divider);
        }

        /* Fixed Sticky Footer */
        /* Unified Footer Layout across all tabs */
        .profile-modal-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 14px 24px 16px 24px;
            flex-shrink: 0;
            border-top: 1px solid var(--pm-divider);
            background: var(--pm-footer-bg);
            z-index: 10;
            gap: 12px;
            box-sizing: border-box;
        }
        .profile-modal-cancel-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            min-height: 42px;
            border-radius: 12px !important;
            background: var(--pm-btn-secondary-bg) !important;
            border: 1px solid var(--pm-btn-secondary-border) !important;
            color: var(--pm-btn-secondary-text) !important;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            font-family: inherit;
            box-sizing: border-box;
        }
        .profile-modal-cancel-btn:hover {
            background: var(--pm-btn-secondary-hover-bg) !important;
            color: var(--pm-btn-secondary-hover-text) !important;
            border-color: var(--pm-border-focus) !important;
        }
        .profile-modal-footer-left {
            display: none;
        }
        .profile-modal-footer-right {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }

        /* Floating Input and Label */
        .floating-field-group {
            position: relative;
            width: 100%;
        }
        .floating-input {
            width: 100%;
            height: 48px;
            border-radius: 12px !important;
            background: var(--pm-input-bg) !important;
            border: 1.5px solid var(--pm-input-border) !important;
            color: var(--pm-input-text) !important;
            font-size: 14.5px;
            padding: 10px 42px 10px 42px;
            outline: none !important;
            box-sizing: border-box !important;
            transition: all 0.2s ease;
        }
        .floating-input.has-no-right-icon {
            padding-right: 16px;
        }
        .floating-input.has-badge {
            padding-right: 88px;
        }
        .floating-input:focus {
            border-color: var(--pm-input-focus-border) !important;
            box-shadow: 0 0 0 3px var(--pm-input-focus-ring) !important;
        }
        .floating-label {
            position: absolute;
            left: 42px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13.5px;
            font-weight: 500;
            color: var(--pm-label-color);
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            background: transparent;
            padding: 0 6px;
            border-radius: 4px;
            z-index: 2;
            white-space: nowrap;
        }
        .floating-input:focus ~ .floating-label,
        .floating-input:not(:placeholder-shown) ~ .floating-label {
            top: 0px !important;
            left: 16px !important;
            transform: translateY(-50%) scale(0.82) !important;
            background: var(--pm-label-bg) !important;
            color: var(--pm-label-active-text) !important;
            font-weight: 700 !important;
        }
        .floating-input:focus ~ .floating-label {
            color: var(--pm-input-focus-border) !important;
        }

        .input-lead-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 17px;
            height: 17px;
            pointer-events: none;
            z-index: 3;
            stroke: var(--pm-input-icon);
            transition: stroke 0.2s ease;
        }
        .input-eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--pm-input-icon);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
            transition: color 0.2s ease;
        }
        .input-eye-btn:hover { color: var(--pm-title); }

        /* Close Button */
        .modal-close-circle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--pm-btn-secondary-bg);
            border: 1px solid var(--pm-btn-secondary-border);
            color: var(--pm-btn-secondary-text);
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
            outline: none;
        }
        .modal-close-circle-btn:hover {
            background: var(--pm-btn-secondary-hover-bg);
            color: var(--pm-title);
            transform: scale(1.06);
        }

        /* Buttons */
        .profile-modal-save-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 22px;
            min-height: 44px;
            border-radius: 12px !important;
            background: var(--pm-btn-primary-bg) !important;
            border: 1.5px solid var(--pm-btn-primary-border) !important;
            color: var(--pm-btn-primary-text) !important;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: var(--pm-btn-primary-shadow) !important;
            transition: all 0.2s ease;
            font-family: inherit;
            white-space: nowrap;
        }
        .profile-modal-save-btn:hover:not(:disabled) {
            background: var(--pm-btn-primary-hover-bg) !important;
            border-color: var(--pm-btn-primary-hover-border) !important;
            color: var(--pm-btn-primary-hover-text) !important;
            transform: translateY(-1px);
        }

        .profile-cancel-req-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            min-height: 44px;
            border-radius: 12px;
            background: var(--pm-btn-secondary-bg);
            border: 1px solid var(--pm-btn-secondary-border);
            color: var(--pm-btn-secondary-text);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .profile-cancel-req-btn:hover {
            background: var(--pm-btn-secondary-hover-bg);
            color: var(--pm-btn-secondary-hover-text);
        }

        /* Avatar */
        .profile-modal-avatar {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: var(--pm-avatar-bg) !important;
            border: 2px solid var(--pm-avatar-border) !important;
            color: var(--pm-avatar-text) !important;
            font-size: 26px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--pm-avatar-shadow) !important;
            transition: all 0.25s ease;
            user-select: none;
        }
        .profile-modal-pencil-btn {
            position: absolute;
            bottom: 0px;
            right: 0px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--pm-pencil-bg);
            border: 1.5px solid var(--pm-pencil-border);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            cursor: pointer;
            padding: 0;
            outline: none;
            transition: all 0.2s ease;
            z-index: 5;
        }
        .profile-modal-pencil-btn svg { stroke: var(--pm-pencil-stroke); }
        .profile-modal-pencil-btn:hover { transform: scale(1.1); }

        .profile-modal-preview-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--pm-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .profile-modal-preview-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--pm-title);
            margin: 6px 0 2px 0;
            word-break: break-word;
        }
        .profile-modal-preview-role {
            font-size: 12px;
            color: var(--pm-muted);
            margin: 0 0 6px 0;
            font-weight: 600;
        }
        .profile-modal-preview-id {
            font-size: 12px;
            font-weight: 700;
            color: var(--pm-muted);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Sec box & current email */
        .profile-modal-sec-box {
            background: var(--pm-sec-box-bg);
            border: 1px solid var(--pm-sec-box-border);
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
            transition: all 0.25s ease;
        }
        .profile-modal-sec-box strong {
            font-size: 13.5px;
            display: block;
            margin-bottom: 3px;
            color: var(--pm-sec-box-title);
        }
        .profile-modal-sec-box span {
            font-size: 12.5px;
            line-height: 1.5;
            display: block;
            color: var(--pm-sec-box-text);
        }

        .profile-modal-current-email {
            background: var(--pm-current-email-bg);
            border: 1px solid var(--pm-current-email-border);
            border-radius: 14px;
            padding: 14px 18px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.25s ease;
        }
        .profile-modal-current-email-label {
            font-size: 11px;
            font-weight: 800;
            color: var(--pm-current-email-label);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: block;
        }
        .profile-modal-current-email-val {
            font-size: 14px;
            font-weight: 700;
            color: var(--pm-current-email-val);
            display: block;
            margin-top: 2px;
            word-break: break-all;
        }

        /* Password badges & meter */
        .pass-helper-label {
            font-size: 11.5px;
            color: var(--pm-muted);
            font-weight: 600;
        }
        .meter-bar {
            width: 9px;
            height: 4px;
            border-radius: 2px;
            transition: all 0.25s ease;
            background: var(--pm-meter-bg);
        }
        .pass-req-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }
        .pass-req-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.2s ease;
            background: var(--pm-badge-inactive-bg);
            border: 1px solid var(--pm-badge-inactive-border);
            color: var(--pm-badge-inactive-text);
            user-select: none;
        }
        .pass-req-badge.active {
            background: rgba(34, 197, 94, 0.15);
            border-color: rgba(34, 197, 94, 0.4);
            color: #22C55E;
            font-weight: 700;
        }
        .pass-req-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            font-size: 8px;
            font-weight: 800;
            transition: all 0.2s ease;
            background: var(--pm-badge-inactive-border);
            color: var(--pm-badge-inactive-text);
        }
        .pass-req-badge.active .pass-req-icon {
            background: #22C55E;
            color: #FFFFFF;
        }

        .pass-clear-btn {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--pm-muted);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: color 0.2s ease;
        }
        .pass-clear-btn:hover { color: var(--pm-title); }

        .pass-status-checking { color: var(--pm-muted); display: inline-flex; align-items: center; gap: 4px; font-size: 11.5px; font-weight: 600; }
        .pass-status-correct  { color: #22C55E; display: inline-flex; align-items: center; gap: 4px; font-size: 11.5px; font-weight: 700; }
        .pass-status-error    { color: #EF4444; display: inline-flex; align-items: center; gap: 4px; font-size: 11.5px; font-weight: 700; }

        .profile-forgot-link {
            font-size: 12px;
            font-weight: 600;
            color: #22C55E;
            text-decoration: none;
            transition: color 0.2s ease, text-decoration 0.2s ease;
            display: inline-flex;
            align-items: center;
        }
        .profile-forgot-link:hover {
            color: #16A34A;
            text-decoration: underline;
        }

        .floating-input.border-correct { border-color: #22C55E !important; }
        .floating-input.border-incorrect { border-color: #EF4444 !important; }
        .floating-input.input-locked {
            opacity: 0.6;
            cursor: not-allowed !important;
            background: var(--pm-input-locked-bg) !important;
            border-color: var(--pm-input-locked-border) !important;
        }
        .floating-input.input-unlocked {
            opacity: 1;
            cursor: text !important;
        }

        /* Mobile Breakpoint (< 640px) */
        @media (max-width: 640px) {
            .profile-modal-overlay {
                padding: 10px !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .profile-modal-card {
                border-radius: 24px !important;
                max-width: 100% !important;
                height: 80vh !important;
                max-height: 80vh !important;
                margin: auto !important;
            }
            .profile-modal-header {
                padding: 14px 16px !important;
            }
            .profile-modal-tabs {
                margin: 10px 16px 0 16px !important;
                gap: 4px !important;
                padding: 3px !important;
            }
            .profile-modal-tab-btn {
                padding: 8px 6px !important;
                font-size: 11.5px !important;
                gap: 4px !important;
                min-height: 44px !important;
            }
            .profile-modal-body {
                padding: 16px !important;
            }
            .profile-modal-grid {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
            }
            .profile-modal-preview {
                order: -1 !important;
                border-left: none !important;
                border-bottom: 1px solid var(--pm-divider) !important;
                padding: 0 0 14px 0 !important;
                flex-direction: row !important;
                align-items: center !important;
                text-align: left !important;
                gap: 14px !important;
            }
            .profile-modal-preview-title { display: none !important; }
            .profile-modal-avatar {
                width: 52px !important;
                height: 52px !important;
                font-size: 20px !important;
            }
            .floating-input {
                font-size: 16px !important; /* Prevents auto zoom */
                height: 48px !important;
            }
            .profile-modal-footer {
                padding: 12px 16px !important;
                flex-direction: column !important;
                gap: 10px !important;
                align-items: stretch !important;
            }
            .profile-modal-footer-left {
                order: 2 !important;
                justify-content: center !important;
                font-size: 11px !important;
            }
            .profile-modal-footer-right {
                order: 1 !important;
                width: 100% !important;
                margin-left: 0 !important;
            }
            .profile-modal-footer-right .profile-modal-save-btn,
            .profile-modal-footer-right .profile-cancel-req-btn {
                width: 100% !important;
                min-height: 44px !important;
                justify-content: center !important;
            }
            .tab-label-desktop { display: none !important; }
            .tab-label-mobile { display: inline !important; }
        }

        .ws-page-hero {
            background: linear-gradient(135deg, #051F20 0%, #0B2B26 100%);
            padding: 16px 24px 14px;
            border-bottom: 1px solid rgba(142, 182, 155, 0.2);
            flex-shrink: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }
        .ws-page-hero-inner { max-width: 1400px; margin: 0 auto; width: 100%; }
        .ws-breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: #8EB69B; margin-bottom: 4px;
            font-weight: 500;
        }
        .ws-breadcrumb a { color: #DAF1DE; text-decoration: none; font-weight: 600; transition: color 0.2s ease; }
        .ws-breadcrumb a:hover { text-decoration: underline; color: #8EB69B; }
        .ws-page-title {
            font-size: clamp(1.3rem, 2.5vw, 1.75rem); font-weight: 900;
            color: #DAF1DE; margin: 0 0 2px 0; letter-spacing: -0.5px; line-height: 1.2;
        }
        .ws-page-sub { font-size: 13px; color: #8EB69B; margin: 0; font-weight: 400; }

        /* Auth guard */
        .auth-required-banner {
            max-width: 520px;
            margin: 60px auto;
            text-align: center;
            padding: 56px 36px;
            background: linear-gradient(145deg, #0B2B26 0%, #163832 100%);
            border-radius: 24px;
            border: 1px solid rgba(142, 182, 155, 0.3);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4);
            color: #DAF1DE;
        }
        .auth-required-banner h2 { font-size: 22px; font-weight: 900; color: #DAF1DE; margin: 0 0 10px 0; }
        .auth-required-banner p  { font-size: 14px; color: #8EB69B; margin: 0 0 28px 0; line-height: 1.6; }

        .profile-alert-container {
            max-width: 1400px;
            margin: 20px auto 0;
            padding: 0 24px;
            box-sizing: border-box;
            width: 100%;
        }

        /* Profile wrapper */
        .profile-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 24px 80px;
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 28px;
            align-items: start;
            box-sizing: border-box;
            width: 100%;
        }

        .profile-sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        /* Avatar card */
        .profile-avatar-card {
            background: linear-gradient(145deg, #0B2B26 0%, #163832 100%);
            border-radius: 20px;
            border: 1px solid rgba(142, 182, 155, 0.25);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            padding: 36px 24px;
            text-align: center;
        }
        .profile-avatar {
            width: 88px; height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, #235347, #163832);
            border: 2px solid #8EB69B;
            color: #DAF1DE;
            font-size: 38px;
            font-weight: 900;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 8px 24px rgba(35, 83, 71, 0.5);
        }
        .profile-name {
            font-size: 20px;
            font-weight: 900;
            color: #DAF1DE;
            margin: 0 0 6px 0;
            letter-spacing: -0.01em;
        }
        .profile-role-badge {
            display: inline-block;
            background: transparent !important;
            border: none !important;
            color: #8EB69B !important;
            font-size: 13.5px;
            font-weight: 600;
            padding: 0 !important;
            border-radius: 0 !important;
            margin-bottom: 6px;
        }
        .profile-stat-row {
            display: flex;
            justify-content: center;
            gap: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(142, 182, 155, 0.18);
        }
        .profile-stat { text-align: center; }
        .profile-stat-num {
            font-size: 32px;
            font-weight: 900;
            color: #4ADE80;
            line-height: 1;
        }
        .profile-stat-label {
            font-size: 12.5px;
            color: #8EB69B;
            margin-top: 6px;
            font-weight: 600;
        }

        /* Info cards */
        .profile-info-stack {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .profile-info-card {
            background: linear-gradient(145deg, #0B2B26 0%, #163832 100%);
            border-radius: 20px;
            border: 1px solid rgba(142, 182, 155, 0.25);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }
        .profile-info-header {
            padding: 18px 24px;
            border-bottom: 1px solid rgba(142, 182, 155, 0.18);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .profile-info-header-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: rgba(5, 31, 32, 0.65);
            border: 1px solid rgba(142, 182, 155, 0.25);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .profile-info-header-icon svg { stroke: #4ADE80; }
        .profile-info-header h3 {
            font-size: 15px;
            font-weight: 800;
            color: #DAF1DE;
            margin: 0;
            letter-spacing: -0.01em;
        }
        .profile-info-rows { padding: 4px 0; }
        .profile-info-row {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            gap: 14px;
            border-bottom: 1px solid rgba(142, 182, 155, 0.1);
        }
        .profile-info-row:last-child { border-bottom: none; }
        .profile-info-row-label {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #8EB69B;
            width: 120px;
            flex-shrink: 0;
        }
        .profile-info-row-value {
            font-size: 14px;
            font-weight: 600;
            color: #FFFFFF !important;
            flex: 1;
        }
        .profile-user-id-val {
            color: #FFFFFF !important;
        }
        .profile-stat-count-val {
            color: #FFFFFF !important;
        }
        .profile-scan-link {
            color: #4ADE80 !important;
            transition: opacity 0.2s ease;
        }
        .profile-scan-link svg {
            stroke: #4ADE80 !important;
        }
        .profile-scan-link:hover {
            opacity: 0.85;
            text-decoration: underline;
        }

        /* Logout card */
        .profile-logout-card {
            background: linear-gradient(145deg, #1f0b0b 0%, #2b1111 100%);
            border-radius: 20px;
            border: 1px solid rgba(239, 68, 68, 0.35);
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.12);
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .profile-logout-info h4 { font-size: 15px; font-weight: 800; color: #DAF1DE; margin: 0 0 4px 0; }
        .profile-logout-info p  { font-size: 13px; color: #8EB69B; margin: 0; }
        .ws-btn-red {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: rgba(239, 68, 68, 0.85);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.3);
            font-family: inherit;
        }
        .ws-btn-red:hover { background: #ef4444; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(239, 68, 68, 0.45); }
        .ws-btn-green {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: rgba(35, 83, 71, 0.45);
            border: 1.5px solid #8EB69B;
            color: #DAF1DE;
            font-size: 14px;
            font-weight: 700;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            transition: all 0.25s ease;
        }
        .ws-btn-green:hover { background: #235347; border-color: #DAF1DE; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(35, 83, 71, 0.5); }

        .profile-admin-btn {
            margin-top: 18px;
            width: 100%;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 12px;
            background: rgba(11, 43, 38, 0.65);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1.5px solid rgba(142, 182, 155, 0.6);
            color: #DAF1DE !important;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
            transition: all 0.25s ease;
        }
        .profile-admin-btn svg {
            stroke: #DAF1DE !important;
        }
        .profile-admin-btn:hover {
            background: rgba(35, 83, 71, 0.8);
            border-color: #DAF1DE;
            color: #FFFFFF !important;
            box-shadow: 0 6px 20px rgba(35, 83, 71, 0.5);
            transform: translateY(-2px);
        }
        .profile-admin-btn:hover svg {
            stroke: #FFFFFF !important;
        }

        /* ── Profile Card Divider & Settings Group ── */
        .profile-card-divider {
            width: 100%;
            height: 1px;
            background: rgba(142, 182, 155, 0.2);
            margin: 20px 0 16px 0;
            border: none;
        }

        .profile-settings-group {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: left;
        }

        .profile-settings-header-label {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #8EB69B;
            padding-left: 4px;
            margin-bottom: 2px;
        }

        .profile-setting-item {
            width: 100%;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 14px;
            background: rgba(11, 43, 38, 0.55);
            border: 1px solid rgba(142, 182, 155, 0.25);
            color: inherit;
            cursor: pointer;
            transition: all 0.22s ease;
            font-family: inherit;
            text-decoration: none;
        }
        .profile-setting-item:hover {
            background: rgba(35, 83, 71, 0.65);
            border-color: rgba(142, 182, 155, 0.5);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        }

        .profile-setting-left {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .profile-setting-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .profile-setting-icon.notif {
            background: rgba(34, 197, 94, 0.18);
            color: #4ADE80;
        }
        .profile-setting-icon.edit {
            background: rgba(34, 197, 94, 0.18);
            color: #4ADE80;
        }
        .profile-setting-icon.theme {
            background: rgba(251, 191, 36, 0.16);
            color: #FBBF24;
        }
        .profile-setting-icon.danger {
            background: rgba(239, 68, 68, 0.18);
            color: #F87171;
        }
        .profile-setting-icon.pending {
            background: rgba(245, 158, 11, 0.18);
            color: #FBBF24;
        }
        .profile-setting-title.danger {
            color: #F87171 !important;
        }
        .profile-setting-item.danger-item:hover {
            background: rgba(127, 29, 29, 0.35) !important;
            border-color: rgba(248, 113, 113, 0.45) !important;
        }
        .profile-danger-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 12px 0 6px 0;
            padding: 0 4px;
        }
        .profile-danger-divider span {
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #F87171;
            white-space: nowrap;
        }
        .profile-danger-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(248, 113, 113, 0.25);
        }
        .profile-cancel-req-btn {
            font-size: 11px;
            font-weight: 700;
            color: #F87171;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 4px 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .profile-cancel-req-btn:hover {
            background: rgba(239, 68, 68, 0.22);
            border-color: rgba(239, 68, 68, 0.5);
            color: #fca5a5;
        }

        .profile-setting-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .profile-setting-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #DAF1DE;
            line-height: 1.2;
        }
        .profile-setting-desc {
            font-size: 11px;
            font-weight: 500;
            color: #8EB69B;
            line-height: 1.2;
        }

        .profile-setting-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-notif-pill {
            display: inline-flex;
            align-items: center;
            background: #22c55e;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 999px;
            box-shadow: 0 2px 6px rgba(34, 197, 94, 0.35);
        }
        .profile-notif-pill.zero {
            background: rgba(142, 182, 155, 0.25);
            color: #8EB69B;
            box-shadow: none;
        }

        .profile-setting-chevron {
            stroke: #8EB69B;
            transition: transform 0.2s ease;
        }
        .profile-setting-item:hover .profile-setting-chevron {
            transform: translateX(2px);
        }

        /* Alert banners: Dark mode default & Light mode adaptive */
        .profile-alert-banner {
            padding: 14px 20px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.25s ease;
        }
        .profile-alert-banner.success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(74, 222, 128, 0.45);
            color: #4ADE80;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        }
        .profile-alert-banner.error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(248, 113, 113, 0.45);
            color: #F87171;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            font-size: 13.5px;
            font-weight: 600;
        }
        html.light-mode .profile-alert-banner.success {
            background: #f0fdf4 !important;
            border-color: #86efac !important;
            color: #16a34a !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.15) !important;
        }
        html.light-mode .profile-alert-banner.error {
            background: #fef2f2 !important;
            border-color: #fca5a5 !important;
            color: #dc2626 !important;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.12) !important;
        }

        .profile-setting-item:focus-visible {
            outline: 2px solid #22c55e;
            outline-offset: 2px;
        }

        /* Toggle switch */
        .profile-theme-switch {
            width: 44px;
            height: 24px;
            border-radius: 999px;
            background: rgba(142, 182, 155, 0.35);
            position: relative;
            cursor: pointer;
            transition: background 0.25s ease;
            flex-shrink: 0;
        }
        .profile-theme-switch.is-light {
            background: #22c55e;
        }
        .profile-switch-knob {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #ffffff;
            position: absolute;
            top: 3px;
            left: 3px;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
        }
        .profile-theme-switch.is-light .profile-switch-knob {
            transform: translateX(20px);
        }
        @media (prefers-reduced-motion: reduce) {
            .profile-theme-switch,
            .profile-switch-knob {
                transition: none !important;
            }
        }

        /* Modal Notifications List */
        .profile-notif-card {
            display: flex;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(11, 43, 38, 0.45);
            border: 1px solid rgba(142, 182, 155, 0.2);
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            text-align: left;
        }
        .profile-notif-card:hover {
            background: rgba(35, 83, 71, 0.6);
            border-color: rgba(142, 182, 155, 0.4);
        }
        .profile-notif-card.unread {
            background: rgba(35, 83, 71, 0.45);
            border-color: rgba(74, 222, 128, 0.45);
        }
        .profile-notif-card-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .profile-notif-card-icon.approved {
            background: rgba(34, 197, 94, 0.2);
            color: #4ADE80;
        }
        .profile-notif-card-icon.rejected {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }
        .profile-notif-card-icon.pin {
            background: rgba(14, 165, 233, 0.2);
            color: #38bdf8;
        }
        .profile-notif-card-body {
            flex: 1;
            min-width: 0;
        }
        .profile-notif-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 3px;
        }
        .profile-notif-card-title {
            font-size: 13px;
            font-weight: 700;
            color: #DAF1DE;
        }
        .profile-notif-card-time {
            font-size: 11px;
            color: #8EB69B;
            white-space: nowrap;
        }
        .profile-notif-card-msg {
            margin: 0 0 6px 0;
            font-size: 12px;
            color: #c0ded0;
            line-height: 1.45;
        }
        .profile-notif-card-tags {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }
        .notif-tag {
            display: inline-block;
            background: rgba(142, 182, 155, 0.18);
            color: #8EB69B;
            font-size: 11px;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 6px;
        }
        .notif-tag.coords {
            font-family: monospace;
            color: #38bdf8;
            background: rgba(14, 165, 233, 0.15);
        }
        .notif-map-link {
            font-size: 11px;
            font-weight: 700;
            color: #4ADE80;
            text-decoration: none;
            margin-left: auto;
        }
        .notif-map-link:hover {
            text-decoration: underline;
        }
        .profile-notif-unread-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            position: absolute;
            top: 12px;
            right: 12px;
            box-shadow: 0 0 8px rgba(34, 197, 94, 0.6);
        }

        .profile-edit-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 18px;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #235347, #163832) !important;
            border: 1.5px solid #8EB69B !important;
            color: #DAF1DE !important;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(35, 83, 71, 0.45) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            font-family: inherit;
        }
        .profile-edit-btn svg { stroke: #DAF1DE !important; }
        .profile-edit-btn:hover {
            background: #235347 !important;
            border-color: #DAF1DE !important;
            color: #FFFFFF !important;
            box-shadow: 0 6px 20px rgba(35, 83, 71, 0.6) !important;
            transform: translateY(-1px);
        }
        .profile-edit-btn:hover svg { stroke: #FFFFFF !important; }

        .profile-logout-btn {
            margin-top: 12px;
            width: 100%;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 12px;
            background: rgba(239, 68, 68, 0.15);
            border: 1.5px solid rgba(239, 68, 68, 0.5);
            color: #f87171 !important;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.15);
            transition: all 0.25s ease;
            font-family: inherit;
        }
        .profile-logout-btn svg {
            stroke: #f87171 !important;
            transition: stroke 0.25s ease;
        }
        .profile-logout-btn:hover {
            background: #ef4444 !important;
            border-color: #ef4444 !important;
            color: #FFFFFF !important;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
            transform: translateY(-2px);
        }
        .profile-logout-btn:hover svg {
            stroke: #FFFFFF !important;
        }

        @media (max-width: 768px) {
            .profile-alert-container {
                max-width: 480px !important;
                padding: 0 16px !important;
            }
            .profile-wrapper {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                padding: 16px 16px 60px !important;
                gap: 16px !important;
                max-width: 480px !important;
                margin: 0 auto !important;
                width: 100% !important;
                box-sizing: border-box !important;
                overflow-x: hidden !important;
            }
            .profile-sidebar-col {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 16px !important;
                box-sizing: border-box !important;
            }
            .profile-info-stack {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 16px !important;
                box-sizing: border-box !important;
            }
            .profile-avatar-card,
            .profile-logout-card,
            .profile-info-card {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
                margin: 0 auto !important;
            }
            .profile-avatar-card {
                padding: 26px 16px !important;
            }
            .profile-logout-card {
                padding: 20px 16px !important;
            }
            .profile-info-card {
                overflow: hidden !important;
            }
            .profile-info-header {
                padding: 14px 16px !important;
                gap: 10px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                flex-wrap: wrap !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
            }
            .profile-info-header h3 {
                font-size: 14px !important;
            }
            .profile-edit-btn {
                padding: 7px 14px !important;
                font-size: 12.5px !important;
                flex-shrink: 0 !important;
            }
            .profile-info-rows {
                padding: 2px 0 !important;
                box-sizing: border-box !important;
            }
            .profile-info-row {
                padding: 12px 16px !important;
                gap: 10px !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
            }
            .profile-info-row-label {
                width: 85px !important;
                font-size: 11px !important;
                flex-shrink: 0 !important;
            }
            .profile-info-row-value {
                font-size: 13.5px !important;
                min-width: 0 !important;
                word-break: break-all !important;
                overflow-wrap: anywhere !important;
            }
            .profile-admin-btn,
            .profile-logout-btn,
            .ws-btn-red {
                width: 100% !important;
                box-sizing: border-box !important;
                justify-content: center !important;
            }
        }

        /* ── LIGHT MODE OVERRIDES ── */
        html.light-mode .ws-page-hero {
            background: linear-gradient(135deg, #DAF1DE 0%, #EEF7F1 100%) !important;
            border-bottom: 1px solid rgba(142, 182, 155, 0.4) !important;
            box-shadow: 0 2px 12px rgba(5, 31, 32, 0.05) !important;
        }
        html.light-mode .ws-page-title { color: #051F20 !important; }
        html.light-mode .ws-page-sub   { color: #163832 !important; }
        html.light-mode .ws-breadcrumb { color: #235347 !important; }
        html.light-mode .ws-breadcrumb a { color: #051F20 !important; }
        html.light-mode .ws-breadcrumb span { color: #051F20 !important; }

        html.light-mode .auth-required-banner {
            background: #FFFFFF !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.08) !important;
            color: #0f172a !important;
        }
        html.light-mode .auth-required-banner h2 { color: #0f172a !important; font-weight: 800 !important; }
        html.light-mode .auth-required-banner p  { color: #475569 !important; }

        html.light-mode .profile-avatar-card {
            background: #FFFFFF !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
            border-radius: 20px !important;
        }
        html.light-mode .profile-avatar {
            background: #22c55e !important;
            border: none !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
        }
        html.light-mode .profile-name { color: #0f172a !important; font-weight: 900 !important; }
        html.light-mode .profile-role-badge,
        .light-mode .profile-role-badge,
        body.light-mode .profile-role-badge {
            background: transparent !important;
            border: none !important;
            color: #64748b !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            padding: 0 !important;
            border-radius: 0 !important;
        }
        html.light-mode .profile-stat-row { border-top-color: #f1f5f9 !important; }
        html.light-mode .profile-stat-num { color: #16a34a !important; font-weight: 900 !important; }
        html.light-mode .profile-stat-label { color: #475569 !important; font-weight: 700 !important; }

        html.light-mode .profile-info-card {
            background: #FFFFFF !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05) !important;
            border-radius: 20px !important;
        }
        html.light-mode .profile-info-header { border-bottom-color: #f1f5f9 !important; }
        html.light-mode .profile-info-header-icon {
            background: #f0fdf4 !important;
            border: 1px solid #86efac !important;
            box-shadow: 0 2px 6px rgba(34, 197, 94, 0.1) !important;
        }
        html.light-mode .profile-info-header-icon svg { stroke: #16a34a !important; }
        html.light-mode .profile-info-header h3 { color: #0f172a !important; font-weight: 800 !important; }
        html.light-mode .profile-info-row { border-bottom-color: #f1f5f9 !important; }
        html.light-mode .profile-info-row-label { color: #16a34a !important; font-weight: 800 !important; }
        html.light-mode .profile-info-row-value,
        .light-mode .profile-info-row-value,
        body.light-mode .profile-info-row-value { color: #0f172a !important; font-weight: 700 !important; }
        html.light-mode .profile-user-id-val,
        .light-mode .profile-user-id-val,
        body.light-mode .profile-user-id-val { color: #0f172a !important; }
        html.light-mode .profile-stat-count-val,
        .light-mode .profile-stat-count-val,
        body.light-mode .profile-stat-count-val { color: #000000 !important; }
        html.light-mode .profile-scan-link,
        .light-mode .profile-scan-link,
        body.light-mode .profile-scan-link { color: #16a34a !important; }
        html.light-mode .profile-scan-link svg,
        .light-mode .profile-scan-link svg,
        body.light-mode .profile-scan-link svg { stroke: #16a34a !important; }

        html.light-mode .profile-logout-card {
            background: #FFF5F5 !important;
            border: 1.5px solid #fecaca !important;
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.06) !important;
            border-radius: 20px !important;
        }
        html.light-mode .profile-logout-info h4 { color: #0f172a !important; font-weight: 800 !important; }
        html.light-mode .profile-logout-info p  { color: #475569 !important; }

        html.light-mode .ws-btn-green,
        .light-mode .ws-btn-green,
        body.light-mode .ws-btn-green,
        html.light-mode .profile-admin-btn,
        .light-mode .profile-admin-btn,
        body.light-mode .profile-admin-btn,
        html.light-mode .profile-edit-btn,
        .light-mode .profile-edit-btn,
        body.light-mode .profile-edit-btn {
            background: #22c55e !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        html.light-mode .ws-btn-green svg,
        .light-mode .ws-btn-green svg,
        body.light-mode .ws-btn-green svg,
        html.light-mode .profile-admin-btn svg,
        .light-mode .profile-admin-btn svg,
        body.light-mode .profile-admin-btn svg,
        html.light-mode .profile-edit-btn svg,
        .light-mode .profile-edit-btn svg,
        body.light-mode .profile-edit-btn svg {
            stroke: #ffffff !important;
        }
        html.light-mode .ws-btn-green:hover,
        .light-mode .ws-btn-green:hover,
        body.light-mode .ws-btn-green:hover,
        html.light-mode .profile-admin-btn:hover,
        .light-mode .profile-admin-btn:hover,
        body.light-mode .profile-admin-btn:hover,
        html.light-mode .profile-edit-btn:hover,
        .light-mode .profile-edit-btn:hover,
        body.light-mode .profile-edit-btn:hover {
            background: #16a34a !important;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.45) !important;
            transform: translateY(-2px) !important;
        }

        html.light-mode .profile-logout-btn {
            background: #fef2f2 !important;
            border: 1.5px solid #fca5a5 !important;
            color: #dc2626 !important;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.12) !important;
        }
        html.light-mode .profile-logout-btn svg {
            stroke: #dc2626 !important;
        }
        html.light-mode .profile-logout-btn:hover {
            background: #dc2626 !important;
            border-color: #dc2626 !important;
            color: #ffffff !important;
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.35) !important;
        }
        html.light-mode .profile-logout-btn:hover svg {
            stroke: #ffffff !important;
        }

        /* ── Modal Light Mode Overrides: Image 5 (Avatar) & Image 6 (Save Button) ── */
        html.light-mode .profile-modal-avatar,
        .light-mode .profile-modal-avatar,
        body.light-mode .profile-modal-avatar {
            background: #22c55e !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
        }
        html.light-mode .profile-modal-save-btn,
        .light-mode .profile-modal-save-btn,
        body.light-mode .profile-modal-save-btn {
            background: #22c55e !important;
            border: none !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
            border-radius: 12px !important;
        }
        html.light-mode .profile-modal-save-btn:hover,
        .light-mode .profile-modal-save-btn:hover,
        body.light-mode .profile-modal-save-btn:hover {
            background: #16a34a !important;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.45) !important;
            transform: translateY(-2px) !important;
        }
        html.light-mode .profile-modal-cancel-btn,
        .light-mode .profile-modal-cancel-btn,
        body.light-mode .profile-modal-cancel-btn {
            background: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
        }
        html.light-mode .profile-modal-cancel-btn:hover,
        .light-mode .profile-modal-cancel-btn:hover,
        body.light-mode .profile-modal-cancel-btn:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }
        html.light-mode .profile-modal-footer,
        .light-mode .profile-modal-footer,
        body.light-mode .profile-modal-footer {
            background: #ffffff !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        html.light-mode .profile-modal-card,
        .light-mode .profile-modal-card,
        body.light-mode .profile-modal-card {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.15) !important;
        }
        html.light-mode .profile-modal-header,
        .light-mode .profile-modal-header,
        body.light-mode .profile-modal-header,
        html.light-mode .profile-modal-footer,
        .light-mode .profile-modal-footer,
        body.light-mode .profile-modal-footer {
            background: #ffffff !important;
            border-color: #f1f5f9 !important;
        }
        html.light-mode .profile-modal-title,
        .light-mode .profile-modal-title,
        body.light-mode .profile-modal-title {
            color: #0f172a !important;
        }
        html.light-mode .profile-modal-last-updated,
        .light-mode .profile-modal-last-updated,
        body.light-mode .profile-modal-last-updated {
            color: #64748b !important;
        }
        html.light-mode .profile-modal-body,
        .light-mode .profile-modal-body,
        body.light-mode .profile-modal-body {
            scrollbar-color: #cbd5e1 transparent !important;
        }
        html.light-mode .profile-modal-body::-webkit-scrollbar-thumb,
        .light-mode .profile-modal-body::-webkit-scrollbar-thumb,
        body.light-mode .profile-modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
        }
        html.light-mode .profile-modal-body::-webkit-scrollbar-thumb:hover,
        .light-mode .profile-modal-body::-webkit-scrollbar-thumb:hover,
        body.light-mode .profile-modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8 !important;
        }
        html.light-mode .profile-modal-body::-webkit-scrollbar-track,
        .light-mode .profile-modal-body::-webkit-scrollbar-track,
        body.light-mode .profile-modal-body::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.04) !important;
        }
        html.light-mode .profile-modal-preview,
        .light-mode .profile-modal-preview,
        body.light-mode .profile-modal-preview {
            border-bottom-color: #f1f5f9 !important;
        }
        html.light-mode .profile-modal-preview-title,
        .light-mode .profile-modal-preview-title,
        body.light-mode .profile-modal-preview-title {
            color: #64748b !important;
        }
        html.light-mode .profile-modal-preview-name,
        .light-mode .profile-modal-preview-name,
        body.light-mode .profile-modal-preview-name {
            color: #0f172a !important;
        }
        html.light-mode .profile-modal-preview-role,
        .light-mode .profile-modal-preview-role,
        body.light-mode .profile-modal-preview-role {
            color: #64748b !important;
        }
        html.light-mode .profile-modal-preview-id,
        .light-mode .profile-modal-preview-id,
        body.light-mode .profile-modal-preview-id {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            color: #64748b !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        html.light-mode .profile-modal-preview-id svg,
        .light-mode .profile-modal-preview-id svg,
        body.light-mode .profile-modal-preview-id svg {
            stroke: #64748b !important;
        }
        html.light-mode .profile-modal-pencil-btn,
        .light-mode .profile-modal-pencil-btn,
        body.light-mode .profile-modal-pencil-btn {
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12) !important;
        }
        html.light-mode .profile-modal-pencil-btn svg,
        .light-mode .profile-modal-pencil-btn svg,
        body.light-mode .profile-modal-pencil-btn svg {
            stroke: #475569 !important;
        }
        html.light-mode .profile-modal-pencil-btn:hover,
        .light-mode .profile-modal-pencil-btn:hover,
        body.light-mode .profile-modal-pencil-btn:hover {
            border-color: #94a3b8 !important;
            background: #f8fafc !important;
            transform: scale(1.1) !important;
        }
        html.light-mode .floating-input,
        .light-mode .floating-input,
        body.light-mode .floating-input {
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
            font-weight: 600 !important;
            transition: border-color 0.2s ease, box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        /* Gray focus border & gray popping ring animation in light mode */
        html.light-mode .floating-input:focus,
        .light-mode .floating-input:focus,
        body.light-mode .floating-input:focus {
            border-color: #94a3b8 !important;
            box-shadow: 0 0 0 3.5px rgba(148, 163, 184, 0.35) !important;
        }
        html.light-mode .floating-label,
        .light-mode .floating-label,
        body.light-mode .floating-label {
            color: #475569 !important;
            font-weight: 600 !important;
        }
        html.light-mode .floating-input ~ .floating-label,
        .light-mode .floating-input ~ .floating-label,
        body.light-mode .floating-input ~ .floating-label {
            color: #475569 !important;
        }
        html.light-mode .floating-input:not(:placeholder-shown) ~ .floating-label,
        .light-mode .floating-input:not(:placeholder-shown) ~ .floating-label,
        body.light-mode .floating-input:not(:placeholder-shown) ~ .floating-label {
            background: #ffffff !important;
            color: #475569 !important;
            font-weight: 700 !important;
        }
        html.light-mode .floating-input:focus ~ .floating-label,
        .light-mode .floating-input:focus ~ .floating-label,
        body.light-mode .floating-input:focus ~ .floating-label,
        html.light-mode .floating-input:focus:not(:placeholder-shown) ~ .floating-label,
        .light-mode .floating-input:focus:not(:placeholder-shown) ~ .floating-label,
        body.light-mode .floating-input:focus:not(:placeholder-shown) ~ .floating-label {
            background: #ffffff !important;
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        html.light-mode .floating-input.border-correct,
        .light-mode .floating-input.border-correct,
        body.light-mode .floating-input.border-correct { border-color: #64748b !important; box-shadow: 0 0 0 3.5px rgba(100, 116, 139, 0.2) !important; }
        html.light-mode .floating-input.border-incorrect,
        .light-mode .floating-input.border-incorrect,
        body.light-mode .floating-input.border-incorrect { border-color: #dc2626 !important; box-shadow: 0 0 0 3.5px rgba(220, 38, 38, 0.2) !important; }

        html.light-mode .floating-input.input-locked,
        .light-mode .floating-input.input-locked,
        body.light-mode .floating-input.input-locked,
        html.light-mode .floating-input:disabled,
        .light-mode .floating-input:disabled,
        body.light-mode .floating-input:disabled {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #64748b !important;
            opacity: 0.75 !important;
            cursor: not-allowed !important;
        }
        html.light-mode .floating-input.input-locked ~ .floating-label,
        .light-mode .floating-input.input-locked ~ .floating-label,
        body.light-mode .floating-input.input-locked ~ .floating-label,
        html.light-mode .floating-input:disabled ~ .floating-label,
        .light-mode .floating-input:disabled ~ .floating-label,
        body.light-mode .floating-input:disabled ~ .floating-label {
            color: #64748b !important;
        }
        html.light-mode .floating-input.input-locked ~ .input-lead-icon,
        .light-mode .floating-input.input-locked ~ .input-lead-icon,
        body.light-mode .floating-input.input-locked ~ .input-lead-icon,
        html.light-mode .floating-input:disabled ~ .input-lead-icon,
        .light-mode .floating-input:disabled ~ .input-lead-icon,
        body.light-mode .floating-input:disabled ~ .input-lead-icon {
            stroke: #64748b !important;
        }

        html.light-mode .input-lead-icon,
        .light-mode .input-lead-icon,
        body.light-mode .input-lead-icon {
            stroke: #475569 !important;
        }
        html.light-mode .input-eye-btn,
        .light-mode .input-eye-btn,
        body.light-mode .input-eye-btn {
            color: #475569 !important;
        }
        html.light-mode .input-eye-btn:hover,
        .light-mode .input-eye-btn:hover,
        body.light-mode .input-eye-btn:hover {
            color: #0f172a !important;
        }

        html.light-mode .profile-modal-divider,
        .light-mode .profile-modal-divider,
        body.light-mode .profile-modal-divider {
            border-top: 1px solid #e2e8f0 !important;
        }
        /* Black section title for Change Password (Optional) in light mode */
        html.light-mode .profile-modal-section-title,
        .light-mode .profile-modal-section-title,
        body.light-mode .profile-modal-section-title {
            color: #000000 !important;
            font-weight: 800 !important;
        }

        html.light-mode .pass-helper-label,
        .light-mode .pass-helper-label,
        body.light-mode .pass-helper-label {
            color: #475569 !important;
            font-weight: 600 !important;
        }
        html.light-mode .meter-bar,
        .light-mode .meter-bar,
        body.light-mode .meter-bar {
            background: #cbd5e1 !important;
        }

        html.light-mode .pass-clear-btn,
        .light-mode .pass-clear-btn,
        body.light-mode .pass-clear-btn {
            color: #475569 !important;
            font-weight: 700 !important;
        }
        html.light-mode .pass-clear-btn:hover,
        .light-mode .pass-clear-btn:hover,
        body.light-mode .pass-clear-btn:hover {
            color: #0f172a !important;
        }

        html.light-mode .pass-status-checking,
        .light-mode .pass-status-checking,
        body.light-mode .pass-status-checking { color: #475569 !important; }
        html.light-mode .pass-status-correct,
        .light-mode .pass-status-correct,
        body.light-mode .pass-status-correct  { color: #15803d !important; font-weight: 700 !important; }
        html.light-mode .pass-status-error,
        .light-mode .pass-status-error,
        body.light-mode .pass-status-error    { color: #dc2626 !important; font-weight: 700 !important; }

        html.light-mode .profile-forgot-link,
        .light-mode .profile-forgot-link,
        body.light-mode .profile-forgot-link {
            color: #16a34a !important;
        }
        html.light-mode .profile-forgot-link:hover,
        .light-mode .profile-forgot-link:hover,
        body.light-mode .profile-forgot-link:hover {
            color: #15803d !important;
            text-decoration: underline;
        }

        html.light-mode .pass-req-badge,
        .light-mode .pass-req-badge,
        body.light-mode .pass-req-badge {
            background: #f1f5f9 !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #334155 !important;
            font-weight: 700 !important;
        }
        html.light-mode .pass-req-badge.active,
        .light-mode .pass-req-badge.active,
        body.light-mode .pass-req-badge.active {
            background: #ecfdf5 !important;
            border-color: #86efac !important;
            color: #15803d !important;
            font-weight: 800 !important;
        }
        html.light-mode .pass-req-icon,
        .light-mode .pass-req-icon,
        body.light-mode .pass-req-icon {
            background: #cbd5e1 !important;
            color: #334155 !important;
        }
        html.light-mode .pass-req-badge.active .pass-req-icon,
        .light-mode .pass-req-badge.active .pass-req-icon,
        body.light-mode .pass-req-badge.active .pass-req-icon {
            background: #22c55e !important;
            color: #ffffff !important;
        }
        html.light-mode .modal-close-circle-btn,
        .light-mode .modal-close-circle-btn,
        body.light-mode .modal-close-circle-btn {
            background: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
            color: #64748b !important;
        }
        html.light-mode .modal-close-circle-btn:hover,
        .light-mode .modal-close-circle-btn:hover,
        body.light-mode .modal-close-circle-btn:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
        }

        .ws-breadcrumb-current { color: #DAF1DE; font-weight: 700; }
        html.light-mode .ws-breadcrumb-current { color: #051F20 !important; font-weight: 700 !important; }

        html.light-mode .profile-card-divider {
            background: #e2e8f0 !important;
        }
        html.light-mode .profile-settings-header-label {
            color: #64748b !important;
        }
        html.light-mode .profile-setting-item {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }
        html.light-mode .profile-setting-item:hover {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
        }
        html.light-mode .profile-setting-icon.notif {
            background: #dcfce7 !important;
            color: #15803d !important;
        }
        html.light-mode .profile-setting-icon.edit {
            background: #dcfce7 !important;
            color: #15803d !important;
        }
        html.light-mode .profile-setting-icon.theme {
            background: #fef3c7 !important;
            color: #b45309 !important;
        }
        html.light-mode .profile-setting-icon.danger {
            background: #fee2e2 !important;
            color: #b91c1c !important;
        }
        html.light-mode .profile-setting-icon.pending {
            background: #fef3c7 !important;
            color: #b45309 !important;
        }
        html.light-mode .profile-setting-title.danger {
            color: #b91c1c !important;
        }
        html.light-mode .profile-setting-item.danger-item:hover {
            background: #fef2f2 !important;
            border-color: #fca5a5 !important;
            box-shadow: 0 4px 12px rgba(185, 28, 28, 0.08) !important;
        }
        html.light-mode .profile-danger-divider span {
            color: #b91c1c !important;
        }
        html.light-mode .profile-danger-divider::after {
            background: #fecaca !important;
        }
        html.light-mode .profile-cancel-req-btn {
            background: #fee2e2 !important;
            color: #b91c1c !important;
            border-color: #fca5a5 !important;
        }
        html.light-mode .profile-cancel-req-btn:hover {
            background: #fecaca !important;
            color: #991b1b !important;
        }
        html.light-mode .profile-setting-title {
            color: #0f172a !important;
        }
        html.light-mode .profile-setting-desc {
            color: #64748b !important;
        }
        html.light-mode .profile-setting-chevron {
            stroke: #94a3b8 !important;
        }
        html.light-mode .profile-notif-pill.zero {
            background: #e2e8f0 !important;
            color: #64748b !important;
        }
        html.light-mode .profile-theme-switch {
            background: #cbd5e1 !important;
        }
        html.light-mode .profile-theme-switch.is-light {
            background: #22c55e !important;
        }
        html.light-mode .profile-notif-card {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04) !important;
        }
        html.light-mode .profile-notif-card:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }
        html.light-mode .profile-notif-card.unread {
            background: #f0fdf4 !important;
            border-color: #86efac !important;
        }
        html.light-mode .profile-notif-card-title {
            color: #0f172a !important;
        }
        html.light-mode .profile-notif-card-time {
            color: #64748b !important;
        }
        html.light-mode .profile-notif-card-msg {
            color: #334155 !important;
        }
        html.light-mode .notif-tag {
            background: #f1f5f9 !important;
            color: #475569 !important;
        }
        html.light-mode .notif-tag.coords {
            background: #e0f2fe !important;
            color: #0284c7 !important;
        }
        html.light-mode .notif-map-link {
            color: #16a34a !important;
        }
        html.light-mode .profile-admin-btn {
            border-radius: 12px !important;
        }
    </style>

    {{-- Page Hero --}}
    <div class="ws-page-hero">
        <div class="ws-page-hero-inner ui-reveal">
            <div class="ws-breadcrumb">
                <a href="{{ route('website.home') }}">Home</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                <span class="ws-breadcrumb-current">Profile</span>
            </div>
            <h1 class="ws-page-title">My Profile</h1>
            <p class="ws-page-sub">View your account information and scan activity.</p>
        </div>
    </div>

    @guest
        <div style="padding:24px;">
            <div class="auth-required-banner">
                <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#235347,#163832);
                            border:1.5px solid #8EB69B;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;
                            box-shadow:0 8px 24px rgba(35,83,71,0.4);">
                    <svg width="32" height="32" fill="none" stroke="#4ADE80" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h2>Login Required</h2>
                <p>Please log in to view your AgriCo profile and account details.</p>
                <a href="{{ route('login') }}" class="ws-btn-green">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
                    </svg>
                    Login to AgriCo
                </a>
            </div>
        </div>
    @else
    @php
        /** @var \App\Models\User $user */
        $initial   = strtoupper(substr($user->name ?? 'U', 0, 1));
        $userId    = $user->formatted_id ?? ('USR-' . str_pad($user->id, 4, '0', STR_PAD_LEFT));
        $joinedDate = $user->created_at ? $user->created_at->format('d M Y') : '—';
        $roleBadge  = ucfirst($user->role ?? 'User');
        $isAdminUser = in_array(strtolower($user->role ?? ''), ['admin', 'superadmin', 'super admin']) || Auth::guard('admin')->check();
    @endphp

    @if(session('success'))
        <div class="profile-alert-container">
            <div class="profile-alert-banner success">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="profile-alert-container">
            <div class="profile-alert-banner error">
                <ul style="margin:0; padding-left:18px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="profile-wrapper" x-data="{
        showEditModal: false,
        showDeleteModal: false,
        showCancelDeleteModal: false,
        showPasswordModal: {{ ($errors->has('current_password') || $errors->has('password')) ? 'true' : 'false' }},
        editTab: 'personal',
        editName: '{{ addslashes(old('name', $user->name ?? '')) }}',
        editEmail: '{{ addslashes(old('email', $user->email ?? '')) }}',
        editStudentId: '{{ addslashes(old('student_id', $user->student_id ?? '')) }}',
        editSection: '{{ addslashes(old('section', $user->section ?? '')) }}',
        photoPreview: null,

        // Password change state
        showCurrentPass: false,
        showNewPass: false,
        showConfirmPass: false,
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
        isCurrentPassCorrect: false,
        checkingCurrentPass: false,
        passStatusMsg: '',

        // Email change state
        newEmailInput: '',
        emailReadonly: true,
        emailStep: 1,
        emailCode: '',
        sendingEmailCode: false,
        verifyingEmailCode: false,
        emailChangeMsg: '',
        emailChangeError: '',
        resendCountdown: 0,
        resendTimer: null,

        // Account deletion state
        deletePassword: '',
        showDeletePass: false,
        deleteReason: 'I no longer need the account',
        deleteNotes: '',
        deleteConfirmed: false,
        submittingDeletion: false,
        deleteError: '',
        deleteSuccessMsg: '',

        // Cancel deletion state
        cancellingDeletion: false,
        cancelDeletionError: '',
        cancelDeletionSuccess: '',

        async sendEmailCode() {
            const email = (this.newEmailInput || '').trim();
            if (!email || !email.includes('@')) {
                this.emailChangeError = 'Please enter a valid email address.';
                this.emailChangeMsg = '';
                return;
            }
            this.sendingEmailCode = true;
            this.emailChangeError = '';
            this.emailChangeMsg = '';
            try {
                const res = await fetch('{{ route('website.profile.email.send-code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ new_email: email })
                });
                const data = await res.json();
                if (data.success) {
                    this.emailStep = 2;
                    this.emailChangeMsg = data.message;
                    this.startResendCountdown(60);
                } else {
                    this.emailChangeError = data.message || 'Could not send verification code.';
                }
            } catch (e) {
                this.emailChangeError = 'Network error. Please try again.';
            } finally {
                this.sendingEmailCode = false;
            }
        },

        startResendCountdown(seconds) {
            this.resendCountdown = seconds;
            if (this.resendTimer) clearInterval(this.resendTimer);
            this.resendTimer = setInterval(() => {
                if (this.resendCountdown > 0) {
                    this.resendCountdown--;
                } else {
                    clearInterval(this.resendTimer);
                }
            }, 1000);
        },

        async verifyEmailCode() {
            const code = (this.emailCode || '').trim();
            if (!code || code.length !== 6) {
                this.emailChangeError = 'Please enter the 6-digit verification code.';
                this.emailChangeMsg = '';
                return;
            }
            this.verifyingEmailCode = true;
            this.emailChangeError = '';
            this.emailChangeMsg = '';
            try {
                const res = await fetch('{{ route('website.profile.email.verify-code') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        code: code,
                        new_email: (this.newEmailInput || '').trim()
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.emailChangeMsg = data.message;
                    this.editEmail = data.new_email;
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    this.emailChangeError = data.message || 'The verification code is invalid.';
                }
            } catch (e) {
                this.emailChangeError = 'Network error. Please try again.';
            } finally {
                this.verifyingEmailCode = false;
            }
        },

        async submitDeletion() {
            if (!this.deleteConfirmed) {
                this.deleteError = 'Please confirm that you understand account deletion is permanent.';
                return;
            }
            if (!this.deletePassword) {
                this.deleteError = 'Please enter your password to confirm account deletion.';
                return;
            }
            this.submittingDeletion = true;
            this.deleteError = '';
            this.deleteSuccessMsg = '';
            try {
                const res = await fetch('{{ route('website.profile.delete-request') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        password: this.deletePassword,
                        confirmation: true,
                        reason: this.deleteReason,
                        notes: this.deleteNotes
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.deleteSuccessMsg = data.message;
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    this.deleteError = data.message || 'Could not submit deletion request.';
                }
            } catch (e) {
                this.deleteError = 'Network error. Please try again.';
            } finally {
                this.submittingDeletion = false;
            }
        },

        async submitCancelDeletion() {
            this.cancellingDeletion = true;
            this.cancelDeletionError = '';
            try {
                const res = await fetch('{{ route('website.profile.delete-cancel') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.cancelDeletionSuccess = data.message;
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    this.cancelDeletionError = data.message || 'Could not cancel request.';
                }
            } catch (e) {
                this.cancelDeletionError = 'Network error. Please try again.';
            } finally {
                this.cancellingDeletion = false;
            }
        },

        async verifyCurrentPassword() {
            if (!this.currentPassword || this.currentPassword.trim() === '') {
                this.isCurrentPassCorrect = false;
                this.passStatusMsg = '';
                this.checkingCurrentPass = false;
                this.newPassword = '';
                this.confirmPassword = '';
                return;
            }
            this.checkingCurrentPass = true;
            this.passStatusMsg = '';
            try {
                const res = await fetch('{{ route('website.profile.verify-password') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ current_password: this.currentPassword })
                });
                const data = await res.json();
                this.isCurrentPassCorrect = !!data.valid;
                this.passStatusMsg = data.message || (data.valid ? 'Current password verified' : 'Incorrect current password');
                if (!this.isCurrentPassCorrect) {
                    this.newPassword = '';
                    this.confirmPassword = '';
                }
            } catch (e) {
                this.isCurrentPassCorrect = false;
                this.passStatusMsg = 'Verification failed';
            } finally {
                this.checkingCurrentPass = false;
            }
        },

        get hasUppercase() { return /[A-Z]/.test(this.newPassword); },
        get hasLowercase() { return /[a-z]/.test(this.newPassword); },
        get hasNumber() { return /[0-9]/.test(this.newPassword); },
        get hasSpecial() { return /[^A-Za-z0-9]/.test(this.newPassword); },
        get hasMinLen() { return this.newPassword.length >= 8; },
        get strengthScore() {
            let score = 0;
            if (this.hasMinLen) score++;
            if (this.hasUppercase) score++;
            if (this.hasLowercase) score++;
            if (this.hasNumber) score++;
            if (this.hasSpecial) score++;
            return score;
        },
        clearPasswords() {
            this.currentPassword = '';
            this.newPassword = '';
            this.confirmPassword = '';
            this.isCurrentPassCorrect = false;
            this.checkingCurrentPass = false;
            this.passStatusMsg = '';
        },
        showNotificationsModal: false,
        isLightMode: document.documentElement.classList.contains('light-mode'),
        unreadCount: {{ (int) ($unreadCount ?? 0) }},
        init() {
            window.addEventListener('agrico:theme-changed', (e) => {
                if (e.detail && typeof e.detail.isLight === 'boolean') {
                    this.isLightMode = e.detail.isLight;
                } else {
                    this.isLightMode = document.documentElement.classList.contains('light-mode');
                }
            });

            this.$watch('showEditModal', (val) => {
                if (val) {
                    this.newEmailInput = '';
                    this.emailCode = '';
                    this.emailStep = 1;
                    this.emailChangeError = '';
                    this.emailChangeMsg = '';
                    this.emailReadonly = true;
                }
            });

            this.$watch('editTab', (tab) => {
                if (tab === 'email') {
                    this.newEmailInput = '';
                    this.emailReadonly = true;
                }
            });
        },
        toggleTheme() {
            // Update local state immediately for responsive switch animation (200-300ms)
            this.isLightMode = !this.isLightMode;

            if (typeof window.agricoToggleTheme === 'function') {
                window.agricoToggleTheme();
            } else {
                const isLight = this.isLightMode;
                document.documentElement.classList.toggle('light-mode', isLight);
                const newTheme = isLight ? 'light' : 'dark';
                document.documentElement.style.colorScheme = newTheme;
                localStorage.setItem('agrico-theme', newTheme);
                fetch('{{ route("user.update-theme") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ theme: newTheme })
                }).catch(e => console.warn(e));
            }
        },
        async markAllAsRead() {
            try {
                await fetch('{{ route("website.notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ user_id: '{{ $userId }}' })
                });
                this.unreadCount = 0;
                document.querySelectorAll('.profile-notif-card.unread').forEach(el => {
                    el.classList.remove('unread');
                    const dot = el.querySelector('.profile-notif-unread-dot');
                    if (dot) dot.remove();
                });
            } catch (e) {
                console.error(e);
            }
        },
        async markSingleRead(id, el) {
            if (el && el.classList.contains('unread')) {
                try {
                    await fetch(`/api/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    el.classList.remove('unread');
                    const dot = el.querySelector('.profile-notif-unread-dot');
                    if (dot) dot.remove();
                    if (this.unreadCount > 0) this.unreadCount--;
                } catch (e) {
                    console.error(e);
                }
            }
        }
    }" x-init="$watch('showEditModal', val => {
        if (val) {
            clearPasswords();
            emailChangeError = '';
            emailChangeMsg = '';
            emailStep = 1;
            emailCode = '';
            document.documentElement.classList.add('no-scroll');
            document.body.classList.add('no-scroll');
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
        } else {
            photoPreview = null;
            if ($refs.photoInput) $refs.photoInput.value = '';
            document.documentElement.classList.remove('no-scroll');
            document.body.classList.remove('no-scroll');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
        }
    }); $watch('showDeleteModal', val => {
        if (val) {
            deletePassword = '';
            deleteConfirmed = false;
            deleteError = '';
            deleteSuccessMsg = '';
            document.documentElement.classList.add('no-scroll');
            document.body.classList.add('no-scroll');
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
        } else {
            document.documentElement.classList.remove('no-scroll');
            document.body.classList.remove('no-scroll');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
        }
    }); $watch('showCancelDeleteModal', val => {
        if (val) {
            cancelDeletionError = '';
            cancelDeletionSuccess = '';
            document.documentElement.classList.add('no-scroll');
            document.body.classList.add('no-scroll');
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
        } else {
            document.documentElement.classList.remove('no-scroll');
            document.body.classList.remove('no-scroll');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
        }
    }); $watch('showNotificationsModal', val => {
        if (val) {
            document.documentElement.classList.add('no-scroll');
            document.body.classList.add('no-scroll');
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
        } else {
            document.documentElement.classList.remove('no-scroll');
            document.body.classList.remove('no-scroll');
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';
        }
    }); clearPasswords();">

        {{-- LEFT COLUMN: Avatar Card + Logout Card --}}
        <div class="profile-sidebar-col">
            <div class="profile-avatar-card ui-reveal">
                <div class="profile-avatar" style="overflow:hidden; padding:0; display:flex; align-items:center; justify-content:center;">
                    @if($user->profile_photo_url)
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                    @else
                        {{ $initial }}
                    @endif
                </div>
                <p class="profile-name">{{ $user->name }}</p>
                <div class="profile-role-badge">{{ $roleBadge }}</div>

                @if($isAdminUser)
                    <a href="{{ route('dashboard') }}" class="profile-admin-btn">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        </svg>
                        Admin Dashboard
                    </a>
                @endif

                {{-- Line Separator --}}
                <div class="profile-card-divider"></div>

                {{-- Settings Group: Edit Info, Notifications, Appearance & Danger Zone --}}
                <div class="profile-settings-group">
                    <span class="profile-settings-header-label">Settings</span>

                    {{-- 1. Edit Information --}}
                    <div class="profile-setting-item" @click="showEditModal = true" role="button" tabindex="0">
                        <div class="profile-setting-left">
                            <div class="profile-setting-icon edit">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </div>
                            <div class="profile-setting-text">
                                <span class="profile-setting-title">Edit Information</span>
                                <span class="profile-setting-desc">Name, email and password</span>
                            </div>
                        </div>
                        <div class="profile-setting-right">
                            <svg class="profile-setting-chevron" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </div>
                    </div>

                    {{-- 2. Notification --}}
                    <div class="profile-setting-item" @click="showNotificationsModal = true" role="button" tabindex="0">
                        <div class="profile-setting-left">
                            <div class="profile-setting-icon notif">
                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                </svg>
                            </div>
                            <div class="profile-setting-text">
                                <span class="profile-setting-title">Notifications</span>
                                <span class="profile-setting-desc">Pins & scan reviews</span>
                            </div>
                        </div>
                        <div class="profile-setting-right">
                            <template x-if="unreadCount > 0">
                                <span class="profile-notif-pill" x-text="unreadCount + ' new'"></span>
                            </template>
                            <template x-if="unreadCount === 0">
                                <span class="profile-notif-pill zero">All read</span>
                            </template>
                            <svg class="profile-setting-chevron" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </div>
                    </div>

                    {{-- 3. Dark and Light Mode --}}
                    <div class="profile-setting-item" 
                         @click="toggleTheme()" 
                         @keydown.space.prevent="toggleTheme()" 
                         @keydown.enter.prevent="toggleTheme()"
                         role="switch" 
                         :aria-checked="!isLightMode" 
                         aria-label="Toggle dark and light appearance mode"
                         tabindex="0">
                        <div class="profile-setting-left">
                            <div class="profile-setting-icon theme">
                                <template x-if="isLightMode">
                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="5"/>
                                        <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                                        <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                                    </svg>
                                </template>
                                <template x-if="!isLightMode">
                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                                    </svg>
                                </template>
                            </div>
                            <div class="profile-setting-text">
                                <span class="profile-setting-title">Appearance</span>
                                <span class="profile-setting-desc" x-text="isLightMode ? 'Light mode' : 'Dark mode'"></span>
                            </div>
                        </div>
                        <div class="profile-setting-right">
                            <div class="profile-theme-switch" :class="{ 'is-light': isLightMode }">
                                <div class="profile-switch-knob"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Danger Zone Divider --}}
                    <div class="profile-danger-divider">
                        <span>Danger zone</span>
                    </div>

                    {{-- 4. Delete Account (or Deletion Requested) --}}
                    @if(!empty($pendingDeletionRequest))
                        <div class="profile-setting-item danger-item" style="border-color: rgba(245, 158, 11, 0.45); background: rgba(245, 158, 11, 0.08);">
                            <div class="profile-setting-left">
                                <div class="profile-setting-icon pending">
                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </div>
                                <div class="profile-setting-text">
                                    <span class="profile-setting-title" style="color:#FBBF24;">Deletion requested</span>
                                    <span class="profile-setting-desc">Status: Pending review ({{ $pendingDeletionRequest->request_code }})</span>
                                </div>
                            </div>
                            <div class="profile-setting-right">
                                <button type="button" @click="showCancelDeleteModal = true" class="profile-cancel-req-btn" title="Cancel your deletion request">
                                    Cancel request
                                </button>
                            </div>
                        </div>
                    @elseif($isAdminUser)
                        <div class="profile-setting-item" style="opacity: 0.65; cursor: not-allowed;" title="Admin accounts cannot request deletion through this form.">
                            <div class="profile-setting-left">
                                <div class="profile-setting-icon danger">
                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                </div>
                                <div class="profile-setting-text">
                                    <span class="profile-setting-title danger">Delete Account</span>
                                    <span class="profile-setting-desc">Protected administrative account</span>
                                </div>
                            </div>
                            <div class="profile-setting-right">
                                <svg width="15" height="15" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                        </div>
                    @else
                        <div class="profile-setting-item danger-item" @click="showDeleteModal = true" role="button" tabindex="0">
                            <div class="profile-setting-left">
                                <div class="profile-setting-icon danger">
                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M3 6h18"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                </div>
                                <div class="profile-setting-text">
                                    <span class="profile-setting-title danger">Delete Account</span>
                                    <span class="profile-setting-desc">Request account deletion</span>
                                </div>
                            </div>
                            <div class="profile-setting-right">
                                <svg class="profile-setting-chevron" width="14" height="14" fill="none" stroke="#F87171" stroke-width="2.2" viewBox="0 0 24 24">
                                    <polyline points="9 18 15 12 9 6"/>
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Logout Card right after Profile Card --}}
            <div class="profile-logout-card ui-reveal" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 12px;">
                <div class="profile-logout-info" style="text-align: center;">
                    <h4>Logout</h4>
                    <p style="font-size: 12.5px; line-height: 1.4;">You will be logged out of your AgriCo account.</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0; width:100%;">
                    @csrf
                    <button type="submit" class="ws-btn-red" style="width: 100%; justify-content: center; box-sizing: border-box;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- RIGHT: Info Stack --}}
        <div class="profile-info-stack">

            {{-- Account Info --}}
            <div class="profile-info-card ui-reveal">
                <div class="profile-info-header">
                    <div class="profile-info-header-icon">
                        <svg width="18" height="18" fill="none" stroke="#4ADE80" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <h3>Account Information</h3>
                </div>
                <div class="profile-info-rows">
                    <div class="profile-info-row">
                        <span class="profile-info-row-label">Full Name</span>
                        <span class="profile-info-row-value">{{ $user->name ?? '—' }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-row-label">Email</span>
                        <span class="profile-info-row-value">{{ $user->email ?? '—' }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-row-label">User ID</span>
                        <span class="profile-info-row-value profile-user-id-val" style="font-family:monospace;font-weight:800;">
                            {{ $userId }}
                        </span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-row-label">Role</span>
                        <span class="profile-info-row-value">{{ $roleBadge }}</span>
                    </div>
                    @if(strtolower(trim($user->role ?? '')) === 'student')
                        <div class="profile-info-row">
                            <span class="profile-info-row-label">Section</span>
                            <span class="profile-info-row-value">{{ $user->section ?: '—' }}</span>
                        </div>
                    @endif
                    <div class="profile-info-row">
                        <span class="profile-info-row-label">Joined</span>
                        <span class="profile-info-row-value">{{ $joinedDate }}</span>
                    </div>
                </div>
                  {{-- EDIT YOUR PROFILE MODAL (Absolute Viewport Centering: Top 50%, Left 50%, Transform Translate) --}}
            {{-- EDIT YOUR PROFILE MODAL WITH TABS --}}
            <template x-teleport="body">
                <div
                    x-show="showEditModal"
                    x-cloak
                    @click.self="showEditModal = false"
                    class="profile-modal-overlay"
                >
                    <div class="profile-modal-card">
                        {{-- FIXED STICKY HEADER --}}
                        <div class="profile-modal-header">
                            <div>
                                <h2 class="profile-modal-title">
                                    Edit Information
                                </h2>
                                <p class="profile-modal-subtitle">
                                    Manage your personal identity, email, and password
                                </p>
                            </div>
                            <x-close-button
                                @click="showEditModal = false"
                                ariaLabel="Close"
                                title="Close"
                            />
                        </div>

                        {{-- MODAL NAVIGATION TABS --}}
                        <div class="profile-modal-tabs">
                            <button
                                type="button"
                                @click="editTab = 'personal'"
                                class="profile-modal-tab-btn"
                                :class="{ 'active': editTab === 'personal' }"
                            >
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span class="tab-label-desktop">Personal Info</span>
                                <span class="tab-label-mobile">Personal</span>
                            </button>
                            <button
                                type="button"
                                @click="editTab = 'email'"
                                class="profile-modal-tab-btn"
                                :class="{ 'active': editTab === 'email' }"
                            >
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                <span class="tab-label-desktop">Change Email</span>
                                <span class="tab-label-mobile">Email</span>
                            </button>
                            <button
                                type="button"
                                @click="editTab = 'password'"
                                class="profile-modal-tab-btn"
                                :class="{ 'active': editTab === 'password' }"
                            >
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                <span class="tab-label-desktop">Password & Security</span>
                                <span class="tab-label-mobile">Password</span>
                            </button>
                        </div>

                        {{-- TAB 1: PERSONAL INFO --}}
                        <div x-show="editTab === 'personal'" style="display:flex; flex-direction:column; flex:1; min-height:0;">
                            <form method="POST" action="{{ route('website.profile.update-personal') }}" enctype="multipart/form-data" autocomplete="off" style="display:flex; flex-direction:column; flex:1; min-height:0; margin:0;">
                                @csrf

                                {{-- Hidden photo file input --}}
                                <input
                                    type="file"
                                    id="profile_photo_input"
                                    name="photo"
                                    x-ref="photoInput"
                                    accept="image/*"
                                    class="hidden"
                                    style="display:none;"
                                    @change="
                                        const file = $event.target.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                photoPreview = e.target.result;
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    "
                                >

                                <div class="profile-modal-body">
                                    <div class="profile-modal-grid">
                                        {{-- LEFT COLUMN: Personal Info Inputs --}}
                                        <div class="profile-modal-inputs">
                                            {{-- Full name --}}
                                            <div class="floating-field-group">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                                </svg>
                                                <input
                                                    id="edit_name"
                                                    type="text"
                                                    name="name"
                                                    x-model="editName"
                                                    required
                                                    placeholder=" "
                                                    class="floating-input has-no-right-icon"
                                                >
                                                <label for="edit_name" class="floating-label">Full Name</label>
                                            </div>

                                            {{-- Current Email (Read-only on personal tab) --}}
                                            <div class="floating-field-group">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                                </svg>
                                                <input
                                                    id="edit_email_readonly"
                                                    type="email"
                                                    :value="editEmail"
                                                    disabled
                                                    placeholder=" "
                                                    class="floating-input has-badge"
                                                    style="opacity:0.85; cursor:not-allowed;"
                                                >
                                                <label for="edit_email_readonly" class="floating-label">Current Email</label>
                                                <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:11px; font-weight:700; color:#22c55e; background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.3); border-radius:9999px; padding:2px 8px; z-index:3;">
                                                    Verified
                                                </span>
                                            </div>

                                            @if(strtolower(trim($user->role ?? '')) === 'student')
                                                {{-- Student ID --}}
                                                <div class="floating-field-group">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.364a4.125 4.125 0 0 0-6.338 0c-.267.319-.064.811.353.811h5.632c.417 0 .62-.492.353-.811Z" />
                                                    </svg>
                                                    <input
                                                        id="edit_student_id"
                                                        type="text"
                                                        name="student_id"
                                                        x-model="editStudentId"
                                                        placeholder=" "
                                                        class="floating-input has-no-right-icon"
                                                    >
                                                    <label for="edit_student_id" class="floating-label">Student ID</label>
                                                </div>

                                                {{-- Section --}}
                                                <div class="floating-field-group">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-5.25 6.557c.75-.45 1.5-.85 2.25-1.2" />
                                                    </svg>
                                                    <input
                                                        id="edit_section"
                                                        type="text"
                                                        name="section"
                                                        x-model="editSection"
                                                        placeholder=" "
                                                        class="floating-input has-no-right-icon"
                                                    >
                                                    <label for="edit_section" class="floating-label">Section</label>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- RIGHT COLUMN: Live Preview Card --}}
                                        <div class="profile-modal-preview">
                                            <span class="profile-modal-preview-title">Preview</span>

                                            {{-- Circular Avatar Badge with Interactive Pencil Edit Button --}}
                                            <div style="position:relative; margin-bottom:8px; cursor:pointer;" @click="$refs.photoInput.click()" title="Click to change profile picture">
                                                <div class="profile-modal-avatar" style="overflow:hidden; position:relative; padding:0; display:flex; align-items:center; justify-content:center;">
                                                    <template x-if="photoPreview">
                                                        <img :src="photoPreview" alt="Profile Photo Preview" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                                                    </template>
                                                    <template x-if="!photoPreview">
                                                        @if($user->profile_photo_url)
                                                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                                                        @else
                                                            <span x-text="(editName ? editName.charAt(0).toUpperCase() : '{{ $initial }}')"></span>
                                                        @endif
                                                    </template>
                                                </div>
                                                <button
                                                    type="button"
                                                    @click.stop="$refs.photoInput.click()"
                                                    title="Change Profile Photo"
                                                    aria-label="Change Profile Photo"
                                                    class="profile-modal-pencil-btn"
                                                >
                                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </button>
                                            </div>

                                            {{-- Name --}}
                                            <h4 class="profile-modal-preview-name" x-text="editName || '{{ $user->name }}'"></h4>

                                            {{-- Role --}}
                                            <p class="profile-modal-preview-role">{{ $roleBadge }}</p>

                                            {{-- User ID --}}
                                            <div class="profile-modal-preview-id">
                                                @if(strtolower(trim($user->role ?? '')) === 'student')
                                                    <span x-text="editStudentId || '{{ $userId }}'"></span>
                                                @else
                                                    {{ $userId }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- UNIFIED FOOTER --}}
                                <div class="profile-modal-footer">
                                    <button
                                        type="button"
                                        @click="showEditModal = false"
                                        class="profile-modal-cancel-btn"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        class="profile-modal-save-btn"
                                    >
                                        Save changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- TAB 2: CHANGE EMAIL --}}
                        <div x-show="editTab === 'email'" style="display:flex; flex-direction:column; flex:1; min-height:0;">
                            <div class="profile-modal-body">
                                {{-- Privacy & Security Banner --}}
                                <div class="profile-modal-sec-box">
                                    <div style="display:flex; align-items:flex-start; gap:12px;">
                                        <svg width="20" height="20" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:2px;">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        </svg>
                                        <div>
                                            <strong>Encrypted & Private Information</strong>
                                            <span>
                                                To ensure strict account privacy and security, a 6-digit confirmation code will be sent to your new email before updating. A security notification will also be sent to your current email.
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Current Email Card --}}
                                <div class="profile-modal-current-email">
                                    <div>
                                        <span class="profile-modal-current-email-label">Current Email</span>
                                        <span class="profile-modal-current-email-val">{{ $user->email }}</span>
                                    </div>
                                    <span style="font-size:11.5px; font-weight:700; color:#22c55e; background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.3); border-radius:9999px; padding:3px 10px;">
                                        Verified
                                    </span>
                                </div>

                                {{-- Inline Alerts --}}
                                <template x-if="emailChangeError">
                                    <div style="background:rgba(239,68,68,0.12); border:1px solid #F87171; border-radius:12px; padding:12px 16px; font-size:13px; color:#EF4444; font-weight:600; margin-bottom:16px;" x-text="emailChangeError"></div>
                                </template>
                                <template x-if="emailChangeMsg">
                                    <div style="background:rgba(34,197,94,0.12); border:1px solid #4ADE80; border-radius:12px; padding:12px 16px; font-size:13px; color:#22C55E; font-weight:600; margin-bottom:16px;" x-text="emailChangeMsg"></div>
                                </template>

                                {{-- STEP 1: Enter New Email --}}
                                <div x-show="emailStep === 1" style="display:flex; flex-direction:column; gap:16px;">
                                    <div class="floating-field-group">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>
                                        <input
                                            id="edit_new_email"
                                            type="text"
                                            inputmode="email"
                                            name="agrico_new_email_no_autofill"
                                            x-model="newEmailInput"
                                            :readonly="emailReadonly"
                                            @focus="emailReadonly = false"
                                            @click="emailReadonly = false"
                                            autocomplete="off"
                                            autocorrect="off"
                                            autocapitalize="off"
                                            spellcheck="false"
                                            data-lpignore="true"
                                            data-form-type="other"
                                            placeholder=" "
                                            class="floating-input has-no-right-icon"
                                            @keydown.enter.prevent="sendEmailCode()"
                                        >
                                        <label for="edit_new_email" class="floating-label">New Email Address</label>
                                    </div>
                                </div>

                                {{-- STEP 2: Enter 6-digit Code --}}
                                <div x-show="emailStep === 2" style="display:flex; flex-direction:column; gap:16px;">
                                    <div style="text-align:center; padding:6px 0;">
                                        <span class="profile-email-verify-sub" style="font-size:13px; color:var(--pm-muted);">Enter the 6-digit verification code sent to</span>
                                        <div class="profile-email-verify-target" style="font-size:15px; font-weight:800; color:#22c55e; margin-top:3px;" x-text="newEmailInput"></div>
                                    </div>

                                    <div style="display:flex; justify-content:center;">
                                        <input
                                            type="text"
                                            x-model="emailCode"
                                            maxlength="6"
                                            placeholder="------"
                                            class="floating-input email-code-box"
                                            style="width:220px; height:50px; border-radius:14px; text-align:center;"
                                            @keydown.enter.prevent="verifyEmailCode()"
                                            autocomplete="one-time-code"
                                        >
                                    </div>

                                    <div style="text-align:center; margin-top:2px;">
                                        <template x-if="resendCountdown > 0">
                                            <span style="font-size:12px; color:var(--pm-muted);">Resend code in <strong x-text="resendCountdown + 's'" style="color:var(--pm-title);"></strong></span>
                                        </template>
                                        <template x-if="resendCountdown === 0">
                                            <button type="button" @click="sendEmailCode()" style="background:none; border:none; color:#22c55e; font-size:12.5px; font-weight:700; cursor:pointer; text-decoration:underline;">
                                                Didn't receive code? Resend
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- UNIFIED FOOTER --}}
                            <div class="profile-modal-footer">
                                <template x-if="emailStep === 1">
                                    <div style="display:flex; align-items:center; gap:12px; justify-content:flex-end;">
                                        <button
                                            type="button"
                                            @click="showEditModal = false"
                                            class="profile-modal-cancel-btn"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="button"
                                            @click="sendEmailCode()"
                                            :disabled="sendingEmailCode || !newEmailInput"
                                            class="profile-modal-save-btn"
                                            :style="(sendingEmailCode || !newEmailInput) ? 'opacity:0.6; cursor:not-allowed;' : ''"
                                        >
                                            <template x-if="sendingEmailCode">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite; margin-right:6px;"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
                                            </template>
                                            <span x-text="sendingEmailCode ? 'Sending code...' : 'Send 6-digit Verification Code'"></span>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="emailStep === 2">
                                    <div style="display:flex; align-items:center; gap:12px; justify-content:flex-end;">
                                        <button
                                            type="button"
                                            @click="emailStep = 1; emailCode = '';"
                                            class="profile-modal-cancel-btn"
                                        >
                                            Change Email
                                        </button>
                                        <button
                                            type="button"
                                            @click="verifyEmailCode()"
                                            :disabled="verifyingEmailCode || emailCode.length !== 6"
                                            class="profile-modal-save-btn"
                                            :style="(verifyingEmailCode || emailCode.length !== 6) ? 'opacity:0.6; cursor:not-allowed;' : ''"
                                        >
                                            <template x-if="verifyingEmailCode">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite; margin-right:6px;"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
                                            </template>
                                            <span x-text="verifyingEmailCode ? 'Verifying...' : 'Verify & Change Email'"></span>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- TAB 3: PASSWORD & SECURITY --}}
                        <div x-show="editTab === 'password'" style="display:flex; flex-direction:column; flex:1; min-height:0;">
                            <form method="POST" action="{{ route('website.profile.password.update') }}" autocomplete="off" style="display:flex; flex-direction:column; flex:1; min-height:0; margin:0;">
                                @csrf

                                <div class="profile-modal-body">
                                    <div class="profile-modal-inputs">
                                        {{-- Current Password --}}
                                        <div>
                                            <div class="floating-field-group">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                                </svg>
                                                <input
                                                    id="tab_current_password"
                                                    :type="showCurrentPass ? 'text' : 'password'"
                                                    name="current_password"
                                                    x-model="currentPassword"
                                                    @input.debounce.300ms="verifyCurrentPassword()"
                                                    autocomplete="off"
                                                    data-lpignore="true"
                                                    placeholder=" "
                                                    class="floating-input"
                                                    :class="{
                                                        'border-correct': currentPassword.length > 0 && isCurrentPassCorrect,
                                                        'border-incorrect': currentPassword.length > 0 && !checkingCurrentPass && !isCurrentPassCorrect
                                                    }"
                                                >
                                                <label for="tab_current_password" class="floating-label">Current Password</label>
                                                <button
                                                    type="button"
                                                    @click="showCurrentPass = !showCurrentPass"
                                                    class="input-eye-btn"
                                                    aria-label="Toggle password visibility"
                                                >
                                                    <template x-if="!showCurrentPass">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:17px; height:17px;">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                        </svg>
                                                    </template>
                                                    <template x-if="showCurrentPass">
                                                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </template>
                                                </button>
                                            </div>

                                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px; min-height:20px;">
                                                <div>
                                                    <div x-show="currentPassword.length > 0">
                                                        <template x-if="checkingCurrentPass">
                                                            <span class="pass-status-checking">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite;"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
                                                                Verifying password...
                                                            </span>
                                                        </template>
                                                        <template x-if="!checkingCurrentPass && isCurrentPassCorrect">
                                                            <span class="pass-status-correct">
                                                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                Current password correct
                                                            </span>
                                                        </template>
                                                        <template x-if="!checkingCurrentPass && !isCurrentPassCorrect">
                                                            <span class="pass-status-error">
                                                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                Incorrect current password
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div style="margin-left:auto;">
                                                    <a href="{{ route('password.request') }}" class="profile-forgot-link">Forgot password?</a>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- New Password --}}
                                        <div>
                                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px; gap:6px;">
                                                <span class="pass-helper-label" x-show="!isCurrentPassCorrect">(Enter current password first)</span>
                                                <div style="display:flex; align-items:center; gap:6px; margin-left:auto;">
                                                    <span class="pass-helper-label">Security level</span>
                                                    <div style="display:flex; align-items:center; gap:3px;">
                                                        <div class="meter-bar" :style="{ background: strengthScore >= 1 ? (strengthScore <= 2 ? '#EF4444' : (strengthScore <= 4 ? '#F59E0B' : '#22C55E')) : '' }"></div>
                                                        <div class="meter-bar" :style="{ background: strengthScore >= 2 ? (strengthScore <= 2 ? '#EF4444' : (strengthScore <= 4 ? '#F59E0B' : '#22C55E')) : '' }"></div>
                                                        <div class="meter-bar" :style="{ background: strengthScore >= 3 ? (strengthScore <= 4 ? '#F59E0B' : '#22C55E') : '' }"></div>
                                                        <div class="meter-bar" :style="{ background: strengthScore >= 4 ? (strengthScore <= 4 ? '#F59E0B' : '#22C55E') : '' }"></div>
                                                        <div class="meter-bar" :style="{ background: strengthScore >= 5 ? '#22C55E' : '' }"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="floating-field-group">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                                </svg>
                                                <input
                                                    id="tab_new_password"
                                                    :type="showNewPass ? 'text' : 'password'"
                                                    name="password"
                                                    x-model="newPassword"
                                                    :disabled="!isCurrentPassCorrect"
                                                    autocomplete="new-password"
                                                    placeholder=" "
                                                    class="floating-input"
                                                    :class="!isCurrentPassCorrect ? 'input-locked' : 'input-unlocked'"
                                                >
                                                <label for="tab_new_password" class="floating-label">New Password</label>
                                                <button
                                                    type="button"
                                                    @click="showNewPass = !showNewPass"
                                                    class="input-eye-btn"
                                                    aria-label="Toggle new password visibility"
                                                >
                                                    <template x-if="!showNewPass">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:17px; height:17px;">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                        </svg>
                                                    </template>
                                                    <template x-if="showNewPass">
                                                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </template>
                                                </button>
                                            </div>

                                            <div class="pass-req-badges">
                                                <div class="pass-req-badge" :class="{ 'active': hasMinLen }">
                                                    <span class="pass-req-icon" x-text="hasMinLen ? '✓' : '✕'">✕</span>
                                                    <span>8+ characters</span>
                                                </div>
                                                <div class="pass-req-badge" :class="{ 'active': hasNumber }">
                                                    <span class="pass-req-icon" x-text="hasNumber ? '✓' : '✕'">✕</span>
                                                    <span>Number</span>
                                                </div>
                                                <div class="pass-req-badge" :class="{ 'active': hasUppercase }">
                                                    <span class="pass-req-icon" x-text="hasUppercase ? '✓' : '✕'">✕</span>
                                                    <span>Uppercase letter</span>
                                                </div>
                                                <div class="pass-req-badge" :class="{ 'active': hasLowercase }">
                                                    <span class="pass-req-icon" x-text="hasLowercase ? '✓' : '✕'">✕</span>
                                                    <span>Lowercase letter</span>
                                                </div>
                                                <div class="pass-req-badge" :class="{ 'active': hasSpecial }">
                                                    <span class="pass-req-icon" x-text="hasSpecial ? '✓' : '✕'">✕</span>
                                                    <span>Special character</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Confirm Password --}}
                                        <div>
                                            <div style="display:flex; justify-content:flex-end; margin-bottom:4px;">
                                                <button
                                                    type="button"
                                                    @click="clearPasswords()"
                                                    x-show="currentPassword || newPassword || confirmPassword"
                                                    class="pass-clear-btn"
                                                >
                                                    Clear
                                                </button>
                                            </div>
                                            <div class="floating-field-group">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                                </svg>
                                                <input
                                                    id="tab_password_confirmation"
                                                    :type="showConfirmPass ? 'text' : 'password'"
                                                    name="password_confirmation"
                                                    x-model="confirmPassword"
                                                    :disabled="!isCurrentPassCorrect"
                                                    autocomplete="new-password"
                                                    placeholder=" "
                                                    class="floating-input"
                                                    :class="!isCurrentPassCorrect ? 'input-locked' : 'input-unlocked'"
                                                >
                                                <label for="tab_password_confirmation" class="floating-label">Confirm Password</label>
                                                <button
                                                    type="button"
                                                    @click="showConfirmPass = !showConfirmPass"
                                                    class="input-eye-btn"
                                                    aria-label="Toggle confirm password visibility"
                                                >
                                                    <template x-if="!showConfirmPass">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:17px; height:17px;">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                        </svg>
                                                    </template>
                                                    <template x-if="showConfirmPass">
                                                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </template>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- UNIFIED FOOTER --}}
                                <div class="profile-modal-footer">
                                    <button
                                        type="button"
                                        @click="showEditModal = false"
                                        class="profile-modal-cancel-btn"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        :disabled="!isCurrentPassCorrect || strengthScore < 5 || newPassword !== confirmPassword"
                                        class="profile-modal-save-btn"
                                        :style="(!isCurrentPassCorrect || strengthScore < 5 || newPassword !== confirmPassword) ? 'opacity:0.6; cursor:not-allowed;' : ''"
                                    >
                                        Update password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            {{-- DELETE ACCOUNT REQUEST MODAL (With Full Data Privacy) --}}
            <template x-teleport="body">
                <div
                    x-show="showDeleteModal"
                    x-cloak
                    @click.self="showDeleteModal = false"
                    class="profile-modal-overlay"
                >
                    <div class="profile-modal-card">
                        {{-- Header --}}
                        <div class="profile-modal-header" style="justify-content:space-between; align-items:center; border-bottom:1px solid rgba(248,113,113,0.25);">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div class="profile-setting-icon danger" style="width:38px; height:38px; border-radius:12px;">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="profile-modal-title" style="margin:0; font-size:18px; font-weight:800; color:#F87171;">Delete Account Request</h3>
                                    <p class="profile-modal-last-updated" style="margin:2px 0 0; font-size:12px;">Permanent account removal & data privacy</p>
                                </div>
                            </div>
                            <x-close-button
                                @click="showDeleteModal = false"
                                ariaLabel="Close"
                                title="Close"
                            />
                        </div>

                        {{-- Scrollable Body --}}
                        <div class="profile-modal-body" style="padding:20px 24px; flex:1; overflow-y:auto; display:flex; flex-direction:column; gap:16px;">
                            {{-- Privacy & Deletion Warning Banner --}}
                            <div style="background:rgba(239,68,68,0.1); border:1.5px solid rgba(248,113,113,0.35); border-radius:14px; padding:14px 16px; font-size:13px; color:#FCA5A5; line-height:1.5;">
                                <div style="display:flex; align-items:flex-start; gap:10px;">
                                    <svg width="18" height="18" fill="none" stroke="#F87171" stroke-width="2.2" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:2px;">
                                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div>
                                        <strong style="color:#ffffff; display:block; margin-bottom:4px; font-weight:800;">What happens when you request deletion:</strong>
                                        <ul style="margin:0; padding-left:18px; display:flex; flex-direction:column; gap:4px;">
                                            <li><strong>Complete Privacy:</strong> All your personal information (name, email, profile photo, student details, credentials) will be permanently expunged.</li>
                                            <li><strong>Anonymous Telemetry:</strong> Soil scans & map pins are unlinked (<code style="font-size:11px; padding:1px 4px; background:rgba(0,0,0,0.25); border-radius:4px;">user_id = null</code>) so municipal agricultural data remains anonymous with zero connection to you.</li>
                                            <li><strong>Email Confirmation:</strong> We'll email your Gmail (<span style="color:#ffffff;">{{ $user->email }}</span>) with request details and a secure link to cancel if you change your mind.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Error and Success messages --}}
                            <template x-if="deleteError">
                                <div style="background:rgba(239,68,68,0.15); border:1px solid #F87171; border-radius:10px; padding:10px 14px; font-size:12.5px; color:#FCA5A5; font-weight:600;" x-text="deleteError"></div>
                            </template>
                            <template x-if="deleteSuccessMsg">
                                <div style="background:rgba(34,197,94,0.15); border:1px solid #4ADE80; border-radius:10px; padding:10px 14px; font-size:12.5px; color:#86EFAC; font-weight:600;" x-text="deleteSuccessMsg"></div>
                            </template>

                            {{-- Reason Dropdown --}}
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#8EB69B; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">Reason for deletion (Optional)</label>
                                <select x-model="deleteReason" class="floating-input" style="width:100%; height:42px; padding:8px 14px; font-size:13.5px; border-radius:12px; outline:none; cursor:pointer;">
                                    <option value="I no longer need the account">I no longer need the account</option>
                                    <option value="Privacy concerns">Privacy concerns</option>
                                    <option value="Creating a new account">Creating a new account</option>
                                    <option value="Too many emails or notifications">Too many emails or notifications</option>
                                    <option value="Temporary break">Temporary break</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            {{-- Additional notes --}}
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#8EB69B; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">Additional feedback / notes (Optional)</label>
                                <textarea x-model="deleteNotes" rows="2" placeholder="Let us know if there is anything we could improve..." class="floating-input" style="width:100%; padding:10px 14px; font-size:13px; border-radius:12px; outline:none; resize:vertical; box-sizing:border-box;"></textarea>
                            </div>

                            {{-- Current Password --}}
                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#8EB69B; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">Confirm your password <span style="color:#F87171;">*</span></label>
                                <div style="position:relative;">
                                    <input
                                        :type="showDeletePass ? 'text' : 'password'"
                                        x-model="deletePassword"
                                        placeholder="Enter your current password"
                                        class="floating-input"
                                        style="width:100%; height:44px; padding:10px 42px 10px 14px; font-size:13.5px; border-radius:12px; outline:none; box-sizing:border-box;"
                                    >
                                    <button type="button" @click="showDeletePass = !showDeletePass" class="input-eye-btn" style="right:12px;">
                                        <template x-if="!showDeletePass">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                        </template>
                                        <template x-if="showDeletePass">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </template>
                                    </button>
                                </div>
                            </div>

                            {{-- Checkbox confirmation --}}
                            <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; user-select:none; font-size:12.5px; color:#8EB69B; line-height:1.4;">
                                <input type="checkbox" x-model="deleteConfirmed" style="margin-top:3px; accent-color:#ef4444; width:16px; height:16px; cursor:pointer;">
                                <span>I understand that this action is irreversible once approved by administrators and all my personal data will be wiped private.</span>
                            </label>
                        </div>

                        {{-- Footer --}}
                        <div class="profile-modal-footer" style="justify-content:space-between; align-items:center; border-top:1px solid rgba(248,113,113,0.25);">
                            <button type="button" @click="showDeleteModal = false" class="profile-cancel-req-btn" style="border-radius:12px; padding:10px 18px; font-size:13px; font-weight:700; cursor:pointer;">
                                Keep My Account
                            </button>
                            <button
                                type="button"
                                @click="submitDeletion()"
                                :disabled="submittingDeletion || !deleteConfirmed || !deletePassword"
                                style="background:#dc2626; color:#ffffff; border:none; border-radius:12px; padding:10px 22px; font-size:13.5px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 14px rgba(220,38,38,0.4); transition:all 0.2s ease;"
                                :style="(submittingDeletion || !deleteConfirmed || !deletePassword) ? 'opacity:0.55; cursor:not-allowed;' : ''"
                            >
                                <template x-if="submittingDeletion">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite;"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
                                </template>
                                <span x-text="submittingDeletion ? 'Submitting...' : 'Confirm Account Deletion'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- CANCEL DELETION REQUEST MODAL --}}
            <template x-teleport="body">
                <div
                    x-show="showCancelDeleteModal"
                    x-cloak
                    @click.self="showCancelDeleteModal = false"
                    class="profile-modal-overlay"
                >
                    <div class="profile-modal-card">
                        <div class="profile-modal-header" style="justify-content:space-between; align-items:center;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div class="profile-setting-icon notif" style="width:38px; height:38px; border-radius:12px;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="profile-modal-title" style="margin:0; font-size:17px; font-weight:800;">Cancel Deletion Request</h3>
                                    <p class="profile-modal-last-updated" style="margin:2px 0 0; font-size:12px;">Keep your AgriCo account active</p>
                                </div>
                            </div>
                            <x-close-button
                                @click="showCancelDeleteModal = false"
                                ariaLabel="Close"
                                title="Close"
                            />
                        </div>

                        <div class="profile-modal-body" style="padding:20px 24px; display:flex; flex-direction:column; gap:14px;">
                            <p style="margin:0; font-size:13.5px; line-height:1.5; color:inherit;">
                                You currently have an active account deletion request pending administrator review
                                @if($pendingDeletionRequest)
                                    (<strong style="font-family:monospace;">{{ $pendingDeletionRequest->request_code }}</strong> submitted on {{ $pendingDeletionRequest->submitted_at ? $pendingDeletionRequest->submitted_at->format('M d, Y') : $pendingDeletionRequest->created_at->format('M d, Y') }})
                                @endif.
                            </p>
                            <p style="margin:0; font-size:13px; opacity:0.85;">
                                If you cancel now, your account will remain fully active with all your scans, history, and records preserved.
                            </p>

                            <template x-if="cancelDeletionError">
                                <div style="background:rgba(239,68,68,0.15); border:1px solid #F87171; border-radius:10px; padding:10px 14px; font-size:12.5px; color:#FCA5A5; font-weight:600;" x-text="cancelDeletionError"></div>
                            </template>
                            <template x-if="cancelDeletionSuccess">
                                <div style="background:rgba(34,197,94,0.15); border:1px solid #4ADE80; border-radius:10px; padding:10px 14px; font-size:12.5px; color:#86EFAC; font-weight:600;" x-text="cancelDeletionSuccess"></div>
                            </template>
                        </div>

                        <div class="profile-modal-footer" style="justify-content:flex-end; gap:10px;">
                            <button type="button" @click="showCancelDeleteModal = false" class="profile-cancel-req-btn" style="border-radius:12px; padding:9px 18px; font-size:13px; font-weight:700; cursor:pointer;">
                                Keep Pending
                            </button>
                            <button
                                type="button"
                                @click="submitCancelDeletion()"
                                :disabled="cancellingDeletion"
                                class="profile-modal-save-btn"
                                style="border-radius:12px; padding:9px 22px; font-size:13px; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:8px;"
                            >
                                <template x-if="cancellingDeletion">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: spin 1s linear infinite;"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
                                </template>
                                <span x-text="cancellingDeletion ? 'Cancelling...' : 'Cancel Request & Keep Account'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- NOTIFICATIONS MODAL --}}
            <template x-teleport="body">
                <div
                    x-show="showNotificationsModal"
                    x-cloak
                    @click.self="showNotificationsModal = false"
                    class="profile-modal-overlay"
                >
                    <div class="profile-modal-card">
                        {{-- Modal Header --}}
                        <div class="profile-modal-header" style="justify-content: space-between; align-items: center; padding-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="profile-setting-icon notif" style="width: 38px; height: 38px; border-radius: 12px;">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="profile-modal-title" style="margin: 0; font-size: 18px; font-weight: 800;">Notifications</h3>
                                    <p class="profile-modal-last-updated" style="margin: 2px 0 0; font-size: 12px;" x-text="unreadCount > 0 ? unreadCount + ' unread updates' : 'All notifications read'"></p>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px;">
                                <template x-if="unreadCount > 0">
                                    <button
                                        type="button"
                                        @click="markAllAsRead()"
                                        style="background: transparent; border: 1.5px solid #22c55e; color: #22c55e; font-size: 11.5px; font-weight: 700; border-radius: 12px; padding: 4px 12px; cursor: pointer; transition: all 0.2s ease;"
                                        onmouseover="this.style.background='#22c55e';this.style.color='#ffffff';"
                                        onmouseout="this.style.background='transparent';this.style.color='#22c55e';"
                                    >
                                        Mark all read
                                    </button>
                                </template>
                                <x-close-button
                                    @click="showNotificationsModal = false"
                                    ariaLabel="Close"
                                    title="Close"
                                />
                            </div>
                        </div>

                        {{-- Modal Scrollable Body --}}
                        <div class="profile-modal-body" style="padding: 16px 24px; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                            @forelse($notifications as $notif)
                                @php
                                    $data = $notif->data ?? [];
                                    $isPin = $notif->type === 'pin_submitted';
                                    $isApproved = in_array($notif->type, ['scan_approved', 'account_approved']);
                                    $isRejected = in_array($notif->type, ['scan_rejected', 'account_rejected', 'account_disapproved']);
                                @endphp
                                <div
                                    class="profile-notif-card {{ $notif->is_read ? 'read' : 'unread' }}"
                                    data-notif-id="{{ $notif->notification_id }}"
                                    @click="markSingleRead({{ $notif->notification_id }}, $el)"
                                >
                                    <div class="profile-notif-card-icon {{ $isApproved ? 'approved' : ($isRejected ? 'rejected' : 'pin') }}">
                                        @if($isApproved)
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                        @elseif($isRejected)
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        @else
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        @endif
                                    </div>
                                    <div class="profile-notif-card-body">
                                        <div class="profile-notif-card-head">
                                            <span class="profile-notif-card-title">{{ $notif->title }}</span>
                                            <span class="profile-notif-card-time">{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="profile-notif-card-msg">{{ $notif->message }}</p>
                                        @if(!empty($data['barangay']) || !empty($data['latitude']))
                                            <div class="profile-notif-card-tags">
                                                @if(!empty($data['barangay']))
                                                    <span class="notif-tag">📍 Brgy. {{ $data['barangay'] }}</span>
                                                @endif
                                                @if(!empty($data['latitude']) && !empty($data['longitude']))
                                                    <span class="notif-tag coords">{{ number_format($data['latitude'], 4) }}, {{ number_format($data['longitude'], 4) }}</span>
                                                @endif
                                                <a href="{{ route('website.map') }}" class="notif-map-link">View on Map &rarr;</a>
                                            </div>
                                        @endif
                                    </div>
                                    @if(!$notif->is_read)
                                        <span class="profile-notif-unread-dot"></span>
                                    @endif
                                </div>
                            @empty
                                <div style="text-align: center; padding: 48px 20px; color: #8EB69B;">
                                    <div style="font-size: 34px; margin-bottom: 10px;">🔔</div>
                                    <h4 style="margin: 0; font-size: 16px; font-weight: 700; color: inherit;">No Notifications Yet</h4>
                                    <p style="margin: 6px 0 0; font-size: 13px; opacity: 0.85;">When you pin soil coordinates or scans are reviewed, alerts will show here.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Modal Footer --}}
                        <div class="profile-modal-footer" style="justify-content: flex-end;">
                            <button
                                type="button"
                                @click="showNotificationsModal = false"
                                class="profile-modal-save-btn"
                                style="width: auto; padding: 9px 24px; font-size: 13.5px; border-radius: 12px;"
                            >
                                Done
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Activity --}}
            <div class="profile-info-card ui-reveal">
                <div class="profile-info-header">
                    <div class="profile-info-header-icon">
                        <svg width="18" height="18" fill="none" stroke="#4ADE80" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <h3>Activity Summary</h3>
                </div>
                <div class="profile-info-rows">
                    <div class="profile-info-row">
                        <span class="profile-info-row-label">Total Scans</span>
                        <span class="profile-info-row-value profile-stat-count-val" style="font-size:20px;font-weight:900;">
                            {{ number_format($scanCount) }}
                        </span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-row-label">Scan History</span>
                        <span class="profile-info-row-value">
                            <a href="{{ route('website.scan') }}"
                               class="profile-scan-link"
                               style="font-weight:700;text-decoration:none;
                                      display:inline-flex;align-items:center;gap:6px;font-size:13.5px;">
                                View all scans
                                <svg width="14" height="14" fill="none" stroke="currentColor"
                                     stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endguest

</x-website-layout>
