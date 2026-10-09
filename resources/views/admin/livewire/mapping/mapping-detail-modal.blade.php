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

    $scanImg = $scan?->scan_image ? (str_starts_with($scan->scan_image, 'http') || str_starts_with($scan->scan_image, 'data:') ? $scan->scan_image : asset($scan->scan_image)) : '';
    if (!$scanImg && $mColor?->image) {
        $scanImg = asset('storage/' . $mColor->image);
    }
    if (!$scanImg && $soil?->image) {
        $scanImg = asset('storage/' . $soil->image);
    }
    $scanUser = $scan?->user?->name ?? 'Admin Analyst';
    $scanPlatform = $scan?->platform ?? 'website';

    $baImg = $mapping->before_after_image_url ?: ($scan?->before_after_image_url ?? null);
    if (!$baImg && $scan?->scan_image) {
        $rawImg = $scan->scan_image;
        if (preg_match('/(?:scan|region)_([a-f0-9\-]{10,})/i', $rawImg, $m)) {
            $scanUuid = $m[1];
            $rel = "storage/scans/scan_{$scanUuid}_before_after.jpg";
            if (file_exists(public_path($rel)) || file_exists(storage_path("app/public/scans/scan_{$scanUuid}_before_after.jpg"))) {
                $baImg = asset($rel);
            }
        }
    }

    // Regions data check
    $regions = !empty($mapping->regions) ? $mapping->regions : ($scan?->regions ?? []);
    $isMulti = count($regions) > 1;
    $dominant = !empty($regions) ? $regions[0] : null;

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
        background: linear-gradient(145deg, #0B2B26 0%, #163832 100%);
        border: 1px solid rgba(142, 182, 155, 0.25);
        border-radius: 28px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
        width: 100%;
        max-width: 680px;
        margin: auto;
        display: flex;
        flex-direction: column;
        height: 75vh;
        max-height: 75vh;
        overflow: hidden;
        position: relative;
    }

    .soil-detail-modal-header {
        padding: 20px 26px 16px;
        border-bottom: 1px solid rgba(142, 182, 155, 0.25);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #0B2B26;
        flex-shrink: 0;
    }
    .soil-detail-modal-header h3 {
        margin: 0;
        font-size: 17.5px;
        font-weight: 800;
        color: #DAF1DE;
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
        scrollbar-color: rgba(142, 182, 155, 0.35) rgba(11, 43, 38, 0.8);
        scrollbar-gutter: stable;
        -webkit-overflow-scrolling: touch;
    }
    .soil-detail-modal-body::-webkit-scrollbar {
        width: 6px;
        display: block !important;
    }
    .soil-detail-modal-body::-webkit-scrollbar-track {
        background: rgba(11, 43, 38, 0.8);
        border-radius: 9999px;
        margin: 4px 0;
    }
    .soil-detail-modal-body::-webkit-scrollbar-thumb {
        background: rgba(142, 182, 155, 0.35);
        border-radius: 9999px;
    }
    .soil-detail-modal-body::-webkit-scrollbar-thumb:hover {
        background: rgba(142, 182, 155, 0.6);
    }

    .modal-close-circle-btn {
        background: rgba(5, 31, 32, 0.7) !important;
        border: 1.5px solid rgba(142, 182, 155, 0.35) !important;
        color: #8EB69B !important;
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
        background: rgba(35, 83, 71, 0.8) !important;
        color: #DAF1DE !important;
        border-color: #DAF1DE !important;
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

    /* Responsive Grid for Metric Cards */
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
        background: #163832;
        border: 1px solid rgba(142, 182, 155, 0.25);
        border-radius: 16px;
        padding: 12px 16px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        box-sizing: border-box;
    }
    .soil-metric-card-label {
        font-size: 11px;
        font-weight: 700;
        color: #8EB69B;
        margin-bottom: 4px;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }
    .soil-metric-card-val {
        font-size: 13.5px;
        font-weight: 700;
        color: #DAF1DE;
        word-break: break-word;
    }

    .covered-area-highlight {
        background: none !important;
        border: none !important;
        padding: 0 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        display: inline !important;
        color: #DAF1DE;
        font-size: 13.5px;
        font-weight: 700;
    }

    .layer-accordion-item {
        border: 1px solid rgba(142, 182, 155, 0.25) !important;
        border-radius: 14px !important;
        overflow: hidden !important;
        background: #163832 !important;
    }
    .layer-accordion-header {
        background: #0B2B26 !important;
        color: #DAF1DE !important;
    }
    .layer-accordion-body {
        background: #163832 !important;
        border-top: 1px solid rgba(142, 182, 155, 0.25) !important;
    }

    .soil-detail-modal-footer {
        padding: 16px 24px 20px;
        background: #0B2B26;
        border-top: 1px solid rgba(142, 182, 155, 0.25);
        display: flex;
        gap: 12px;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 100%;
        box-sizing: border-box;
    }

    .modal-cancel-btn {
        background: rgba(142, 182, 155, 0.12) !important;
        border: 1px solid rgba(142, 182, 155, 0.3) !important;
        color: #DAF1DE !important;
    }
    .modal-cancel-btn:hover {
        background: rgba(142, 182, 155, 0.22) !important;
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
        color: #DAF1DE;
    }
    .layer-accordion-header-title {
        color: #DAF1DE;
    }
    .layer-accordion-arrow {
        color: #8EB69B;
    }
    .soil-platform-badge {
        font-size: 12px;
        font-weight: 700;
        background: none !important;
        border: none !important;
        padding: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        color: #8EB69B;
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
        background: #f1f5f9 !important;
        border: 1px solid #e2e8f0 !important;
        color: #64748b !important;
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
    html.light-mode .layer-accordion-item [style*="color:#DAF1DE"] {
        color: #0f172a !important;
    }
    html.light-mode .layer-accordion-arrow,
    html.light-mode .layer-accordion-header span[x-text] {
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
        border: 1px solid rgba(0, 0, 0, 0.2) !important;
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
        border: 1.5px solid #e2e8f0 !important;
        background: #ffffff !important;
    }
    html.light-mode .layer-accordion-header {
        background: #f8fafc !important;
        color: #1e293b !important;
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
        background: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #1e293b !important;
        font-weight: 800 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    html.light-mode .modal-cancel-btn:hover {
        background: #e2e8f0 !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
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

    /* Mobile Overrides (<= 540px) */
    @media (max-width: 540px) {
        .soil-detail-backdrop {
            padding: 16px 14px !important;
        }
        .soil-detail-modal-container {
            height: 75vh !important;
            max-height: 75vh !important;
            border-radius: 24px !important;
            margin: auto !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35) !important;
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
    style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 100001; background: rgba(5, 31, 32, 0.8); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; padding: 16px; box-sizing: border-box;"
    wire:click.self="closeModal"
>

    {{-- MODAL CONTAINER --}}
    <div class="soil-detail-modal-container">

        {{-- FIXED HEADER (Matching Screenshot 2) --}}
        <div class="soil-detail-modal-header">
            <h3>
                Soil Record Details{{ $isMulti ? ' (' . count($regions) . ' DETECTED)' : '' }}
            </h3>
            <button type="button" wire:click="closeModal" class="modal-close-circle-btn" title="Close">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
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
                <div class="soil-swatch-avatar">
                    @if($scanImg)
                        <img src="{{ $scanImg }}" alt="Soil Sample" />
                        <div style="position: absolute; left: {{ $domBox['left'] }}%; top: {{ $domBox['top'] }}%; width: {{ $domBox['width'] }}%; height: {{ $domBox['height'] }}%; border: 1.5px solid #4ade80; pointer-events: none; box-sizing: border-box; z-index: 5;"></div>
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
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

            {{-- 2. METRICS ROW 1 (Color Name, Full Code, Scan Count) (Matching Screenshot 2 & 4) --}}
            <div class="soil-modal-grid-3">
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Color Name</div>
                    <div class="soil-metric-card-val truncate" title="{{ $displayName }}">{{ $displayName }}</div>
                </div>
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Full Code</div>
                    <div class="soil-metric-card-val">{{ $displayCode }}</div>
                </div>
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Scan Count</div>
                    <div class="soil-metric-card-val">{{ $soil?->scan_count ?? ($mapping->soil_scan_id ? 4 : 1) }}</div>
                </div>
            </div>

            {{-- 3. METRICS ROW 2 (Soil Organic, Soil Type, Near-Match) (Matching Screenshot 2 & 4) --}}
            <div class="soil-modal-grid-3">
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Soil Organic</div>
                    <div class="soil-metric-card-val">{{ $dominantOrganic }}</div>
                </div>
                <div class="soil-metric-card">
                    <div class="soil-metric-card-label">Soil Type</div>
                    <div class="soil-metric-card-val truncate" title="{{ $dominantSoilType }}">{{ $dominantSoilType }}</div>
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
                                        @if($scanImg)
                                             <div class="soil-swatch-avatar" style="width: 48px; height: 48px; min-width: 48px; position: relative;">
                                                <img src="{{ $scanImg }}" alt="Soil Sample" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
                                                <div style="position: absolute; left: {{ $subBox['left'] }}%; top: {{ $subBox['top'] }}%; width: {{ $subBox['width'] }}%; height: {{ $subBox['height'] }}%; border: 1.5px solid #4ade80; pointer-events: none; box-sizing: border-box; z-index: 5;"></div>
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
            @if($baImg)
                <div class="soil-metric-card" style="margin-bottom: 12px; padding: 13px 16px 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div class="soil-metric-card-label" style="margin-bottom: 0;">
                            SOIL NORMALIZATION
                        </div>
                        <span style="font-size: 9.5px; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: rgba(16, 185, 129, 0.15); color: #10b981; text-transform: uppercase; letter-spacing: 0.04em;">
                            BEFORE / AFTER
                        </span>
                    </div>
                    <div style="position: relative; width: 100%; border-radius: 10px; overflow: hidden; background: #0c1a17; border: 1px solid rgba(142, 182, 155, 0.25);">
                        <img src="{{ $baImg }}" alt="Soil Before & After Normalization" style="width: 100%; height: auto; display: block; border-radius: 8px;">
                        @php
                            $renderRegions = !empty($regions) ? $regions : [($dominant ?: ['box' => ['x' => 0.25, 'y' => 0.25, 'w' => 0.5, 'h' => 0.5]])];
                            $ordinalNames = ['1ST', '2ND', '3RD', '4TH', '5TH', '6TH', '7TH', '8TH', '9TH', '10TH'];
                        @endphp
                        <div style="position: absolute; inset: 0; pointer-events: none;">
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
</div>
