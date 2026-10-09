@php
    // 1. MOST SCANNED SOIL (Based on Soil Scan History & Soils Database)
    $mostScanned = \App\Models\Soil::with('munsellColor')
        ->where('scan_count', '>', 0)
        ->orderByDesc('scan_count')
        ->orderBy('soil_id', 'asc')
        ->limit(5)
        ->get();

    $scannedItems = [];
    foreach ($mostScanned as $s) {
        $scannedItems[] = [
            'id'    => $s->soil_id,
            'name'  => $s->effective_soil_name,
            'code'  => $s->full_code,
            'hue'   => $s->hue ?: ($s->munsellColor?->hue ?: '—'),
            'scans' => (int) $s->scan_count,
        ];
    }

    // 2. MOST ACTIVE USERS (Based on Scans & Pin Maps across all roles)
    $mostActiveUsers = \App\Models\User::withCount(['soilScans', 'locationMappings'])
        ->get()
        ->map(function ($u) {
            $scans = (int) $u->soil_scans_count;
            $pins  = (int) $u->location_mappings_count;
            $total = $scans + $pins;
            return [
                'id'       => $u->user_id,
                'name'     => $u->name,
                'role'     => $u->role ?: 'user',
                'scans'    => $scans,
                'pins'     => $pins,
                'total'    => $total,
            ];
        })
        ->filter(function ($item) {
            return $item['total'] > 0;
        })
        ->sort(function ($a, $b) {
            if ($b['total'] !== $a['total']) {
                return $b['total'] <=> $a['total'];
            }
            if ($b['scans'] !== $a['scans']) {
                return $b['scans'] <=> $a['scans'];
            }
            return $b['pins'] <=> $a['pins'];
        })
        ->values()
        ->take(5);
@endphp

<style>
.ranking-grid-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    width: 100%;
    align-items: stretch;
    box-sizing: border-box;
}

/* 2 Equal Columns on Medium Viewports (800px to 1279px) */
@media (min-width: 800px) {
    .ranking-grid-container {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .app-version-card {
        grid-column: 1 / -1 !important;
    }
}

/* 3 Equal Columns on Full Desktop Viewports (>= 1280px) */
@media (min-width: 1280px) {
    .ranking-grid-container {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .app-version-card {
        grid-column: auto !important;
    }
}
</style>

<div class="ranking-grid-container">

    {{-- 1. MOST SCANNED SOIL --}}
    <div class="ui-reveal" style="--i:6; background: #ffffff; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.03); padding: 22px; display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-sizing: border-box; min-width: 0;">
        <div>
            {{-- Header --}}
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <div style="width: 38px; height: 38px; border-radius: 50%; background: #e8f5e9; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" fill="none" stroke="#22c55e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                        <polyline points="17 6 23 6 23 12"/>
                    </svg>
                </div>
                <h2 class="ui-reveal-text" style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    Most Scanned Soil
                </h2>
            </div>

            {{-- List of Ranked Items --}}
            <div style="display: flex; flex-direction: column; gap: 14px;">
                @if(count($scannedItems) > 0)
                    @foreach($scannedItems as $index => $item)
                        @php
                            $rank = $index + 1;
                            $badgeStyle = 'background: #f8fafc; color: #94a3b8;';
                            if ($rank === 1) {
                                $badgeStyle = 'background: #22c55e; color: #ffffff;';
                            } elseif ($rank === 2) {
                                $badgeStyle = 'background: #f1f5f9; color: #475569;';
                            } elseif ($rank === 3) {
                                $badgeStyle = 'background: #fef3c7; color: #d97706;';
                            }
                        @endphp

                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; width: 100%;">
                            <div style="display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1; overflow: hidden;">
                                <div style="width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0; {{ $badgeStyle }}">
                                    {{ $rank }}
                                </div>
                                <div style="min-width: 0; flex: 1; overflow: hidden;">
                                    <p class="ui-reveal-text" style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">
                                        {{ $item['name'] }}
                                    </p>
                                    <p class="ui-reveal-text" style="font-size: 11px; color: #94a3b8; font-weight: 500; margin: 2px 0 0 0;">
                                        Hue: {{ $item['hue'] }} @if(!empty($item['code'])) ({{ $item['code'] }}) @endif
                                    </p>
                                </div>
                            </div>
                            <span class="ui-reveal-num" style="color: #22c55e; font-weight: 700; font-size: 12.5px; white-space: nowrap; flex-shrink: 0; text-align: right; margin-left: 4px;"
                                  x-data x-init="animateCount($el, {{ (int) $item['scans'] }}, { suffix: ' scans', delay: 1400 + {{ $index }} * 50 })">
                                {{ number_format($item['scans']) }} scans
                            </span>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 36px 12px; color: #94a3b8; font-size: 13px; font-weight: 500; background: #f8fafc; border-radius: 16px;">
                        No scanned soil records yet.
                    </div>
                @endif
            </div>
        </div>
    </div>


    {{-- 2. MOST ACTIVE USERS --}}
    <div class="ui-reveal" style="--i:7; background: #ffffff; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.03); padding: 22px; display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-sizing: border-box; min-width: 0;">
        <div>
            {{-- Header --}}
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <div style="width: 38px; height: 38px; border-radius: 50%; background: #e8f5e9; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" fill="none" stroke="#22c55e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div style="min-width: 0;">
                    <h2 class="ui-reveal-text" style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Most Active Users
                    </h2>
                </div>
            </div>

            {{-- List of Ranked Users --}}
            <div style="display: flex; flex-direction: column; gap: 14px;">
                @if(count($mostActiveUsers) > 0)
                    @foreach($mostActiveUsers as $index => $item)
                        @php
                            $rank = $index + 1;
                            $badgeStyle = 'background: #f8fafc; color: #94a3b8;';
                            if ($rank === 1) {
                                $badgeStyle = 'background: #22c55e; color: #ffffff;';
                            } elseif ($rank === 2) {
                                $badgeStyle = 'background: #f1f5f9; color: #475569;';
                            } elseif ($rank === 3) {
                                $badgeStyle = 'background: #fef3c7; color: #d97706;';
                            }

                            $roleLower = strtolower($item['role']);
                            $roleLabel = ucfirst($roleLower);
                        @endphp

                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; width: 100%;">
                            <div style="display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1; overflow: hidden;">
                                <div style="width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0; {{ $badgeStyle }}">
                                    {{ $rank }}
                                </div>
                                <div style="min-width: 0; flex: 1; overflow: hidden;">
                                    <p class="ui-reveal-text" style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">
                                        {{ $item['name'] }}
                                    </p>
                                    <div class="ui-reveal-text" style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                        <span style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.02em;">
                                            {{ $roleLabel }}
                                        </span>
                                        <span style="font-size: 11px; color: #94a3b8; font-weight: 500;">
                                            {{ $item['id'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right; flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 1px;">
                                <span class="ui-reveal-num" style="color: #16a34a; font-weight: 800; font-size: 12px; white-space: nowrap;"
                                      x-data x-init="animateCount($el, {{ (int) $item['scans'] }}, { suffix: ' {{ Str::plural('scan', $item['scans']) }}', delay: 1600 + {{ $index }} * 50 })">
                                    {{ number_format($item['scans']) }} {{ Str::plural('scan', $item['scans']) }}
                                </span>
                                <span class="ui-reveal-num" style="color: #0284c7; font-weight: 700; font-size: 11px; white-space: nowrap;"
                                      x-data x-init="animateCount($el, {{ (int) $item['pins'] }}, { suffix: ' {{ Str::plural('pin map', $item['pins']) }}', delay: 1650 + {{ $index }} * 50 })">
                                    {{ number_format($item['pins']) }} {{ Str::plural('pin map', $item['pins']) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 36px 12px; color: #94a3b8; font-size: 13px; font-weight: 500; background: #f8fafc; border-radius: 16px;">
                        No active user records yet.
                    </div>
                @endif
            </div>
        </div>
    </div>


    {{-- 3. APPLICATION VERSION --}}
    @php
        $latestApp = \App\Models\Application::latest()->first();
        $appVersion = $latestApp->version ?? 'v2.4.0';
        $appDate = $latestApp ? $latestApp->created_at->format('d M Y') : '08 Apr 2026';
        $appFile = ($latestApp && !empty($latestApp->file_path)) ? basename((string)$latestApp->file_path) : 'AgriCo_v2.4.0.apk';
        $appDownloads = $latestApp->download_count ?? 0;
    @endphp

    <div class="app-version-card ui-reveal" style="--i:8; background: #ffffff; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.03); padding: 22px; display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-sizing: border-box; min-width: 0;">
        <div>
            {{-- Header --}}
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 38px; height: 38px; border-radius: 50%; background: #e8f5e9; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>
                <h2 class="ui-reveal-text" style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">
                    Application Version
                </h2>
            </div>

            {{-- Center Version Badge --}}
            <div style="text-align: center; margin: 12px 0 16px 0;">
                <div style="width: 64px; height: 64px; border-radius: 20px; background: #e8f5e9; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto;">
                    <svg width="28" height="28" fill="none" stroke="#22c55e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                        <path d="M12 12v9"/>
                        <path d="m16 16-4-4-4 4"/>
                    </svg>
                </div>
                <h1 class="ui-reveal-text" style="font-size: 28px; font-weight: 800; color: #111827; margin: 0; line-height: 1.2;">
                    {{ $appVersion }}
                </h1>
                <p class="ui-reveal-text" style="font-size: 13px; color: #22c55e; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 4px; margin: 4px 0 0 0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Latest Stable
                </p>
            </div>

            {{-- Details List Pills --}}
            <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 16px; font-size: 12.5px;">
                {{-- Upload Date Pill --}}
                <div class="ui-reveal-text" style="background: #f0fdf4; border-radius: 14px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;">
                    <span style="color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Upload Date
                    </span>
                    <span style="color: #1e293b; font-weight: 700;">
                        {{ $appDate }}
                    </span>
                </div>

                {{-- File Pill --}}
                <div class="ui-reveal-text" style="background: #f0fdf4; border-radius: 14px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;">
                    <span style="color: #64748b; font-weight: 500;">
                        File
                    </span>
                    <span style="color: #1e293b; font-weight: 700;">
                        {{ $appFile }}
                    </span>
                </div>

                {{-- Total Downloads Pill --}}
                <div class="ui-reveal-text" style="background: #f0fdf4; border-radius: 14px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;">
                    <span style="color: #64748b; font-weight: 500;">
                        Total Downloads
                    </span>
                    <span class="ui-reveal-num" style="color: #1e293b; font-weight: 700;"
                          x-data x-init="animateCount($el, {{ (int) $appDownloads }}, { delay: 1800 })">
                        {{ number_format($appDownloads) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>
