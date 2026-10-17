<!-- Soil Record Details Pop-up Modal (Shared Single Source of Truth for AgriCo) -->
<div id="{{ $modalId ?? 'soilRecordModal' }}" 
     class="soil-modal-backdrop" 
     onclick="if (event.target === this) closeSoilRecordModal()" 
     style="display: none !important;">
    <div class="soil-modal-card">
        
        <!-- Sticky Modal Header -->
        <div class="soil-modal-header">
            <h3 class="soil-modal-title" id="modalRecordTitle">Soil Record Details</h3>
            <x-close-button onclick="closeSoilRecordModal()" title="Close details" ariaLabel="Close details" />
        </div>

        <!-- Scrollable Modal Body Container -->
        <div class="soil-modal-body-container" id="modalBodyContainer">
            
            <!-- Dual-Preview Soil Badge Banner (Dominant Color Swatch) -->
            <div id="modalSoilDualPreviewBadge" class="soil-modal-banner">
                <!-- Single-box Photo Thumbnail Avatar (Clean single container, no nested frames or boxes) -->
                <div id="modalSoilSnippetAvatarContainer" class="soil-modal-avatar-box">
                    <img id="modalSoilCircleSnippet" src="" alt="Soil sample photo" class="soil-modal-avatar-img" />
                    <div id="modalSoilSnippetFallback" class="soil-modal-avatar-fallback" title="Image not available for this record">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <span>IMAGE NOT AVAILABLE</span>
                    </div>
                </div>

                <!-- Center Code & Percentage (Word-break safe, no mid-word breaks) -->
                <div id="modalSwatchBlock" class="soil-modal-swatch-center" title="Click to view all detected soil results">
                    <div class="soil-modal-code-row">
                        <span id="modalDominantChevron" class="soil-modal-chevron" style="display: none;">▼</span>
                        <span id="modalCodeDisplay" class="soil-modal-code-display">7.5YR 5/3</span>
                    </div>
                    <div id="modalSoilPercentageDisplay" class="soil-modal-percentage-display" style="display: none;">
                        Soil Percentage (60%)
                    </div>
                </div>

                <!-- Right Hex Pill (Never squeezed, flex-shrink: 0) -->
                <span id="modalHexPill" class="soil-modal-hex-pill">#8F694E</span>

                <!-- Interactive Dropdown Menu for all detected soil layers in Record Modal -->
                <div id="modalLayerDropdownMenu" class="res-layer-dropdown-menu" style="display: none;">
                    <div class="res-layer-dropdown-header">
                        <span>Detected Soil Layers</span>
                        <span id="modalLayerDropdownCount" class="res-layer-dropdown-count">0 DETECTED</span>
                    </div>
                    <div id="modalLayerDropdownList" class="res-layer-dropdown-list"></div>
                </div>
            </div>

            <!-- Row 1: Code Details (Color Name, Full Code, Scan Count) -->
            <div class="soil-modal-grid-3 soil-metrics-row-1">
                <div class="soil-modal-grid-card">
                    <div class="soil-modal-label">Color Name</div>
                    <div id="modalCardColorName" class="soil-modal-value">Brown</div>
                </div>
                <div class="soil-modal-grid-card">
                    <div class="soil-modal-label">Full Code</div>
                    <div id="modalCardFullCode" class="soil-modal-value">7.5YR 5/3</div>
                </div>
                <div class="soil-modal-grid-card">
                    <div class="soil-modal-label">Scan Count</div>
                    <div id="modalCardScanCount" class="soil-modal-value">1</div>
                </div>
            </div>

            <!-- Row 2: Soil Properties (Soil Organic, Soil Type, Near-Match) -->
            <div class="soil-modal-grid-3 soil-metrics-row-2">
                <div class="soil-modal-grid-card">
                    <div class="soil-modal-label">Soil Organic</div>
                    <div id="modalCardOrganic" class="soil-modal-value">Moderate (2.2% – 3.7%)</div>
                </div>
                <div class="soil-modal-grid-card">
                    <div class="soil-modal-label">Soil Type</div>
                    <div id="modalCardDrainage" class="soil-modal-value">Well-Drained Clay / Latosol</div>
                </div>
                <div class="soil-modal-grid-card card-near-match">
                    <div class="soil-modal-label">Near-Match</div>
                    <div id="modalCardConfidence" class="soil-modal-value soil-conf-emerald">97.4%</div>
                </div>
            </div>

            <!-- Row 3: Scan Date & Scanned By -->
            <div id="modalCardMetaRow" class="soil-modal-grid-2 soil-metrics-row-3">
                <div id="modalCardDateContainer" class="soil-modal-grid-card">
                    <div class="soil-modal-label">Scan Date</div>
                    <div id="modalCardDate" class="soil-modal-value">08/21/2026</div>
                </div>
                <div id="modalCardScannedByContainer" class="soil-modal-grid-card">
                    <div class="soil-modal-label">Scanned By</div>
                    <div class="soil-modal-scanned-by-wrap">
                        <span id="modalCardScannedBy" class="soil-modal-value">Admin Analyst</span>
                        <span id="modalCardPlatform" class="soil-platform-badge" style="display: none;">Web</span>
                    </div>
                </div>
            </div>

            <!-- Scan Note Card (Full Width 100%) -->
            <div id="modalScanNoteCard" class="soil-modal-grid-card soil-modal-desc-card">
                <div class="soil-modal-label">Scan Note</div>
                <div id="modalCardScanNote" class="soil-modal-value soil-modal-long-text">
                    No scan note available for this record.
                </div>
            </div>

            <!-- Description Card (Full Width 100%) -->
            <div id="modalDescCard" class="soil-modal-grid-card soil-modal-desc-card">
                <div class="soil-modal-label">Description</div>
                <div id="modalCardDesc" class="soil-modal-value soil-modal-long-text">
                    7.5YR 5/3 - brown
                </div>
            </div>

            <!-- Multi-Layer Accordion Section (Secondary & Tertiary Layers) -->
            <div id="modalMultiLayerSection" class="soil-modal-multi-layer-section" style="display: none;">
                <div id="modalMultiLayerItems" class="soil-modal-multi-layer-items"></div>
            </div>

            <!-- Soil Pin Location & Covered Area Card -->
            <div id="modalSoilLocationCard" class="soil-modal-grid-card" style="display: none;">
                <div class="modal-location-header">
                    <div class="soil-modal-label" style="margin-bottom: 0;">SOIL PIN LOCATION &amp; COVERED AREA</div>
                    <div class="modal-location-badges">
                        <span id="modalLocStatusBadge" class="modal-loc-badge-status" style="display: none;">--</span>
                        <span id="modalLocBrgyBadge" class="modal-loc-badge-brgy">--</span>
                        <span id="modalLocCoverageBadge" class="modal-loc-badge-coverage" style="display: none;">--</span>
                        <span id="modalLocCoordsBadge" class="modal-loc-badge-coords">--</span>
                    </div>
                </div>
                <div class="modal-soil-map-wrapper">
                    <div id="modalSoilDetailMap" style="height: 220px; width: 100%;"></div>
                    <div class="modal-soil-map-watermark">AgriCo Soil GPS Preview</div>
                </div>
            </div>

            <!-- Soil Normalization: Before & After Card -->
            <div id="modalBeforeAfterCard" class="soil-modal-grid-card" style="display: none;">
                <div class="modal-ba-header">
                    <div class="soil-modal-label" style="color: #8EB69B; margin-bottom: 0;">SOIL NORMALIZATION</div>
                    <span class="before-after-badge">BEFORE / AFTER</span>
                </div>
                <div id="modalBeforeAfterContainer" class="modal-ba-container" onclick="openBeforeAfterLightbox('modalBeforeAfterImg')" title="Click to view full resolution comparison">
                    <img id="modalBeforeAfterImg" src="" alt="Soil Before & After Normalization" class="modal-ba-img">
                    <div id="modalBeforeAfterBoxes" class="modal-ba-boxes"></div>
                    <div id="modalBeforeAfterPlaceholder" class="modal-ba-placeholder" style="display: none;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 8px;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <span style="font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.85);">Image not available</span>
                        <span style="font-size: 10.5px; color: rgba(255,255,255,0.5); margin-top: 3px;">Normalization comparison image was not retained for this record</span>
                    </div>
                </div>
                <div id="modalBeforeAfterSwatches" style="display: none; flex-wrap: wrap; gap: 6px; margin-top: 8px;"></div>
                <div class="modal-ba-meta-footer">
                    <strong>METADATA</strong>
                    <span id="modalBeforeAfterMetaText">CREATED: — | DETECTED COLORS: 1</span>
                </div>
            </div>

            <!-- Metadata Footer -->
            <div id="modalMetadataFooter" class="soil-modal-meta-footer" style="display: none;">
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

<style>
    /* ── UNIFIED SOIL RECORD MODAL ARCHITECTURE (Shared Single Source of Truth) ── */
    .soil-modal-backdrop {
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
    .soil-modal-backdrop[style*="display:none"],
    .soil-modal-backdrop[style*="display: none"] {
        display: none !important;
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
        height: auto !important;
        max-height: min(85vh, calc(100dvh - 24px)) !important;
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

    /* Dominant Swatch Banner */
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

    /* Single Clean Image Box */
    .soil-modal-avatar-box {
        position: relative !important;
        width: 56px !important;
        height: 56px !important;
        min-width: 56px !important;
        aspect-ratio: 1 / 1 !important;
        border-radius: 12px !important;
        overflow: hidden !important;
        flex-shrink: 0 !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .soil-modal-avatar-img {
        position: absolute !important;
        inset: 0 !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: none;
        border-radius: 0 !important;
        border: none !important;
        outline: none !important;
    }
    .soil-modal-avatar-fallback {
        position: absolute !important;
        inset: 0 !important;
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(0, 0, 0, 0.24) !important;
        color: rgba(255, 255, 255, 0.85) !important;
        text-align: center !important;
        padding: 4px !important;
        box-sizing: border-box !important;
        border-radius: 0 !important;
    }
    .soil-modal-avatar-fallback svg {
        opacity: 0.85;
        margin-bottom: 2px;
    }
    .soil-modal-avatar-fallback span {
        font-size: 7px;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.02em;
        opacity: 0.95;
        text-transform: uppercase;
    }

    /* Center Text with Word-break Protection */
    .soil-modal-swatch-center {
        flex: 1 !important;
        min-width: 0 !important;
        cursor: pointer !important;
    }
    .soil-modal-code-row {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        min-width: 0 !important;
    }
    .soil-modal-chevron {
        font-size: 12px !important;
        color: #ffffff !important;
        opacity: 0.9 !important;
        transition: transform 0.2s ease !important;
    }
    .soil-modal-code-display {
        font-size: 24px !important;
        font-weight: 900 !important;
        color: #ffffff !important;
        letter-spacing: -0.01em !important;
        line-height: 1.15 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .soil-modal-percentage-display {
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: rgba(255, 255, 255, 0.95) !important;
        margin-top: 3px !important;
        letter-spacing: 0.02em !important;
        overflow-wrap: normal !important;
        word-break: normal !important;
        white-space: normal !important;
    }
    .soil-modal-hex-pill {
        font-size: 11px !important;
        font-weight: 800 !important;
        color: #ffffff !important;
        background: rgba(0, 0, 0, 0.58) !important;
        backdrop-filter: blur(6px) !important;
        -webkit-backdrop-filter: blur(6px) !important;
        padding: 5px 12px !important;
        border-radius: 20px !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        user-select: none !important;
        line-height: 1 !important;
        flex-shrink: 0 !important;
        white-space: nowrap !important;
    }

    /* Grids & Cards */
    .soil-modal-grid-3 {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 10px !important;
        margin-bottom: 10px !important;
        box-sizing: border-box !important;
        width: 100% !important;
    }
    .soil-modal-grid-2 {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
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
        word-break: normal !important;
        text-transform: uppercase !important;
    }
    .soil-modal-value {
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: #DAF1DE !important;
        word-break: break-word !important;
    }
    .soil-conf-emerald {
        color: #10b981 !important;
        font-weight: 800 !important;
    }
    .soil-modal-scanned-by-wrap {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 6px !important;
        flex-wrap: wrap !important;
    }
    .soil-modal-desc-card {
        min-height: 58px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        margin-bottom: 10px !important;
    }
    .soil-modal-long-text {
        font-size: 13px !important;
        font-weight: 700 !important;
        margin: 0 !important;
        line-height: 1.5 !important;
        word-break: normal !important;
        overflow-wrap: break-word !important;
        white-space: pre-line !important;
    }

    .soil-modal-multi-layer-section {
        margin-bottom: 12px !important;
        border-top: 1px dashed rgba(142, 182, 155, 0.25) !important;
        padding-top: 12px !important;
    }
    .soil-modal-multi-layer-items {
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
    }

    .modal-location-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        margin-bottom: 8px !important;
    }
    .modal-location-badges {
        display: flex !important;
        gap: 6px !important;
        align-items: center !important;
        flex-wrap: wrap !important;
    }
    .modal-ba-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 8px !important;
    }
    .modal-ba-container {
        position: relative !important;
        width: 100% !important;
        border-radius: 10px !important;
        overflow: hidden !important;
        cursor: pointer !important;
    }
    .modal-ba-img {
        width: 100% !important;
        height: auto !important;
        display: block !important;
        border-radius: 8px !important;
    }
    .modal-ba-boxes {
        position: absolute !important;
        inset: 0 !important;
        pointer-events: none !important;
        z-index: 10 !important;
    }
    .modal-ba-placeholder {
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 28px 16px !important;
        background: rgba(12, 26, 23, 0.95) !important;
        text-align: center !important;
        border-radius: 8px !important;
    }
    .modal-ba-meta-footer {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-top: 12px !important;
        padding-top: 10px !important;
        border-top: 1px solid rgba(142, 182, 155, 0.25) !important;
        font-size: 11px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        color: #DAF1DE !important;
    }
    .soil-modal-meta-footer {
        margin-top: 10px !important;
        padding-top: 10px !important;
        border-top: 1px solid rgba(142, 182, 155, 0.2) !important;
        font-size: 11.5px !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        color: #DAF1DE !important;
    }

    /* Fixed Footer */
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
    .btn-modal-cancel {
        height: 44px !important;
        min-height: 44px !important;
        padding: 0 20px !important;
        border-radius: 14px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        background: rgba(11, 43, 38, 0.8) !important;
        border: 1.5px solid rgba(142, 182, 155, 0.35) !important;
        color: #DAF1DE !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex: 1 !important;
        transition: all 0.2s ease !important;
    }
    .btn-modal-cancel:hover {
        background: rgba(35, 83, 71, 0.5) !important;
        border-color: #DAF1DE !important;
        color: #DAF1DE !important;
        transform: translateY(-1px) !important;
    }
    .btn-save-hardcopy {
        height: 44px !important;
        min-height: 44px !important;
        padding: 0 20px !important;
        border-radius: 14px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        background: #10b981 !important;
        border: none !important;
        color: #ffffff !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        flex: 1.2 !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35) !important;
        transition: all 0.2s ease !important;
    }
    .btn-save-hardcopy:hover {
        background: #059669 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45) !important;
    }

    /* ── RESPONSIVE POP-UP SIZE STANDARDS ── */
    @media (max-width: 640px) {
        .soil-modal-backdrop {
            padding: 10px 12px !important;
        }
        .soil-modal-card {
            width: 100% !important;
            max-width: calc(100vw - 24px) !important;
            height: auto !important;
            max-height: calc(100dvh - 20px) !important;
            border-radius: 24px !important;
        }
        .soil-modal-header {
            padding: 14px 18px 12px !important;
        }
        .soil-modal-title {
            font-size: 16.5px !important;
        }
        .soil-modal-body-container {
            padding: 12px 16px 14px !important;
        }
        .soil-modal-grid-3 {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 8px !important;
            margin-bottom: 8px !important;
        }
        .soil-modal-grid-3 > div:nth-child(3),
        .soil-modal-grid-3 > div:last-child:nth-child(odd) {
            grid-column: 1 / -1 !important;
        }
        .soil-modal-grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 8px !important;
            margin-bottom: 8px !important;
        }
        .soil-modal-grid-card {
            padding: 9px 12px !important;
            border-radius: 14px !important;
        }
        .soil-modal-label {
            font-size: 10.5px !important;
            margin-bottom: 2px !important;
        }
        .soil-modal-value {
            font-size: 13px !important;
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
            border-radius: 12px !important;
        }
    }

    @media (max-width: 380px) {
        .soil-modal-banner {
            padding: 10px 14px !important;
            gap: 10px !important;
            min-height: 68px !important;
        }
        .soil-modal-avatar-box {
            width: 48px !important;
            height: 48px !important;
            min-width: 48px !important;
        }
        .soil-modal-code-display {
            font-size: 20px !important;
        }
        .soil-modal-percentage-display {
            font-size: 11px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .soil-modal-hex-pill {
            font-size: 10px !important;
            padding: 4px 8px !important;
        }
        .soil-modal-grid-card {
            padding: 8px 10px !important;
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
    }

    @media (max-height: 520px) and (orientation: landscape) {
        .soil-modal-card {
            width: 100% !important;
            max-width: 680px !important;
            height: auto !important;
            max-height: calc(100dvh - 20px) !important;
            border-radius: 24px !important;
        }
    }

    /* ── LIGHT MODE OVERRIDES ── */
    html.light-mode .soil-modal-backdrop {
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
    html.light-mode .soil-modal-body-container {
        scrollbar-color: #cbd5e1 #f1f5f9 !important;
    }
    html.light-mode .soil-modal-body-container::-webkit-scrollbar-track {
        background: #f1f5f9 !important;
    }
    html.light-mode .soil-modal-body-container::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
    }
    html.light-mode .soil-modal-footer {
        background: #ffffff !important;
        border-top: 1px solid #f1f5f9 !important;
    }
    html.light-mode .btn-modal-cancel {
        background: #f1f5f9 !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #1e293b !important;
    }
    html.light-mode .btn-modal-cancel:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
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
    html.light-mode .soil-modal-value,
    html.light-mode .soil-modal-long-text,
    html.light-mode .modal-ba-meta-footer,
    html.light-mode .soil-modal-meta-footer {
        color: #0f172a !important;
    }
    html.light-mode .modal-ba-container {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }
</style>

<script>
    if (typeof window.getContrastColor !== 'function') {
        window.getContrastColor = function(hex) {
            if (!hex || typeof hex !== 'string') {
                return {
                    isLight: false,
                    text: '#ffffff',
                    subtext: 'rgba(255, 255, 255, 0.92)',
                    chevron: '#ffffff',
                    pillBg: 'rgba(0, 0, 0, 0.6)',
                    pillBorder: 'rgba(255, 255, 255, 0.2)',
                    pillText: '#ffffff'
                };
            }
            let c = hex.replace('#', '').trim();
            if (c.length === 3) c = c.split('').map(x => x + x).join('');
            if (c.length !== 6) {
                return {
                    isLight: false,
                    text: '#ffffff',
                    subtext: 'rgba(255, 255, 255, 0.92)',
                    chevron: '#ffffff',
                    pillBg: 'rgba(0, 0, 0, 0.6)',
                    pillBorder: 'rgba(255, 255, 255, 0.2)',
                    pillText: '#ffffff'
                };
            }
            const r = parseInt(c.substring(0, 2), 16) || 0;
            const g = parseInt(c.substring(2, 4), 16) || 0;
            const b = parseInt(c.substring(4, 6), 16) || 0;
            const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
            const isLight = yiq >= 145;
            return {
                isLight: isLight,
                text: isLight ? '#111827' : '#ffffff',
                subtext: isLight ? 'rgba(17, 24, 39, 0.88)' : 'rgba(255, 255, 255, 0.92)',
                chevron: isLight ? '#111827' : '#ffffff',
                pillBg: isLight ? 'rgba(255, 255, 255, 0.88)' : 'rgba(0, 0, 0, 0.6)',
                pillBorder: isLight ? 'rgba(0, 0, 0, 0.15)' : 'rgba(255, 255, 255, 0.2)',
                pillText: isLight ? '#111827' : '#ffffff'
            };
        };
    }

    if (typeof window.closeSoilRecordModal !== 'function') {
        window.closeSoilRecordModal = function() {
            const modal = document.getElementById('{{ $modalId ?? "soilRecordModal" }}') || document.getElementById('soilRecordModal');
            if (modal) {
                modal.style.setProperty('display', 'none', 'important');
            }
            document.body.style.overflow = '';
            const menu = document.getElementById('modalLayerDropdownMenu');
            if (menu) menu.style.display = 'none';
            const chevron = document.getElementById('modalDominantChevron');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        };
    }
</script>
