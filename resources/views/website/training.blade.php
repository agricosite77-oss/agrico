<x-website-layout>
    <x-slot name="title">Manual Training-Data Labeling</x-slot>

    <style>
        .ws-page-hero {
            background: linear-gradient(135deg, #051F20 0%, #0B2B26 100%);
            padding: 20px 24px 16px;
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
            margin: 0 0 4px 0;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .ws-page-sub {
            font-size: 13px;
            color: #8EB69B;
            margin: 0;
            font-weight: 400;
        }

        /* Container Section */
        .training-section {
            background: #051F20;
            padding: 32px 24px;
            min-height: calc(100vh - 180px);
        }
        .training-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 28px;
            align-items: start;
            width: 100%;
        }
        @media (max-width: 960px) {
            .training-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Card Styles */
        .training-card {
            background: rgba(11, 43, 38, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(142, 182, 155, 0.25);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
        }
        .training-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(142, 182, 155, 0.2);
        }
        .training-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: #DAF1DE;
            letter-spacing: -0.01em;
        }
        .training-card-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 9999px;
            background: rgba(142, 182, 155, 0.15);
            color: #8EB69B;
            border: 1px solid rgba(142, 182, 155, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Viewfinder Box */
        .training-viewfinder {
            position: relative;
            width: 100%;
            height: 380px;
            background: #020d0e;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1.5px dashed rgba(142, 182, 155, 0.35);
            touch-action: none;
        }
        @media (max-width: 640px) {
            .training-viewfinder {
                height: 300px;
            }
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
        .training-viewfinder video,
        .training-viewfinder img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: inherit;
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

        /* Circular Reticle Overlay */
        .reticle-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 10;
        }
        .reticle-circle {
            width: 240px;
            height: 240px;
            border-radius: 8px;
            border: 2px dashed rgba(218, 241, 222, 0.85);
            box-shadow: 0 0 0 9999px rgba(5, 31, 32, 0.55);
            position: relative;
            pointer-events: auto;
            cursor: move;
            touch-action: none;
            user-select: none;
            -webkit-user-select: none;
            animation: pulse-reticle 2.5s infinite ease-in-out;
        }
        @keyframes pulse-reticle {
            0%, 100% { border-color: rgba(218, 241, 222, 0.7); }
            50% { border-color: rgba(34, 197, 94, 0.95); }
        }
        .resize-handle {
            position: absolute;
            width: 14px;
            height: 14px;
            background: #DAF1DE;
            border: 2px solid #051f20;
            border-radius: 3px;
            z-index: 10;
            pointer-events: auto;
            touch-action: none;
        }
        .resize-handle-nw { top: -7px; left: -7px; cursor: nwse-resize; }
        .resize-handle-ne { top: -7px; right: -7px; cursor: nesw-resize; }
        .resize-handle-sw { bottom: -7px; left: -7px; cursor: nesw-resize; }
        .resize-handle-se { bottom: -7px; right: -7px; cursor: nwse-resize; }
        .resize-handle-n { top: -7px; left: 50%; transform: translateX(-50%); cursor: ns-resize; }
        .resize-handle-s { bottom: -7px; left: 50%; transform: translateX(-50%); cursor: ns-resize; }
        .resize-handle-e { right: -7px; top: 50%; transform: translateY(-50%); cursor: ew-resize; }
        .resize-handle-w { left: -7px; top: 50%; transform: translateY(-50%); cursor: ew-resize; }

        @media (pointer: coarse) {
            .resize-handle {
                width: 28px;
                height: 28px;
                background: transparent;
                border: none;
            }
            .resize-handle-nw { top: -14px; left: -14px; }
            .resize-handle-ne { top: -14px; right: -14px; }
            .resize-handle-sw { bottom: -14px; left: -14px; }
            .resize-handle-se { bottom: -14px; right: -14px; }
            .resize-handle-n { top: -14px; left: 50%; transform: translateX(-50%); }
            .resize-handle-s { bottom: -14px; left: 50%; transform: translateX(-50%); }
            .resize-handle-e { right: -14px; top: 50%; transform: translateY(-50%); }
            .resize-handle-w { left: -14px; top: 50%; transform: translateY(-50%); }
            .resize-handle::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 14px;
                height: 14px;
                background: #DAF1DE;
                border: 2px solid #051f20;
                border-radius: 3px;
            }
        }

        /* Action Buttons */
        .btn-row {
            display: flex;
            gap: 12px;
            margin-top: 18px;
            flex-wrap: wrap;
        }
        .btn-action {
            flex: 1;
            min-width: 130px;
            height: 46px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(22, 163, 74, 0.45);
        }
        .btn-secondary {
            background: rgba(142, 182, 155, 0.15);
            color: #DAF1DE;
            border: 1px solid rgba(142, 182, 155, 0.3);
        }
        .btn-secondary:hover {
            background: rgba(142, 182, 155, 0.25);
            color: #ffffff;
            border-color: rgba(142, 182, 155, 0.5);
            transform: translateY(-1px);
        }

        /* Label Selector Pills */
        .label-selector-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }
        .label-option-card {
            border: 2px solid rgba(142, 182, 155, 0.25);
            background: rgba(5, 31, 32, 0.5);
            border-radius: 14px;
            padding: 16px 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            position: relative;
        }
        .label-option-card:hover {
            border-color: rgba(142, 182, 155, 0.6);
            background: rgba(11, 43, 38, 0.6);
        }
        .label-option-card.active-real {
            border-color: #22c55e;
            background: rgba(34, 197, 94, 0.12);
            box-shadow: 0 0 16px rgba(34, 197, 94, 0.25);
        }
        .label-option-card.active-fake {
            border-color: #f59e0b;
            background: rgba(245, 158, 11, 0.12);
            box-shadow: 0 0 16px rgba(245, 158, 11, 0.25);
        }
        .label-title {
            font-size: 15px;
            font-weight: 800;
            color: #DAF1DE;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .label-desc {
            font-size: 11px;
            color: #8EB69B;
            line-height: 1.3;
        }

        /* Form Inputs */
        .input-group {
            margin-bottom: 14px;
        }
        .input-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: #DAF1DE;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }
        .input-select, .input-text {
            width: 100%;
            height: 44px;
            background: rgba(5, 31, 32, 0.7);
            border: 1px solid rgba(142, 182, 155, 0.35);
            border-radius: 10px;
            padding: 0 14px;
            color: #DAF1DE;
            font-size: 14px;
            font-weight: 600;
            outline: none;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        .input-select:focus, .input-text:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        }
        .input-select option {
            background: #0B2B26;
            color: #DAF1DE;
        }

        /* Munsell Form Box */
        .munsell-form-box {
            background: rgba(5, 31, 32, 0.45);
            border: 1px solid rgba(142, 182, 155, 0.2);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 20px;
        }
        .munsell-inline-row {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr;
            gap: 12px;
        }

        /* Notation Preview Pill */
        .notation-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.4);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 800;
            color: #86efac;
            margin-top: 10px;
            width: 100%;
            box-sizing: border-box;
            justify-content: center;
        }

        /* Toast Alert */
        #statusToast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 100;
            max-width: 420px;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            display: none;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .toast-success {
            background: #14532d;
            border: 1px solid #22c55e;
            color: #dcfce7;
        }
        .toast-error {
            background: #7f1d1d;
            border: 1px solid #ef4444;
            color: #fee2e2;
        }

        /* Counter Widget */
        .counter-pill {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 8px 14px;
            background: rgba(5, 31, 32, 0.5);
            border: 1px solid rgba(142, 182, 155, 0.2);
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            color: #8EB69B;
            margin-top: 14px;
        }
        .counter-pill strong {
            color: #DAF1DE;
        }
    </style>

    {{-- Hero Banner --}}
    <div class="ws-page-hero">
        <div class="ws-page-hero-inner ui-reveal">
            <div class="ws-breadcrumb">
                <a href="{{ route('website.home') }}">Home</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                <span>Training Data</span>
            </div>
            <h1 class="ws-page-title">Manual Training-Data Labeling</h1>
            <p class="ws-page-sub">Directly capture or upload photos and file them into ground-truth training folders (bypasses ML inference).</p>
        </div>
    </div>

    {{-- Main Workspace Section --}}
    <div class="training-section">
        <div class="training-grid">

            {{-- Left Column: Camera / Upload Viewfinder --}}
            <div class="training-card ui-reveal">
                <div class="training-card-header">
                    <h2 class="training-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        <span>Sample Capture</span>
                    </h2>
                    <span id="cameraStatusBadge" class="training-card-badge">Standby</span>
                </div>

                <div class="training-viewfinder" id="viewfinderContainer">
                    <div class="image-mask" id="trainingImageMask">
                        <video id="webcamVideo" autoplay playsinline muted style="display: none;"></video>
                        <img id="imagePreview" alt="Sample Preview" style="display: none;" />
                    </div>

                    {{-- Reticle Guide --}}
                    <div class="reticle-overlay" id="reticleOverlay" style="display: none;">
                        <div class="reticle-circle"></div>
                    </div>

                    {{-- Placeholder --}}
                    <div id="capturePlaceholder" style="text-align: center; padding: 24px;">
                        <svg width="44" height="44" fill="none" stroke="#8EB69B" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 12px;">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        <p style="margin: 0; font-size: 14px; font-weight: 700; color: #DAF1DE;">Start Camera or Upload a Photo</p>
                        <p style="margin: 4px 0 0; font-size: 12px; color: #8EB69B;">Sample should be centered in the viewfinder</p>
                    </div>
                </div>

                {{-- Zoom Controls for static preview --}}
                <div class="zoom-controls" id="zoomControls" style="display: none;">
                    <button type="button" class="zoom-btn" id="zoomOutBtn" title="Zoom Out">−</button>
                    <button type="button" class="zoom-btn" id="zoomResetBtn" title="Reset Zoom">Reset</button>
                    <button type="button" class="zoom-btn" id="zoomInBtn" title="Zoom In">+</button>
                </div>

                {{-- Hidden Input and Canvas --}}
                <canvas id="captureCanvas" style="display: none;"></canvas>
                <input type="file" id="fileUploadInput" accept="image/*" style="display: none;" />

                {{-- Camera and Upload Controls --}}
                <div class="btn-row">
                    <button type="button" class="btn-action btn-primary" id="btnToggleCamera" onclick="toggleCamera()">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <span id="btnCameraText">Start Camera</span>
                    </button>
                    <button type="button" class="btn-action btn-secondary" onclick="document.getElementById('fileUploadInput').click()">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <span>Upload Photo</span>
                    </button>
                    <button type="button" class="btn-action btn-secondary" id="newPhotoBtn" onclick="resetImagePreview()" title="Clear and load a new photo">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                        <span>New Photo</span>
                    </button>
                </div>
            </div>

            {{-- Right Column: Ground-Truth Labeling & Submission --}}
            <div class="training-card ui-reveal">
                <div class="training-card-header">
                    <h2 class="training-card-title">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 11l3 3L22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        <span>Ground-Truth Classification</span>
                    </h2>
                    <span class="training-card-badge">Human Labeler</span>
                </div>

                {{-- Category Selector (Real Soil vs Fake Soil) --}}
                <div class="label-selector-grid">
                    <div class="label-option-card active-real" id="cardRealSoil" onclick="selectCategory('real_soil')">
                        <div class="label-title">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Real Soil</span>
                        </div>
                        <div class="label-desc">Genuine agricultural/earthen soil sample</div>
                    </div>

                    <div class="label-option-card" id="cardFakeSoil" onclick="selectCategory('fake_soil')">
                        <div class="label-title">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            <span>Fake / Non-Soil</span>
                        </div>
                        <div class="label-desc">Wood, skin, fabric, paper, mulch, rock, etc.</div>
                    </div>
                </div>

                {{-- Real Soil Munsell Properties Form --}}
                <div class="munsell-form-box" id="munsellFormBox">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; margin-bottom: 12px; user-select: none;">
                        <span class="input-label" style="margin: 0; color: #86efac; display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" id="chkAssignMunsell" onchange="toggleMunsellFields()" style="width: 17px; height: 17px; accent-color: #22c55e; cursor: pointer;" />
                            <span>Munsell Chart Parameters (Optional)</span>
                        </span>
                        <span style="font-size: 11px; color: #8EB69B;">Unchecked: puts photo directly in Real_Soil/</span>
                    </label>

                    <div id="munsellInputsRow" style="display: none;">
                        <div class="munsell-inline-row">
                            <div class="input-group" style="margin: 0;">
                                <label class="input-label" for="selectHue">Hue</label>
                                <select id="selectHue" class="input-select" onchange="updateNotationPreview()">
                                    <option value="10R">10R</option>
                                    <option value="2.5YR">2.5YR</option>
                                    <option value="5YR">5YR</option>
                                    <option value="7.5YR">7.5YR</option>
                                    <option value="10YR" selected>10YR</option>
                                    <option value="2.5Y">2.5Y</option>
                                    <option value="5Y">5Y</option>
                                </select>
                            </div>

                            <div class="input-group" style="margin: 0;">
                                <label class="input-label" for="inputValue">Value</label>
                                <input type="number" id="inputValue" class="input-text" step="0.5" min="1" max="10" value="4" oninput="updateNotationPreview()" />
                            </div>

                            <div class="input-group" style="margin: 0;">
                                <label class="input-label" for="inputChroma">Chroma</label>
                                <input type="number" id="inputChroma" class="input-text" step="0.5" min="1" max="12" value="3" oninput="updateNotationPreview()" />
                            </div>
                        </div>
                    </div>

                    <div class="notation-pill" id="notationPreview">
                        Destination: <code>Scan_Soil_Image/Real_Soil/</code> &bull; General Real Soil
                    </div>
                </div>

                {{-- Fake Soil Info Box --}}
                <div class="munsell-form-box" id="fakeSoilInfoBox" style="display: none; border-color: rgba(245, 158, 11, 0.4); background: rgba(245, 158, 11, 0.08);">
                    <div style="font-size: 13px; font-weight: 700; color: #fcd34d; margin-bottom: 4px;">
                        Negative Class Reference
                    </div>
                    <div style="font-size: 12px; color: #fde68a; line-height: 1.4;">
                        This sample will be stored flat in <code style="background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 4px;">Scan_Soil_Image/Fake_Soil/</code> to train the gate classifier to reject non-soil surfaces.
                    </div>
                </div>

                {{-- Submit Action Button --}}
                <button type="button" class="btn-action btn-primary" id="btnSaveTraining" style="width: 100%; height: 50px; font-size: 15px;" onclick="submitTrainingImage()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <span id="btnSaveText">Save Training Image</span>
                </button>

                {{-- Session Counter --}}
                <div class="counter-pill">
                    <span>Session Saves:</span>
                    <span>Real Soil: <strong id="countRealSoil">0</strong></span>
                    <span>&bull;</span>
                    <span>Fake Soil: <strong id="countFakeSoil">0</strong></span>
                </div>
            </div>

        </div>
    </div>

    {{-- Toast Notification --}}
    <div id="statusToast">
        <svg id="toastIcon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"></svg>
        <span id="toastMessage"></span>
    </div>

    <script src="{{ asset('js/image-pan-zoom.js') }}?v={{ file_exists(public_path('js/image-pan-zoom.js')) ? filemtime(public_path('js/image-pan-zoom.js')) : time() }}"></script>
    <script>
        let currentLabel = 'real_soil';
        let mediaStream = null;
        let isCameraRunning = false;
        let capturedBase64 = null;
        let sessionRealCount = 0;
        let sessionFakeCount = 0;
        let trainingPanZoom = null;
        let trainingResizable = null;

        const videoEl = document.getElementById('webcamVideo');
        const imgPreview = document.getElementById('imagePreview');
        const placeholderEl = document.getElementById('capturePlaceholder');
        const reticleEl = document.getElementById('reticleOverlay');
        const canvasEl = document.getElementById('captureCanvas');
        const fileInput = document.getElementById('fileUploadInput');
        const statusBadge = document.getElementById('cameraStatusBadge');
        const btnCameraText = document.getElementById('btnCameraText');

        function initTrainingPanZoom() {
            const img = document.getElementById('imagePreview');
            const imageMask = document.getElementById('trainingImageMask');
            const container = document.getElementById('viewfinderContainer');
            const reticleBox = document.querySelector('.reticle-circle');

            if (!trainingPanZoom && img && imageMask && typeof window.ImagePanZoom !== 'undefined') {
                trainingPanZoom = new window.ImagePanZoom(img, imageMask, { minScale: 1, maxScale: 4 });

                const zoomInBtn = document.getElementById('zoomInBtn');
                const zoomOutBtn = document.getElementById('zoomOutBtn');
                const zoomResetBtn = document.getElementById('zoomResetBtn');

                if (zoomInBtn) zoomInBtn.addEventListener('click', () => trainingPanZoom && trainingPanZoom.zoomBy(0.25));
                if (zoomOutBtn) zoomOutBtn.addEventListener('click', () => trainingPanZoom && trainingPanZoom.zoomBy(-0.25));
                if (zoomResetBtn) zoomResetBtn.addEventListener('click', () => {
                    if (trainingPanZoom) trainingPanZoom.reset();
                    if (trainingResizable) trainingResizable.reset();
                });
            }

            if (!trainingResizable && reticleBox && container && typeof window.ResizableViewfinder !== 'undefined') {
                trainingResizable = new window.ResizableViewfinder(reticleBox, container, { minSize: 60, defaultSize: 240 });
            }
        }

        function selectCategory(label) {
            currentLabel = label;
            const cardReal = document.getElementById('cardRealSoil');
            const cardFake = document.getElementById('cardFakeSoil');
            const formBox = document.getElementById('munsellFormBox');
            const fakeBox = document.getElementById('fakeSoilInfoBox');

            if (label === 'real_soil') {
                cardReal.className = 'label-option-card active-real';
                cardFake.className = 'label-option-card';
                formBox.style.display = 'block';
                fakeBox.style.display = 'none';
            } else {
                cardReal.className = 'label-option-card';
                cardFake.className = 'label-option-card active-fake';
                formBox.style.display = 'none';
                fakeBox.style.display = 'block';
            }
        }

        function toggleMunsellFields() {
            const chk = document.getElementById('chkAssignMunsell');
            const row = document.getElementById('munsellInputsRow');
            if (row) {
                row.style.display = (chk && chk.checked) ? 'block' : 'none';
            }
            updateNotationPreview();
        }

        function updateNotationPreview() {
            const chk = document.getElementById('chkAssignMunsell');
            const preview = document.getElementById('notationPreview');
            if (!preview) return;

            if (!chk || !chk.checked) {
                preview.innerHTML = `Destination: <code>Scan_Soil_Image/Real_Soil/</code> &bull; General Real Soil`;
                return;
            }

            const hue = document.getElementById('selectHue').value;
            const val = document.getElementById('inputValue').value;
            const chr = document.getElementById('inputChroma').value;

            const valNum = parseFloat(val);
            const chrNum = parseFloat(chr);
            const valStr = (!isNaN(valNum) && Number.isInteger(valNum)) ? String(parseInt(valNum)) : String(valNum);
            const chrStr = (!isNaN(chrNum) && Number.isInteger(chrNum)) ? String(parseInt(chrNum)) : String(chrNum);

            preview.innerHTML = `Notation: <strong>${hue} ${valStr}/${chrStr}</strong> &bull; Destination: <code>Real_Soil/${hue}/${hue} ${valStr}/${chrStr}/</code>`;
        }

        async function toggleCamera() {
            if (isCameraRunning) {
                stopCamera();
            } else {
                await startCamera();
            }
        }

        async function startCamera() {
            try {
                mediaStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: false
                });
                videoEl.srcObject = mediaStream;
                videoEl.style.display = 'block';
                imgPreview.style.display = 'none';
                placeholderEl.style.display = 'none';
                reticleEl.style.display = 'flex';
                isCameraRunning = true;
                statusBadge.textContent = 'Live Camera';
                statusBadge.style.color = '#86efac';
                btnCameraText.textContent = 'Stop Camera';

                const zoomCtrl = document.getElementById('zoomControls');
                if (zoomCtrl) zoomCtrl.style.display = 'none';
                if (trainingPanZoom) trainingPanZoom.reset();
                if (trainingResizable) trainingResizable.reset();
            } catch (err) {
                showToast('Unable to access camera: ' + (err.message || 'Permission denied'), 'error');
            }
        }

        function stopCamera() {
            if (mediaStream) {
                mediaStream.getTracks().forEach(t => t.stop());
                mediaStream = null;
            }
            videoEl.style.display = 'none';
            isCameraRunning = false;
            btnCameraText.textContent = 'Start Camera';
            statusBadge.textContent = 'Standby';
            statusBadge.style.color = '#8EB69B';

            if (!capturedBase64) {
                placeholderEl.style.display = 'block';
                reticleEl.style.display = 'none';
                const zoomCtrl = document.getElementById('zoomControls');
                if (zoomCtrl) zoomCtrl.style.display = 'none';
                if (trainingPanZoom) trainingPanZoom.reset();
                if (trainingResizable) trainingResizable.reset();
            }
        }

        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            stopCamera();

            const reader = new FileReader();
            reader.onload = function (evt) {
                capturedBase64 = evt.target.result;
                imgPreview.src = capturedBase64;
                imgPreview.style.display = 'block';
                placeholderEl.style.display = 'none';
                reticleEl.style.display = 'flex';
                statusBadge.textContent = 'Photo Loaded';
                statusBadge.style.color = '#38bdf8';

                initTrainingPanZoom();
                if (trainingPanZoom) trainingPanZoom.reset();
                if (trainingResizable) trainingResizable.reset();
                const zoomCtrl = document.getElementById('zoomControls');
                if (zoomCtrl) zoomCtrl.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        });

        function getReticleCroppedImageBase64(sourceEl) {
            try {
                if (!sourceEl) return null;
                const srcW = sourceEl.videoWidth || sourceEl.naturalWidth || sourceEl.width || 640;
                const srcH = sourceEl.videoHeight || sourceEl.naturalHeight || sourceEl.height || 480;
                if (!srcW || !srcH) return null;

                const vfEl = document.getElementById('viewfinderContainer');
                const reticleBox = document.querySelector('.reticle-circle');
                if (!vfEl || !reticleBox) return null;

                const vfRect = vfEl.getBoundingClientRect();
                const retRect = reticleBox.getBoundingClientRect();

                let cx, cy, size;

                if (sourceEl.tagName === 'IMG') {
                    const imgRect = sourceEl.getBoundingClientRect();
                    const scaleX = imgRect.width / srcW;
                    const scaleY = imgRect.height / srcH;

                    const reticleCenterX = retRect.left + retRect.width / 2;
                    const reticleCenterY = retRect.top + retRect.height / 2;
                    const reticleHalfSize = retRect.width / 2;

                    const realCx = (reticleCenterX - imgRect.left) / scaleX;
                    const realCy = (reticleCenterY - imgRect.top) / scaleY;
                    const realHalf = reticleHalfSize / scaleX;

                    const r = Math.max(10, realHalf);
                    cx = Math.max(r, Math.min(srcW - r, realCx));
                    cy = Math.max(r, Math.min(srcH - r, realCy));
                    size = r * 2;
                } else {
                    const containerW = vfRect.width;
                    const containerH = vfRect.height;
                    const scale = Math.max(containerW / srcW, containerH / srcH);
                    const renderW = srcW * scale;
                    const renderH = srcH * scale;

                    const offsetX = (containerW - renderW) / 2;
                    const offsetY = (containerH - renderH) / 2;

                    const reticleCenterX = (retRect.left + retRect.width / 2) - vfRect.left;
                    const reticleCenterY = (retRect.top + retRect.height / 2) - vfRect.top;
                    const reticleHalfSize = retRect.width / 2;

                    const realCx = (reticleCenterX - offsetX) / scale;
                    const realCy = (reticleCenterY - offsetY) / scale;
                    const realHalf = reticleHalfSize / scale;

                    const r = Math.max(10, realHalf);
                    cx = Math.max(r, Math.min(srcW - r, realCx));
                    cy = Math.max(r, Math.min(srcH - r, realCy));
                    size = r * 2;
                }

                const outCanvas = document.createElement('canvas');
                const outDimension = Math.max(64, Math.round(size));
                outCanvas.width = outDimension;
                outCanvas.height = outDimension;
                const ctx = outCanvas.getContext('2d');
                ctx.drawImage(
                    sourceEl,
                    cx - size / 2, cy - size / 2, size, size,
                    0, 0, outDimension, outDimension
                );
                return outCanvas.toDataURL('image/jpeg', 0.95);
            } catch (e) {
                console.warn('Reticle crop fallback:', e);
                return null;
            }
        }

        function getActiveImageBase64() {
            if (isCameraRunning && videoEl.readyState >= 2) {
                const cropped = getReticleCroppedImageBase64(videoEl);
                if (cropped) return cropped;

                // Capture frame from active video
                const w = videoEl.videoWidth || 640;
                const h = videoEl.videoHeight || 480;
                canvasEl.width = w;
                canvasEl.height = h;
                const ctx = canvasEl.getContext('2d');
                ctx.drawImage(videoEl, 0, 0, w, h);
                return canvasEl.toDataURL('image/jpeg', 0.95);
            }

            if (imgPreview && imgPreview.style.display !== 'none' && imgPreview.src) {
                const cropped = getReticleCroppedImageBase64(imgPreview);
                if (cropped) return cropped;
            }

            return capturedBase64;
        }

        async function submitTrainingImage() {
            const base64 = getActiveImageBase64();
            if (!base64) {
                showToast('Please start the camera or upload a photo first.', 'error');
                return;
            }

            const btnSave = document.getElementById('btnSaveTraining');
            const btnSaveText = document.getElementById('btnSaveText');
            btnSave.disabled = true;
            btnSaveText.textContent = 'Saving to Dataset...';

            const payload = {
                image_base64: base64,
                label: currentLabel,
            };

            if (currentLabel === 'real_soil') {
                const chk = document.getElementById('chkAssignMunsell');
                if (chk && chk.checked) {
                    payload.hue = document.getElementById('selectHue').value;
                    payload.value = parseFloat(document.getElementById('inputValue').value);
                    payload.chroma = parseFloat(document.getElementById('inputChroma').value);

                    if (isNaN(payload.value) || isNaN(payload.chroma)) {
                        showToast('Please enter valid numeric Value and Chroma, or uncheck Munsell parameters.', 'error');
                        btnSave.disabled = false;
                        btnSaveText.textContent = 'Save Training Image';
                        return;
                    }
                }
            }

            try {
                const res = await fetch('{{ route("website.training.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    if (currentLabel === 'real_soil') {
                        sessionRealCount++;
                        document.getElementById('countRealSoil').textContent = sessionRealCount;
                        const notationText = (data.notation && data.notation !== 'NA') ? ` (${data.notation})` : '';
                        showToast(`Saved Real Soil${notationText} successfully!`, 'success');
                    } else {
                        sessionFakeCount++;
                        document.getElementById('countFakeSoil').textContent = sessionFakeCount;
                        showToast('Saved Fake Soil successfully!', 'success');
                    }

                    // Image preview intentionally left as-is — user may want to save the
                    // same photo under multiple labels, review it, or manually start a new
                    // capture when ready (via the "New Photo" button).
                } else {
                    showToast(data.error || 'Failed to save training image.', 'error');
                }
            } catch (err) {
                showToast('Network error while saving image: ' + err.message, 'error');
            } finally {
                btnSave.disabled = false;
                btnSaveText.textContent = 'Save Training Image';
            }
        }

        let toastTimer = null;
        function showToast(message, type = 'success') {
            const toast = document.getElementById('statusToast');
            const msgEl = document.getElementById('toastMessage');
            const iconEl = document.getElementById('toastIcon');

            clearTimeout(toastTimer);

            toast.className = (type === 'success') ? 'toast-success' : 'toast-error';
            msgEl.textContent = message;

            if (type === 'success') {
                iconEl.innerHTML = '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>';
            } else {
                iconEl.innerHTML = '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>';
            }

            toast.style.display = 'flex';
            toastTimer = setTimeout(() => {
                toast.style.display = 'none';
            }, 3500);
        }

        function resetImagePreview() {
            if (isCameraRunning) {
                stopCamera();
            }
            capturedBase64 = null;
            imgPreview.src = '';
            imgPreview.style.display = 'none';
            placeholderEl.style.display = 'block';
            reticleEl.style.display = 'none';
            statusBadge.textContent = 'Standby';
            statusBadge.style.color = '#8EB69B';
            fileInput.value = '';
            const zoomCtrl = document.getElementById('zoomControls');
            if (zoomCtrl) zoomCtrl.style.display = 'none';
            if (trainingPanZoom) trainingPanZoom.reset();
            if (trainingResizable) trainingResizable.reset();

            const chk = document.getElementById('chkAssignMunsell');
            if (chk) {
                chk.checked = false;
                toggleMunsellFields();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateNotationPreview();
            initTrainingPanZoom();

            const newPhotoBtn = document.getElementById('newPhotoBtn');
            if (newPhotoBtn) {
                newPhotoBtn.addEventListener('click', resetImagePreview);
            }
        });
    </script>
</x-website-layout>
