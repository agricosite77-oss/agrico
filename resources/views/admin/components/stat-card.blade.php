@props([
    'title',
    'value',
    'icon',
    'subtitle',
    'color' => 'green',
    'index' => 0,
])

@php
    $styleConfig = match($color) {
        'emerald' => [
            'iconBg'   => 'background: #dcfce7; color: #16a34a;',
            'subColor' => 'color: #16a34a;',
        ],
        'green' => [
            'iconBg'   => 'background: #dcfce7; color: #16a34a;',
            'subColor' => 'color: #16a34a;',
        ],
        'yellow', 'amber' => [
            'iconBg'   => 'background: #fef9c3; color: #d97706;',
            'subColor' => 'color: #d97706;',
        ],
        'red', 'rose' => [
            'iconBg'   => 'background: #ffe4e6; color: #e11d48;',
            'subColor' => 'color: #e11d48;',
        ],
        'blue', 'sky' => [
            'iconBg'   => 'background: #e0f2fe; color: #0284c7;',
            'subColor' => 'color: #0284c7;',
        ],
        default => [
            'iconBg'   => 'background: #dcfce7; color: #16a34a;',
            'subColor' => 'color: #16a34a;',
        ],
    };
@endphp

<div
    class="agri-hover-card"
    style="background: #ffffff; border-radius: 20px; border: 1px solid rgba(241, 245, 249, 0.9); box-shadow: 0 2px 12px rgba(0,0,0,0.03), 0 1px 3px rgba(0,0,0,0.02); padding: 18px 20px; display: flex; align-items: center; gap: 14px; width: 100%; box-sizing: border-box; transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer;"
    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.06)';"
    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 12px rgba(0,0,0,0.03)';"
>
    {{-- PASTEL ROUNDED SQUARE ICON BADGE --}}
    <div class="ui-reveal-text" style="width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; {{ $styleConfig['iconBg'] }}">
        @if($icon === 'document-text')
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        @elseif($icon === 'users')
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        @elseif($icon === 'map-pin')
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        @elseif($icon === 'clipboard-document')
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/></svg>
        @else
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 12l2 2 4-4"/></svg>
        @endif
    </div>

@php
    $subNum = null;
    $subPrefix = '';
    $subSuffix = '';
    if (!empty($subtitle) && preg_match('/^([^\d]*)(\d+)(.*)$/', $subtitle, $matches)) {
        $subPrefix = $matches[1];
        $subNum    = (int) $matches[2];
        $subSuffix = $matches[3];
    }
@endphp

    {{-- CARD CONTENT --}}
    <div style="flex: 1; min-width: 0;">
        <div class="ui-reveal-text" style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; line-height: 1.2; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            {{ $title }}
        </div>

        <div class="ui-reveal-num" @if(is_numeric($value)) x-data x-init="animateCount($el, {{ (int) $value }}, { delay: ({{ (int) $index }} * 200) + 400 })" @endif style="font-size: 32px; font-weight: 800; color: #0f172a; line-height: 1; letter-spacing: -0.02em; margin-bottom: 5px;">
            {{ $value }}
        </div>

        <div class="ui-reveal-text" style="font-size: 12px; font-weight: 700; line-height: 1.2; {{ $styleConfig['subColor'] }}">
            @if($subNum !== null)
                <span class="ui-reveal-num" x-data x-init="animateCount($el, {{ $subNum }}, { prefix: '{{ $subPrefix }}', suffix: '{{ $subSuffix }}', delay: ({{ (int) $index }} * 200) + 400 })">
                    {{ $subtitle }}
                </span>
            @else
                {{ $subtitle }}
            @endif
        </div>
    </div>
</div>
