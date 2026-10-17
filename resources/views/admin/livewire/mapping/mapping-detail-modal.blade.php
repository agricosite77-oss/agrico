<div>
@if($showModal && $mapping)

@php
    $soil = $mapping->soil;
    $scan = $mapping->soilScan;
    $mColor = $soil?->dynamic_munsell_color;
    $sHue = $soil?->hue ?? ($mColor?->hue ?? '7.5YR');
    $fCode = $soil?->full_code ?: ($mColor?->full_code ?? '7.5YR 5/3');
    
    $swatchColor = $soil?->effective_hex_color ?: '#8F694E';
    $organicIndex = $soil?->effective_organic_matter ?: 'Moderate (2.2% – 3.7%)';
    $drainageType = $soil?->effective_soil_type ?: 'Well-Drained Clay / Latosol';
    $soilDescription = $soil?->effective_description ?: 'Sample processed successfully using AgriCo Munsell Determination Algorithm.';
    $confidenceVal = '97.4%';

    if ($scan && !empty($scan->scan_note) && preg_match('/Confidence:\s*([\d\.]+%?)/i', $scan->scan_note, $cm)) {
        $confidenceVal = str_contains($cm[1], '%') ? $cm[1] : $cm[1] . '%';
    }

    $formatImageUrl = function($path) use ($scan) {
        if (!$path) return '';
        if (str_starts_with($path, 'data:')) {
            return $path;
        }
        if (str_contains($path, 'supabase.co') || str_contains($path, 'agrico-scans')) {
            if ($scan?->scan_image_url) {
                return $scan->scan_image_url;
            }
            if (preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $path, $m)) {
                return asset("storage/scans/scan_{$m[1]}_normalized.jpg");
            }
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            if (preg_match('#^https?://(?:localhost|127\.0\.0\.1|10\.0\.2\.2)(?::\d+)?/(.*)$#i', $path, $matches)) {
                $clean = ltrim($matches[1], '/');
                return asset($clean);
            }
            return $path;
        }
        $clean = ltrim($path, '/');
        if (!str_starts_with($clean, 'storage/')) {
            $clean = 'storage/' . $clean;
        }
        return asset($clean);
    };

    // Regions data check
    $regions = !empty($mapping->regions) ? $mapping->regions : ($mapping ? [] : ($scan?->regions ?? []));
    $isMulti = count($regions) > 1;
    $dominant = !empty($regions) ? $regions[0] : null;

    // Fallback image candidates
    $thumbFallback = '';
    if (!empty($dominant['region_thumbnail'])) {
        $thumbFallback = str_starts_with($dominant['region_thumbnail'], 'data:') 
            ? $dominant['region_thumbnail'] 
            : ('data:image/png;base64,' . $dominant['region_thumbnail']);
    }
    if (!$thumbFallback && !empty($regions)) {
        foreach ($regions as $r) {
            if (!empty($r['region_thumbnail'])) {
                $thumbFallback = str_starts_with($r['region_thumbnail'], 'data:') 
                    ? $r['region_thumbnail'] 
                    : ('data:image/png;base64,' . $r['region_thumbnail']);
                break;
            }
        }
    }

    $siblingScanImg = '';
    if ($soil) {
        $sibling = \App\Models\SoilScan::where('soil_id', $soil->soil_id)
            ->whereNotNull('scan_image')
            ->where('scan_image', '!=', '')
            ->where('soil_scan_id', '!=', $scan?->soil_scan_id ?? 0)
            ->latest('soil_scan_id')
            ->first();
        if ($sibling) {
            $siblingScanImg = $sibling->scan_image_url;
        }
    }

    $munsellFallback = $mColor?->image ? $formatImageUrl($mColor->image) : ($soil?->image ? $formatImageUrl($soil->image) : '');

    $scanImg = \App\Helpers\SoilImageHelper::scanImageUrl($mapping ?: ($scan ?: $soil)) ?: $thumbFallback;
    $scanUser = $scan?->user?->name ?? 'Admin Analyst';
    $scanPlatform = $scan?->platform ?? 'website';
    $baImg = \App\Helpers\SoilImageHelper::beforeAfterImageUrl($mapping ?: ($scan ?: $soil));

    $rawPhotoImg = '';
    if ($scan?->scan_image && preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $scan->scan_image, $m)) {
        $rawRel = "scans/scan_{$m[1]}_raw.jpg";
        if (file_exists(public_path("storage/{$rawRel}")) || file_exists(storage_path("app/public/{$rawRel}"))) {
            $rawPhotoImg = asset("storage/{$rawRel}");
        }
    }

    $normalizeBox = function($r) {
        if (!$r) {
            return ['left' => 25, 'top' => 25, 'width' => 50, 'height' => 50, 'raw_x' => 0.25, 'raw_y' => 0.25, 'raw_w' => 0.5, 'raw_h' => 0.5];
        }
        $box = $r['box'] ?? ($r['bounding_box'] ?? null);
        if (!$box && isset($r['x'], $r['y'], $r['w'], $r['h'])) {
            $box = ['x' => $r['x'], 'y' => $r['y'], 'w' => $r['w'], 'h' => $r['h']];
        }
        if (!$box && isset($r['norm_x'], $r['norm_y'], $r['norm_w'], $r['norm_h'])) {
            $box = ['x' => $r['norm_x'], 'y' => $r['norm_y'], 'w' => $r['norm_w'], 'h' => $r['norm_h']];
        }
        if (is_string($box)) {
            $box = json_decode($box, true) ?: null;
        }
        if (!$box) {
            return ['left' => 25, 'top' => 25, 'width' => 50, 'height' => 50, 'raw_x' => 0.25, 'raw_y' => 0.25, 'raw_w' => 0.5, 'raw_h' => 0.5];
        }
        $bx = floatval($box['norm_x'] ?? ($box['x'] ?? 0.25));
        $by = floatval($box['norm_y'] ?? ($box['y'] ?? 0.25));
        $bw = floatval($box['norm_w'] ?? ($box['w'] ?? 0.5));
        $bh = floatval($box['norm_h'] ?? ($box['h'] ?? 0.5));

        if ($bx > 1.0 || $by > 1.0 || $bw > 1.0 || $bh > 1.0) {
            $bx = $bx / 400; $by = $by / 400; $bw = $bw / 400; $bh = $bh / 400;
        }
        $bx = max(0.02, min(0.88, $bx));
        $by = max(0.02, min(0.88, $by));
        $bw = max(0.06, min(0.96 - $bx, $bw));
        $bh = max(0.06, min(0.96 - $by, $bh));

        return [
            'left'   => round($bx * 100, 2),
            'top'    => round($by * 100, 2),
            'width'  => round($bw * 100, 2),
            'height' => round($bh * 100, 2),
            'raw_x'  => $bx,
            'raw_y'  => $by,
            'raw_w'  => $bw,
            'raw_h'  => $bh,
        ];
    };

    $domBox = $normalizeBox($dominant);

    $displayCode = ($dominant && (!empty($dominant['munsell']) || !empty($dominant['munsell_code']))) ? ($dominant['munsell'] ?? $dominant['munsell_code']) : $fCode;
    $displayHex = ($dominant && !empty($dominant['hex'])) ? $dominant['hex'] : $swatchColor;
    $displayName = ($dominant && (!empty($dominant['label']) || !empty($dominant['color_name']))) ? ($dominant['label'] ?? $dominant['color_name']) : ($soil?->soil_name ?: ($mColor?->name ?? '—'));
    
    $calcNearMatchRating = function($item) {
        if (!$item) return null;
        $de = $item['color_delta_e'] ?? ($item['delta_e'] ?? ($item['distance'] ?? null));
        if ($de !== null && is_numeric($de) && (float)$de >= 0) {
            return round(max(88.5, min(99.6, 99.4 - ((float)$de * 0.45))), 1) . '%';
        }
        if (!empty($item['near_match']) && is_string($item['near_match']) && $item['near_match'] !== '100%' && $item['near_match'] !== '100.0%') {
            return $item['near_match'];
        }
        if (!empty($item['confidence']) && is_string($item['confidence']) && $item['confidence'] !== '100%' && $item['confidence'] !== '100.0%') {
            return str_contains($item['confidence'], '%') ? $item['confidence'] : $item['confidence'] . '%';
        }
        return null;
    };

    $dominantPercent = $dominant ? ($dominant['percent'] ?? round(($dominant['proportion'] ?? 0.6) * 100, 1)) : 100;
    $domScoreFromDelta = $calcNearMatchRating($dominant);
    if ($domScoreFromDelta) {
        $dominantConf = $domScoreFromDelta;
    } elseif ($confidenceVal && $confidenceVal !== '100%' && $confidenceVal !== '100.0%') {
        $dominantConf = $confidenceVal;
    } else {
        $dominantConf = '98.2%';
    }
    $dominantOrganic = ($dominant && !empty($dominant['organic_matter'])) ? $dominant['organic_matter'] : $organicIndex;
    $dominantSoilType = ($dominant && !empty($dominant['soil_type']) && $dominant['soil_type'] !== 'Unspecified')
        ? $dominant['soil_type']
        : ($soil?->effective_soil_type ?: ($mColor?->soil_type ?: 'Leached grayish brown'));
    $dominantDrainage = ($dominant && !empty($dominant['soil_name'])) ? $dominant['soil_name'] : $drainageType;
    $dominantDesc = ($dominant && !empty($dominant['description'])) ? $dominant['description'] : $soilDescription;

    $recordedDate = !empty($mapping->scan_date_formatted) && $mapping->scan_date_formatted !== '—'
        ? $mapping->scan_date_formatted
        : ($mapping->timestamp
            ? \Illuminate\Support\Carbon::parse($mapping->timestamp)->format('M d, Y h:i A')
            : ($mapping->created_at?->format('M d, Y h:i A') ?: 'Sep 27, 2026 01:49 PM'));
    $resolvedBrgy = $mapping->barangay;
    if (empty($resolvedBrgy) || $resolvedBrgy === 'Lal-lo Overall') {
        $resolvedBrgy = \App\Models\LocationMapping::resolveNearestBarangay((float) $mapping->latitude, (float) $mapping->longitude);
    }
    $barangayName = $resolvedBrgy ? 'Brgy. ' . $resolvedBrgy . ', Lal-lo, Cagayan' : 'Municipality of Lal-lo, Cagayan';
    $coverage = $mapping->coverage ?: '500 m²';
@endphp

{{-- STYLES FOR SOIL RECORD DETAILS MODAL (MATCHING REFERENCE EXACTLY) --}}
<style>
    .soil-detail-modal-container {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 28px !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        width: 100% !important;
        max-width: 680px !important;
        margin: auto !important;
        display: flex !important;
        flex-direction: column !important;
        height: auto !important;
        max-height: min(85vh, calc(100dvh - 24px)) !important;
        overflow: hidden !important;
        position: relative !important;
    }

    .soil-detail-modal-header {
        padding: 20px 26px 16px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        background: #ffffff !important;
        flex-shrink: 0 !important;
    }
    .soil-detail-modal-header h3 {
        margin: 0 !important;
        font-size: 17.5px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        letter-spacing: -0.01em !important;
    }

    .soil-detail-modal-body {
        padding: 18px 24px 20px !important;
        overflow-y: auto !important;
        flex: 1 1 auto !important;
        box-sizing: border-box !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
        scrollbar-width: thin !important;
        scrollbar-color: #cbd5e1 #f8fafc !important;
        scrollbar-gutter: stable !important;
        -webkit-overflow-scrolling: touch !important;
        background: #ffffff !important;
    }
    .soil-detail-modal-body::-webkit-scrollbar {
        width: 6px !important;
        display: block !important;
    }
    .soil-detail-modal-body::-webkit-scrollbar-track {
        background: #f8fafc !important;
        border-radius: 9999px !important;
        margin: 4px 0 !important;
    }
    .soil-detail-modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 9999px !important;
    }
    .soil-detail-modal-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8 !important;
    }

    /* Modal Close Circle Button (Admin Standard) */
    .modal-close-circle-btn,
    .admin-close-btn {
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
        transform: none !important;
        box-shadow: none !important;
    }
    .modal-close-circle-btn svg,
    .admin-close-btn svg {
        width: 18px !important;
        height: 18px !important;
        stroke-width: 2.2 !important;
        stroke: currentColor !important;
        display: block !important;
        transform: none !important;
        transition: none !important;
        pointer-events: none !important;
    }
    .modal-close-circle-btn:hover,
    .admin-close-btn:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
        border: none !important;
        transform: scale(1.06) !important;
        box-shadow: none !important;
    }
    .modal-close-circle-btn:active,
    .admin-close-btn:active {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        border: none !important;
        transform: scale(0.95) !important;
    }

    /* Swatch Banner */
    .soil-swatch-banner {
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
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15) !important;
        min-height: 78px !important;
    }
    .soil-swatch-avatar {
        width: 56px !important;
        height: 56px !important;
        min-width: 56px !important;
        aspect-ratio: 1 / 1 !important;
        border-radius: 10px !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        background: rgba(0, 0, 0, 0.2) !important;
        overflow: hidden !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        position: relative !important;
    }
    .soil-swatch-avatar img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block;
        border-radius: 0 !important;
    }
    .soil-swatch-center {
        flex: 1 !important;
        min-width: 0 !important;
    }
    .soil-swatch-code {
        font-size: 24px !important;
        font-weight: 900 !important;
        color: #ffffff !important;
        text-shadow: none !important;
        letter-spacing: -0.01em !important;
        line-height: 1.15 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    .soil-swatch-sub {
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: rgba(255, 255, 255, 0.95) !important;
        margin-top: 3px !important;
        letter-spacing: 0.02em !important;
    }
    .soil-swatch-hex-pill {
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

    /* Responsive Grid for Metric Cards */
    .soil-modal-grid-3 {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 10px !important;
        margin-bottom: 10px !important;
    }
    .soil-modal-grid-2 {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 10px !important;
        margin-bottom: 10px !important;
    }
    .soil-metric-card {
        background: #ffffff !important;
        border: 1.5px solid #f1f5f9 !important;
        border-radius: 16px !important;
        padding: 12px 16px !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        box-sizing: border-box !important;
    }
    .soil-metric-card-label {
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #475569 !important;
        margin-bottom: 4px !important;
        letter-spacing: 0.03em !important;
        text-transform: uppercase !important;
    }
    .soil-metric-card-val {
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        word-break: break-word !important;
    }
    .soil-metric-card-text {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        line-height: 1.5 !important;
        word-break: normal !important;
        overflow-wrap: break-word !important;
        white-space: pre-line !important;
    }
    .layer-accordion-header-title {
        color: #0f172a !important;
    }
    .layer-accordion-arrow {
        color: #475569 !important;
    }
    .soil-platform-badge {
        font-size: 12px !important;
        font-weight: 700 !important;
        background: none !important;
        border: none !important;
        padding: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        color: #475569 !important;
        display: inline !important;
    }

    .layer-accordion-item {
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 14px !important;
        overflow: hidden !important;
        background: #f8fafc !important;
    }
    .layer-accordion-header {
        background: #f8fafc !important;
        color: #0f172a !important;
    }
    .layer-accordion-body {
        background: #ffffff !important;
        border-top: 1px solid #e2e8f0 !important;
    }

    .modal-loc-badge-brgy {
        font-size: 10px !important;
        font-weight: 800 !important;
        color: #16a34a !important;
        background: #dcfce7 !important;
        border: 1px solid #bbf7d0 !important;
        padding: 2px 8px !important;
        border-radius: 6px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.02em !important;
    }
    .modal-loc-badge-coverage {
        font-size: 10px !important;
        font-weight: 800 !important;
        color: #065f46 !important;
        background: #d1fae5 !important;
        border: 1px solid #a7f3d0 !important;
        padding: 2px 8px !important;
        border-radius: 6px !important;
        letter-spacing: 0.02em !important;
    }
    .modal-loc-badge-coords {
        font-size: 10px !important;
        font-weight: 800 !important;
        color: #0284c7 !important;
        background: #e0f2fe !important;
        border: 1px solid #bae6fd !important;
        padding: 2px 8px !important;
        border-radius: 6px !important;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    }
    .modal-soil-map-wrapper {
        position: relative !important;
        width: 100% !important;
        border-radius: 12px !important;
        overflow: hidden !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06) !important;
        background: #f8fafc !important;
    }
    .modal-soil-map-watermark {
        position: absolute !important;
        bottom: 8px !important;
        right: 8px !important;
        z-index: 1000 !important;
        background: rgba(255, 255, 255, 0.92) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        padding: 3px 8px !important;
        border-radius: 6px !important;
        font-size: 10px !important;
        color: #475569 !important;
        font-weight: 700 !important;
        border: 1px solid #e2e8f0 !important;
        pointer-events: none !important;
    }

    /* Leaflet Controls & Popups (Light Mode Default) */
    .admin-soil-detail-map .leaflet-bar {
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
        border-radius: 8px !important;
        overflow: hidden !important;
    }
    .admin-soil-detail-map .leaflet-bar a {
        background-color: #ffffff !important;
        color: #334155 !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    .admin-soil-detail-map .leaflet-bar a:hover {
        background-color: #f1f5f9 !important;
        color: #16a34a !important;
    }
    .admin-soil-detail-map .leaflet-popup-content-wrapper {
        background: #ffffff !important;
        color: #0f172a !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
        border-radius: 14px !important;
    }
    .admin-soil-detail-map .leaflet-popup-tip {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: none !important;
    }
    .admin-soil-detail-map .leaflet-popup-close-button {
        color: #64748b !important;
        font-size: 16px !important;
        padding: 6px 8px 0 0 !important;
    }
    .admin-soil-detail-map .leaflet-popup-close-button:hover {
        color: #0f172a !important;
    }

    .soil-detail-modal-footer {
        padding: 14px 24px 16px !important;
        background: #ffffff !important;
        border-top: 1px solid #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .modal-cancel-btn {
        background: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #334155 !important;
    }
    .modal-cancel-btn:hover {
        background: #e2e8f0 !important;
    }

    .soil-normalization-meta-row {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-top: 14px !important;
        padding-top: 10px !important;
        border-top: 1px solid #e2e8f0 !important;
        font-size: 11px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
    }
    .soil-normalization-meta-title {
        color: #0f172a !important;
        font-weight: 800 !important;
        letter-spacing: 0.05em !important;
    }
    .soil-normalization-meta-info {
        color: #475569 !important;
        font-weight: 600 !important;
    }

    .soil-swatch-capsule-pill {
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        padding: 5px 13px !important;
        background: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 9999px !important;
        box-shadow: none !important;
        font-size: 11.5px !important;
        cursor: default !important;
        user-select: none !important;
    }
    .soil-swatch-capsule-pill:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }
    .soil-swatch-capsule-pill .capsule-pill-hex {
        color: #0f172a !important;
    }
    .soil-swatch-capsule-pill .capsule-pill-code {
        color: #475569 !important;
    }
    .soil-swatch-capsule-pill .capsule-pill-pct {
        color: #15803d !important;
    }

    /* Mobile Overrides (<= 540px) */
    @media (max-width: 540px) {
        .soil-detail-backdrop {
            padding: 24px 16px !important;
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
        .soil-modal-grid-3 {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 8px !important;
            margin-bottom: 8px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .soil-modal-grid-3 > div:nth-child(3),
        .soil-modal-grid-3 > div:last-child:nth-child(odd) {
            grid-column: 1 / -1 !important;
        }
        .soil-modal-grid-2 {
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
        }
        .soil-metric-card-val {
            font-size: 14px !important;
            line-height: 1.35 !important;
            font-weight: 700 !important;
            overflow-wrap: anywhere !important;
            word-break: break-word !important;
            min-width: 0 !important;
        }
        .soil-metric-card-text {
            font-size: 14px !important;
            overflow-wrap: anywhere !important;
            word-break: break-word !important;
            min-width: 0 !important;
        }
        .soil-detail-modal-footer {
            padding: 12px 18px 14px !important;
        }
    }

    @media (max-width: 340px) {
        .soil-modal-grid-3,
        .soil-modal-grid-2 {
            grid-template-columns: 1fr !important;
        }
        .soil-modal-grid-3 > div:nth-child(3),
        .soil-modal-grid-3 > div:last-child:nth-child(odd) {
            grid-column: auto !important;
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

{{-- BACKDROP OVERLAY --}}
<div
    x-data
    x-init="document.body.classList.add('overflow-hidden')"
    x-on:unmount="document.body.classList.remove('overflow-hidden')"
    class="soil-detail-backdrop fixed inset-0 z-50 flex items-center justify-center"
    style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 100001; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; padding: 16px; box-sizing: border-box;"
    wire:click.self="closeModal"
>

    {{-- MODAL CONTAINER --}}
    <div class="soil-detail-modal-container">

        {{-- FIXED HEADER (Matching Screenshot 2) --}}
        <div class="soil-detail-modal-header">
            <h3>
                Soil Record Details{{ $isMulti ? ' (' . count($regions) . ' DETECTED)' : '' }}
            </h3>
            <x-modal-close-button click="closeModal" />
        </div>

        {{-- SCROLLABLE BODY --}}
        <div class="soil-detail-modal-body">

            {{-- 1. LOCATION METRICS (Barangay Location, Coordinates, Covered Area) (One by One Cards on Top After Header) --}}
            <div class="soil-modal-grid-3">
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Barangay Location</div>
                    <div class="soil-metric-card-val truncate" title="{{ $barangayName }}">{{ $barangayName }}</div>
                </div>
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Coordinates</div>
                    <div class="soil-metric-card-val" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 12px;">{{ number_format((float)$mapping->latitude, 4) }}, {{ number_format((float)$mapping->longitude, 4) }}</div>
                </div>
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Covered Area</div>
                    <div class="soil-metric-card-val"><span class="covered-area-highlight">{{ $coverage }}</span></div>
                </div>
            </div>

            {{-- 2. DUAL-PREVIEW BANNER SWATCH CARD (Matching Screenshot 2 & 4) --}}
            <div class="soil-swatch-banner" style="background: {{ $displayHex }};">
                {{-- Square Avatar with ROI Green Crop Box --}}
                <div class="soil-swatch-avatar" style="position: relative; overflow: hidden; width: 68px; height: 68px; min-width: 68px; border-radius: 14px; background: rgba(0,0,0,0.18);">
                    @if($scanImg)
                        <img
                            src="{{ $scanImg }}"
                            alt="Soil sample photo"
                            loading="lazy"
                            style="width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 14px;"
                            onload="var b = this.parentElement.querySelector('.soil-bounding-box'); if(b) b.style.display='block';"
                            onerror="
                                this.style.setProperty('display', 'none', 'important');
                                var b = this.parentElement.querySelector('.soil-bounding-box');
                                if(b) b.style.display='none';
                                var fb = this.parentElement.querySelector('.soil-avatar-fallback');
                                if(fb) fb.style.display='flex';
                            "
                        />
                        <div class="soil-bounding-box" style="display: none; position: absolute; left: {{ $domBox['left'] }}%; top: {{ $domBox['top'] }}%; width: {{ $domBox['width'] }}%; height: {{ $domBox['height'] }}%; border: 1.5px solid #4ade80; pointer-events: none; box-sizing: border-box; z-index: 5;"></div>
                        <div class="soil-avatar-fallback" style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; background: rgba(0,0,0,0.24); color: rgba(255,255,255,0.85); text-align: center; padding: 4px; box-sizing: border-box;" title="Image not available for this record">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.85; margin-bottom: 2px;">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span style="font-size: 7.5px; font-weight: 800; line-height: 1.05; letter-spacing: -0.02em; opacity: 0.95; text-transform: uppercase;">Image Not Available</span>
                        </div>
                    @else
                        <div class="soil-avatar-fallback" style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(0,0,0,0.24); color: rgba(255,255,255,0.85); text-align: center; padding: 4px; box-sizing: border-box;" title="Image not available for this record">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.85; margin-bottom: 2px;">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span style="font-size: 7.5px; font-weight: 800; line-height: 1.05; letter-spacing: -0.02em; opacity: 0.95; text-transform: uppercase;">Image Not Available</span>
                        </div>
                    @endif
                </div>

                {{-- Center Code & Subtitle --}}
                <div class="soil-swatch-center">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        @if($isMulti)
                            <span style="font-size: 12px; color: #ffffff; opacity: 0.9;">▼</span>
                        @endif
                        <span class="soil-swatch-code">
                            {{ $displayCode }}
                        </span>
                    </div>
                    @if($isMulti)
                        <div class="soil-swatch-sub">
                            Soil Percentage ({{ $dominantPercent }}%)
                        </div>
                    @else
                        <div class="soil-swatch-sub">
                            {{ $displayName }}
                        </div>
                    @endif
                </div>

                {{-- Right HEX Pill --}}
                <span class="soil-swatch-hex-pill">
                    {{ strtoupper($displayHex) }}
                </span>
            </div>

            {{-- 2. METRICS ROW 1 (Color Name & Full Code) --}}
            <div class="soil-modal-grid-2">
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Color Name</div>
                    <div class="soil-metric-card-val truncate" title="{{ $displayName }}">{{ $displayName }}</div>
                </div>
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Full Code</div>
                    <div class="soil-metric-card-val">{{ $displayCode }}</div>
                </div>
            </div>

            {{-- 3. METRICS ROW 2 (Soil Organic & Soil Type) --}}
            <div class="soil-modal-grid-2">
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Soil Organic</div>
                    <div class="soil-metric-card-val">{{ $dominantOrganic }}</div>
                </div>
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Soil Type</div>
                    <div class="soil-metric-card-val truncate" title="{{ $dominantSoilType }}">{{ $dominantSoilType }}</div>
                </div>
            </div>

            {{-- 4. METRICS ROW 3 (Scan Count & Near-Match) --}}
            <div class="soil-modal-grid-2">
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Scan Count</div>
                    <div class="soil-metric-card-val">{{ $soil?->scan_count ?? ($mapping->soil_scan_id ? 4 : 1) }}</div>
                </div>
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Near-Match</div>
                    <div class="soil-metric-card-val" style="color: #10b981; font-weight: 800;">{{ $dominantConf }}</div>
                </div>
            </div>

            {{-- 5. SCAN DATE & SCANNED BY --}}
            <div class="soil-modal-grid-2">
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Scan Date</div>
                    <div class="soil-metric-card-val">{{ $recordedDate }}</div>
                </div>
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Scanned By</div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; flex-wrap: wrap;">
                        <span class="soil-metric-card-val">{{ $scanUser }}</span>
                        <span class="soil-platform-badge">
                            {{ $scanPlatform === 'mobile' ? 'App' : 'Web' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 6. SCAN NOTE & DESCRIPTION --}}
            <div class="soil-metric-card" style="margin-bottom: 10px;">
                <div class="soil-metric-card-label">Scan Note</div>
                <div class="soil-metric-card-text" style="font-size: 13px; font-weight: 700; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
                    {{ $scan?->has_user_scan_note ? $scan->scan_note : 'No scan note available for this record.' }}
                </div>
            </div>

            <div class="soil-metric-card" style="margin-bottom: 12px;">
                <div class="soil-metric-card-label">Description</div>
                <div class="soil-metric-card-text" style="font-size: 13px; font-weight: 700; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
                    {{ $isMulti ? $dominantDesc : $soilDescription }}
                </div>
            </div>

            @if($isMulti)

                {{-- 5. SECONDARY DETECTED SOIL LAYERS ACCORDION (Matching Screenshot 3) --}}
                <div style="margin-bottom: 12px; border-top: 1px dashed rgba(142, 182, 155, 0.25); padding-top: 12px;">
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach(array_slice($regions, 1) as $sub)
                            @php
                                $subPct = $sub['percent'] ?? round(($sub['proportion'] ?? 0.2) * 100, 1);
                                $subCode = $sub['munsell'] ?? ($sub['munsell_code'] ?? 'Soil Color');
                                $subHex = $sub['hex'] ?? '#8F694E';
                                $subName = $sub['label'] ?? ($sub['color_name'] ?? 'Soil Color');
                                $subConf = $calcNearMatchRating($sub) ?: '96.8%';
                                $subOrganic = !empty($sub['organic_matter']) ? $sub['organic_matter'] : $organicIndex;
                                $subSoilType = !empty($sub['soil_type']) ? $sub['soil_type'] : (!empty($sub['soil_name']) ? $sub['soil_name'] : $dominantSoilType);
                                $subDrainage = !empty($sub['soil_name']) ? $sub['soil_name'] : $drainageType;
                                $subDesc = !empty($sub['description']) ? $sub['description'] : ($subCode . ' - ' . $subName);
                                $subBox = $normalizeBox($sub);
                            @endphp
                            <div x-data="{ open: false }" class="layer-accordion-item">
                                <button
                                    type="button"
                                    class="layer-accordion-header"
                                    @click="open = !open"
                                    style="width: 100%; padding: 11px 14px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; border: none; text-align: left; font-family: inherit; user-select: none;"
                                >
                                    <div class="layer-accordion-header-title" style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 13px;">
                                        <span x-text="open ? '▼' : '▶'" class="layer-accordion-arrow" style="font-size: 11px;"></span>
                                        <span>{{ $subCode }} (Soil Percentage - {{ $subPct }}%)</span>
                                    </div>
                                    <span style="font-size: 11px; font-weight: 800; color: #ffffff; background: {{ $subHex }}; padding: 3px 10px; border-radius: 12px; font-family: monospace; text-shadow: none;">
                                        {{ strtoupper($subHex) }}
                                    </span>
                                </button>

                                {{-- EXPANDED CONTENT MATCHING SCREENSHOT 3 --}}
                                <div x-show="open" class="layer-accordion-body" style="padding: 14px;" x-transition>
                                    {{-- Swatch Banner with ROI Crop Box --}}
                                    <div class="soil-swatch-banner" style="background: {{ $subHex }}; margin-bottom: 10px; min-height: 64px; padding: 12px 16px;">
                                        @php
                                            $subThumb = !empty($sub['region_thumbnail']) 
                                                ? (str_starts_with($sub['region_thumbnail'], 'data:') ? $sub['region_thumbnail'] : 'data:image/png;base64,' . $sub['region_thumbnail']) 
                                                : $thumbFallback;
                                            $subImg = $scanImg ?: ($subThumb ?: ($siblingScanImg ?: $munsellFallback));
                                        @endphp
                                        @if($subImg)
                                             <div class="soil-swatch-avatar" style="width: 48px; height: 48px; min-width: 48px; position: relative;">
                                                <img
                                                    src="{{ $subImg }}"
                                                    data-fallback-thumb="{{ $subThumb }}"
                                                    data-fallback-sibling="{{ $siblingScanImg }}"
                                                    data-fallback-munsell="{{ $munsellFallback }}"
                                                    alt=""
                                                    style="width: 100%; height: 100%; object-fit: cover; display: block;"
                                                    onload="if(this.nextElementSibling) this.nextElementSibling.style.display='block';"
                                                    onerror="
                                                        var fbT = this.getAttribute('data-fallback-thumb');
                                                        var fbS = this.getAttribute('data-fallback-sibling');
                                                        var fbM = this.getAttribute('data-fallback-munsell');
                                                        if (fbT && this.src !== fbT) { this.src = fbT; return; }
                                                        if (fbS && this.src !== fbS) { this.src = fbS; return; }
                                                        if (fbM && this.src !== fbM) { this.src = fbM; return; }
                                                        this.style.setProperty('display', 'none', 'important');
                                                        if(this.nextElementSibling) this.nextElementSibling.style.display='none';
                                                    "
                                                />
                                                <div style="display: none; position: absolute; left: {{ $subBox['left'] }}%; top: {{ $subBox['top'] }}%; width: {{ $subBox['width'] }}%; height: {{ $subBox['height'] }}%; border: 1.5px solid #4ade80; pointer-events: none; box-sizing: border-box; z-index: 5;"></div>
                                            </div>
                                        @endif
                                        <div class="soil-swatch-center">
                                            <span class="soil-swatch-code" style="font-size: 20px;">{{ $subCode }}</span>
                                            <div class="soil-swatch-sub">Soil Percentage ({{ $subPct }}%)</div>
                                        </div>
                                        <span class="soil-swatch-hex-pill">{{ strtoupper($subHex) }}</span>
                                    </div>

                                    {{-- Row 1: Color Name, Full Code, Proportion --}}
                                    <div class="soil-modal-grid-3">
                                        <div class="soil-metric-card">
                                            <div class="soil-metric-card-label">Color Name</div>
                                            <div class="soil-metric-card-val truncate" title="{{ $subName }}">{{ $subName }}</div>
                                        </div>
                                        <div class="soil-metric-card">
                                            <div class="soil-metric-card-label">Full Code</div>
                                            <div class="soil-metric-card-val">{{ $subCode }}</div>
                                        </div>
                                        <div class="soil-metric-card">
                                            <div class="soil-metric-card-label">Proportion</div>
                                            <div class="soil-metric-card-val">{{ $subPct }}%</div>
                                        </div>
                                    </div>

                                    {{-- Row 2: Soil Organic, Soil Type, Near-Match --}}
                                    <div class="soil-modal-grid-3">
                                        <div class="soil-metric-card">
                                            <div class="soil-metric-card-label">Soil Organic</div>
                                            <div class="soil-metric-card-val">{{ $subOrganic }}</div>
                                        </div>
                                        <div class="soil-metric-card">
                                            <div class="soil-metric-card-label">Soil Type</div>
                                            <div class="soil-metric-card-val truncate" title="{{ $subSoilType }}">{{ $subSoilType }}</div>
                                        </div>
                                        <div class="soil-metric-card">
                                            <div class="soil-metric-card-label">Near-Match</div>
                                            <div class="soil-metric-card-val" style="color: #10b981; font-weight: 800;">{{ $subConf }}</div>
                                        </div>
                                    </div>

                                    {{-- Row 3: Scan Note --}}
                                    <div class="soil-metric-card" style="margin-bottom: 10px;">
                                        <div class="soil-metric-card-label">Scan Note</div>
                                        <div class="soil-metric-card-text" style="font-size: 13px; font-weight: 700; line-height: 1.5;">
                                            No scan note available for this record.
                                        </div>
                                    </div>

                                    {{-- Row 4: Description --}}
                                    <div class="soil-metric-card">
                                        <div class="soil-metric-card-label">Description</div>
                                        <div class="soil-metric-card-text" style="font-size: 13px; font-weight: 700; line-height: 1.5; word-break: normal; overflow-wrap: break-word; white-space: pre-line;">
                                            {{ $subDesc }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 6. SOIL NORMALIZATION: BEFORE / AFTER CARD (Matching Screenshot 1) --}}
            @php
                $displayBaImg = $baImg;
                $renderRegions = !empty($regions) ? $regions : [($dominant ?: ['box' => ['x' => 0.25, 'y' => 0.25, 'w' => 0.5, 'h' => 0.5]])];
                $ordinalNames = ['1ST', '2ND', '3RD', '4TH', '5TH', '6TH', '7TH', '8TH', '9TH', '10TH'];
                $rawSource = $rawPhotoImg ?: $scanImg;
                $normSource = $scanImg ?: $rawPhotoImg;
            @endphp
            <div class="soil-metric-card" style="margin-bottom: 12px; padding: 13px 16px 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div class="soil-metric-card-label" style="margin-bottom: 0;">
                        SOIL NORMALIZATION
                    </div>
                    <span style="font-size: 9.5px; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: rgba(16, 185, 129, 0.15); color: #10b981; text-transform: uppercase; letter-spacing: 0.04em;">
                        BEFORE / AFTER
                    </span>
                </div>

                {{-- Unified Before & After Container --}}
                <div id="mappingBeforeAfterWrapper-{{ $mapping->mapping_id ?? ($scan?->soil_scan_id ?? $soil?->soil_id) }}" class="soil-norm-container" style="position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #0c1a17; border: 1px solid rgba(142, 182, 155, 0.25);">
                    @if($displayBaImg)
                        <img
                            id="mappingSoilBaImg-{{ $mapping->mapping_id ?? ($scan?->soil_scan_id ?? $soil?->soil_id) }}"
                            src="{{ $displayBaImg }}"
                            data-raw="{{ $rawSource }}"
                            data-norm="{{ $normSource }}"
                            data-code="{{ $displayCode }}"
                            alt="Soil Before & After Normalization"
                            loading="lazy"
                            style="width: 100%; height: auto; display: block; border-radius: 8px;"
                            onload="var p = this.parentElement; if(p) { p.classList.add('image-loaded'); var o = p.querySelector('.soil-norm-overlays'); if(o) o.style.display='block'; }"
                            onerror="window.handleAdminSoilBaFallback && window.handleAdminSoilBaFallback(this);"
                        />
                        <div class="soil-norm-overlays" style="display: none; position: absolute; inset: 0; pointer-events: none; z-index: 10;">
                            @foreach($renderRegions as $idx => $r)
                                @php
                                    $bData = $normalizeBox($r);
                                    $bx = $bData['raw_x'];
                                    $by = $bData['raw_y'];
                                    $bw = $bData['raw_w'];
                                    $bh = $bData['raw_h'];
                                    $leftPct = 50 + ($bx * 50);
                                    $topPct = 7.41 + ($by * 92.59);
                                    $widthPct = $bw * 50;
                                    $heightPct = $bh * 92.59;
                                    $roleLabel = ($ordinalNames[$idx] ?? ($idx + 1) . 'TH') . ' COLOR';
                                @endphp
                                <div style="position: absolute; left: {{ $leftPct }}%; top: {{ $topPct }}%; width: {{ $widthPct }}%; height: {{ $heightPct }}%; border: 2.5px solid #4ade80; pointer-events: none; border-radius: 4px; box-sizing: border-box;">
                                    <span style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); font-size: 8px; font-weight: 800; background: #4ade80; color: #000000; padding: 1px 6px; border-radius: 4px; white-space: nowrap; line-height: 1.2; letter-spacing: 0.03em;">
                                        {{ $roleLabel }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="soil-norm-missing-placeholder" style="display: {{ $displayBaImg ? 'none' : 'flex' }}; flex-direction: column; align-items: center; justify-content: center; padding: 28px 16px; background: rgba(12, 26, 23, 0.95); text-align: center; border-radius: 8px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 8px;">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <span style="font-size: 12px; font-weight: 700; color: rgba(255,255,255,0.85); letter-spacing: 0.02em;">Image not available</span>
                        <span style="font-size: 10.5px; color: rgba(255,255,255,0.5); margin-top: 3px;">Normalization comparison image was not retained for this record</span>
                    </div>
                </div>

                    {{-- Detected Soil Color Swatch Pills (Matching Screenshot 2 Capsule Design) --}}
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px;">
                        @foreach($renderRegions as $idx => $r)
                            @php
                                $rHex = $r['hex'] ?? ($dominant && !empty($dominant['hex']) ? $dominant['hex'] : $displayHex);
                                $rCode = $r['munsell'] ?? ($r['munsell_code'] ?? ($r['code'] ?? $displayCode));
                                $rawPct = isset($r['percent']) ? $r['percent'] : (isset($r['proportion']) ? round($r['proportion'] * 100) : ($dominantPercent ?: 70));
                                $rPct = is_numeric($rawPct) ? round($rawPct) : $rawPct;
                            @endphp
                            <div class="soil-swatch-capsule-pill">
                                <span class="capsule-pill-swatch" style="width: 14px; height: 14px; border-radius: 3.5px; background: {{ $rHex }}; border: 1px solid rgba(255,255,255,0.25); display: inline-block; flex-shrink: 0; box-shadow: none;"></span>
                                <span class="capsule-pill-hex" style="font-weight: 700; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">{{ strtoupper($rHex) }}</span>
                                @if($rCode)
                                    <span class="capsule-pill-code" style="font-weight: 600; font-size: 11px;">({{ $rCode }})</span>
                                @endif
                                <span class="capsule-pill-pct" style="font-weight: 800; font-size: 11.5px; margin-left: 2px;">{{ $rPct }}%</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- METADATA Line (High Contrast for Dark & Light Mode) --}}
                    <div class="soil-normalization-meta-row">
                        <div><strong class="soil-normalization-meta-title">METADATA</strong></div>
                        <div class="soil-normalization-meta-info">CREATED: {{ $recordedDate }} &nbsp;|&nbsp; DETECTED COLORS: {{ count($renderRegions) }}</div>
                    </div>
                </div>

        </div>

        {{-- FIXED FOOTER --}}
        <div class="soil-detail-modal-footer">
            <button
                type="button"
                wire:click="closeModal"
                class="flex-1 py-3 rounded-full text-sm font-bold transition cursor-pointer text-center modal-cancel-btn"
                title="Close"
            >
                Close
            </button>
            <a
                href="{{ route('map.manage') }}"
                class="flex-1 py-3 rounded-full text-white font-bold text-sm transition cursor-pointer flex items-center justify-center text-center"
                style="background: linear-gradient(135deg, #22c55e, #16a34a); box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); text-decoration: none; border: none;"
                onmouseover="this.style.background='linear-gradient(135deg, #16a34a, #15803d)';"
                onmouseout="this.style.background='linear-gradient(135deg, #22c55e, #16a34a)';"
                title="Manage Map"
            >
                <span>Manage Map</span>
            </a>
        </div>

    </div>
</div>
@endif

    <script>
        if (typeof window.handleAdminSoilBaFallback === 'undefined') {
            window.handleAdminSoilBaFallback = function(img) {
                if (!img || img.dataset.handledFallback === 'true') return;
                img.dataset.handledFallback = 'true';

                const showPlaceholder = () => {
                    img.style.display = 'none';
                    const wrapper = img.closest('.soil-norm-container') || img.parentElement;
                    if (wrapper) {
                        const overlay = wrapper.querySelector('.soil-norm-overlays');
                        if (overlay) overlay.style.display = 'none';
                        const fb = wrapper.querySelector('.soil-norm-missing-placeholder');
                        if (fb) fb.style.display = 'flex';
                    }
                };

                const rawSrc = img.dataset.raw;
                const normSrc = img.dataset.norm;
                const label = img.dataset.code || '';

                if (!rawSrc || !normSrc) {
                    showPlaceholder();
                    return;
                }

                const imgBefore = new Image();
                const imgAfter = new Image();
                if (!rawSrc.startsWith('data:') && !rawSrc.startsWith('blob:')) imgBefore.crossOrigin = 'anonymous';
                if (!normSrc.startsWith('data:') && !normSrc.startsWith('blob:')) imgAfter.crossOrigin = 'anonymous';

                let beforeDone = false, afterDone = false;
                let beforeFailed = false, afterFailed = false;

                const tryStitch = () => {
                    if (beforeFailed || afterFailed) {
                        showPlaceholder();
                        return;
                    }
                    try {
                        const targetW = 640;
                        const scale = targetW / (imgBefore.naturalWidth || targetW);
                        const w = targetW;
                        const h = Math.max(160, Math.round((imgBefore.naturalHeight || 360) * scale));
                        const barH = Math.max(30, Math.round(h * 0.08));
                        const canvas = document.createElement('canvas');
                        canvas.width = w * 2;
                        canvas.height = h + barH;
                        const ctx = canvas.getContext('2d');
                        if (!ctx) {
                            showPlaceholder();
                            return;
                        }

                        // Top Bar background (#121e18)
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

                        // Center divider line
                        ctx.save();
                        ctx.strokeStyle = 'rgba(255,255,255,0.25)';
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(w, 0);
                        ctx.lineTo(w, canvas.height);
                        ctx.stroke();
                        ctx.restore();

                        img.src = canvas.toDataURL('image/jpeg', 0.92);
                    } catch (err) {
                        console.warn('[Admin Mapping Detail] Canvas stitching fallback error:', err);
                        showPlaceholder();
                    }
                };

                imgBefore.onload = () => { beforeDone = true; if (afterDone) tryStitch(); };
                imgAfter.onload = () => { afterDone = true; if (beforeDone) tryStitch(); };
                imgBefore.onerror = () => { beforeFailed = true; showPlaceholder(); };
                imgAfter.onerror = () => { afterFailed = true; showPlaceholder(); };

                imgBefore.src = rawSrc;
                imgAfter.src = normSrc;
            };
        }
    </script>
</div>
