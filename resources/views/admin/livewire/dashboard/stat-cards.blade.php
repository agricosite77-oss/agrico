<style>
.stat-cards-grid-layout {
    display: grid;
    grid-template-columns: repeat(1, minmax(0, 1fr));
    gap: 16px;
    width: 100%;
    box-sizing: border-box;
}
@media (min-width: 640px) {
    .stat-cards-grid-layout {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (min-width: 1024px) {
    .stat-cards-grid-layout {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}
</style>

<div class="stat-cards-grid-layout">
    <div class="ui-reveal" style="--i:1">
    <x-dashboard.stat-card
        :index="1"
        title="TOTAL SOIL SCANS"
        value="{{ $totalScans }}"
        icon="document-text"
        color="green"
        subtitle="+{{ $weeklyScans }} new this week"
        href="{{ route('soil.manage') }}" />
    </div>

    <div class="ui-reveal" style="--i:2">
    <x-dashboard.stat-card
        :index="2"
        title="TOTAL ACTIVE USERS"
        value="{{ $totalUsers }}"
        icon="users"
        color="emerald"
        subtitle="+{{ $weeklyUsers }} new this week"
        href="{{ route('users.manage') }}" />
    </div>

    <div class="ui-reveal" style="--i:3">
    <x-dashboard.stat-card
        :index="3"
        title="TOTAL MAPPING REPORTS"
        value="{{ $totalMappingReports }}"
        icon="map-pin"
        color="yellow"
        subtitle="+{{ $weeklyMappingReports }} new this week"
        href="{{ route('map.manage') }}" />
    </div>

    <div class="ui-reveal" style="--i:4">
    <x-dashboard.stat-card
        :index="4"
        title="PENDING USER REQUESTS"
        value="{{ $pendingRequests ?? $pendingApplications ?? 0 }}"
        icon="clipboard-document"
        color="red"
        subtitle="+{{ $weeklyRequests ?? $weeklyApplications ?? 0 }} new this week"
        href="{{ route('user.requests') }}" />
    </div>
</div>
