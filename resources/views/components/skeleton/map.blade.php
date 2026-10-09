@props([
    'height' => '440px',
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'skeleton-map ' . $class]) }} style="height: {{ $height }};" role="status" aria-busy="true" aria-label="Loading map preview...">
    {{-- Search / Filter bar overlay --}}
    <div class="skeleton-map-topbar">
        <div class="skeleton skeleton-card" style="width: 260px; height: 42px; padding: 0 16px; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
            <span class="skeleton skeleton-icon" style="width: 20px; height: 20px; border-radius: 50%;"></span>
            <span class="skeleton skeleton-text" style="width: 140px; height: 12px; margin: 0;"></span>
        </div>
        <div class="skeleton skeleton-button" style="min-width: 90px; height: 42px; border-radius: 12px;"></div>
    </div>

    {{-- Map zoom & location controls overlay --}}
    <div class="skeleton-map-controls">
        <div class="skeleton skeleton-card" style="width: 40px; height: 40px; padding: 0; border-radius: 10px;"></div>
        <div class="skeleton skeleton-card" style="width: 40px; height: 40px; padding: 0; border-radius: 10px;"></div>
        <div class="skeleton skeleton-card" style="width: 40px; height: 40px; padding: 0; border-radius: 10px; margin-top: 6px;"></div>
    </div>
</div>
