@props([
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'space-y-6 ' . $class]) }} role="status" aria-busy="true" aria-label="Loading dashboard...">
    {{-- Header / Welcome Skeleton --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-2">
            <span class="skeleton skeleton-heading" style="width: 220px; height: 28px;"></span>
            <span class="skeleton skeleton-text" style="width: 340px; height: 14px;"></span>
        </div>
        <div class="flex items-center gap-3">
            <span class="skeleton skeleton-button" style="width: 110px; height: 40px; border-radius: 12px;"></span>
            <span class="skeleton skeleton-button" style="width: 130px; height: 40px; border-radius: 12px;"></span>
        </div>
    </div>

    {{-- Stat Cards Skeleton --}}
    <x-skeleton.stat-cards :count="4" />

    {{-- Main Grid: Chart & Soil Analysis / Recent Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-skeleton.chart />
        </div>
        <div>
            <x-skeleton.soil-result />
        </div>
    </div>

    {{-- Recent Scans Table Skeleton --}}
    <div class="skeleton-card space-y-4">
        <div class="flex items-center justify-between">
            <span class="skeleton skeleton-title" style="width: 180px;"></span>
            <span class="skeleton skeleton-pill" style="width: 80px;"></span>
        </div>
        <x-skeleton.table :rows="4" />
    </div>
</div>
