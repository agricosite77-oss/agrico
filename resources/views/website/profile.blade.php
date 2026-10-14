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
        
        .profile-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            z-index: 99999;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(20px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 16px;
        }

        /* Modal Responsive Layout Styling - Dark Mode Theme */
        .profile-modal-card {
            position: relative;
            z-index: 100000;
            width: 100%;
            max-width: 540px;
            background: #0B2B26;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 35px rgba(35, 83, 71, 0.25);
            border: 1px solid rgba(142, 182, 155, 0.28);
            display: flex;
            flex-direction: column;
            max-height: min(88vh, 560px);
            overflow: hidden;
            margin: auto;
        }

        .profile-modal-body {
            padding: 16px 20px 20px 20px;
            flex: 1;
            min-height: 0;
            display: grid;
            grid-template-columns: 1fr 180px;
            gap: 20px;
            align-items: start;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: rgba(142, 182, 155, 0.4) transparent;
        }
        .profile-modal-body::-webkit-scrollbar { width: 7px; }
        .profile-modal-body::-webkit-scrollbar-thumb { background: rgba(142, 182, 155, 0.35); border-radius: 999px; transition: background 0.2s ease; }
        .profile-modal-body::-webkit-scrollbar-thumb:hover { background: #4ADE80; }
        .profile-modal-body::-webkit-scrollbar-track { background: rgba(5, 31, 32, 0.3); border-radius: 999px; }

        .profile-modal-inputs {
            display: flex;
            flex-direction: column;
            gap: 14px;
            padding-top: 10px;
        }

        .profile-modal-preview {
            padding-left: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        @media (max-width: 640px) {
            .profile-modal-card {
                width: calc(100vw - 32px) !important;
                max-height: 88vh !important;
                border-radius: 20px !important;
            }
            .profile-modal-body {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
                padding: 14px 16px !important;
            }
            .profile-modal-preview {
                order: -1 !important;
                padding-left: 0 !important;
                padding-bottom: 14px !important;
                border-bottom: 1px solid rgba(142, 182, 155, 0.2) !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }
            .profile-modal-inputs {
                padding-top: 8px !important;
            }
        }

        /* Animated Floating Label & Color State Styling (Dark Mode Theme) */
        .floating-input {
            border-radius: 12px !important;
            transition: all 0.25s ease !important;
            background: rgba(5, 31, 32, 0.7) !important;
            border: 1.5px solid rgba(142, 182, 155, 0.28) !important;
            color: #DAF1DE !important;
        }
        .floating-input:focus {
            border-color: #4ADE80 !important;
            box-shadow: 0 0 0 3.5px rgba(74, 222, 128, 0.18) !important;
        }
        .floating-input:focus ~ .floating-label {
            top: 0px !important;
            left: 20px !important;
            transform: translateY(-50%) scale(0.82) !important;
            background: #0B2B26 !important;
            color: #4ADE80 !important;
            font-weight: 700 !important;
            border-radius: 4px !important;
            padding: 0 5px !important;
        }
        .floating-input:not(:placeholder-shown) ~ .floating-label {
            top: 0px !important;
            left: 20px !important;
            transform: translateY(-50%) scale(0.82) !important;
            background: #0B2B26 !important;
            color: #8EB69B !important;
            font-weight: 700 !important;
            border-radius: 4px !important;
            padding: 0 5px !important;
        }
        .floating-input:focus:not(:placeholder-shown) ~ .floating-label {
            color: #4ADE80 !important;
        }

        .modal-close-circle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(142, 182, 155, 0.12);
            border: 1px solid rgba(142, 182, 155, 0.25);
            color: #8EB69B;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
            outline: none;
        }
        .modal-close-circle-btn:hover {
            background: rgba(142, 182, 155, 0.25);
            color: #DAF1DE;
            border-color: #DAF1DE;
            transform: scale(1.06);
        }

        /* ── Modal Avatar: Image 3 (Dark Mode) ── */
        .profile-modal-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #235347, #163832) !important;
            border: 2px solid #8EB69B !important;
            color: #DAF1DE !important;
            font-size: 26px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(35, 83, 71, 0.5) !important;
            transition: all 0.25s ease;
            user-select: none;
        }

        /* ── Modal Save Button: Image 4 (Dark Mode) ── */
        .profile-modal-save-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 24px;
            border-radius: 12px !important;
            background: linear-gradient(135deg, #235347, #163832) !important;
            border: 1.5px solid #8EB69B !important;
            color: #DAF1DE !important;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(35, 83, 71, 0.5) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            font-family: inherit;
        }
        .profile-modal-save-btn:hover {
            background: #235347 !important;
            border-color: #DAF1DE !important;
            color: #ffffff !important;
            box-shadow: 0 6px 20px rgba(35, 83, 71, 0.6) !important;
            transform: translateY(-1px);
        }

        /* ── Preview Pencil Button ── */
        .profile-modal-pencil-btn {
            position: absolute;
            bottom: 0px;
            right: 0px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #163832;
            border: 1.5px solid #8EB69B;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            cursor: pointer;
            padding: 0;
            outline: none;
            transition: all 0.2s ease;
            z-index: 5;
        }
        .profile-modal-pencil-btn svg {
            stroke: #DAF1DE;
        }
        .profile-modal-pencil-btn:hover {
            background: #235347;
            border-color: #DAF1DE;
            transform: scale(1.1);
        }

        .profile-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 24px 14px 24px;
            border-bottom: 1px solid rgba(142, 182, 155, 0.2);
            flex-shrink: 0;
            background: rgba(11, 43, 38, 0.98);
            z-index: 10;
        }
        .profile-modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 24px 16px 24px;
            flex-shrink: 0;
            border-top: 1px solid rgba(142, 182, 155, 0.2);
            background: rgba(11, 43, 38, 0.98);
            z-index: 10;
        }
        .profile-modal-title {
            font-size: 16.5px;
            font-weight: 800;
            color: #DAF1DE;
            margin: 0;
        }
        .profile-modal-last-updated {
            font-size: 11.5px;
            color: #8EB69B;
            font-weight: 500;
        }
        .profile-modal-preview-title {
            font-size: 11px;
            font-weight: 700;
            color: #8EB69B;
            margin-bottom: 8px;
        }
        .profile-modal-preview-name {
            font-size: 14.5px;
            font-weight: 800;
            color: #DAF1DE;
            margin: 4px 0 2px 0;
        }
        .profile-modal-preview-role {
            font-size: 12px;
            color: #8EB69B;
            margin: 0 0 6px 0;
            font-weight: 600;
        }
        .profile-modal-preview-id {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            font-size: 12px;
            font-weight: 700;
            color: #8EB69B;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .profile-modal-preview-id svg {
            stroke: #8EB69B;
        }

        /* ── Modal Form Elements (Dark Mode) ── */
        .input-lead-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            pointer-events: none;
            z-index: 3;
            stroke: #8EB69B;
            transition: stroke 0.2s ease;
        }
        .input-eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #8EB69B;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            z-index: 3;
            transition: color 0.2s ease;
        }
        .input-eye-btn:hover { color: #DAF1DE; }

        .profile-modal-divider {
            border-top: 1px solid rgba(142, 182, 155, 0.2);
            margin-top: 2px;
            padding-top: 8px;
        }
        .profile-modal-section-title {
            font-size: 10.5px;
            font-weight: 700;
            color: #8EB69B;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .pass-helper-label {
            font-size: 0.65rem;
            color: #8EB69B;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .meter-bar {
            width: 8px;
            height: 3.5px;
            border-radius: 2px;
            transition: all 0.25s ease;
            background: rgba(142, 182, 155, 0.25);
        }

        .pass-clear-btn {
            font-size: 11px;
            font-weight: 700;
            color: #8EB69B;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: color 0.2s ease;
        }
        .pass-clear-btn:hover { color: #DAF1DE; }

        .pass-status-checking { color: #8EB69B; display: inline-flex; align-items: center; gap: 4px; }
        .pass-status-correct  { color: #4ADE80; display: inline-flex; align-items: center; gap: 4px; }
        .pass-status-error    { color: #F87171; display: inline-flex; align-items: center; gap: 4px; }

        .profile-forgot-link {
            font-size: 0.8rem;
            font-weight: 600;
            color: #4ADE80;
            text-decoration: none;
            transition: color 0.2s ease, text-decoration 0.2s ease;
            display: inline-flex;
            align-items: center;
        }
        .profile-forgot-link:hover {
            color: #86EFAC;
            text-decoration: underline;
        }

        .floating-input.border-correct { border-color: #4ADE80 !important; }
        .floating-input.border-incorrect { border-color: #F87171 !important; }

        .floating-input.input-locked {
            opacity: 0.55;
            cursor: not-allowed !important;
            background: rgba(5, 31, 32, 0.4) !important;
            border-color: rgba(142, 182, 155, 0.18) !important;
        }
        .floating-input.input-unlocked {
            opacity: 1;
            cursor: text !important;
        }

        /* ── Password Requirements Badges ── */
        .pass-req-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 6px;
        }
        .pass-req-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 600;
            transition: all 0.2s ease;
            background: rgba(5, 31, 32, 0.7);
            border: 1px solid rgba(142, 182, 155, 0.25);
            color: #8EB69B;
            user-select: none;
        }
        .pass-req-badge.active {
            background: rgba(74, 222, 128, 0.15);
            border-color: rgba(74, 222, 128, 0.4);
            color: #4ADE80;
            font-weight: 700;
        }
        .pass-req-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            font-size: 7px;
            font-weight: 800;
            transition: all 0.2s ease;
            background: rgba(142, 182, 155, 0.2);
            color: #8EB69B;
        }
        .pass-req-badge.active .pass-req-icon {
            background: #4ADE80;
            color: #051F20;
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
            border-radius: 999px;
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
        .profile-setting-icon.theme {
            background: rgba(251, 191, 36, 0.16);
            color: #FBBF24;
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
            border-radius: 999px;
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
            box-shadow: 0 6px 18px rgba(34, 197, 94, 0.45) !important;
            transform: translateY(-1px) !important;
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
        body.light-mode .floating-input.border-correct { border-color: #16a34a !important; box-shadow: 0 0 0 3.5px rgba(34, 197, 94, 0.25) !important; }
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
        html.light-mode .profile-setting-icon.theme {
            background: #fef3c7 !important;
            color: #b45309 !important;
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
            border-radius: 999px !important;
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
            <div style="background:#f0fdf4; border:1px solid #86efac; color:#16a34a; padding:14px 20px; border-radius:16px; font-size:14px; font-weight:700; display:flex; align-items:center; gap:10px; box-shadow:0 4px 14px rgba(34,197,94,0.15);">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="profile-alert-container">
            <div style="background:#fef2f2; border:1px solid #fca5a5; color:#dc2626; padding:14px 20px; border-radius:16px; font-size:13.5px; font-weight:600; box-shadow:0 4px 14px rgba(220,38,38,0.12);">
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
        showPasswordModal: {{ ($errors->has('current_password') || $errors->has('password')) ? 'true' : 'false' }},
        editName: '{{ addslashes(old('name', $user->name ?? '')) }}',
        editEmail: '{{ addslashes(old('email', $user->email ?? '')) }}',
        editStudentId: '{{ addslashes(old('student_id', $user->student_id ?? '')) }}',
        editSection: '{{ addslashes(old('section', $user->section ?? '')) }}',
        photoPreview: null,
        showCurrentPass: false,
        showNewPass: false,
        showConfirmPass: false,
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
        isCurrentPassCorrect: false,
        checkingCurrentPass: false,
        passStatusMsg: '',

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
        toggleTheme() {
            const isLight = document.documentElement.classList.toggle('light-mode');
            this.isLightMode = isLight;
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

                {{-- Settings Part: Notification & Dark/Light Mode --}}
                <div class="profile-settings-group">
                    <span class="profile-settings-header-label">Settings</span>

                    {{-- 1. Notification --}}
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

                    {{-- 2. Dark and Light Mode --}}
                    <div class="profile-setting-item" @click="toggleTheme()" role="button" tabindex="0">
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
                </div>
            </div>

            {{-- Logout Card right after Profile Card --}}
            <div class="profile-logout-card ui-reveal" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 12px;">
                <div class="profile-logout-info" style="text-align: center;">
                    <h4>Logout</h4>
                    <p style="font-size: 12.5px; line-height: 1.4;">You will be logged out of your AgriCo account.</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" onsubmit="localStorage.removeItem('agrico-theme');" style="margin:0; width:100%;">
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
                <div class="profile-info-header" style="justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="profile-info-header-icon">
                            <svg width="18" height="18" fill="none" stroke="#4ADE80" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <h3>Account Information</h3>
                    </div>

                    <button type="button" @click="showEditModal = true" class="profile-edit-btn">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Edit Profile
                    </button>
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
            <template x-teleport="body">
                <div
                    x-show="showEditModal"
                    x-cloak
                    @click.self="showEditModal = false"
                    class="profile-modal-overlay"
                >
                    <div class="profile-modal-card">
                        <form method="POST" action="{{ route('website.profile.update') }}" enctype="multipart/form-data" autocomplete="off" style="display:flex; flex-direction:column; flex:1; min-height:0; margin:0;">
                            @csrf

                            {{-- Hidden photo file input for gallery/file explorer --}}
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

                            {{-- FIXED STICKY HEADER --}}
                            <div class="profile-modal-header">
                                <h2 class="profile-modal-title">
                                    Edit your profile
                                </h2>
                                <button
                                    type="button"
                                    @click="showEditModal = false"
                                    class="modal-close-circle-btn"
                                    aria-label="Close"
                                    title="Close"
                                >
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- 2-COLUMN BODY (RESPONSIVE SINGLE COLUMN ON SMALL SCREENS) --}}
                            <div class="profile-modal-body">
                                
                                {{-- LEFT COLUMN: Form Inputs --}}
                                <div class="profile-modal-inputs">
                                    {{-- Full name --}}
                                    <div style="position:relative;">
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
                                            class="floating-input"
                                            style="width:100%; height:44px; padding:10px 16px 10px 42px; font-size:0.88rem; outline:none; box-sizing:border-box;"
                                        >
                                        <label for="edit_name" class="floating-label" style="position:absolute; left:40px; top:50%; transform:translateY(-50%); font-size:0.86rem; pointer-events:none; transition:all 0.2s ease; background:transparent; padding:0 4px; z-index:2;">Full Name</label>
                                    </div>

                                    {{-- Email --}}
                                    <div style="position:relative;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>
                                        <input
                                            id="edit_email"
                                            type="email"
                                            name="email"
                                            x-model="editEmail"
                                            required
                                            placeholder=" "
                                            class="floating-input"
                                            style="width:100%; height:44px; padding:10px 16px 10px 42px; font-size:0.88rem; outline:none; box-sizing:border-box;"
                                        >
                                        <label for="edit_email" class="floating-label" style="position:absolute; left:40px; top:50%; transform:translateY(-50%); font-size:0.86rem; pointer-events:none; transition:all 0.2s ease; background:transparent; padding:0 4px; z-index:2;">Email</label>
                                    </div>

                                    @if(strtolower(trim($user->role ?? '')) === 'student')
                                        {{-- Student ID --}}
                                        <div style="position:relative;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.364a4.125 4.125 0 0 0-6.338 0c-.267.319-.064.811.353.811h5.632c.417 0 .62-.492.353-.811Z" />
                                            </svg>
                                            <input
                                                id="edit_student_id"
                                                type="text"
                                                name="student_id"
                                                x-model="editStudentId"
                                                placeholder=" "
                                                class="floating-input"
                                                style="width:100%; height:44px; padding:10px 16px 10px 42px; font-size:0.88rem; outline:none; box-sizing:border-box;"
                                            >
                                            <label for="edit_student_id" class="floating-label" style="position:absolute; left:40px; top:50%; transform:translateY(-50%); font-size:0.86rem; pointer-events:none; transition:all 0.2s ease; background:transparent; padding:0 4px; z-index:2;">Student ID</label>
                                        </div>

                                        {{-- Section --}}
                                        <div style="position:relative;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-5.25 6.557c.75-.45 1.5-.85 2.25-1.2" />
                                            </svg>
                                            <input
                                                id="edit_section"
                                                type="text"
                                                name="section"
                                                x-model="editSection"
                                                placeholder=" "
                                                class="floating-input"
                                                style="width:100%; height:44px; padding:10px 16px 10px 42px; font-size:0.88rem; outline:none; box-sizing:border-box;"
                                            >
                                            <label for="edit_section" class="floating-label" style="position:absolute; left:40px; top:50%; transform:translateY(-50%); font-size:0.86rem; pointer-events:none; transition:all 0.2s ease; background:transparent; padding:0 4px; z-index:2;">Section</label>
                                        </div>
                                    @endif

                                    {{-- Divider --}}
                                    <div class="profile-modal-divider">
                                        <span class="profile-modal-section-title">Change Password (Optional)</span>
                                    </div>

                                    {{-- Current Password --}}
                                    <div>
                                        <div style="position:relative;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                            </svg>
                                            <input
                                                id="edit_current_password"
                                                :type="showCurrentPass ? 'text' : 'password'"
                                                name="current_password"
                                                x-model="currentPassword"
                                                @input.debounce.300ms="verifyCurrentPassword()"
                                                autocomplete="new-password"
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                                placeholder=" "
                                                class="floating-input"
                                                :class="{
                                                    'border-correct': currentPassword.length > 0 && isCurrentPassCorrect,
                                                    'border-incorrect': currentPassword.length > 0 && !checkingCurrentPass && !isCurrentPassCorrect
                                                }"
                                                style="width:100%; height:44px; padding:10px 40px 10px 42px; font-size:0.88rem; outline:none; box-sizing:border-box;"
                                            >
                                            <label for="edit_current_password" class="floating-label" style="position:absolute; left:40px; top:50%; transform:translateY(-50%); font-size:0.86rem; pointer-events:none; transition:all 0.2s ease; background:transparent; padding:0 4px; z-index:2;">Current Password</label>
                                            <button
                                                type="button"
                                                @click="showCurrentPass = !showCurrentPass"
                                                class="input-eye-btn"
                                            >
                                                <template x-if="!showCurrentPass">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:16px; height:16px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                    </svg>
                                                </template>
                                                <template x-if="showCurrentPass">
                                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </template>
                                            </button>
                                        </div>

                                        {{-- Verification Status Indicator & Forgot Password Link --}}
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:5px; margin-bottom:2px; min-height:18px;">
                                            <div>
                                                <div x-show="currentPassword.length > 0" style="font-size:11.5px; font-weight:600;">
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
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3px; white-space:nowrap; gap:6px;">
                                            <span class="pass-helper-label" x-show="!isCurrentPassCorrect">(Enter current password first)</span>
                                            <div style="display:flex; align-items:center; gap:4px; margin-left:auto; white-space:nowrap; flex-shrink:0;">
                                                <span class="pass-helper-label">Security level</span>
                                                <div style="display:flex; align-items:center; gap:2px;">
                                                    <div class="meter-bar" :style="{ background: strengthScore >= 1 ? (strengthScore <= 2 ? '#F87171' : (strengthScore <= 4 ? '#FBBF24' : '#4ADE80')) : '' }"></div>
                                                    <div class="meter-bar" :style="{ background: strengthScore >= 2 ? (strengthScore <= 2 ? '#F87171' : (strengthScore <= 4 ? '#FBBF24' : '#4ADE80')) : '' }"></div>
                                                    <div class="meter-bar" :style="{ background: strengthScore >= 3 ? (strengthScore <= 4 ? '#FBBF24' : '#4ADE80') : '' }"></div>
                                                    <div class="meter-bar" :style="{ background: strengthScore >= 4 ? (strengthScore <= 4 ? '#FBBF24' : '#4ADE80') : '' }"></div>
                                                    <div class="meter-bar" :style="{ background: strengthScore >= 5 ? '#4ADE80' : '' }"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="position:relative;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                            </svg>
                                            <input
                                                id="edit_password"
                                                :type="showNewPass ? 'text' : 'password'"
                                                name="password"
                                                x-model="newPassword"
                                                :disabled="!isCurrentPassCorrect"
                                                autocomplete="new-password"
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                                placeholder=" "
                                                class="floating-input"
                                                :class="!isCurrentPassCorrect ? 'input-locked' : 'input-unlocked'"
                                                style="width:100%; height:44px; padding:10px 40px 10px 42px; font-size:0.88rem; outline:none; box-sizing:border-box;"
                                            >
                                            <label for="edit_password" class="floating-label" style="position:absolute; left:40px; top:50%; transform:translateY(-50%); font-size:0.86rem; pointer-events:none; transition:all 0.2s ease; background:transparent; padding:0 4px; z-index:2;">New Password</label>
                                            <button
                                                type="button"
                                                @click="showNewPass = !showNewPass"
                                                class="input-eye-btn"
                                            >
                                                <template x-if="!showNewPass">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:16px; height:16px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                    </svg>
                                                </template>
                                                <template x-if="showNewPass">
                                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </template>
                                            </button>
                                        </div>

                                        {{-- Password Requirements Badges --}}
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
                                        <div style="display:flex; justify-content:flex-end; margin-bottom:2px;">
                                            <button
                                                type="button"
                                                @click="clearPasswords()"
                                                x-show="currentPassword || newPassword || confirmPassword"
                                                class="pass-clear-btn"
                                            >
                                                Clear
                                            </button>
                                        </div>
                                        <div style="position:relative;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" class="input-lead-icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                            </svg>
                                            <input
                                                id="edit_password_confirmation"
                                                :type="showConfirmPass ? 'text' : 'password'"
                                                name="password_confirmation"
                                                x-model="confirmPassword"
                                                :disabled="!isCurrentPassCorrect"
                                                autocomplete="new-password"
                                                readonly
                                                onfocus="this.removeAttribute('readonly');"
                                                placeholder=" "
                                                class="floating-input"
                                                :class="!isCurrentPassCorrect ? 'input-locked' : 'input-unlocked'"
                                                style="width:100%; height:44px; padding:10px 40px 10px 42px; font-size:0.88rem; outline:none; box-sizing:border-box;"
                                            >
                                            <label for="edit_password_confirmation" class="floating-label" style="position:absolute; left:40px; top:50%; transform:translateY(-50%); font-size:0.86rem; pointer-events:none; transition:all 0.2s ease; background:transparent; padding:0 4px; z-index:2;">Confirm Password</label>
                                            <button
                                                type="button"
                                                @click="showConfirmPass = !showConfirmPass"
                                                class="input-eye-btn"
                                            >
                                                <template x-if="!showConfirmPass">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:16px; height:16px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                    </svg>
                                                </template>
                                                <template x-if="showConfirmPass">
                                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </template>
                                            </button>
                                        </div>
                                    </div>
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
                                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
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

                            {{-- FIXED STICKY FOOTER --}}
                            <div class="profile-modal-footer">
                                <span class="profile-modal-last-updated">
                                    Last updated: {{ $user->updated_at ? $user->updated_at->format('M d, Y') : now()->format('M d, Y') }}
                                </span>

                                <button
                                    type="submit"
                                    class="profile-modal-save-btn"
                                >
                                    Save changes
                                </button>
                            </div>
                        </form>
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
                    <div class="profile-modal-card" style="max-width: 540px; width: 92%; max-height: 85vh; display: flex; flex-direction: column;">
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
                                        style="background: transparent; border: 1.5px solid #22c55e; color: #22c55e; font-size: 11.5px; font-weight: 700; border-radius: 999px; padding: 4px 12px; cursor: pointer; transition: all 0.2s ease;"
                                        onmouseover="this.style.background='#22c55e';this.style.color='#ffffff';"
                                        onmouseout="this.style.background='transparent';this.style.color='#22c55e';"
                                    >
                                        Mark all read
                                    </button>
                                </template>
                                <button
                                    type="button"
                                    @click="showNotificationsModal = false"
                                    class="profile-modal-close-btn"
                                    aria-label="Close"
                                >
                                    &times;
                                </button>
                            </div>
                        </div>

                        {{-- Modal Scrollable Body --}}
                        <div class="profile-modal-body" style="padding: 16px 24px; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                            @forelse($notifications as $notif)
                                @php
                                    $data = $notif->data ?? [];
                                    $isPin = $notif->type === 'pin_submitted';
                                    $isApproved = $notif->type === 'scan_approved';
                                    $isRejected = $notif->type === 'scan_rejected';
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
                                style="width: auto; padding: 9px 24px; font-size: 13.5px; border-radius: 999px;"
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
