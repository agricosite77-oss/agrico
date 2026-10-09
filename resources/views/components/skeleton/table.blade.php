@props([
    'rows' => 5,
    'cols' => 4,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'skeleton-table ' . $class]) }} role="status" aria-busy="true" aria-label="Loading table records...">
    {{-- Header --}}
    <div class="skeleton-table-header">
        <span class="skeleton skeleton-heading" style="width: 25%; height: 16px; margin: 0;"></span>
        <span class="skeleton skeleton-heading" style="width: 30%; height: 16px; margin: 0;"></span>
        <span class="skeleton skeleton-heading" style="width: 20%; height: 16px; margin: 0;"></span>
        <span class="skeleton skeleton-heading" style="width: 15%; height: 16px; margin: 0;"></span>
    </div>

    {{-- Rows with staggered delay (Section 65 & 66) --}}
    @for($i = 0; $i < $rows; $i++)
        @php
            $delay = ($i * 0.08) . 's';
        @endphp
        <div class="skeleton-row" style="--skel-delay: {{ $delay }};">
            <span class="skeleton skeleton-text" style="width: 24%; height: 14px; margin: 0; --skel-delay: {{ $delay }};"></span>
            <span class="skeleton skeleton-text" style="width: 32%; height: 14px; margin: 0; --skel-delay: {{ $delay }};"></span>
            <span class="skeleton skeleton-text" style="width: 18%; height: 14px; margin: 0; --skel-delay: {{ $delay }};"></span>
            <span class="skeleton skeleton-pill" style="width: 14%; height: 22px; --skel-delay: {{ $delay }};"></span>
        </div>
    @endfor
</div>
