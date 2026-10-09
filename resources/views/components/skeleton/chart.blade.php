@props([
    'title' => true,
    'bars' => 7,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'skeleton-card ' . $class]) }} role="status" aria-busy="true" aria-label="Loading chart data...">
    @if($title)
        <div class="flex items-center justify-between mb-4">
            <span class="skeleton skeleton-title w-1-3"></span>
            <span class="skeleton skeleton-pill" style="width: 72px;"></span>
        </div>
    @endif

    <div class="skeleton-chart-box">
        <div class="skeleton-chart-bars">
            <div class="skeleton skeleton-bar" style="height: 38%;"></div>
            <div class="skeleton skeleton-bar" style="height: 64%;"></div>
            <div class="skeleton skeleton-bar" style="height: 82%;"></div>
            <div class="skeleton skeleton-bar" style="height: 48%;"></div>
            <div class="skeleton skeleton-bar" style="height: 95%;"></div>
            <div class="skeleton skeleton-bar" style="height: 70%;"></div>
            <div class="skeleton skeleton-bar" style="height: 55%;"></div>
        </div>
        <div class="flex justify-between px-1 pt-1">
            <span class="skeleton skeleton-text" style="width: 28px; height: 10px;"></span>
            <span class="skeleton skeleton-text" style="width: 28px; height: 10px;"></span>
            <span class="skeleton skeleton-text" style="width: 28px; height: 10px;"></span>
            <span class="skeleton skeleton-text" style="width: 28px; height: 10px;"></span>
            <span class="skeleton skeleton-text" style="width: 28px; height: 10px;"></span>
            <span class="skeleton skeleton-text" style="width: 28px; height: 10px;"></span>
            <span class="skeleton skeleton-text" style="width: 28px; height: 10px;"></span>
        </div>
    </div>
</div>
