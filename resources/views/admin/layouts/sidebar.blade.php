<aside
    id="appSidebar"
    :class="{
        'mobile-nav-open': mobileNavOpen
    }"
    class="fixed top-0 left-0 h-screen bg-white dark:bg-slate-900 border-r border-gray-100 dark:border-slate-800 shadow-sm flex flex-col justify-between select-none shrink-0 z-50 overflow-visible"
    style="transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
    @click.stop
>

    {{-- COLLAPSE TOGGLE CIRCLE BUTTON (Desktop / Tablet only) --}}
    <button
        @click="toggleSidebar()"
        class="sidebar-collapse-toggle-btn"
        style="position: absolute; top: 22px; right: -12px; width: 25px; height: 25px; border-radius: 50%; background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; color: #64748b; cursor: pointer; z-index: 50; outline: none; transition: all 0.2s ease;"
        onmouseover="this.style.color='#22c55e'; this.style.borderColor='#22c55e';"
        onmouseout="this.style.color='#64748b'; this.style.borderColor='#e2e8f0';"
        title="Toggle Sidebar"
    >
        <span x-text="collapsed ? '›' : '‹'" style="font-size: 14px; font-weight: 800; line-height: 1; display: inline-block; margin-top: -1px;"></span>
    </button>

    {{-- 1. TOP LOGO HEADER --}}
    <div class="bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 sidebar-top-logo-header" style="height: 72px; padding: 0 16px; display: flex; align-items: center; justify-content: space-between; overflow: hidden; flex-shrink: 0; box-sizing: border-box;">
        <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
            <img
                src="{{ asset('images/agrico-logo.png') }}"
                style="width: 40px; height: 40px; object-fit: contain; flex-shrink: 0; display: block;"
                alt="AgriCo"
            >
            <div
                class="overflow-hidden whitespace-nowrap sidebar-hide-collapsed"
                style="display: flex; flex-direction: column; justify-content: center;"
            >
                <span style="font-size: 22px !important; font-weight: 900 !important; color: #22c55e !important; margin: 0 0 3px 0; line-height: 1; letter-spacing: -0.02em !important; font-family: system-ui, -apple-system, sans-serif !important; display: block;">
                    AgriCo
                </span>
                <p style="font-size: 11px; color: #9ca3af; font-weight: 500; margin: 0; line-height: 1.2;">
                    Soil Management
                </p>
            </div>
        </div>

        {{-- Mobile Drawer Close Button (Visible only on screens <= 768px) --}}
        <div class="mobile-drawer-close-btn" style="display: none;">
            <x-close-button @click="mobileNavOpen = false" title="Close menu" />
        </div>
    </div>

    {{-- 2. MIDDLE NAVIGATION MENU --}}
    @php
        $authUser = auth()->user() ?? Auth::guard('admin')->user();
        $adminEmail = strtolower(trim((string) ($authUser?->email ?: ($authUser?->Email ?: ''))));
        $rawRole = $authUser?->role ?: 'Admin';
        $isSuperAdmin = ($authUser && method_exists($authUser, 'isSuperAdmin') && $authUser->isSuperAdmin())
            || in_array(strtolower($rawRole), ['superadmin', 'super admin'])
            || in_array($adminEmail, ['agricosite77@gmail.com', 'admin@agrico.com'])
            || strtoupper((string) ($authUser?->user_id ?? $authUser?->admin_id ?? '')) === 'ADM-001';

        $adminIdKey = \App\Models\Admin::currentAdminId();
        $initialSuspendedCount = \Illuminate\Support\Facades\Cache::remember("sb_susp_{$adminIdKey}", 20, function () {
            $q = \App\Models\User::where(function($q) {
                    $q->whereRaw('LOWER(role) = ?', ['student'])
                      ->orWhereNotNull('student_id');
                })
                ->where(function($q) {
                    $q->whereRaw('LOWER(status) = ?', ['suspended'])
                      ->orWhere(function($sub) {
                          $sub->whereNotNull('suspended_until')->where('suspended_until', '>', now());
                      });
                });
            \App\Models\Admin::scopeForCurrentAdmin($q);
            return $q->count();
        });

        $initialPendingCount = \Illuminate\Support\Facades\Cache::remember('sb_pend_req_count', 20, function () {
            return \App\Models\UserRequest::where('status', 'Pending')->count();
        });
    @endphp
    <nav
        x-data="{
            suspendedCount: {{ $initialSuspendedCount }},
            pendingCount: {{ $initialPendingCount }}
        }"
        @sidebar-counts-updated.window="
            if ($event.detail) {
                if (typeof $event.detail.suspendedCount !== 'undefined') suspendedCount = $event.detail.suspendedCount;
                else if (Array.isArray($event.detail) && typeof $event.detail[0]?.suspendedCount !== 'undefined') suspendedCount = $event.detail[0].suspendedCount;
                if (typeof $event.detail.pendingCount !== 'undefined') pendingCount = $event.detail.pendingCount;
                else if (Array.isArray($event.detail) && typeof $event.detail[0]?.pendingCount !== 'undefined') pendingCount = $event.detail[0].pendingCount;
            }
        "
        @request-updated.window="if (pendingCount > 0) pendingCount--"
        class="flex-1 overflow-y-auto min-h-0 px-3 py-3 space-y-1 custom-sidebar-scroll flex flex-col"
    >

        {{-- 1. DASHBOARD --}}
        @php $isActive = request()->routeIs('dashboard'); @endphp
        <a
            href="{{ route('dashboard') }}"
            wire:navigate
            @click="if (screenWidth <= 768) mobileNavOpen = false;"
            class="sidebar-nav-link {{ $isActive ? 'is-active' : '' }}"
            style="position:relative; display:flex; align-items:center; gap:12px; padding:6px 8px; border-radius:16px; font-size:14px; font-weight:{{ $isActive ? '700' : '500' }}; color:{{ $isActive ? '#22c55e' : '#64748b' }}; background:{{ $isActive ? '#eefbe7' : 'transparent' }}; text-decoration:none; width:100%; box-sizing:border-box;"
        >
            @if($isActive)
                <span style="position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:#22c55e; border-radius:0 4px 4px 0;"></span>
            @endif
            <div class="sidebar-nav-icon-wrap" style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:{{ $isActive ? '#22c55e' : '#94a3b8' }};">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                </svg>
            </div>
            <span
                class="whitespace-nowrap sidebar-hide-collapsed"
            >Dashboard</span>
        </a>

        {{-- 2. SOIL SCAN HISTORY --}}
        @php $isActive = request()->routeIs('soil.manage'); @endphp
        <a
            href="{{ route('soil.manage') }}"
            wire:navigate
            @click="if (screenWidth <= 768) mobileNavOpen = false;"
            class="sidebar-nav-link {{ $isActive ? 'is-active' : '' }}"
            style="position:relative; display:flex; align-items:center; gap:12px; padding:6px 8px; border-radius:16px; font-size:14px; font-weight:{{ $isActive ? '700' : '500' }}; color:{{ $isActive ? '#22c55e' : '#64748b' }}; background:{{ $isActive ? '#eefbe7' : 'transparent' }}; text-decoration:none; width:100%; box-sizing:border-box;"
        >
            @if($isActive)
                <span style="position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:#22c55e; border-radius:0 4px 4px 0;"></span>
            @endif
            <div class="sidebar-nav-icon-wrap" style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:{{ $isActive ? '#22c55e' : '#94a3b8' }};">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <span
                class="whitespace-nowrap sidebar-hide-collapsed"
            >Soil Scan History</span>
        </a>

        {{-- 3. STUDENT RECORDS --}}
        @php 
            $isActive = request()->routeIs(['students.record', 'student.record']); 
        @endphp
        <a
            href="{{ route('students.record') }}"
            wire:navigate
            @click="if (screenWidth <= 768) mobileNavOpen = false;"
            class="sidebar-nav-link {{ $isActive ? 'is-active' : '' }}"
            style="position:relative; display:flex; align-items:center; gap:12px; padding:6px 8px; border-radius:16px; font-size:14px; font-weight:{{ $isActive ? '700' : '500' }}; color:{{ $isActive ? '#22c55e' : '#64748b' }}; background:{{ $isActive ? '#eefbe7' : 'transparent' }}; text-decoration:none; width:100%; box-sizing:border-box;"
        >
            @if($isActive)
                <span style="position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:#22c55e; border-radius:0 4px 4px 0;"></span>
            @endif
            <div class="sidebar-nav-icon-wrap" style="position:relative; width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:{{ $isActive ? '#22c55e' : '#94a3b8' }};">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
                <span
                    x-show="suspendedCount > 0"
                    class="sidebar-show-collapsed"
                    style="@if($initialSuspendedCount < 1) display:none !important; @endif position:absolute; top:4px; right:4px; background:#ef4444; width:9px; height:9px; border-radius:50%; border:2px solid #ffffff; box-shadow:0 1px 2px rgba(0,0,0,0.15);"
                ></span>
            </div>
            <span
                class="whitespace-nowrap flex-1 sidebar-hide-collapsed"
            >Student Records</span>
            <span
                x-show="suspendedCount > 0"
                class="sidebar-nav-badge"
                :title="suspendedCount + ' suspended student(s)'"
                x-text="suspendedCount"
                style="@if($initialSuspendedCount < 1) display:none !important; @else display:inline-flex; @endif background:#ef4444; color:#fff; font-size:11px; font-weight:800; min-width:22px; height:22px; padding:0 6px; border-radius:9999px; align-items:center; justify-content:center; margin-left:auto; flex-shrink:0; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.35); box-sizing: border-box;"
            >{{ $initialSuspendedCount }}</span>
        </a>

        {{-- 4. MANAGE MAP --}}
        @php $isActive = request()->routeIs('map.manage'); @endphp
        <a
            href="{{ route('map.manage') }}"
            wire:navigate
            @click="if (screenWidth <= 768) mobileNavOpen = false;"
            class="sidebar-nav-link {{ $isActive ? 'is-active' : '' }}"
            style="position:relative; display:flex; align-items:center; gap:12px; padding:6px 8px; border-radius:16px; font-size:14px; font-weight:{{ $isActive ? '700' : '500' }}; color:{{ $isActive ? '#22c55e' : '#64748b' }}; background:{{ $isActive ? '#eefbe7' : 'transparent' }}; text-decoration:none; width:100%; box-sizing:border-box;"
        >
            @if($isActive)
                <span style="position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:#22c55e; border-radius:0 4px 4px 0;"></span>
            @endif
            <div class="sidebar-nav-icon-wrap" style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:{{ $isActive ? '#22c55e' : '#94a3b8' }};">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                    <line x1="8" y1="2" x2="8" y2="18"/>
                    <line x1="16" y1="6" x2="16" y2="22"/>
                </svg>
            </div>
            <span
                class="whitespace-nowrap sidebar-hide-collapsed"
            >Manage Map</span>
        </a>

        {{-- 4. MANAGE MUNSELL --}}
        @php $isActive = request()->routeIs('munsell-color.manage'); @endphp
        <a
            href="{{ route('munsell-color.manage') }}"
            wire:navigate
            @click="if (screenWidth <= 768) mobileNavOpen = false;"
            class="sidebar-nav-link {{ $isActive ? 'is-active' : '' }}"
            style="position:relative; display:flex; align-items:center; gap:12px; padding:6px 8px; border-radius:16px; font-size:14px; font-weight:{{ $isActive ? '700' : '500' }}; color:{{ $isActive ? '#22c55e' : '#64748b' }}; background:{{ $isActive ? '#eefbe7' : 'transparent' }}; text-decoration:none; width:100%; box-sizing:border-box;"
        >
            @if($isActive)
                <span style="position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:#22c55e; border-radius:0 4px 4px 0;"></span>
            @endif
            <div class="sidebar-nav-icon-wrap" style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:{{ $isActive ? '#22c55e' : '#94a3b8' }};">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
            </div>
            <span
                class="whitespace-nowrap sidebar-hide-collapsed"
            >Manage Munsell</span>
        </a>

        {{-- 5. MANAGE FAQ --}}
        @php $isActive = request()->routeIs('faqs.manage'); @endphp
        <a
            href="{{ route('faqs.manage') }}"
            wire:navigate
            @click="if (screenWidth <= 768) mobileNavOpen = false;"
            class="sidebar-nav-link {{ $isActive ? 'is-active' : '' }}"
            style="position:relative; display:flex; align-items:center; gap:12px; padding:6px 8px; border-radius:16px; font-size:14px; font-weight:{{ $isActive ? '700' : '500' }}; color:{{ $isActive ? '#22c55e' : '#64748b' }}; background:{{ $isActive ? '#eefbe7' : 'transparent' }}; text-decoration:none; width:100%; box-sizing:border-box;"
        >
            @if($isActive)
                <span style="position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:#22c55e; border-radius:0 4px 4px 0;"></span>
            @endif
            <div class="sidebar-nav-icon-wrap" style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:{{ $isActive ? '#22c55e' : '#94a3b8' }};">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <span
                class="whitespace-nowrap sidebar-hide-collapsed"
            >Manage FAQ</span>
        </a>

        {{-- 6. MANAGE USER --}}
        @php $isActive = request()->routeIs('users.manage'); @endphp
        <a
            href="{{ route('users.manage') }}"
            wire:navigate
            @click="if (screenWidth <= 768) mobileNavOpen = false;"
            class="sidebar-nav-link {{ $isActive ? 'is-active' : '' }}"
            style="position:relative; display:flex; align-items:center; gap:12px; padding:6px 8px; border-radius:16px; font-size:14px; font-weight:{{ $isActive ? '700' : '500' }}; color:{{ $isActive ? '#22c55e' : '#64748b' }}; background:{{ $isActive ? '#eefbe7' : 'transparent' }}; text-decoration:none; width:100%; box-sizing:border-box;"
        >
            @if($isActive)
                <span style="position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:#22c55e; border-radius:0 4px 4px 0;"></span>
            @endif
            <div class="sidebar-nav-icon-wrap" style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:{{ $isActive ? '#22c55e' : '#94a3b8' }};">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <span
                class="whitespace-nowrap sidebar-hide-collapsed"
            >Manage User</span>
        </a>


        {{-- 7. USER REQUEST --}}
        @php 
            $isActive = request()->routeIs('user.requests'); 
        @endphp
        <a
            href="{{ route('user.requests') }}"
            wire:navigate
            @click="if (screenWidth <= 768) mobileNavOpen = false;"
            class="sidebar-nav-link {{ $isActive ? 'is-active' : '' }}"
            style="position:relative; display:flex; align-items:center; gap:12px; padding:6px 8px; border-radius:16px; font-size:14px; font-weight:{{ $isActive ? '700' : '500' }}; color:{{ $isActive ? '#22c55e' : '#64748b' }}; background:{{ $isActive ? '#eefbe7' : 'transparent' }}; text-decoration:none; width:100%; box-sizing:border-box;"
        >
            @if($isActive)
                <span style="position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:#22c55e; border-radius:0 4px 4px 0;"></span>
            @endif
            <div class="sidebar-nav-icon-wrap" style="position:relative; width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:{{ $isActive ? '#22c55e' : '#94a3b8' }};">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span
                    x-show="pendingCount > 0"
                    class="sidebar-show-collapsed"
                    style="@if($initialPendingCount < 1) display:none !important; @endif position:absolute; top:4px; right:4px; background:#f59e0b; width:9px; height:9px; border-radius:50%; border:2px solid #ffffff; box-shadow:0 1px 2px rgba(0,0,0,0.15);"
                ></span>
            </div>
            <span
                class="whitespace-nowrap flex-1 sidebar-hide-collapsed"
            >User Request</span>
            <span
                x-show="pendingCount > 0"
                class="sidebar-nav-badge"
                :title="pendingCount + ' pending request(s)'"
                x-text="pendingCount"
                style="@if($initialPendingCount < 1) display:none !important; @else display:inline-flex; @endif background:#f59e0b; color:#fff; font-size:11px; font-weight:800; min-width:22px; height:22px; padding:0 6px; border-radius:9999px; align-items:center; justify-content:center; margin-left:auto; flex-shrink:0; box-shadow: 0 2px 5px rgba(245, 158, 11, 0.35); box-sizing: border-box;"
            >{{ $initialPendingCount }}</span>
        </a>

        {{-- 8. UPLOAD APPLICATION (SUPER ADMIN ONLY) --}}
        @if($isSuperAdmin)
            @php $isActive = request()->routeIs('application.upload'); @endphp
            <a
                href="{{ route('application.upload') }}"
                wire:navigate
                @click="if (screenWidth <= 768) mobileNavOpen = false;"
                class="sidebar-nav-link {{ $isActive ? 'is-active' : '' }}"
                style="position:relative; display:flex; align-items:center; gap:12px; padding:6px 8px; border-radius:16px; font-size:14px; font-weight:{{ $isActive ? '700' : '500' }}; color:{{ $isActive ? '#22c55e' : '#64748b' }}; background:{{ $isActive ? '#eefbe7' : 'transparent' }}; text-decoration:none; width:100%; box-sizing:border-box;"
            >
                @if($isActive)
                    <span style="position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:#22c55e; border-radius:0 4px 4px 0;"></span>
                @endif
                <div class="sidebar-nav-icon-wrap" style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:{{ $isActive ? '#22c55e' : '#94a3b8' }};">
                        <path d="M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 9l5-5 5 5M12 4v12"/>
                    </svg>
                </div>
                <span
                    class="whitespace-nowrap sidebar-hide-collapsed"
                >Upload Application</span>
            </a>
        @endif

        {{-- GO TO WEBSITE LINK --}}
        <a
            href="{{ route('admin.go-to-website') }}"
            @click="if (screenWidth <= 768) mobileNavOpen = false;"
            class="sidebar-nav-link sidebar-goto-web"
            style="position:relative; display:flex; align-items:center; gap:12px; padding:6px 8px; border-radius:16px; font-size:14px; font-weight:500; color:#64748b; background:transparent; text-decoration:none; width:100%; box-sizing:border-box;"
            title="Go to Website"
        >
            <div class="sidebar-nav-icon-wrap" style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:#94a3b8;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="2" y1="12" x2="22" y2="12"/>
                    <line x1="12" y1="2" x2="12" y2="22"/>
                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                </svg>
            </div>
            <span
                class="whitespace-nowrap sidebar-hide-collapsed"
            >Go to Website</span>
        </a>

    </nav>

    {{-- 3. BOTTOM PROFILE & LOGOUT CARD --}}
    @php
        $authUser = auth()->user() ?? Auth::guard('admin')->user();
        $adminName = $authUser?->name ?: ($authUser?->Full_Name ?: ($authUser?->full_name ?: 'User'));
        $adminEmail = strtolower(trim((string) ($authUser?->email ?: ($authUser?->Email ?: ''))));
        $rawRole = $authUser?->role ?: 'Admin';
        $isSuperAdmin = ($authUser && method_exists($authUser, 'isSuperAdmin') && $authUser->isSuperAdmin())
            || in_array(strtolower($rawRole), ['superadmin', 'super admin'])
            || in_array($adminEmail, ['agricosite77@gmail.com', 'admin@agrico.com'])
            || strtoupper((string) ($authUser?->user_id ?? $authUser?->admin_id ?? '')) === 'ADM-001';
        $adminRole = $isSuperAdmin ? 'Super Admin' : ucfirst($rawRole);
        $adminInitial = strtoupper(substr($adminName ?: 'U', 0, 1));
        $adminPhotoUrl = $authUser?->profile_photo_url;
    @endphp

    <div class="sidebar-bottom-profile-card px-3 py-3 border-t border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-1 shrink-0 z-40" style="padding-bottom: max(16px, env(safe-area-inset-bottom, 16px)); box-sizing: border-box;">

        {{-- USER AVATAR & DETAILS --}}
        <div
            @click="Livewire.dispatch('open-admin-profile'); if (screenWidth <= 768) mobileNavOpen = false;"
            class="sidebar-action-item"
            style="display: flex; align-items: center; gap: 12px; padding: 6px 8px; border-radius: 16px; cursor: pointer; transition: background-color 0.2s ease; user-select: none; width: 100%; box-sizing: border-box;"
        >
            @if($adminPhotoUrl)
                <img
                    src="{{ $adminPhotoUrl }}"
                    alt="{{ $adminName }}"
                    style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.15);"
                />
            @else
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #22c55e !important; color: #ffffff !important; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 800; font-size: 15px; box-shadow: 0 2px 6px rgba(34,197,94,0.3); border: none; user-select: none;">
                    {{ $adminInitial }}
                </div>
            @endif
            <div
                class="sidebar-hide-collapsed"
                style="min-width: 0; flex: 1; overflow: hidden;"
            >
                <p style="font-size: 13px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.25; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: -0.2px;">
                    {{ $adminName ?: 'AgriCo' }}
                </p>
                <p style="font-size: 11px; font-weight: 600; color: #16a34a; margin: 2px 0 0 0; white-space: nowrap;">
                    {{ $adminRole }}
                </p>
            </div>
        </div>

        {{-- DARK / LIGHT MODE THEME TOGGLE --}}
        <div
            x-data="{ isDark: document.documentElement.classList.contains('dark') }"
            x-init="window.addEventListener('agrico:theme-changed', (e) => { isDark = !e.detail.isLight; })"
            @click="if (typeof window.agricoToggleTheme === 'function') { window.agricoToggleTheme(); isDark = document.documentElement.classList.contains('dark'); }"
            class="sidebar-action-item"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 6px 8px; border-radius: 16px; background: transparent; border: none; font-size: 13.5px; font-weight: 600; cursor: pointer; transition: background-color 0.2s ease; box-sizing: border-box; user-select: none;"
            title="Toggle Dark / Light Mode"
            role="switch"
            :aria-checked="isDark"
        >
            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                <div style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgba(251, 191, 36, 0.12); color: #f59e0b;" class="dark:bg-slate-800 dark:text-amber-400">
                    <template x-if="!isDark">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="5"/>
                            <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                            <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                        </svg>
                    </template>
                    <template x-if="isDark">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                    </template>
                </div>
                <span class="whitespace-nowrap sidebar-hide-collapsed text-slate-700 dark:text-slate-200" style="font-weight: 700;" x-text="isDark ? 'Dark Mode' : 'Light Mode'">
                </span>
            </div>

            {{-- Toggle Pill Switch --}}
            <div class="sidebar-hide-collapsed" style="width: 38px; height: 22px; border-radius: 999px; background: #e2e8f0; position: relative; transition: background 0.25s ease; flex-shrink: 0;" :style="isDark ? 'background: #22c55e;' : 'background: #cbd5e1;'">
                <div style="width: 16px; height: 16px; border-radius: 50%; background: #ffffff; position: absolute; top: 3px; left: 3px; transition: transform 0.25s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.25);" :style="isDark ? 'transform: translateX(16px);' : 'transform: translateX(0);'"></div>
            </div>
        </div>

        {{-- RED LOGOUT BUTTON --}}
        <form method="POST" action="{{ route('admin.logout') }}" style="margin: 0; width: 100%;">
            @csrf
            <button
                type="submit"
                class="sidebar-logout-item"
                style="width: 100%; display: flex; align-items: center; gap: 12px; padding: 6px 8px; border-radius: 16px; background: transparent; border: none; font-size: 13.5px; font-weight: 600; color: #ef4444; cursor: pointer; transition: background-color 0.2s ease; box-sizing: border-box;"
            >
                <div style="width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <span
                    class="whitespace-nowrap sidebar-hide-collapsed"
                    style="color: #ef4444; font-weight: 700;"
                >Logout</span>
            </button>
        </form>
    </div>

</aside>
