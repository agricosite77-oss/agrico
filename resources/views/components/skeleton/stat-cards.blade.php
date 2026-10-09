{{-- AgriCo Premium Stat Cards Skeleton (Sections 51, 60) --}}
<div class="stat-cards-grid-layout" style="display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 16px; width: 100%; box-sizing: border-box;">
    @for ($i = 0; $i < 4; $i++)
        <div class="skeleton-card" style="display: flex; align-items: center; gap: 16px; padding: 20px 22px;">
            {{-- Icon Skeleton --}}
            <div class="skeleton-icon skel-delay-{{ $i + 1 }}" style="width: 48px; height: 48px; border-radius: 16px;"></div>

            {{-- Text Content --}}
            <div style="flex: 1; min-width: 0;">
                {{-- Uppercase label --}}
                <div class="skeleton-text skel-delay-{{ $i + 1 }}" style="width: 55%; height: 11px; margin-bottom: 8px;"></div>

                {{-- Stat Value --}}
                <div class="skeleton-heading skel-delay-{{ $i + 1 }}" style="width: 35%; height: 28px; margin-bottom: 6px;"></div>

                {{-- Subtitle --}}
                <div class="skeleton-text skel-delay-{{ $i + 1 }}" style="width: 70%; height: 12px; margin-bottom: 0;"></div>
            </div>
        </div>
    @endfor
</div>
