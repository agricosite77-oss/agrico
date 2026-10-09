<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6" style="background: #ffffff; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); padding: 24px;">

    @php
        $totalScanCount = (int) \App\Models\SoilScan::count();

        // Standard 6 agricultural Munsell soil hues in canonical order with signature colors
        $standardHuesConfig = [
            '10R'   => ['name' => '10R',   'color' => '#ef4444'], // Red
            '2.5YR' => ['name' => '2.5YR', 'color' => '#f97316'], // Red-Orange
            '5YR'   => ['name' => '5YR',   'color' => '#f59e0b'], // Orange
            '7.5YR' => ['name' => '7.5YR', 'color' => '#eab308'], // Yellow-Orange
            '10YR'  => ['name' => '10YR',  'color' => '#84cc16'], // Yellow-Brown
            '2.5Y'  => ['name' => '2.5Y',  'color' => '#22c55e'], // Yellow
        ];

        // Map incoming dataset counts by hue key
        $scansByHue = [];
        if (isset($munsellHues) && is_iterable($munsellHues)) {
            foreach ($munsellHues as $h) {
                $hName  = is_object($h) ? ($h->hue ?? '') : ($h['hue'] ?? '');
                $hCount = is_object($h) ? ($h->scans_count ?? $h->soils_count ?? $h->total_scans ?? 0) : ($h['scans_count'] ?? $h['soils_count'] ?? $h['total_scans'] ?? 0);
                if ($hName) {
                    $scansByHue[strtoupper(trim($hName))] = (int) $hCount;
                }
            }
        }

        // Calculate total scans across hues to compute percentages
        $totalHueScans = array_sum($scansByHue);
        $totalDenominator = max($totalHueScans, 1);

        // Assemble 6 balanced items
        $items = [];
        foreach ($standardHuesConfig as $hueKey => $cfg) {
            $hueScans = $scansByHue[$hueKey] ?? 0;
            $pct = round(($hueScans / $totalDenominator) * 100);
            $items[] = [
                'hue'   => $cfg['name'],
                'color' => $cfg['color'],
                'scans' => $hueScans,
                'pct'   => $pct,
            ];
        }
    @endphp

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 24px;">
        <div style="flex: 1; min-width: 200px;">
            <h2 class="ui-reveal-text text-lg font-bold text-gray-800" style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.01em;">
                Munsell Hue Prevalence Monitor
            </h2>
            <p class="ui-reveal-text text-sm text-gray-400" style="font-size: 0.82rem; color: #64748b; margin: 3px 0 0 0; font-weight: 500;">
                Distribution across all active soil scans
            </p>
        </div>

        <div class="ui-reveal-num" style="white-space: nowrap; flex-shrink: 0; padding: 6px 14px; border-radius: 9999px; background: #f0fdf4; color: #16a34a; font-size: 0.78rem; font-weight: 700; border: 1px solid #bbf7d0;">
            <span x-data x-init="animateCount($el, {{ (int) $totalScanCount }}, { suffix: ' scans', delay: 1200 })">{{ number_format($totalScanCount) }} scans</span>
        </div>
    </div>

    {{-- 6-Item Balanced Horizontal Grid Layout --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 1.25rem; width: 100%; align-items: start;">
        @foreach($items as $item)
            @php
                $dashOffset = 251.2 - (251.2 * ($item['pct'] / 100));
            @endphp

            <div class="text-center" style="--i:4; display: flex; flex-direction: column; align-items: center; justify-content: space-between; height: 100%;">
                {{-- Circle Gauge --}}
                <div class="relative mx-auto" style="position: relative; margin: 0 auto; width: 84px; height: 84px;">
                    <svg class="w-full h-full" viewBox="0 0 100 100" style="width: 100%; height: 100%; display: block;">
                        <g transform="rotate(-90 50 50)">
                            <circle
                                cx="50"
                                cy="50"
                                r="40"
                                stroke="#F1F5F9"
                                stroke-width="9"
                                fill="none"
                            />
                            <circle
                                class="ui-ring"
                                cx="50"
                                cy="50"
                                r="40"
                                stroke="{{ $item['color'] }}"
                                stroke-width="9"
                                fill="none"
                                stroke-linecap="round"
                                stroke-dasharray="251.2"
                                stroke-dashoffset="{{ $dashOffset }}"
                                style="transition: stroke-dashoffset 0.6s ease;"
                            />
                        </g>
                    </svg>

                    <div class="absolute inset-0 flex items-center justify-center" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
                        <span class="ui-reveal-num font-bold text-gray-800" x-data x-init="animateCount($el, {{ (int) $item['pct'] }}, { suffix: '%', delay: 1200 + {{ $loop->index }} * 60 })" style="font-weight: 800; font-size: 1rem; color: #0f172a;">
                            {{ $item['pct'] }}%
                        </span>
                    </div>
                </div>

                {{-- Label & Scan Count --}}
                <div style="margin-top: 12px; text-align: center;">
                    <h3 class="ui-reveal-text font-bold text-gray-800" style="font-size: 0.92rem; font-weight: 800; color: #0f172a; margin: 0;">
                        {{ $item['hue'] }}
                    </h3>

                    <p class="text-xs text-gray-400 font-medium" style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin: 2px 0 0 0;">
                        <span class="ui-reveal-num" x-data x-init="animateCount($el, {{ (int) $item['scans'] }}, { suffix: ' {{ \Illuminate\Support\Str::plural('Scan', $item['scans']) }}', delay: 1250 + {{ $loop->index }} * 60 })">
                            {{ number_format($item['scans']) }} {{ \Illuminate\Support\Str::plural('Scan', $item['scans']) }}
                        </span>
                    </p>
                </div>

                {{-- Bottom Colored Accent Bar --}}
                <div class="w-full rounded-full overflow-hidden" style="width: 100%; height: 4px; background: #f1f5f9; border-radius: 9999px; overflow: hidden; margin-top: 12px;">
                    <div
                        class="ui-bar-x h-full rounded-full transition-all duration-500"
                        style="height: 100%; border-radius: 9999px; width: {{ max($item['pct'], $item['scans'] > 0 ? 8 : 0) }}%; background: {{ $item['color'] }}; transition: width 0.5s ease;"
                    ></div>
                </div>
            </div>
        @endforeach
    </div>

</div>
