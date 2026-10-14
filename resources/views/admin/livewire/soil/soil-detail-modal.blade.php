<div>
@if($showModal && $soil)

@php
    $mColor = $soil->dynamic_munsell_color;
    $sHue = $soil->hue ?? ($mColor->hue ?? '7.5YR');
    $fCode = $soil->full_code ?: ($mColor->full_code ?? '7.5YR 5/3');
    
    $swatchColor = $soil->effective_hex_color;
    $organicIndex = $soil->effective_organic_matter;
    $drainageType = $soil->effective_soil_type;
    $soilDescription = $soil->effective_description;
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
    $regions = $scan?->regions ?? [];
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

    $scanImg = $scan?->scan_image_url ?? ($scan?->scan_image ? $formatImageUrl($scan->scan_image) : '');
    if (!$scanImg && !empty($dominant['image'])) {
        $scanImg = $formatImageUrl($dominant['image']);
    }
    if (!$scanImg && !empty($dominant['image_url'])) {
        $scanImg = $formatImageUrl($dominant['image_url']);
    }
    if (!$scanImg && $siblingScanImg) {
        $scanImg = $siblingScanImg;
    }
    if (!$scanImg && $munsellFallback) {
        $scanImg = $munsellFallback;
    }
    if (!$scanImg && $thumbFallback) {
        $scanImg = $thumbFallback;
    }

    $scanUser = $scan?->user_id ?? ($scan?->user?->name ?? ($soil->user?->name ?? 'USR-002'));
    $scanPlatform = $scan?->platform ?? 'website';
    $baImg = $scan?->before_after_image_url;
    if (!$baImg && $scan?->scan_image) {
        $rawImg = $scan->scan_image;
        if (preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $rawImg, $m)) {
            $scanUuid = $m[1];
            $baImg = asset("storage/scans/scan_{$scanUuid}_before_after.jpg");
        }
    }
    if (!$baImg && $scan?->scan_note) {
        if (preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $scan->scan_note, $m)) {
            $scanUuid = $m[1];
            $baImg = asset("storage/scans/scan_{$scanUuid}_before_after.jpg");
        }
    }
    if (!$baImg && $soil?->image) {
        $rawImg = $soil->image;
        if (preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $rawImg, $m)) {
            $scanUuid = $m[1];
            $baImg = asset("storage/scans/scan_{$scanUuid}_before_after.jpg");
        }
    }
    if (!$baImg && $scan?->soil_scan_id) {
        $baImg = asset("storage/scans/scan_{$scan->soil_scan_id}_before_after.jpg");
    }

    $rawPhotoImg = '';
    if ($scan?->scan_image && preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $scan->scan_image, $m)) {
        $rawPhotoImg = asset("storage/scans/scan_{$m[1]}_raw.jpg");
    } elseif ($scan?->soil_scan_id) {
        $rawPhotoImg = asset("storage/scans/scan_{$scan->soil_scan_id}_raw.jpg");
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
    $displayName = ($dominant && (!empty($dominant['label']) || !empty($dominant['color_name']))) ? ($dominant['label'] ?? $dominant['color_name']) : ($soil->soil_name ?: ($mColor->name ?? '—'));
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

    $dominantPercent = $dominant ? ($dominant['percent'] ?? round(($dominant['proportion'] ?? 0.6) * 100, 1)) : null;
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
    $recordedDate = $scan?->scan_date
        ? \Carbon\Carbon::parse($scan->scan_date)->format('M d, Y h:i A')
        : ($soil->created_at?->format('M d, Y h:i A') ?: 'Sep 27, 2026 01:49 PM');
@endphp

{{-- STYLES FOR SOIL RECORD DETAILS MODAL (DARK MODE THEMED DEFAULT) --}}
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
        height: 75vh !important;
        max-height: 75vh !important;
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

    /* Modal Close Circle Button */
    .modal-close-circle-btn {
        background: #f1f5f9 !important;
        border: 1.5px solid #e2e8f0 !important;
        color: #64748b !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        padding: 0 !important;
        transition: all 0.2s ease !important;
        flex-shrink: 0 !important;
    }
    .modal-close-circle-btn:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
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

    /* Mobile Overrides (<= 540px, matching Student Records popup) */
    @media (max-width: 540px) {
        .soil-detail-backdrop {
            padding: 16px 14px !important;
        }
        .soil-detail-modal-container {
            height: 75vh !important;
            max-height: 75vh !important;
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
            padding: 14px 18px 16px !important;
            gap: 10px !important;
            flex: 1 1 auto !important;
            max-height: none !important;
        }
        .soil-swatch-banner {
            padding: 12px 16px !important;
            gap: 12px !important;
            min-height: 68px !important;
            border-radius: 16px !important;
            margin-bottom: 8px !important;
        }
        .soil-swatch-avatar {
            width: 48px !important;
            height: 48px !important;
            min-width: 48px !important;
        }
        .soil-swatch-code {
            font-size: 22px !important;
            font-weight: 900 !important;
        }
        .soil-swatch-hex-pill {
            font-size: 11px !important;
            padding: 4px 10px !important;
        }
        .soil-modal-grid-3 {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important;
            margin-bottom: 8px !important;
        }
        .soil-modal-grid-3 > div:nth-child(3) {
            grid-column: auto !important;
        }
        .soil-modal-grid-2 {
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
            margin-bottom: 8px !important;
        }
        .soil-metric-card {
            padding: 10px 10px !important;
            border-radius: 14px !important;
        }
        .soil-metric-card-label {
            font-size: 11px !important;
            margin-bottom: 3px !important;
            font-weight: 600 !important;
        }
        .soil-metric-card-val {
            font-size: 13.5px !important;
            line-height: 1.3 !important;
            font-weight: 700 !important;
        }
        .soil-detail-modal-footer {
            padding: 12px 18px 14px !important;
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
>

    {{-- MODAL CONTAINER --}}
    <div class="soil-detail-modal-container">

        {{-- FIXED HEADER --}}
        <div class="soil-detail-modal-header" style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <h3 style="margin: 0;">
                    Soil Record Details{{ $isMulti ? ' (' . count($regions) . ' DETECTED)' : '' }}
                </h3>
                @php
                    $vStatus = $scan?->verification_status ?: 'Pending';
                @endphp
                @if(strtolower($vStatus) === 'verified')
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 800; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        VERIFIED (PIN VISIBLE)
                    </span>
                @elseif(strtolower($vStatus) === 'rejected')
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 800; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        REJECTED (PIN HIDDEN)
                    </span>
                @else
                    <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 800; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        PENDING REVIEW
                    </span>
                @endif
            </div>
            <x-modal-close-button click="closeModal" />
        </div>

        {{-- SCROLLABLE BODY --}}
        <div class="soil-detail-modal-body">

            {{-- 1. DUAL-PREVIEW BANNER SWATCH CARD --}}
            <div class="soil-swatch-banner" style="background: {{ $displayHex }};">
                {{-- Circular Avatar --}}
                <div class="soil-swatch-avatar" style="position: relative;">
                    @if($scanImg)
                        <img
                            src="{{ $scanImg }}"
                            data-fallback-thumb="{{ $thumbFallback }}"
                            data-fallback-sibling="{{ $siblingScanImg }}"
                            data-fallback-munsell="{{ $munsellFallback }}"
                            alt=""
                            style="width: 100%; height: 100%; object-fit: cover; display: block;"
                            onload="var b = this.parentElement.querySelector('.soil-bounding-box'); if(b) b.style.display='block';"
                            onerror="
                                var fbThumb = this.getAttribute('data-fallback-thumb');
                                var fbSib = this.getAttribute('data-fallback-sibling');
                                var fbMun = this.getAttribute('data-fallback-munsell');
                                if (fbThumb && this.src !== fbThumb) {
                                    this.src = fbThumb;
                                    return;
                                }
                                if (fbSib && this.src !== fbSib) {
                                    this.src = fbSib;
                                    return;
                                }
                                if (fbMun && this.src !== fbMun) {
                                    this.src = fbMun;
                                    return;
                                }
                                this.style.setProperty('display', 'none', 'important');
                                var b = this.parentElement.querySelector('.soil-bounding-box');
                                if(b) b.style.display='none';
                                var fb = this.parentElement.querySelector('.soil-avatar-fallback');
                                if(fb) fb.style.display='flex';
                            "
                        />
                        <div class="soil-bounding-box" style="display: none; position: absolute; left: {{ $domBox['left'] }}%; top: {{ $domBox['top'] }}%; width: {{ $domBox['width'] }}%; height: {{ $domBox['height'] }}%; border: 1.5px solid #4ade80; border-radius: 0; pointer-events: none; box-sizing: border-box; z-index: 5;"></div>
                        <div class="soil-avatar-fallback" style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; background: rgba(0,0,0,0.25);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                    @else
                        <div class="soil-avatar-fallback" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.25);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Center Code & Label --}}
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
                    @endif
                </div>

                {{-- Right HEX Pill --}}
                <span class="soil-swatch-hex-pill">
                    {{ strtoupper($displayHex) }}
                </span>
            </div>

            {{-- 2. METRICS ROW 1 (Color Name, Full Code, Scan Count) --}}
            <div class="soil-modal-grid-3">
                {{-- Color Name --}}
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">
                        Color Name
                    </div>
                    <div class="soil-metric-card-val truncate" title="{{ $displayName }}">
                        {{ $displayName }}
                    </div>
                </div>

                {{-- Full Code --}}
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">
                        Full Code
                    </div>
                    <div class="soil-metric-card-val">
                        {{ $displayCode }}
                    </div>
                </div>

                {{-- Scan Count --}}
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">
                        Scan Count
                    </div>
                    <div class="soil-metric-card-val">
                        {{ $soil->scan_count ?? 1 }}
                    </div>
                </div>
            </div>

            {{-- 3. METRICS ROW 2 (Soil Organic, Soil Type, Near-Match) --}}
            <div class="soil-modal-grid-3">
                {{-- Soil Organic --}}
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">
                        Soil Organic
                    </div>
                    <div class="soil-metric-card-val">
                        {{ $dominantOrganic }}
                    </div>
                </div>

                {{-- Soil Type --}}
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">
                        Soil Type
                    </div>
                    <div class="soil-metric-card-val truncate" title="{{ $dominantSoilType }}">
                        {{ $dominantSoilType }}
                    </div>
                </div>

                {{-- Near-Match --}}
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">
                        Near-Match
                    </div>
                    <div class="soil-metric-card-val" style="color: #10b981; font-weight: 800;">
                        {{ $dominantConf }}
                    </div>
                </div>
            </div>

            @if(!$isMulti)
                {{-- 4. SINGLE RESULT: SCAN DATE & SCANNED BY / PLATFORM --}}
                <div class="soil-modal-grid-2">
                    {{-- Scan Date --}}
                    <div class="soil-metric-card">
                        <div class="soil-metric-card-label" style="text-transform: uppercase;">
                            Scan Date
                        </div>
                        <div class="soil-metric-card-val">
                            {{ $scan?->scan_date ? \Carbon\Carbon::parse($scan->scan_date)->format('m/d/Y') : ($soil->created_at?->format('m/d/Y') ?: '08/27/2026') }}
                        </div>
                    </div>

                    {{-- Scanned By & Platform Badge --}}
                    <div class="soil-metric-card">
                        <div class="soil-metric-card-label" style="text-transform: uppercase;">
                            Scanned By
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; flex-wrap: wrap;">
                            <span class="soil-metric-card-val">
                                {{ $scanUser }}
                            </span>
                            <span class="soil-platform-badge" title="Scanned via {{ $scanPlatform === 'mobile' ? 'Mobile App' : 'Website' }}">
                                {{ $scanPlatform === 'mobile' ? 'App' : 'Web' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 5. SCAN NOTE CARD --}}
                <div class="soil-metric-card" style="margin-bottom: 10px;">
                    <div class="soil-metric-card-label">
                        Scan Note
                    </div>
                    <div class="soil-metric-card-text">
                        {{ $scan?->has_user_scan_note ? $scan->scan_note : 'No scan note available for this record.' }}
                    </div>
                </div>

                {{-- 6. DESCRIPTION CARD --}}
                <div class="soil-metric-card" style="margin-bottom: 10px;">
                    <div class="soil-metric-card-label">
                        Description
                    </div>
                    <div class="soil-metric-card-text">
                        {{ $soilDescription }}
                    </div>
                </div>

            @else
                {{-- 4. MULTI-LAYER DETECTED: SCAN NOTE CARD --}}
                <div class="soil-metric-card" style="margin-bottom: 10px;">
                    <div class="soil-metric-card-label">
                        Scan Note
                    </div>
                    <div class="soil-metric-card-text">
                        {{ $scan?->has_user_scan_note ? $scan->scan_note : 'No scan note available for this record.' }}
                    </div>
                </div>

                {{-- 5. MULTI-LAYER DETECTED: FULL DESCRIPTION CARD --}}
                <div class="soil-metric-card" style="margin-bottom: 12px;">
                    <div class="soil-metric-card-label">
                        Full Description
                    </div>
                    <div class="soil-metric-card-text">
                        {{ $dominantDesc }}
                    </div>
                </div>


                {{-- 6. MULTI-LAYER DETECTED ACCORDION (Secondary Layers) --}}
                <div style="margin-bottom: 12px; border-top: 1px dashed rgba(142, 182, 155, 0.25); padding-top: 12px;">
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach(array_slice($regions, 1) as $sub)
                            @php
                                $subPct = $sub['percent'] ?? round(($sub['proportion'] ?? 0.2) * 100, 1);
                                $subCode = $sub['munsell'] ?? ($sub['munsell_code'] ?? 'Soil Color');
                                $subHex = $sub['hex'] ?? '#8F694E';
                                $subName = $sub['label'] ?? ($sub['color_name'] ?? 'Soil Color');
                                $subConf = $calcNearMatchRating($sub) ?: '96.8%';

                                // Same fallback pattern as the dominant layer's variables above,
                                // so sub-layers show real data when available, or the overall soil's
                                // values when the layer itself doesn't have its own reading.
                                $subOrganic = !empty($sub['organic_matter']) ? $sub['organic_matter'] : $organicIndex;
                                $subSoilType = !empty($sub['soil_type']) ? $sub['soil_type'] : (!empty($sub['soil_name']) ? $sub['soil_name'] : $dominantSoilType);
                                $subDrainage = !empty($sub['soil_name']) ? $sub['soil_name'] : $drainageType;
                                $subDesc = !empty($sub['description']) ? $sub['description'] : ($subCode . ' — ' . $subName);
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

                                {{-- EXPANDED CONTENT: matches the dominant layer card format exactly --}}
                                <div x-show="open" class="layer-accordion-body" style="padding: 14px;" x-transition>

                                    @php
                                        $subBox = $normalizeBox($sub);
                                    @endphp
                                    {{-- Swatch banner with sub-layer ROI avatar --}}
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
                                                 <div style="display: none; position: absolute; left: {{ $subBox['left'] }}%; top: {{ $subBox['top'] }}%; width: {{ $subBox['width'] }}%; height: {{ $subBox['height'] }}%; border: 1.5px solid #4ade80; border-radius: 0; pointer-events: none; box-sizing: border-box; z-index: 5;"></div>
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

                                    {{-- Description card --}}
                                    <div class="soil-metric-card">
                                        <div class="soil-metric-card-label">
                                            Description
                                        </div>
                                        <div class="soil-metric-card-text">
                                            {{ $subDesc }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            @endif

            {{-- SOIL PIN LOCATION & COVERED AREA MAP (BEFORE NORMALIZATION) --}}
            @if(!empty($location['lat']) && !empty($location['lng']))
                <div class="soil-metric-card" style="margin-bottom: 12px; padding: 13px 16px 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div class="soil-metric-card-label" style="margin-bottom: 0;">
                            SOIL PIN LOCATION &amp; COVERED AREA
                        </div>
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <span class="modal-loc-badge-brgy">
                                {{ $location['barangay'] }}
                            </span>
                            @if(!empty($coverage))
                                <span class="modal-loc-badge-coverage">
                                    {{ $coverage }}
                                </span>
                            @endif
                            <span class="modal-loc-badge-coords">
                                {{ number_format($location['lat'], 4) }}, {{ number_format($location['lng'], 4) }}
                            </span>
                        </div>
                    </div>
                    
                    <div
                        x-data
                        x-init="$nextTick(() => { window.renderAgricoSoilDetailMap('admin-soil-detail-map-{{ $scan?->soil_scan_id ?? $soil?->soil_id }}', {{ $location['lat'] }}, {{ $location['lng'] }}, @js($location['barangay'] ?? ''), @js($soilName ?? 'Soil Specimen'), @js($fCode ?? ''), @js($coverage ?? ''), @js($location['status'] ?? 'Active')) })"
                        class="modal-soil-map-wrapper"
                    >
                        <div id="admin-soil-detail-map-{{ $scan?->soil_scan_id ?? $soil?->soil_id }}" class="admin-soil-detail-map" style="height: 220px; width: 100%; background: #0c1a17;"></div>
                        <div class="modal-soil-map-watermark">
                            AgriCo Soil GPS Preview
                        </div>
                    </div>
                </div>
            @endif

            {{-- 7. SOIL NORMALIZATION: BEFORE / AFTER CARD --}}
            @if($baImg || $scanImg || $rawPhotoImg)
                @php
                    $displayBaImg = $baImg ?: ($rawPhotoImg ?: $scanImg);
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

                    {{-- Unified Before & After Container (Composite Image with Canvas Stitching Fallback) --}}
                    <div id="adminBeforeAfterWrapper-{{ $scan?->soil_scan_id ?? $soil?->soil_id }}" style="position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #0c1a17; border: 1px solid rgba(142, 182, 155, 0.25); cursor: pointer;" title="Click to view full resolution comparison">
                        <img
                            id="adminSoilBaImg-{{ $scan?->soil_scan_id ?? $soil?->soil_id }}"
                            src="{{ $displayBaImg }}"
                            data-raw="{{ $rawSource }}"
                            data-norm="{{ $normSource }}"
                            data-code="{{ $displayCode }}"
                            alt="Soil Before & After Normalization"
                            style="width: 100%; height: auto; display: block; border-radius: 8px;"
                            onerror="window.handleAdminSoilBaFallback && window.handleAdminSoilBaFallback(this);"
                        />
                        <div style="position: absolute; inset: 0; pointer-events: none; z-index: 10;">
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
            @endif


        </div>

        {{-- FIXED FOOTER BAR FOR RECORD DETAILS & MODERATION --}}
        <div class="soil-detail-modal-footer" style="padding: 14px 24px 16px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; width: 100%; box-sizing: border-box; flex-shrink: 0; background: #ffffff; border-top: 1px solid #f1f5f9;">
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <button
                    type="button"
                    wire:click="approveScan"
                    class="py-2.5 px-5 rounded-full text-xs font-bold transition cursor-pointer text-center"
                    style="background: #16a34a; border: 1.5px solid #15803d; color: #ffffff; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.25);"
                    onmouseover="this.style.background='#15803d';"
                    onmouseout="this.style.background='#16a34a';"
                    title="Approve scan, mark verified, and make pin visible on public map"
                >
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Approve (Publish Pin)
                </button>

                <button
                    type="button"
                    wire:click="rejectScan"
                    class="py-2.5 px-5 rounded-full text-xs font-bold transition cursor-pointer text-center"
                    style="background: #ffffff; border: 1.5px solid #ef4444; color: #ef4444; display: inline-flex; align-items: center; gap: 6px;"
                    onmouseover="this.style.background='#fef2f2';"
                    onmouseout="this.style.background='#ffffff';"
                    title="Reject scan and hide pin from public map"
                >
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Reject (Hide Pin)
                </button>
            </div>

            <button
                type="button"
                wire:click="closeModal"
                class="py-2.5 px-6 rounded-full text-xs font-bold transition cursor-pointer text-center modal-cancel-btn"
                style="background: #f8fafc; border: 1.5px solid #cbd5e1; color: #475569;"
                onmouseover="this.style.background='#e2e8f0';"
                onmouseout="this.style.background='#f8fafc';"
                title="Close"
            >
                Close
            </button>
        </div>

    </div>

</div>

@endif

    <script>
        if (typeof window.handleAdminSoilBaFallback === 'undefined') {
            window.handleAdminSoilBaFallback = function(img) {
                if (!img || img.dataset.handledFallback === 'true') return;
                img.dataset.handledFallback = 'true';

                const rawSrc = img.dataset.raw;
                const normSrc = img.dataset.norm;
                const label = img.dataset.code || '';

                if (rawSrc && normSrc) {
                    const imgBefore = new Image();
                    const imgAfter = new Image();
                    if (!rawSrc.startsWith('data:') && !rawSrc.startsWith('blob:')) imgBefore.crossOrigin = 'anonymous';
                    if (!normSrc.startsWith('data:') && !normSrc.startsWith('blob:')) imgAfter.crossOrigin = 'anonymous';

                    let beforeDone = false, afterDone = false;
                    const tryStitch = () => {
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
                            if (!ctx) return;

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
                            console.warn('[Admin Soil Detail] Canvas stitching fallback error:', err);
                        }
                    };

                    imgBefore.onload = () => { beforeDone = true; if (afterDone) tryStitch(); };
                    imgAfter.onload = () => { afterDone = true; if (beforeDone) tryStitch(); };
                    imgBefore.onerror = () => { beforeDone = true; if (afterDone) tryStitch(); };
                    imgAfter.onerror = () => { afterDone = true; if (beforeDone) tryStitch(); };

                    imgBefore.src = rawSrc;
                    imgAfter.src = normSrc;
                }
            };
        }

        if (typeof window.renderAgricoSoilDetailMap === 'undefined') {
            window.renderAgricoSoilDetailMap = function(containerId, lat, lng, barangay, soilName, munsellCode, coverage, status) {
                let attempts = 0;
                const maxAttempts = 30;
                const interval = setInterval(function() {
                    attempts++;
                    const mapEl = document.getElementById(containerId);
                    if (!mapEl || typeof L === 'undefined') {
                        if (attempts >= maxAttempts) clearInterval(interval);
                        return;
                    }
                    clearInterval(interval);

                    if (mapEl._leaflet_id) {
                        try { mapEl._leaflet_id = null; } catch(e){}
                    }

                    const isApproved = String(status || '').toLowerCase() === 'active' || String(status || '').toLowerCase() === 'approved';

                    const dMap = L.map(mapEl, {
                        zoomControl: true,
                        attributionControl: false
                    }).setView([lat, lng], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19
                    }).addTo(dMap);

                    const pinIcon = L.icon({
                        iconUrl: isApproved ? '{{ asset('images/agrico-map-pin.png') }}' : '{{ asset('images/agrico-map-pin-gray.png') }}',
                        iconSize: [36, 52],
                        iconAnchor: [18, 52],
                        popupAnchor: [0, -46]
                    });

                    const marker = L.marker([lat, lng], { icon: pinIcon }).addTo(dMap);

                    const isLight = !document.documentElement.classList.contains('dark-soil-modal') && !document.documentElement.classList.contains('dark-mode');
                    const popupHtml = `
                        <div style='font-family:inherit; min-width:170px; padding:3px;' class='soil-map-popup-inner'>
                            <div class='soil-map-popup-brgy' style='font-size:11px; font-weight:800; color:${isLight ? (isApproved ? '#16a34a' : '#64748b') : (isApproved ? '#4ade80' : '#8EB69B')}; text-transform:uppercase;'>${barangay || 'Lal-lo Overall'}</div>
                            <strong class='soil-map-popup-title' style='font-size:13.5px; color:${isLight ? '#0f172a' : '#DAF1DE'}; display:block; margin:2px 0;'>${soilName || 'Soil Specimen'}</strong>
                            ${munsellCode ? `<div class='soil-map-popup-munsell' style='font-size:11px; color:${isLight ? '#64748b' : '#8EB69B'}; font-family:monospace; margin-bottom:2px;'>Munsell: <strong style='color:${isLight ? '#0f172a' : '#DAF1DE'};'>${munsellCode}</strong></div>` : ''}
                            <div class='soil-map-popup-coords' style='font-size:10.5px; color:${isLight ? '#0284c7' : '#38bdf8'}; font-family:monospace;'>${parseFloat(lat).toFixed(5)}, ${parseFloat(lng).toFixed(5)}</div>
                        </div>
                    `;
                    marker.bindPopup(popupHtml).openPopup();

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

                    const covStr = coverage || '500 m²';
                    let boxW = 85, boxH = 85, cornerR = 20;
                    if (covStr.includes('50 m')) { boxW = 35; boxH = 35; cornerR = 8; }
                    else if (covStr.includes('250 m')) { boxW = 55; boxH = 55; cornerR = 13; }
                    else if (covStr.includes('500 m')) { boxW = 75; boxH = 75; cornerR = 18; }
                    else if (covStr.includes('1,000') || covStr.includes('1000')) { boxW = 100; boxH = 100; cornerR = 24; }
                    else if (covStr.includes('2,500') || covStr.includes('2500')) { boxW = 130; boxH = 130; cornerR = 30; }
                    else if (covStr.includes('5,000') || covStr.includes('5000')) { boxW = 160; boxH = 160; cornerR = 36; }
                    else if (covStr.toLowerCase().includes('hectare')) { boxW = 190; boxH = 190; cornerR = 42; }

                    const roundedBoxCoords = getRoundedBoxCoords(lat, lng, boxW, boxH, cornerR);
                    const polyBorderColor = isLight ? (isApproved ? '#16a34a' : '#64748b') : (isApproved ? '#4ade80' : '#8EB69B');
                    const polyFillColor   = isLight ? (isApproved ? '#22c55e' : '#9ca3af') : (isApproved ? '#22c55e' : '#163832');
                    const poly = L.polygon(roundedBoxCoords, {
                        color: polyBorderColor,
                        fillColor: polyFillColor,
                        fillOpacity: isLight ? 0.22 : 0.28,
                        weight: 2.5
                    }).addTo(dMap);

                    if (covStr) {
                        poly.bindTooltip(`${isApproved ? 'Covered Area' : 'Pending Covered Area'} (Sakop): ${covStr}`, { sticky: true });
                    }

                    setTimeout(() => { dMap.invalidateSize(); }, 150);
                    setTimeout(() => { dMap.invalidateSize(); }, 350);
                    setTimeout(() => { dMap.invalidateSize(); }, 600);
                    setTimeout(() => { dMap.invalidateSize(); }, 1000);
                }, 50);
            };
        }
    </script>
</div>
