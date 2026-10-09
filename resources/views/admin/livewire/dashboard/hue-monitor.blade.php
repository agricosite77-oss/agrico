@php
    $standardHuesConfig = [
        '10R'   => ['name' => '10R',   'color' => '#ef4444'],
        '2.5YR' => ['name' => '2.5YR', 'color' => '#f97316'],
        '5YR'   => ['name' => '5YR',   'color' => '#f59e0b'],
        '7.5YR' => ['name' => '7.5YR', 'color' => '#eab308'],
        '10YR'  => ['name' => '10YR',  'color' => '#84cc16'],
        '2.5Y'  => ['name' => '2.5Y',  'color' => '#22c55e'],
    ];

    $scansByHue = [];
    if (isset($hues) && is_iterable($hues)) {
        foreach ($hues as $h) {
            $hName = is_array($h) ? ($h['name'] ?? $h['hue'] ?? '') : ($h->name ?? $h->hue ?? '');
            $hVal  = is_array($h) ? ($h['value'] ?? $h['scans'] ?? 0) : ($h->value ?? $h->scans ?? 0);
            if ($hName) {
                $scansByHue[strtoupper(trim($hName))] = (int) $hVal;
            }
        }
    }

    $totalHueScans = array_sum($scansByHue);
    $totalDenominator = max($totalHueScans, 1);

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

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 1.25rem; width: 100%; align-items: start;">
    @foreach($items as $item)
        @php
            $dashOffset = 251.2 - (251.2 * ($item['pct'] / 100));
        @endphp

        <div class="text-center" style="display: flex; flex-direction: column; align-items: center; justify-content: space-between; height: 100%;">
            <div class="relative mx-auto" style="position: relative; margin: 0 auto; width: 84px; height: 84px;">
                <svg class="w-full h-full" viewBox="0 0 100 100" style="width: 100%; height: 100%; display: block;">
                    <g transform="rotate(-90 50 50)">
                        <circle cx="50" cy="50" r="40" stroke="#F1F5F9" stroke-width="9" fill="none"/>
                        <circle cx="50" cy="50" r="40" stroke="{{ $item['color'] }}" stroke-width="9" fill="none" stroke-linecap="round" stroke-dasharray="251.2" stroke-dashoffset="{{ $dashOffset }}" style="transition: stroke-dashoffset 0.6s ease;"/>
                    </g>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
                    <span class="font-bold text-gray-800" style="font-weight: 800; font-size: 1rem; color: #0f172a;">
                        {{ $item['pct'] }}%
                    </span>
                </div>
            </div>

            <div style="margin-top: 12px; text-align: center;">
                <h3 class="font-bold text-gray-800" style="font-size: 0.92rem; font-weight: 800; color: #0f172a; margin: 0;">
                    {{ $item['hue'] }}
                </h3>
                <p class="text-xs text-gray-400 font-medium" style="font-size: 0.75rem; color: #64748b; font-weight: 600; margin: 2px 0 0 0;">
                    {{ number_format($item['scans']) }} {{ \Illuminate\Support\Str::plural('Scan', $item['scans']) }}
                </p>
            </div>

            <div class="w-full rounded-full overflow-hidden" style="width: 100%; height: 4px; background: #f1f5f9; border-radius: 9999px; overflow: hidden; margin-top: 12px;">
                <div class="h-full rounded-full transition-all duration-500" style="height: 100%; border-radius: 9999px; width: {{ max($item['pct'], $item['scans'] > 0 ? 8 : 0) }}%; background: {{ $item['color'] }};"></div>
            </div>
        </div>
    @endforeach
</div>
