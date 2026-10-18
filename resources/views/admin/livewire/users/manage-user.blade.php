<div class="space-y-6">
    <style>
        @keyframes modalBackdropFadeIn {
            0% { opacity: 0; backdrop-filter: blur(0px); }
            100% { opacity: 1; backdrop-filter: blur(8px); }
        }
        @keyframes modalPopIn {
            0% { opacity: 0; transform: scale(0.92) translateY(14px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .masked-password-input {
            -webkit-text-security: disc !important;
        }
        .unmasked-password-input {
            -webkit-text-security: none !important;
        }
        .modal-animated-backdrop {
            position: fixed !important;
            inset: 0 !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            min-width: 100% !important;
            height: 100vh !important;
            height: 100dvh !important;
            min-height: 100% !important;
            margin: 0 !important;
            z-index: 99999 !important;
            box-sizing: border-box !important;
            background: rgba(0, 0, 0, 0.45) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            overscroll-behavior: contain !important;
            animation: modalBackdropFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .modal-animated-card {
            animation: modalPopIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .modal-footer-btns {
            padding: 16px 24px 20px !important;
            display: flex !important;
            gap: 12px !important;
            background: #ffffff !important;
            border-top: 1px solid #f1f5f9 !important;
            width: 100% !important;
            box-sizing: border-box !important;
            align-items: center !important;
        }
        @media (max-width: 480px) {
            .modal-footer-btns {
                padding: 12px 16px 16px !important;
                gap: 8px !important;
            }
            .modal-footer-btns button {
                padding-left: 8px !important;
                padding-right: 8px !important;
                font-size: 13px !important;
            }
        }
        /* Mobile Responsive Table (< 680px) */
        @media (max-width: 679px) {
            .user-table-container {
                overflow-x: hidden !important;
                width: 100% !important;
            }
            .user-table {
                table-layout: fixed !important;
                width: 100% !important;
                min-width: 0 !important;
            }
            .col-user-email,
            .col-user-role,
            .col-user-joined {
                display: none !important;
            }
            .col-user-name {
                width: auto !important;
                padding-left: 12px !important;
                padding-right: 6px !important;
            }
            .col-user-status {
                width: 96px !important;
                padding-left: 4px !important;
                padding-right: 4px !important;
            }
            .col-sticky-action {
                width: 108px !important;
                min-width: 108px !important;
                max-width: 108px !important;
                padding-left: 6px !important;
                padding-right: 10px !important;
                text-align: center !important;
            }
            .user-table th,
            .user-table td {
                padding-top: 12px !important;
                padding-bottom: 12px !important;
                box-sizing: border-box !important;
            }
        }
    </style>

    {{-- PAGE HEADER & ONLINE STATUS BADGE --}}
    <div class="ui-reveal" style="--i:0; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
        <div>
            <h1 class="ui-reveal-text" style="--i:0; font-size:32px; font-weight:800; color:#0f172a; margin:0; line-height:1.2; letter-spacing:-0.02em;">
                Manage Users
            </h1>
            <p class="ui-reveal-text" style="--i:0; font-size:13.5px; font-weight:500; color:#94a3b8; margin:4px 0 0 0; line-height:1.4;">
                {{ $totalRegistered }} registered users — {{ $onlineCount }} currently online
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-bold text-gray-700 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 hover:text-gray-900 transition"
                title="Back to Dashboard"
            >
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>

            {{-- ONLINE / OFFLINE STATUS PILL --}}
            <div class="ui-reveal-text" style="--i:0; background:#ffffff; border:1px solid #f1f5f9; border-radius:9999px; padding:4px 6px; box-shadow:0 2px 8px rgba(0,0,0,0.04); display:inline-flex; align-items:center; gap:8px; flex-shrink:0;">
                {{-- GREEN ONLINE PILL --}}
                <span style="background:#dcfce7; color:#22c55e; border-radius:9999px; padding:4px 14px; font-size:12.5px; font-weight:700; display:inline-flex; align-items:center; white-space:nowrap;">
                    {{ $onlineCount }} Online
                </span>

                {{-- VERTICAL DIVIDER --}}
                <span style="width:1px; height:14px; background:#cbd5e1; display:inline-block;"></span>

                {{-- GRAY OFFLINE TEXT --}}
                <span style="color:#64748b; font-size:12.5px; font-weight:600; padding-right:12px; display:inline-flex; align-items:center; white-space:nowrap;">
                    {{ $offlineCount }} Offline
                </span>
            </div>
        </div>
    </div>

    {{-- SEARCH AND FILTER CARD --}}
    <div class="ui-reveal" style="--i:1; background:#ffffff; border-radius:20px; border:1px solid #f1f5f9; box-shadow:0 1px 3px rgba(0,0,0,0.03); padding:14px 20px; display:flex; align-items:center; justify-content:space-between; gap:14px; width:100%; box-sizing:border-box; flex-wrap:wrap;">
        {{-- SEARCH INPUT WITH AUTO-SUGGESTIONS --}}
        <div x-data="{ open: true }" @click.outside="open = false" style="position:relative; flex:1; min-width:220px;">
            <svg style="position:absolute; left:16px; top:50%; transform:translateY(-50%); width:18px; height:18px; color:#94a3b8; z-index:2;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input
                wire:key="user-search-input-{{ $showEditModal || $showSuspendModal || $showDeleteModal ? 'modal-open' : 'modal-closed' }}"
                wire:model.live="search"
                @focus="open = true"
                @input="open = true"
                @keydown.escape="$wire.set('search', ''); $el.value = ''; open = false;"
                type="text"
                name="agrico_user_search_filter"
                autocomplete="off"
                placeholder="Search name, email, role, User ID"
                style="width:100%; border:1px solid #e2e8f0; border-radius:16px; background:#ffffff; padding:10px 44px 10px 46px; font-size:14px; font-weight:500; color:#1e293b; outline:none; transition:all 0.2s ease; box-sizing:border-box;"
                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3px rgba(68,175,53,0.15)';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
            />
            @if($search)
                <x-search-clear-button wireModel="search" />
            @endif

            {{-- AUTO SUGGESTION DROPDOWN --}}
            @if(!empty($searchSuggestions) && count($searchSuggestions) > 0)
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    style="position:absolute; top:calc(100% + 6px); left:0; right:0; z-index:50; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05); max-height:300px; overflow-y:auto; padding:6px 0;"
                >
                    <div style="padding:6px 14px 4px 14px; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em;">
                        User Suggestions
                    </div>
                    @foreach($searchSuggestions as $u)
                        @php
                            $uName = $u->name ?: 'User';
                            $uEmail = $u->email ?: '';
                            $uRole = in_array(strtolower($u->role ?? ''), ['superadmin', 'super admin']) ? 'Super Admin' : ucfirst($u->role ?? 'User');
                            $uInitial = strtoupper(substr($uName, 0, 1));
                            $suggestVal = $uName ?: $uEmail;
                        @endphp
                        <div
                            wire:click="selectSuggestion('{{ addslashes($suggestVal) }}')"
                            @click="open = false"
                            style="padding:8px 14px; display:flex; align-items:center; justify-content:space-between; cursor:pointer; transition:background 0.15s ease;"
                            onmouseover="this.style.background='#f8fafc';"
                            onmouseout="this.style.background='transparent';"
                        >
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:28px; height:28px; border-radius:50%; background:#dcfce7; color:#22c55e; font-weight:800; font-size:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    {{ $uInitial }}
                                </div>
                                <div>
                                    <div style="font-size:13.5px; font-weight:700; color:#1e293b;">
                                        {{ $uName }}
                                    </div>
                                    <div style="font-size:11.5px; color:#64748b;">
                                        {{ $uEmail }} &bull; Role: <strong style="color:#334155;">{{ $uRole }}</strong> (ID: #{{ $u->id }})
                                    </div>
                                </div>
                            </div>
                            <span style="font-size:11px; font-weight:700; background:#f0fdf4; color:#22c55e; padding:3px 9px; border-radius:12px;">
                                Select
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- FILTER CONTROLS FLEX CONTAINER --}}
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            {{-- FUNNEL ICON --}}
            <svg width="18" height="18" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="flex-shrink:0;">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>

            {{-- FILTER DROPDOWNS --}}
            <select
                wire:key="role-filter-{{ $showEditModal || $showSuspendModal || $showDeleteModal ? 'open' : 'closed' }}"
                wire:model.live="roleFilter"
                style="border:1px solid #e2e8f0; border-radius:14px; background:#ffffff; padding:8px 14px; font-size:13px; font-weight:600; color:#334155; outline:none; cursor:pointer; transition:all 0.2s ease; flex-shrink:0;"
                onfocus="this.style.borderColor='#22c55e';"
                onblur="this.style.borderColor='#e2e8f0';"
            >
                <option value="">All Roles</option>
                @if($this->isSuperAdminActor())
                    <option value="superadmin">Super Admin</option>
                    <option value="admin">Admin</option>
                @endif
                <option value="user">User</option>
                <option value="student">Student</option>
            </select>

            <select
                wire:key="status-filter-{{ $showEditModal || $showSuspendModal || $showDeleteModal ? 'open' : 'closed' }}"
                wire:model.live="statusFilter"
                style="border:1px solid #e2e8f0; border-radius:14px; background:#ffffff; padding:8px 14px; font-size:13px; font-weight:600; color:#334155; outline:none; cursor:pointer; transition:all 0.2s ease; flex-shrink:0;"
                onfocus="this.style.borderColor='#22c55e';"
                onblur="this.style.borderColor='#e2e8f0';"
            >
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Suspended">Suspended</option>
                <option value="Inactive">Inactive</option>
                <option value="Disapproved">Disapproved</option>
            </select>

            <select
                wire:key="connectivity-filter-{{ $showEditModal || $showSuspendModal || $showDeleteModal ? 'open' : 'closed' }}"
                wire:model.live="connectivityFilter"
                style="border:1px solid #e2e8f0; border-radius:14px; background:#ffffff; padding:8px 14px; font-size:13px; font-weight:600; color:#334155; outline:none; cursor:pointer; transition:all 0.2s ease; flex-shrink:0;"
                onfocus="this.style.borderColor='#22c55e';"
                onblur="this.style.borderColor='#e2e8f0';"
            >
                <option value="">All Connectivity</option>
                <option value="Online">Online</option>
                <option value="Offline">Offline</option>
            </select>

            {{-- CALENDAR DATE FILTER --}}
            <div style="display:inline-flex; align-items:center; gap:8px; border:1px solid #e2e8f0; border-radius:14px; background:#ffffff; padding:6px 14px; transition:all 0.2s ease; flex-shrink:0;" onmouseover="this.style.borderColor='#22c55e';" onmouseout="if(!document.activeElement || document.activeElement!==this.querySelector('input')) this.style.borderColor='#e2e8f0';">
                <svg width="16" height="16" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="flex-shrink:0;">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <input
                    wire:key="date-filter-{{ $showEditModal || $showSuspendModal || $showDeleteModal ? 'open' : 'closed' }}"
                    wire:model.live="selectedDate"
                    type="date"
                    style="border:none; background:transparent; font-size:13px; font-weight:600; color:#334155; outline:none; cursor:pointer; font-family:inherit;"
                    title="Filter by Date"
                />
                @if($selectedDate)
                    <button
                        wire:click="$set('selectedDate', '')"
                        type="button"
                        style="background:transparent; border-radius:50%; width:20px; height:20px; display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; color:#94a3b8; margin-left:2px; transition:all 0.2s ease;"
                        onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a';"
                        onmouseout="this.style.background='transparent'; this.style.color='#94a3b8';"
                        title="Clear Date Filter"
                        aria-label="Clear Date Filter"
                    >
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

            {{-- RESULTS COUNTER --}}
            <div style="font-size:13px; font-weight:500; color:#94a3b8; flex-shrink:0; margin-left:4px;">
                {{ $users->total() }} results
            </div>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="ui-reveal bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden user-table-card w-full max-w-full min-w-0 box-border" style="--i:2">
        {{-- CARD HEADER --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
            <h2 class="ui-reveal-text text-base font-bold text-gray-900 dark:text-slate-100" style="--i:2">
                Manage Users ({{ $users->total() }})
            </h2>
        </div>

        <div class="overflow-x-auto user-table-container no-scrollbar">
            <table class="w-full user-table min-w-0 md:min-w-[1000px]">
                <thead>
                    <tr class="bg-[#F1FBF1] border-b border-gray-100">

                        {{-- FULL NAME --}}
                        <th
                            wire:click="sortBy('name')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-user-name"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">
                                    <span class="user-title-desktop">FULL NAME USER</span>
                                    <span class="user-title-mobile">NAME</span>
                                </span>
                                <span class="sort-arrows">
                                    @if($sortField === 'name')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- EMAIL --}}
                        <th
                            wire:click="sortBy('email')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-user-email"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">EMAIL</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'email')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- ROLE --}}
                        <th
                            wire:click="sortBy('role')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-user-role"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">ROLE</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'role')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- JOINED --}}
                        <th
                            wire:click="sortBy('created_at')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-user-joined"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">JOINED</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'created_at')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- STATUS --}}
                        <th
                            wire:click="sortBy('status')"
                            class="py-4 text-center text-xs font-bold text-gray-500 cursor-pointer select-none group whitespace-nowrap col-user-status"
                        >
                            <div class="flex items-center justify-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">STATUS</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'status')
                                        @if($sortDirection === 'asc')
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-up" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 9L7 5.5L10 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @else
                                            <svg class="sort-chevron sort-chevron-active sort-chevron-down" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M4 5L7 8.5L10 5" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    @else
                                        <span class="sort-chevron-pair">
                                            <svg class="sort-chevron-idle sort-chevron-idle-up" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 5L5 1L9 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <svg class="sort-chevron-idle sort-chevron-idle-down" width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </th>

                        {{-- ACTIONS --}}
                        <th class="py-4 text-center text-xs font-bold text-gray-500 whitespace-nowrap col-sticky-action">
                            <span class="user-action-title-desktop">ACTIONS</span>
                            <span class="user-action-title-mobile">ACTION</span>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                    @php
                        $authAdminEmail = strtolower(trim(Auth::user()?->Email ?? Auth::user()?->email ?? ''));
                        if (!empty($authAdminEmail) && strtolower(trim($user->email)) === $authAdminEmail) {
                            continue;
                        }

                        $isOnline = (bool) ($user->is_online ?? false);
                        $scans = $user->soil_scans_count ?? 0;

                        $statusLower = strtolower(trim($user->status ?? ''));
                        if ($statusLower === 'disapproved') {
                            $isSuspended = true;
                            $statusStr = 'Disapproved';
                            $statusStyle = 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;';
                            $dotStyle = 'background:#b91c1c;';
                        } elseif ($statusLower === 'inactive') {
                            $isSuspended = true;
                            $statusStr = 'Inactive';
                            $statusStyle = 'background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0;';
                            $dotStyle = 'background:#64748b;';
                        } elseif ($statusLower === 'suspended') {
                            $isSuspended = true;
                            $statusStr = 'Suspended';
                            $statusStyle = 'background:#fef2f2; color:#ef4444; border:1px solid #fee2e2;';
                            $dotStyle = 'background:#ef4444;';
                        } else {
                            $isSuspended = false;
                            $statusStr = 'Active';
                            $statusStyle = 'background:#dcfce7; color:#22c55e; border:1px solid #bbf7d0;';
                            $dotStyle = 'background:#22c55e;';
                        }
                        $searchTrim = trim($search);
                        $uName = $user->name ?? '';
                        $uEmail = $user->email ?? '';
                        $uRole = $user->role ?? '';
                        $uFmtId = $user->formatted_id ?? '';
                        $uJoined = $user->created_at ? $user->created_at->format('m/d/Y') : '';
                        $isSearchMatch = !empty($searchTrim) && (
                            (!empty($uName) && stripos($uName, $searchTrim) !== false) ||
                            (!empty($uEmail) && stripos($uEmail, $searchTrim) !== false) ||
                            (!empty($uRole) && stripos($uRole, $searchTrim) !== false) ||
                            (!empty($uFmtId) && stripos($uFmtId, $searchTrim) !== false) ||
                            (!empty($uJoined) && stripos($uJoined, $searchTrim) !== false) ||
                            (str_starts_with($searchTrim, '#') && '#' . $user->id === $searchTrim)
                        );
                    @endphp
                    <tr
                        wire:key="user-row-{{ $user->id }}-{{ strtolower($statusStr) }}"
                        x-data="{
                            suspending: false,
                            async doSuspend() {
                                if (this.suspending) return;
                                this.suspending = true;
                                try {
                                    const res = await fetch('/manage-users/{{ $user->id }}/suspend-one-day', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    });
                                    if (!res.ok) throw new Error();

                                    // Tell the Livewire component the underlying data actually changed,
                                    // so counts/notifications/other rows refresh from the real DB state
                                    if (window.Livewire) {
                                        window.Livewire.dispatch('user-suspended');
                                        window.Livewire.dispatch('refresh-notifications');
                                    }
                                } catch (e) {
                                    this.suspending = false;
                                }
                            }
                        }"
                        class="border-b border-gray-100 transition {{ $isSearchMatch ? 'bg-emerald-50/80 border-l-4 border-l-emerald-500 font-medium' : 'hover:bg-green-50/40' }}"
                    >

                        {{-- FULL NAME --}}
                        <td class="px-5 py-5 whitespace-nowrap col-user-name">
                            <div style="min-width: 0; max-width: 100%; overflow: hidden;">
                                <div style="font-weight:700; font-size:14px; color:#6b7280; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $user->name }}">
                                    {{ $user->name }}
                                </div>
                                <div class="hidden md:block" style="font-size:12px; color:#94a3b8; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $isOnline ? 'Online now' : 'Offline' }}
                                </div>
                                <div class="block md:hidden" style="font-size:11px; color:#94a3b8; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $user->email }}">
                                    {{ $user->email }} &bull; {{ ucfirst($user->role ?? 'User') }}
                                </div>
                            </div>
                        </td>

                        {{-- EMAIL --}}
                        <td class="px-5 py-5 text-sm text-gray-500 font-bold whitespace-nowrap font-mono col-user-email">
                            <div class="truncate" title="{{ $user->email }}">
                                {{ $user->email }}
                            </div>
                        </td>

                        {{-- ROLE BADGE --}}
                        <td class="px-5 py-5 whitespace-nowrap col-user-role">
                            @if(in_array(strtolower($user->role), ['superadmin', 'super admin']))
                                <span style="background:#f3e8ff; color:#7e22ce; border:1px solid #e9d5ff; font-size:12px; font-weight:700; padding:3px 12px; border-radius:9999px; display:inline-block;">
                                    Super Admin
                                </span>
                            @elseif(strtolower($user->role) === 'admin')
                                <span style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-size:12px; font-weight:700; padding:3px 12px; border-radius:9999px; display:inline-block;">
                                    Admin
                                </span>
                            @elseif(strtolower($user->role) === 'student')
                                <span style="background:#f8fafc; color:#475569; border:1px solid #e2e8f0; font-size:12px; font-weight:700; padding:3px 12px; border-radius:9999px; display:inline-block;">
                                    Student
                                </span>
                            @else
                                <span style="background:#f0fdf4; color:#22c55e; border:1px solid #dcfce7; font-size:12px; font-weight:700; padding:3px 12px; border-radius:9999px; display:inline-block;">
                                    User
                                </span>
                            @endif
                        </td>

                        {{-- JOINED --}}
                        <td class="px-5 py-5 text-sm text-gray-500 font-bold whitespace-nowrap col-user-joined">
                            {{ $user->created_at?->format('m/d/Y') }}
                        </td>

                        {{-- STATUS BADGE (READ-ONLY DISPLAY - STATUS CHANGES ONLY VIA SUSPEND ACTION ICON/MODAL) --}}
                        <td class="py-5 whitespace-nowrap col-user-status text-center">
                            @if($user->is_pending_deletion)
                                <div class="user-status-dual-pill cursor-pointer select-none group" wire:click="openViewSuspendModal('{{ $user->id }}')" title="Account Scheduled for Auto-Deletion in {{ $user->deletion_countdown }}">
                                    <span class="agrico-status-pill group-hover:scale-105 active:scale-95 transition-all shadow-2xs status-pill-fixed" style="background:#fef2f2; color:#ef4444; border:1px solid #fecaca;">
                                        <span class="status-dot" style="background:#ef4444;"></span>
                                        <span>Suspended</span>
                                    </span>
                                    <span class="agrico-countdown-pill group-hover:scale-105 active:scale-95 transition-all shadow-2xs" style="background:#fffbeb; color:#d97706; border:1px solid #fde68a;">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" class="flex-shrink-0 text-amber-600">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        <span>Del: {{ $user->deletion_countdown }}</span>
                                    </span>
                                </div>
                            @elseif($user->is_suspended_with_timer)
                                <div class="user-status-dual-pill cursor-pointer select-none group" wire:click="openViewSuspendModal('{{ $user->id }}')" title="Suspended: Click to view details & reason (Ends in {{ $user->suspension_countdown }})">
                                    <span class="agrico-status-pill group-hover:scale-105 active:scale-95 transition-all shadow-2xs status-pill-fixed" style="background:#fef2f2; color:#ef4444; border:1px solid #fecaca;">
                                        <span class="status-dot" style="background:#ef4444;"></span>
                                        <span>Suspended</span>
                                    </span>
                                    <span class="agrico-countdown-pill group-hover:scale-105 active:scale-95 transition-all shadow-2xs" style="background:#fef2f2; color:#ef4444; border:1px solid #fecaca;">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 text-rose-500">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <span>{{ $user->suspension_countdown }}</span>
                                    </span>
                                </div>
                            @else
                                @if(strtolower($statusStr) === 'active' && !in_array(strtolower($user->role), ['superadmin', 'super admin']))
                                    {{-- Optimistic: Active button shows instantly as Suspended on click --}}
                                    <template x-if="!suspending">
                                        <button
                                            type="button"
                                            @click="doSuspend()"
                                            title="Click to suspend user for 1 day"
                                            class="agrico-status-pill cursor-pointer hover:opacity-85 hover:scale-105 active:scale-95 transition-all select-none"
                                            style="{{ $statusStyle }}"
                                        >
                                            <span class="status-dot" style="{{ $dotStyle }}"></span>
                                            <span>Active</span>
                                        </button>
                                    </template>
                                    <template x-if="suspending">
                                        <span class="agrico-status-pill select-none" style="background:#fef2f2; color:#ef4444; border:1px solid #fee2e2;">
                                            <span class="status-dot" style="background:#ef4444;"></span>
                                            <span>Suspended</span>
                                        </span>
                                    </template>
                                @elseif(strtolower($statusStr) === 'active')
                                    <span
                                        class="agrico-status-pill select-none"
                                        style="{{ $statusStyle }}"
                                    >
                                        <span class="status-dot" style="{{ $dotStyle }}"></span>
                                        <span>Active</span>
                                    </span>
                                @else
                                    <button
                                        type="button"
                                        wire:click="openViewSuspendModal('{{ $user->id }}')"
                                        title="Click to view suspension details"
                                        class="agrico-status-pill cursor-pointer hover:opacity-85 select-none"
                                        style="{{ $statusStyle }}"
                                    >
                                        <span class="status-dot" style="{{ $dotStyle }}"></span>
                                        <span>{{ $statusStr }}</span>
                                    </button>
                                @endif
                            @endif
                        </td>

                        {{-- ACTIONS --}}
                        <td class="py-5 whitespace-nowrap col-sticky-action text-center">
                            {{-- DESKTOP INLINE ACTIONS --}}
                            <div class="user-desktop-actions">
                                {{-- VIEW USER DETAILS EYE BUTTON --}}
                                <button
                                    onclick="const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                    wire:click="openViewModal('{{ $user->id }}')"
                                    type="button"
                                    class="agrico-view-btn"
                                    title="View User Details"
                                >
                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>

                                {{-- EDIT DETAILS PENCIL --}}
                                <button
                                    onclick="const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                    wire:click="openEditModal('{{ $user->id }}')"
                                    type="button"
                                    class="agrico-edit-btn"
                                    title="Edit User Details"
                                >
                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>

                                {{-- SUSPEND / UNSUSPEND ACTION ICON --}}
                                @if($isSuspended)
                                    <button
                                        onclick="const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                        wire:click="openViewSuspendModal('{{ $user->id }}')"
                                        type="button"
                                        class="agrico-unsuspend-btn"
                                        title="Unsuspend / Reactivate User Account"
                                    >
                                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                            <circle cx="8.5" cy="7" r="4"/>
                                            <polyline points="17 11 19 13 23 9"/>
                                        </svg>
                                    </button>
                                @else
                                    <button
                                        onclick="const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                        wire:click="openSuspendModal('{{ $user->id }}')"
                                        type="button"
                                        class="agrico-suspend-btn"
                                        title="Suspend User Account"
                                    >
                                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                            <circle cx="8.5" cy="7" r="4"/>
                                            <line x1="17" y1="10" x2="22" y2="15"/>
                                            <line x1="22" y1="10" x2="17" y2="15"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- CANCEL 5-DAY DELETION COUNTDOWN BUTTON (IF PENDING DELETION) --}}
                                @if($user->is_pending_deletion)
                                    <button
                                        onclick="const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                        wire:click="openCancelDeletionModal('{{ $user->id }}')"
                                        type="button"
                                        class="agrico-unsuspend-btn"
                                        title="Cancel 5-day deletion countdown and restore user"
                                    >
                                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/>
                                            <path d="M3 3v5h5"/>
                                        </svg>
                                    </button>
                                @endif

                                {{-- DELETE TRASH --}}
                                <button
                                    onclick="const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                    wire:click="openDeleteModal('{{ $user->id }}')"
                                    type="button"
                                    class="agrico-delete-btn"
                                    title="Delete User Account"
                                >
                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- MOBILE MORE ACTIONS "⋮" DROPDOWN --}}
                            <div class="user-mobile-actions" x-data="{ open: false, dropUp: false }" @click.outside="open = false" :style="open ? 'z-index: 60;' : ''">
                                <button
                                    @click="dropUp = ($el.getBoundingClientRect().bottom + 210 > window.innerHeight) || ($el.closest('.user-table-card')?.getBoundingClientRect().bottom - $el.getBoundingClientRect().bottom < 210); open = !open"
                                    type="button"
                                    class="agrico-more-btn"
                                    title="More Actions"
                                    aria-label="More Actions"
                                >
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0;">
                                        <circle cx="12" cy="5" r="2"/>
                                        <circle cx="12" cy="12" r="2"/>
                                        <circle cx="12" cy="19" r="2"/>
                                    </svg>
                                </button>

                                {{-- DROPDOWN MENU --}}
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="agrico-mobile-dropdown"
                                    :style="dropUp ? 'bottom: calc(100% + 6px); top: auto;' : 'top: calc(100% + 6px); bottom: auto;'"
                                >
                                    {{-- VIEW DETAILS --}}
                                    <button
                                        @click="open = false; const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                        wire:click="openViewModal('{{ $user->id }}')"
                                        type="button"
                                        class="agrico-dropdown-item"
                                    >
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:#16a34a; flex-shrink:0;">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <span>View Details</span>
                                    </button>

                                    {{-- EDIT DETAILS --}}
                                    <button
                                        @click="open = false; const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                        wire:click="openEditModal('{{ $user->id }}')"
                                        type="button"
                                        class="agrico-dropdown-item"
                                    >
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:#2563eb; flex-shrink:0;">
                                            <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        <span>Edit User</span>
                                    </button>

                                    {{-- SUSPEND / UNSUSPEND --}}
                                    @if($isSuspended)
                                        <button
                                            @click="open = false; const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                            wire:click="openViewSuspendModal('{{ $user->id }}')"
                                            type="button"
                                            class="agrico-dropdown-item"
                                        >
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:#16a34a; flex-shrink:0;">
                                                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                                <circle cx="8.5" cy="7" r="4"/>
                                                <polyline points="17 11 19 13 23 9"/>
                                            </svg>
                                            <span>Unsuspend</span>
                                        </button>
                                    @else
                                        <button
                                            @click="open = false; const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                            wire:click="openSuspendModal('{{ $user->id }}')"
                                            type="button"
                                            class="agrico-dropdown-item"
                                        >
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:#dc2626; flex-shrink:0;">
                                                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                                <circle cx="8.5" cy="7" r="4"/>
                                                <line x1="17" y1="10" x2="22" y2="15"/>
                                                <line x1="22" y1="10" x2="17" y2="15"/>
                                            </svg>
                                            <span>Suspend</span>
                                        </button>
                                    @endif

                                    {{-- CANCEL DELETION COUNTDOWN --}}
                                    @if($user->is_pending_deletion)
                                        <button
                                            @click="open = false; const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                            wire:click="openCancelDeletionModal('{{ $user->id }}')"
                                            type="button"
                                            class="agrico-dropdown-item"
                                        >
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:#d97706; flex-shrink:0;">
                                                <path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/>
                                                <path d="M3 3v5h5"/>
                                            </svg>
                                            <span>Cancel Deletion</span>
                                        </button>
                                    @endif

                                    <div style="height:1px; background:#f1f5f9; margin:4px 0;"></div>

                                    {{-- DELETE USER --}}
                                    <button
                                        @click="open = false; const input = document.getElementsByName('agrico_user_search_filter')[0]; if (input) { input.value = ''; }"
                                        wire:click="openDeleteModal('{{ $user->id }}')"
                                        type="button"
                                        class="agrico-dropdown-item agrico-dropdown-item-danger"
                                    >
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color:#ef4444; flex-shrink:0;">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>Delete User</span>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 64px 24px; text-align: center; border-bottom: none;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;">
                                <div style="width: 56px; height: 56px; border-radius: 16px; background: #f0fdf4; border: 1px solid #dcfce7; display: flex; align-items: center; justify-content: center; color: #22c55e; box-shadow: 0 4px 14px rgba(22,163,74,0.12);">
                                    <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 010 7.75"/>
                                    </svg>
                                </div>
                                <div style="font-size: 15.5px; font-weight: 800; color: #0f172a; margin-top: 4px;">
                                    No Users Found
                                </div>
                                <div style="font-size: 12.5px; font-weight: 500; color: #64748b; max-width: 360px; line-height: 1.4;">
                                    No registered user accounts matched your search or active filter criteria.
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION FOOTER (MATCHING IMAGE 1) --}}
        <div style="padding: 14px 20px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: #fff;">
            {{-- LEFT SIDE: ROWS PER PAGE & RANGE --}}
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; flex-wrap: wrap;">
                <span style="white-space: nowrap; font-weight: 500;">Rows per page:</span>
                <select
                    wire:model.live="perPage"
                    style="border: 1px solid #e2e8f0; background: #fff; border-radius: 8px; padding: 4px 8px; font-size: 13px; color: #334155; font-weight: 600; outline: none; cursor: pointer;"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
                <span style="color: #94a3b8; font-size: 13px; margin-left: 4px; white-space: nowrap; font-weight: 500;">
                    {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }}
                </span>
            </div>

            {{-- RIGHT SIDE: PREV, PAGE NUMBERS, NEXT BUTTONS --}}
            @if ($users->hasPages())
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: nowrap;">
                    @if ($users->onFirstPage())
                        <button disabled style="padding: 5px 12px; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; color: #cbd5e1; font-size: 12.5px; font-weight: 600; cursor: not-allowed;">Prev</button>
                    @else
                        <button wire:click="previousPage" style="padding: 5px 12px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569';">Prev</button>
                    @endif

                    @php
                        $currentPage = $users->currentPage();
                        $lastPage = $users->lastPage();

                        if ($lastPage <= 3) {
                            $startPage = 1;
                            $endPage = $lastPage;
                        } else {
                            if ($currentPage <= 2) {
                                $startPage = 1;
                                $endPage = 3;
                            } elseif ($currentPage >= $lastPage - 1) {
                                $startPage = $lastPage - 2;
                                $endPage = $lastPage;
                            } else {
                                $startPage = $currentPage - 1;
                                $endPage = $currentPage + 1;
                            }
                        }
                    @endphp

                    @if ($startPage > 1)
                        <button wire:click="gotoPage({{ max(1, $startPage - 1) }})" style="min-width: 24px; height: 32px; background: transparent; border: none; color: #64748b; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; transition: color 0.15s ease;" onmouseover="this.style.color='#22c55e';" onmouseout="this.style.color='#64748b';" title="Page {{ max(1, $startPage - 1) }}">...</button>
                    @endif

                    @foreach ($users->getUrlRange($startPage, $endPage) as $page => $url)
                        @if ($page == $currentPage)
                            <button wire:click="gotoPage({{ $page }})" style="width: 32px; height: 32px; border-radius: 8px; background: #22c55e !important; color: #fff !important; font-size: 13px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(34,197,94,0.35);">{{ $page }}</button>
                        @else
                            <button wire:click="gotoPage({{ $page }})" style="width: 32px; height: 32px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; color: #334155; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#334155';">{{ $page }}</button>
                        @endif
                    @endforeach

                    @if ($endPage < $lastPage)
                        <button wire:click="gotoPage({{ min($lastPage, $endPage + 1) }})" style="min-width: 24px; height: 32px; background: transparent; border: none; color: #64748b; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; transition: color 0.15s ease;" onmouseover="this.style.color='#22c55e';" onmouseout="this.style.color='#64748b';" title="Page {{ min($lastPage, $endPage + 1) }}">...</button>
                    @endif

                    @if ($users->hasMorePages())
                        <button wire:click="nextPage" style="padding: 5px 12px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569';">Next</button>
                    @else
                        <button disabled style="padding: 5px 12px; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; color: #cbd5e1; font-size: 12.5px; font-weight: 600; cursor: not-allowed;">Next</button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- 1. EDIT USER MODAL (2-STEP VERIFICATION) --}}
    @if($showEditModal)
    <div
        x-data
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-50 flex items-center justify-center modal-animated-backdrop"
        style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; min-width: 100%; height: 100vh; height: 100dvh; min-height: 100%; margin: 0; box-sizing: border-box; background: rgba(0,0,0,0.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999;"
        wire:key="edit-modal-wrapper-{{ $isVerified ? 'verified' : 'unverified' }}"
    >
        @if(!$isVerified)
            {{-- STEP 1: ADMIN VERIFICATION REQUIRED MODAL (MATCHING UPLOADED IMAGE 100%) --}}
            <form
                wire:submit.prevent="verifyAdminPassword"
                wire:key="edit-modal-step-1"
                class="modal-animated-card agrico-standard-popup"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                style="background:#ffffff; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); width:100%; max-width:440px; margin:0 16px; overflow:hidden; position:relative;"
            >
                
                {{-- HEADER WITH LOCK BADGE AND CLOSE BUTTON --}}
                <div style="padding:28px 28px 12px 28px; display:flex; align-items:flex-start; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:14px;">
                        <div style="width:44px; height:44px; border-radius:14px; background:#fef3c7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="22" height="22" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0; line-height:1.2;">
                                Admin Verification Required
                            </h3>
                            <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:3px 0 0 0;">
                                Security authorization step
                            </p>
                        </div>
                    </div>

                    <x-close-button wire:click="closeEditModal" />
                </div>

                {{-- BODY --}}
                <div style="padding:12px 28px 20px 28px; display:flex; flex-direction:column; gap:16px;">
                    <p style="font-size:13px; color:#64748b; font-weight:400; line-height:1.5; margin:0;">
                        To protect user credentials, please enter your <strong style="color:#1e293b; font-weight:700;">admin password</strong> to authorize editing account details for <strong style="color:#0f172a; font-weight:700;">{{ $userRoleName }}</strong>.
                    </p>

                    <div x-data="{ showPass: false }">
                        <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">
                            Your Admin Password
                        </label>
                        @php $hasAdminError = $errors->has('adminPassword') || !empty($adminPasswordError); @endphp
                        <div style="position:relative; width:100%;">
                            <input
                                wire:model="adminPassword"
                                type="text"
                                autocomplete="off"
                                data-lpignore="true"
                                data-form-type="other"
                                name="agrico_admin_verification_sec_pin"
                                placeholder="Enter your password..."
                                :class="showPass ? 'unmasked-password-input' : 'masked-password-input'"
                                style="width:100%; height:44px; box-sizing:border-box; border:1.5px solid {{ $hasAdminError ? '#ef4444' : '#cbd5e1' }}; border-radius:12px; padding:0 42px 0 14px; font-size:13.5px; color:#0f172a; background:#ffffff; outline:none; transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1); {{ $hasAdminError ? 'box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2); animation: shakeError 0.4s ease-in-out;' : '' }}"
                                onfocus="this.style.borderColor='{{ $hasAdminError ? '#ef4444' : '#f59e0b' }}'; this.style.boxShadow='{{ $hasAdminError ? '0 0 0 4px rgba(239, 68, 68, 0.25)' : '0 0 0 4px rgba(245,158,11,0.18)' }}'; this.style.transform='translateY(-1px)';"
                                onblur="this.style.borderColor='{{ $hasAdminError ? '#ef4444' : '#cbd5e1' }}'; this.style.boxShadow='{{ $hasAdminError ? '0 0 0 4px rgba(239, 68, 68, 0.2)' : 'none' }}'; this.style.transform='translateY(0)';"
                            />
                            {{-- EYE TOGGLE BUTTON --}}
                            <button
                                type="button"
                                @click="showPass = !showPass"
                                style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px; display:flex; align-items:center; justify-content:center;"
                                :title="showPass ? 'Hide Password' : 'Show Password'"
                            >
                                <svg x-show="!showPass" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                                <svg x-show="showPass" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        @error('adminPassword')
                            <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:6px; display:block;">
                                {{ $message }}
                            </span>
                        @enderror
                        @if($adminPasswordError)
                            <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:6px; display:block;">
                                {{ $adminPasswordError }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- FOOTER BUTTONS --}}
                <div style="padding: 16px 28px 24px 28px; display: flex; gap: 12px; align-items: center; width: 100%; box-sizing: border-box; background: #ffffff; border-top: 1px solid #f1f5f9;">
                    <button
                        type="button"
                        wire:click="closeEditModal"
                        class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                        title="Cancel"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer flex items-center justify-center text-center whitespace-nowrap min-h-[44px]"
                        style="background: #f59e0b !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35); border: none;"
                        onmouseover="this.style.background='#d97706'"
                        onmouseout="this.style.background='#f59e0b'"
                        title="Authorize & Continue"
                    >
                        <span>Authorize & Continue</span>
                    </button>
                </div>
            </form>

        @else
            {{-- STEP 2: EDIT USER DETAILS FORM MODAL --}}
            <form
                wire:submit.prevent="saveUser"
                wire:key="edit-modal-step-2"
                class="modal-animated-card"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                style="background:#ffffff; border-radius:28px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.3); border:1px solid #f1f5f9; width:100%; max-width:680px; height:auto; max-height:calc(100dvh - 24px); margin:auto; display:flex; flex-direction:column; overflow:hidden; position:relative;"
            >

                {{-- HEADER --}}
                <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 26px 16px; border-bottom:1px solid #f1f5f9; flex-shrink:0; background:#ffffff;">
                    <div>
                        <h3 style="font-size:17.5px; font-weight:800; color:#0f172a; margin:0; letter-spacing:-0.01em;">
                            Edit User Details
                        </h3>
                        <p style="font-size:12px; color:#64748b; margin:2px 0 0 0;">
                            Update account information and credentials
                        </p>
                    </div>
                    <x-close-button wire:click="closeEditModal" />
                </div>

                {{-- BODY --}}
                <div style="padding:18px 24px 20px; display:flex; flex-direction:column; gap:14px; overflow-y:auto; flex:1 1 auto; box-sizing:border-box; scrollbar-width:thin; scrollbar-color:#cbd5e1 #f1f5f9;">
                    
                    {{-- CIRCULAR PROFILE PHOTO AVATAR PICKER (EXACT MATCH TO REFERENCE IMAGE) --}}
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 2px;">
                        <label style="position: relative; cursor: pointer; display: inline-block;" title="Click avatar to change picture">
                            <div style="width: 86px; height: 86px; border-radius: 50%; background: #22c55e !important; color: #ffffff !important; display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 4px 16px rgba(34,197,94,0.35); border: none; transition: all 0.2s ease;">
                                @if($editPhoto)
                                    <img src="{{ $editPhoto->temporaryUrl() }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                @elseif($editProfilePhotoUrl)
                                    <img src="{{ $editProfilePhotoUrl }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                @else
                                    <span style="font-size: 36px; font-weight: 800; color: #ffffff !important; font-family: sans-serif; user-select: none;">
                                        {{ $editUserInitial }}
                                    </span>
                                @endif
                            </div>
                            
                            {{-- DARK CAMERA BADGE AT BOTTOM RIGHT --}}
                            <div style="position: absolute; bottom: 0; right: 0; background: #0f172a; color: #ffffff; border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.25);">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                                    <circle cx="12" cy="13" r="4"/>
                                </svg>
                            </div>
                            
                            <input type="file" wire:model="editPhoto" accept="image/*" style="display: none;" />
                        </label>
                        
                        <span style="font-size: 11.5px; font-weight: 600; color: #64748b; margin-top: 6px;">
                            Click avatar to change picture
                        </span>
                        
                        @error('editPhoto')
                            <span style="color:#ef4444; font-size:11.5px; font-weight:600; margin-top:2px;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- FULL NAME --}}
                    <div>
                        <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Full Name</label>
                        <input
                            wire:model="editName"
                            type="text"
                            style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 14px; font-size:13.5px; color:#0f172a; outline:none; transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                            onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 4px rgba(34, 197, 94, 0.18)'; this.style.transform='translateY(-1px)';"
                            onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'; this.style.transform='translateY(0)';"
                        >
                        @error('editName') <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    {{-- CONDITIONAL STUDENT ID & SECTION FIELDS FOR STUDENT ROLE --}}
                    @if(strtolower($editRole) === 'student')
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">
                                Student ID <span style="font-size:11px; font-weight:500; color:#64748b;">(Auto-assigned if blank)</span>
                            </label>
                            <input
                                wire:model="editStudentId"
                                type="text"
                                placeholder="e.g. 2026-00001"
                                style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 12px; font-size:13.5px; color:#0f172a; outline:none; transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 4px rgba(34, 197, 94, 0.18)'; this.style.transform='translateY(-1px)';"
                                onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'; this.style.transform='translateY(0)';"
                            >
                            @error('editStudentId') <span style="color:#ef4444; font-size:11.5px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">
                                Section
                            </label>
                            <input
                                wire:model="editSection"
                                type="text"
                                placeholder="e.g. BSCS 4-A"
                                style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 12px; font-size:13.5px; color:#0f172a; outline:none; transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 4px rgba(34, 197, 94, 0.18)'; this.style.transform='translateY(-1px)';"
                                onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'; this.style.transform='translateY(0)';"
                            >
                            @error('editSection') <span style="color:#ef4444; font-size:11.5px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @endif

                    {{-- EMAIL ADDRESS --}}
                    <div>
                        <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Email Address</label>
                        <input
                            wire:model="editEmail"
                            type="email"
                            style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 14px; font-size:13.5px; color:#0f172a; outline:none; transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                            onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 4px rgba(34, 197, 94, 0.18)'; this.style.transform='translateY(-1px)';"
                            onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'; this.style.transform='translateY(0)';"
                        >
                        @error('editEmail') <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    {{-- NEW PASSWORD (OPTIONAL) & CONFIRM WITH PASSWORD SECURITY --}}
                    <div x-data="{
                        newPass: '',
                        showNewPass: false,
                        showConfirmPass: false,
                        get reqLength() { return this.newPass.length >= 8; },
                        get reqNumber() { return /[0-9]/.test(this.newPass); },
                        get reqUpper() { return /[A-Z]/.test(this.newPass); },
                        get reqLower() { return /[a-z]/.test(this.newPass); },
                        get reqSpecial() { return /[^A-Za-z0-9]/.test(this.newPass); },
                        get score() {
                            if (!this.newPass) return 0;
                            return (this.reqLength ? 1 : 0) +
                                   (this.reqNumber ? 1 : 0) +
                                   (this.reqUpper ? 1 : 0) +
                                   (this.reqLower ? 1 : 0) +
                                   (this.reqSpecial ? 1 : 0);
                        },
                        get barColor() {
                            const colors = { 1: '#ef4444', 2: '#f97316', 3: '#eab308', 4: '#84cc16', 5: '#22c55e' };
                            return colors[this.score] || '#e2e8f0';
                        }
                    }">
                        {{-- PASSWORD SECURITY HEADER WITH 5 PROGRESS BARS --}}
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <span style="font-size:12px; font-weight:700; color:#475569; display:flex; align-items:center; gap:6px;">
                                Password Security
                            </span>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div id="adminSecurityBars" style="display:flex; align-items:center; gap:4px;" title="Password Security Level">
                                    <span class="admin-sec-progress-bar" id="admin-sec-bar-1" :style="{ backgroundColor: (score >= 1) ? barColor : '#e2e8f0' }"></span>
                                    <span class="admin-sec-progress-bar" id="admin-sec-bar-2" :style="{ backgroundColor: (score >= 2) ? barColor : '#e2e8f0' }"></span>
                                    <span class="admin-sec-progress-bar" id="admin-sec-bar-3" :style="{ backgroundColor: (score >= 3) ? barColor : '#e2e8f0' }"></span>
                                    <span class="admin-sec-progress-bar" id="admin-sec-bar-4" :style="{ backgroundColor: (score >= 4) ? barColor : '#e2e8f0' }"></span>
                                    <span class="admin-sec-progress-bar" id="admin-sec-bar-5" :style="{ backgroundColor: (score >= 5) ? barColor : '#e2e8f0' }"></span>
                                </div>
                            </div>
                        </div>

                        {{-- 2-COLUMN INPUTS (NEW PASSWORD & CONFIRM) --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            {{-- NEW PASSWORD --}}
                            <div>
                                <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">New Password (Optional)</label>
                                @php $hasNewPassError = $errors->has('editPassword'); @endphp
                                <div style="position:relative; width:100%;">
                                    <!-- Lock Icon Left -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="{{ $hasNewPassError ? '#ef4444' : '#64748b' }}" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:17px; height:17px; pointer-events:none; z-index:2;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>

                                    <input
                                        wire:model="editPassword"
                                        @input="newPass = $event.target.value"
                                        type="text"
                                        autocomplete="off"
                                        data-lpignore="true"
                                        data-form-type="other"
                                        name="user_new_credential_sec"
                                        placeholder="Leave blank to keep"
                                        :class="showNewPass ? 'unmasked-password-input' : 'masked-password-input'"
                                        style="width:100%; height:42px; box-sizing:border-box; border:1.5px solid {{ $hasNewPassError ? '#ef4444' : '#cbd5e1' }}; border-radius:12px; padding:0 38px 0 36px; font-size:13px; color:#0f172a; background:#ffffff; outline:none; transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1); {{ $hasNewPassError ? 'box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2); animation: shakeError 0.4s ease-in-out;' : '' }}"
                                        onfocus="this.style.borderColor='{{ $hasNewPassError ? '#ef4444' : '#22c55e' }}'; this.style.boxShadow='{{ $hasNewPassError ? '0 0 0 4px rgba(239, 68, 68, 0.25)' : '0 0 0 4px rgba(34, 197, 94, 0.18)' }}'; this.style.transform='translateY(-1px)';"
                                        onblur="this.style.borderColor='{{ $hasNewPassError ? '#ef4444' : '#cbd5e1' }}'; this.style.boxShadow='{{ $hasNewPassError ? '0 0 0 4px rgba(239, 68, 68, 0.2)' : 'none' }}'; this.style.transform='translateY(0)';"
                                    >
                                    <button
                                        type="button"
                                        @click="showNewPass = !showNewPass"
                                        style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px; display:flex; align-items:center; justify-content:center; z-index:3;"
                                        :title="showNewPass ? 'Hide Password' : 'Show Password'"
                                    >
                                        <svg x-show="!showNewPass" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                            <line x1="1" y1="1" x2="23" y2="23"/>
                                        </svg>
                                        <svg x-show="showNewPass" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('editPassword') <span style="color:#ef4444; font-size:11.5px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                            </div>

                            {{-- CONFIRM PASSWORD --}}
                            <div>
                                <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Confirm Password</label>
                                @php $hasConfirmPassError = $errors->has('editPasswordConfirmation'); @endphp
                                <div style="position:relative; width:100%;">
                                    <!-- Lock Icon Left -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="{{ $hasConfirmPassError ? '#ef4444' : '#64748b' }}" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:17px; height:17px; pointer-events:none; z-index:2;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>

                                    <input
                                        wire:model="editPasswordConfirmation"
                                        type="text"
                                        autocomplete="off"
                                        data-lpignore="true"
                                        data-form-type="other"
                                        name="user_confirm_credential_sec"
                                        placeholder="Confirm password"
                                        :class="showConfirmPass ? 'unmasked-password-input' : 'masked-password-input'"
                                        style="width:100%; height:42px; box-sizing:border-box; border:1.5px solid {{ $hasConfirmPassError ? '#ef4444' : '#cbd5e1' }}; border-radius:12px; padding:0 38px 0 36px; font-size:13px; color:#0f172a; background:#ffffff; outline:none; transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1); {{ $hasConfirmPassError ? 'box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2); animation: shakeError 0.4s ease-in-out;' : '' }}"
                                        onfocus="this.style.borderColor='{{ $hasConfirmPassError ? '#ef4444' : '#22c55e' }}'; this.style.boxShadow='{{ $hasConfirmPassError ? '0 0 0 4px rgba(239, 68, 68, 0.25)' : '0 0 0 4px rgba(34, 197, 94, 0.18)' }}'; this.style.transform='translateY(-1px)';"
                                        onblur="this.style.borderColor='{{ $hasConfirmPassError ? '#ef4444' : '#cbd5e1' }}'; this.style.boxShadow='{{ $hasConfirmPassError ? '0 0 0 4px rgba(239, 68, 68, 0.2)' : 'none' }}'; this.style.transform='translateY(0)';"
                                    >
                                    <button
                                        type="button"
                                        @click="showConfirmPass = !showConfirmPass"
                                        style="position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px; display:flex; align-items:center; justify-content:center; z-index:3;"
                                        :title="showConfirmPass ? 'Hide Password' : 'Show Password'"
                                    >
                                        <svg x-show="!showConfirmPass" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                            <line x1="1" y1="1" x2="23" y2="23"/>
                                        </svg>
                                        <svg x-show="showConfirmPass" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('editPasswordConfirmation') <span style="color:#ef4444; font-size:11.5px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- REQUIREMENT PILLS MATCHING IMAGE 1 --}}
                        <div class="admin-req-pill-container" id="adminReqPillContainer">
                            <!-- Row 1: 8+ characters, Number, Uppercase letter -->
                            <div class="admin-req-pill-row">
                                <div class="admin-req-pill" :class="{ 'is-met': reqLength }">
                                    <span class="admin-req-pill-icon">
                                        <svg x-show="!reqLength" class="icon-x" width="7" height="7" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="3.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                        <svg x-show="reqLength" class="icon-check" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3.2" stroke-linecap="round" style="display: none;"><path d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span>8+ characters</span>
                                </div>

                                <div class="admin-req-pill" :class="{ 'is-met': reqNumber }">
                                    <span class="admin-req-pill-icon">
                                        <svg x-show="!reqNumber" class="icon-x" width="7" height="7" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="3.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                        <svg x-show="reqNumber" class="icon-check" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3.2" stroke-linecap="round" style="display: none;"><path d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span>Number</span>
                                </div>

                                <div class="admin-req-pill" :class="{ 'is-met': reqUpper }">
                                    <span class="admin-req-pill-icon">
                                        <svg x-show="!reqUpper" class="icon-x" width="7" height="7" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="3.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                        <svg x-show="reqUpper" class="icon-check" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3.2" stroke-linecap="round" style="display: none;"><path d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span>Uppercase letter</span>
                                </div>
                            </div>

                            <!-- Row 2: Lowercase letter, Special character -->
                            <div class="admin-req-pill-row">
                                <div class="admin-req-pill" :class="{ 'is-met': reqLower }">
                                    <span class="admin-req-pill-icon">
                                        <svg x-show="!reqLower" class="icon-x" width="7" height="7" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="3.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                        <svg x-show="reqLower" class="icon-check" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3.2" stroke-linecap="round" style="display: none;"><path d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span>Lowercase letter</span>
                                </div>

                                <div class="admin-req-pill" :class="{ 'is-met': reqSpecial }">
                                    <span class="admin-req-pill-icon">
                                        <svg x-show="!reqSpecial" class="icon-x" width="7" height="7" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="3.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                        <svg x-show="reqSpecial" class="icon-check" width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3.2" stroke-linecap="round" style="display: none;"><path d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span>Special character</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ROLE, STATUS & ADMIN ID GRID --}}
                    @php
                        $isSuperAdminActor = $this->isSuperAdminActor();
                        $isMainSuperAdminAccount = (strtolower(trim($editEmail ?? '')) === 'admin@agrico.com');
                        $isRoleDisabled = $isMainSuperAdminAccount;
                    @endphp
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px;">
                        <div>
                            <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Role</label>
                            <select
                                wire:model.live="editRole"
                                @if($isRoleDisabled) disabled @endif
                                style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 12px; font-size:13.5px; color:#0f172a; background:{{ $isRoleDisabled ? '#f8fafc' : '#fff' }}; outline:none; cursor:{{ $isRoleDisabled ? 'not-allowed' : 'pointer' }}; transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 4px rgba(34, 197, 94, 0.18)'; this.style.transform='translateY(-1px)';"
                                onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'; this.style.transform='translateY(0)';"
                            >
                                @if($isSuperAdminActor)
                                    <option value="superadmin">Super Admin</option>
                                    <option value="admin">Admin</option>
                                @elseif(in_array(strtolower($editRole), ['admin', 'superadmin']))
                                    <option value="{{ $editRole }}">{{ in_array(strtolower($editRole), ['superadmin', 'super admin']) ? 'Super Admin' : 'Admin' }}</option>
                                @endif
                                <option value="user">User</option>
                                <option value="student">Student</option>
                            </select>
                            @if($isMainSuperAdminAccount)
                                <span style="font-size:11px; font-weight:600; color:#64748b; margin-top:4px; display:block;">🔒 Main Super Admin role locked</span>
                            @endif
                            @error('editRole') <span style="color:#ef4444; font-size:11.5px; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Status</label>
                            <select
                                wire:model="editStatus"
                                style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 12px; font-size:13.5px; color:#0f172a; background:#fff; outline:none; cursor:pointer; transition:all 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 4px rgba(34, 197, 94, 0.18)'; this.style.transform='translateY(-1px)';"
                                onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'; this.style.transform='translateY(0)';"
                            >
                                <option value="Active">Active</option>
                                <option value="Suspended">Suspended</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Disapproved">Disapproved</option>
                            </select>
                        </div>

                        <div>
                            <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">Admin ID</label>
                            <input
                                wire:model="admin_id"
                                type="text"
                                readonly
                                placeholder="ADM-001"
                                style="width:100%; height:42px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 12px; font-size:13.5px; color:#64748b; background:#f8fafc; outline:none; cursor:not-allowed;"
                            >
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div style="padding: 16px 24px 20px; border-top: 1px solid #f1f5f9; background: #ffffff; display: flex; gap: 12px; align-items: center; width: 100%; box-sizing: border-box; flex-shrink: 0;">
                    <button
                        type="button"
                        wire:click="closeEditModal"
                        class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                        title="Cancel"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-3 rounded-full bg-green-500 hover:bg-green-600 text-white font-bold text-sm shadow-md shadow-green-500/35 transition cursor-pointer flex items-center justify-center text-center"
                        style="box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);"
                        title="Save Changes"
                    >
                        <span>Save Changes</span>
                    </button>
                </div>

            </form>
        @endif
    </div>
    @endif

    {{-- 2. SUSPEND USER MODAL --}}
    @if($showSuspendModal && $suspendTargetUser)
    @php
        $isCurrentlySuspended = (ucfirst($suspendTargetUser->status) === 'Suspended' || strtolower($suspendTargetUser->status) === 'inactive');
    @endphp
    <div
        class="fixed inset-0 z-50 flex items-center justify-center modal-animated-backdrop"
        style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; min-width: 100%; height: 100vh; height: 100dvh; min-height: 100%; margin: 0; box-sizing: border-box; background: rgba(0,0,0,0.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999;"
    >
        <div class="modal-animated-card agrico-standard-popup" style="background:#fff; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); width:100%; max-width:480px; margin:0 16px; overflow:hidden;">
            <div style="padding:28px 28px 12px 28px; display:flex; align-items:flex-start; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:44px; height:44px; border-radius:14px; background:#FEF3C7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="22" height="22" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0;">
                            {{ $isCurrentlySuspended ? 'Unsuspend User Account' : 'Suspend User Account' }}
                        </h3>
                        <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:3px 0 0 0;">
                            {{ $isCurrentlySuspended ? 'Reactivate access for user' : 'Set temporary access restriction & reason' }}
                        </p>
                    </div>
                </div>
                <x-close-button wire:click="closeSuspendModal" />
            </div>

            @if($isCurrentlySuspended)
                <div style="padding:12px 28px 24px 28px;">
                    <p style="font-size:13.5px; color:#475569; margin:0; line-height:1.5;">
                        Are you sure you want to unsuspend account for <strong style="color:#0f172a;">{{ $suspendTargetUser->name }}</strong>? The suspension timer will be cleared and account restored to <strong style="color:#22c55e;">Active</strong>.
                    </p>
                </div>
                <div class="modal-footer-btns">
                    <button
                        type="button"
                        wire:click="closeSuspendModal"
                        class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                        title="Cancel"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        wire:click="confirmSuspend"
                        class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center flex items-center justify-center gap-2 whitespace-nowrap min-h-[44px]"
                        style="background: #22c55e !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); border: none;"
                        onmouseover="this.style.background='#16a34a'"
                        onmouseout="this.style.background='#22c55e'"
                        title="Confirm Unsuspend"
                    >
                        Confirm Unsuspend
                    </button>
                </div>
            @else
                <form wire:submit.prevent="confirmSuspend">
                    <div style="padding:12px 28px 20px 28px; display:flex; flex-direction:column; gap:16px;">
                        <p style="font-size:13px; color:#475569; margin:0; line-height:1.4;">
                            Suspending account for <strong style="color:#0f172a;">{{ $suspendTargetUser->name }}</strong>. Please specify the duration and reason.
                        </p>
                        
                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:6px;">Suspension Duration (Days)</label>
                            <select wire:model="suspendDays" style="width:100%; height:42px; border-radius:12px; border:1.5px solid #cbd5e1; padding:0 14px; font-size:13.5px; font-weight:600; color:#0f172a; outline:none; background:#fff;">
                                <option value="1">1 Day</option>
                                <option value="3">3 Days</option>
                                <option value="5">5 Days</option>
                                <option value="7">7 Days (1 Week)</option>
                                <option value="14">14 Days (2 Weeks)</option>
                                <option value="30">30 Days (1 Month)</option>
                            </select>
                            @error('suspendDays')
                                <span style="color:#ef4444; font-size:11.5px; margin-top:4px; display:block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:6px;">Reason for Suspension</label>
                            <textarea wire:model="suspendReason" rows="3" placeholder="e.g. Violation of usage policy, Pending verification, etc." style="width:100%; border-radius:12px; border:1.5px solid #cbd5e1; padding:10px 14px; font-size:13px; color:#0f172a; outline:none; background:#fff; font-family:inherit; resize:none;"></textarea>
                            @error('suspendReason')
                                <span style="color:#ef4444; font-size:11.5px; margin-top:4px; display:block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div style="background:#fef3c7; border:1px solid #fde68a; border-radius:12px; padding:10px 14px; display:flex; align-items:center; gap:10px;">
                            <div style="font-size:16px;">⏱️</div>
                            <div style="font-size:11.5px; color:#92400e; font-weight:600;">
                                Account will automatically reactivate after the suspension period.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer-btns">
                        <button
                            type="button"
                            wire:click="closeSuspendModal"
                            class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                            title="Cancel"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center flex items-center justify-center gap-2 whitespace-nowrap min-h-[44px]"
                            style="background: #ef4444 !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35); border: none;"
                            onmouseover="this.style.background='#dc2626'"
                            onmouseout="this.style.background='#ef4444'"
                            title="Confirm Suspension"
                        >
                            Confirm Suspension
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endif

    {{-- 3. DELETE USER MODAL --}}
    @if($showDeleteModal && $deleteTargetUser)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center modal-animated-backdrop"
        style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; min-width: 100%; height: 100vh; height: 100dvh; min-height: 100%; margin: 0; box-sizing: border-box; background: rgba(0,0,0,0.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999;"
    >
        <div class="modal-animated-card" style="background:#fff; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); width:100%; max-width:460px; margin:0 16px; overflow:hidden;">
            <div style="padding:28px 28px 12px 28px; display:flex; align-items:flex-start; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0;">
                            Schedule Account Deletion
                        </h3>
                        <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:3px 0 0 0;">
                            5-day grace period process
                        </p>
                    </div>
                </div>
                <x-close-button wire:click="closeDeleteModal" />
            </div>
            <div style="padding:12px 28px 20px 28px; display:flex; flex-direction:column; gap:14px;">
                <p style="font-size:13.5px; color:#475569; margin:0; line-height:1.5;">
                    Deleting account for <strong style="color:#0f172a; font-weight:700;">{{ $deleteTargetUser->name }}</strong> will first change status to <strong style="color:#ef4444; font-weight:700;">Suspended</strong> for <strong style="color:#d97706; font-weight:700;">5 days</strong>.
                </p>
                <div style="background:#fef3c7; border:1px solid #fde68a; border-radius:14px; padding:12px 16px; display:flex; align-items:center; gap:12px;">
                    <div style="font-size:20px; line-height:1;">⏱️</div>
                    <div>
                        <div style="font-size:12.5px; font-weight:700; color:#92400e;">5-Day Countdown Protection</div>
                        <div style="font-size:11.5px; color:#b45309; margin-top:2px;">After 5 days, the account will be automatically and permanently deleted.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer-btns">
                <button
                    type="button"
                    wire:click="closeDeleteModal"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                    title="Cancel"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="confirmDelete"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center flex items-center justify-center whitespace-nowrap min-h-[44px]"
                    style="background: #ef4444 !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35); border: none;"
                    onmouseover="this.style.background='#dc2626'"
                    onmouseout="this.style.background='#ef4444'"
                    title="Schedule Deletion"
                >
                    <span>Schedule Deletion</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 4. CANCEL DELETION MODAL --}}
    @if($showCancelDeletionModal && $cancelTargetUser)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center modal-animated-backdrop"
        style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; min-width: 100%; height: 100vh; height: 100dvh; min-height: 100%; margin: 0; box-sizing: border-box; background: rgba(0,0,0,0.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999;"
    >
        <div class="modal-animated-card" style="background:#fff; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); width:100%; max-width:460px; margin:0 16px; overflow:hidden;">
            <div style="padding:28px 28px 12px 28px; display:flex; align-items:flex-start; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:44px; height:44px; border-radius:14px; background:#f0fdf4; border:1px solid #bbf7d0; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="22" height="22" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0;">
                            Cancel Account Deletion
                        </h3>
                        <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:3px 0 0 0;">
                            Restore account to active status
                        </p>
                    </div>
                </div>
                <x-close-button wire:click="closeCancelDeletionModal" />
            </div>
            <div style="padding:12px 28px 20px 28px; display:flex; flex-direction:column; gap:14px;">
                <p style="font-size:13.5px; color:#475569; margin:0; line-height:1.5;">
                    Are you sure you want to cancel the 5-day deletion process for <strong style="color:#0f172a; font-weight:700;">{{ $cancelTargetUser->name }}</strong>?
                </p>
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:14px; padding:12px 16px; display:flex; align-items:center; gap:12px;">
                    <div style="font-size:20px; line-height:1;">✅</div>
                    <div>
                        <div style="font-size:12.5px; font-weight:700; color:#22c55e;">Stop Countdown & Restore</div>
                        <div style="font-size:11.5px; color:#166534; margin-top:2px;">The 5-day countdown will stop and account status will be restored to <strong>Active</strong>.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer-btns">
                <button
                    type="button"
                    wire:click="closeCancelDeletionModal"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                    title="Cancel"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="confirmCancelDeletion"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer text-center flex items-center justify-center gap-2 whitespace-nowrap min-h-[44px]"
                    style="background: #22c55e !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); border: none;"
                    onmouseover="this.style.background='#16a34a'"
                    onmouseout="this.style.background='#22c55e'"
                    title="Restore Student"
                >
                    Restore Student
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- 5. VIEW SUSPENSION DETAILS MODAL --}}
    @if($showViewSuspendModal && $viewSuspendTargetUser)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center modal-animated-backdrop"
        style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; min-width: 100%; height: 100vh; height: 100dvh; min-height: 100%; margin: 0; box-sizing: border-box; background: rgba(0,0,0,0.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999;"
    >
        <div class="modal-animated-card" style="background:#fff; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35); width:100%; max-width:480px; margin:0 16px; overflow:hidden;">
            <div style="padding:28px 28px 12px 28px; display:flex; align-items:flex-start; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:44px; height:44px; border-radius:14px; background:#FEF3C7; border:1px solid #FDE68A; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="22" height="22" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0;">
                            Suspension Details
                        </h3>
                        <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:3px 0 0 0;">
                            {{ $viewSuspendTargetUser->name }} ({{ $viewSuspendTargetUser->formatted_id }})
                        </p>
                    </div>
                </div>
                <x-close-button wire:click="closeViewSuspendModal" />
            </div>
            <div style="padding:12px 28px 20px 28px; display:flex; flex-direction:column; gap:14px;">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:16px; display:flex; flex-direction:column; gap:10px;">
                    <div>
                        <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:2px;">Reason for Suspension</span>
                        <div style="font-size:13.5px; font-weight:600; color:#0f172a;">
                            {{ $viewSuspendTargetUser->suspend_reason ?: 'No specific reason entered.' }}
                        </div>
                    </div>
                    <div style="height:1px; background:#e2e8f0; margin:4px 0;"></div>
                    <div>
                        <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:2px;">Suspended By Admin</span>
                        <div style="font-size:13px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:6px;">
                            <svg width="15" height="15" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>{{ $viewSuspendTargetUser->suspended_by_admin_detail }}</span>
                        </div>
                    </div>
                    <div style="height:1px; background:#e2e8f0; margin:4px 0;"></div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div>
                            <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:2px;">Suspended Until</span>
                            <div style="font-size:13px; font-weight:700; color:#d97706;">
                                {{ $viewSuspendTargetUser->suspended_until?->format('M d, Y') ?? 'Manual' }}
                            </div>
                        </div>
                        <div>
                            <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:2px;">Time Remaining</span>
                            <div style="font-size:13px; font-weight:700; color:#22c55e; font-mono:true;">
                                {{ $viewSuspendTargetUser->suspension_countdown }}
                            </div>
                        </div>
                    </div>
                </div>

                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:10px 14px; display:flex; align-items:center; gap:10px;">
                    <div style="font-size:16px;">⏱️</div>
                    <div style="font-size:11.5px; color:#166534; font-weight:600;">
                        Account will auto-reactivate when the countdown finishes.
                    </div>
                </div>
                @php
                    $hasPendingReactivation = \App\Models\UserRequest::where('user_email', $viewSuspendTargetUser->email)
                        ->where('type', 'Account Reactivation Request')
                        ->where('status', 'Pending')
                        ->first();
                    $isSuperAdmin = $this->isSuperAdminActor();
                @endphp

                @if($hasPendingReactivation)
                    <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:10px 14px; display:flex; align-items:center; gap:10px;">
                        <div style="font-size:18px;">⏳</div>
                        <div>
                            <div style="font-size:12px; color:#b45309; font-weight:700;">Reactivation Request Pending</div>
                            <div style="font-size:11px; color:#92400e;">Request {{ $hasPendingReactivation->request_code }} was submitted to {{ $hasPendingReactivation->admin_id }} for approval.</div>
                        </div>
                    </div>
                @endif
            </div>
            <div style="padding: 16px 24px 20px; display: flex; gap: 12px; width: 100%; box-sizing: border-box; align-items: center; background: #ffffff; border-top: 1px solid #f1f5f9;">
                <button
                    type="button"
                    wire:click="closeViewSuspendModal"
                    class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                    title="Cancel"
                >
                    Cancel
                </button>
                @if($hasPendingReactivation)
                    <button
                        type="button"
                        disabled
                        class="flex-1 py-3 rounded-full bg-slate-300 text-white font-bold text-sm cursor-not-allowed opacity-75 text-center"
                    >
                        Reactivation Pending
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="cancelSuspensionFromView"
                        class="flex-1 py-3 rounded-full bg-green-500 hover:bg-green-600 text-white font-bold text-sm shadow-md shadow-green-500/35 transition cursor-pointer text-center flex items-center justify-center gap-2"
                        style="box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);"
                        title="{{ $isSuperAdmin ? 'Reactivate account immediately' : 'Submit reactivation request to ' . ($viewSuspendTargetUser->suspended_by_admin_detail ?: 'SuperAdmin') . ' for approval' }}"
                    >
                        Reactivate Account
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- VIEW USER DETAILS MODAL POPUP --}}
    @if($showViewModal && $viewTargetUser)
    <style>
        .user-view-modal-container {
            background: #ffffff;
            border-radius: 28px;
            width: 100%;
            max-width: 680px;
            height: auto;
            max-height: min(85vh, calc(100dvh - 24px));
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
            border: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin: auto;
            position: relative;
            animation: modalFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .user-view-modal-header {
            padding: 20px 26px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
            flex-shrink: 0;
            background: #ffffff;
        }
        .user-view-modal-header h3 {
            font-size: 17.5px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.01em;
        }
        .view-modal-scroll {
            padding: 18px 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1 1 auto;
            box-sizing: border-box;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
            scrollbar-gutter: stable;
            -webkit-overflow-scrolling: touch;
        }
        .view-modal-scroll::-webkit-scrollbar {
            width: 6px;
            display: block !important;
        }
        .view-modal-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
            margin: 4px 0;
        }
        .view-modal-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .view-modal-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .user-view-modal-footer {
            padding: 16px 24px 20px;
            border-top: 1px solid #f1f5f9;
            background: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            width: 100%;
            box-sizing: border-box;
        }
        @media (max-width: 540px) {
            .user-view-backdrop {
                padding: 24px 16px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                box-sizing: border-box !important;
            }
            .user-view-modal-container {
                width: 100% !important;
                max-width: min(calc(100vw - 32px), 420px) !important;
                height: auto !important;
                max-height: min(80vh, 590px) !important;
                border-radius: 24px !important;
                margin: auto !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35) !important;
            }
            .user-view-modal-header {
                padding: 16px 20px 14px !important;
            }
            .user-view-modal-header h3 {
                font-size: 17.5px !important;
            }
            .view-modal-scroll {
                padding: 14px 18px 16px !important;
                gap: 10px !important;
            }
            .user-view-modal-footer {
                padding: 12px 18px 14px !important;
            }
        }
    </style>
    <div class="user-view-backdrop fixed inset-0 z-50 flex items-center justify-center" style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.65); backdrop-filter:blur(6px); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px;" wire:click.self="closeViewModal">
        <div class="user-view-modal-container">
            
            {{-- MODAL HEADER (FIXED) --}}
            <div class="user-view-modal-header">
                <h3>
                    User Details
                </h3>
                <x-modal-close-button click="closeViewModal" />
            </div>

            {{-- MODAL BODY (SCROLLABLE) --}}
            <div class="view-modal-scroll">

                {{-- TOP BANNER CARD (SOFT MINT GREEN CONTAINER) --}}
                <div style="background:#f0fdf4; border:1px solid #dcfce7; border-radius:18px; padding:14px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 3px rgba(0,0,0,0.02); flex-shrink:0;">
                    <div style="position:relative; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                        @if($viewTargetUser->profile_photo_path)
                            <img src="{{ asset('storage/' . $viewTargetUser->profile_photo_path) }}" alt="{{ $viewTargetUser->name }}" style="width:46px; height:46px; border-radius:50%; object-fit:cover; box-shadow:0 2px 6px rgba(0,0,0,0.08);">
                        @else
                            <div style="width:46px; height:46px; border-radius:50%; background:#22c55e !important; color:#ffffff !important; font-weight:800; font-size:18px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(34,197,94,0.3); border:none; user-select:none;">
                                {{ strtoupper(substr($viewTargetUser->name ?: 'U', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div style="flex:1; min-width:0;">
                        <h4 style="font-size:15.5px; font-weight:800; color:#0f172a; margin:0; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">
                            {{ $viewTargetUser->name }}
                        </h4>
                        <div style="font-size:12.5px; font-weight:600; color:#64748b; margin-top:2px;">
                            {{ in_array(strtolower($viewTargetUser->role ?? ''), ['superadmin', 'super admin']) ? 'Super Admin' : ucfirst($viewTargetUser->role ?: 'User') }}
                        </div>
                        <div style="display:inline-flex; align-items:center; gap:5px; margin-top:4px;">
                            <span style="width:7px; height:7px; border-radius:50%; background:{{ (ucfirst($viewTargetUser->status) === 'Suspended' || strtolower($viewTargetUser->status) === 'inactive') ? '#ef4444' : '#22c55e' }}; display:inline-block;"></span>
                            <span style="font-size:11.5px; font-weight:700; color:{{ (ucfirst($viewTargetUser->status) === 'Suspended' || strtolower($viewTargetUser->status) === 'inactive') ? '#ef4444' : '#16a34a' }};">
                                {{ (ucfirst($viewTargetUser->status) === 'Suspended' || strtolower($viewTargetUser->status) === 'inactive') ? 'Suspended' : 'Active' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- GRID INFORMATION CARDS --}}
                @php
                    $isStudent = strtolower($viewTargetUser->role ?? '') === 'student' || !empty($viewTargetUser->student_id);
                @endphp

                @if($isStudent)
                    {{-- ROW 1 FOR STUDENT: STUDENT ID & SECTION --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        {{-- CARD 1: STUDENT ID --}}
                        <div style="background:#ffffff; border:1.5px solid #f1f5f9; border-radius:14px; padding:10px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                            <div style="font-size:11px; font-weight:700; color:#64748b; display:flex; align-items:center; gap:6px;">
                                <span style="width:6px; height:6px; border-radius:50%; background:#475569;"></span>
                                <span>Student ID</span>
                            </div>
                            <div style="font-size:13.5px; font-weight:800; color:#0f172a; margin-top:3px; font-family:monospace;">
                                {{ $viewTargetUser->formatted_id }}
                            </div>
                        </div>

                        {{-- CARD 2: SECTION --}}
                        <div style="background:#ffffff; border:1.5px solid #f1f5f9; border-radius:14px; padding:10px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                            <div style="font-size:11px; font-weight:700; color:#64748b; display:flex; align-items:center; gap:6px;">
                                <svg width="12" height="12" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                                <span>Section</span>
                            </div>
                            <div style="font-size:13.5px; font-weight:800; color:#0f172a; margin-top:3px;">
                                {{ $viewTargetUser->section ?: 'N/A' }}
                            </div>
                        </div>
                    </div>
                @else
                    {{-- ROW 1 FOR NON-STUDENT: USER ID & JOINED DATE --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        {{-- CARD 1: USER ID --}}
                        <div style="background:#ffffff; border:1.5px solid #f1f5f9; border-radius:14px; padding:10px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                            <div style="font-size:11px; font-weight:700; color:#64748b; display:flex; align-items:center; gap:6px;">
                                <span style="width:6px; height:6px; border-radius:50%; background:#475569;"></span>
                                <span>User ID</span>
                            </div>
                            <div style="font-size:13.5px; font-weight:800; color:#0f172a; margin-top:3px; font-family:monospace;">
                                {{ $viewTargetUser->formatted_id }}
                            </div>
                        </div>

                        {{-- CARD 2: JOINED DATE --}}
                        <div style="background:#ffffff; border:1.5px solid #f1f5f9; border-radius:14px; padding:10px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                            <div style="font-size:11px; font-weight:700; color:#64748b; display:flex; align-items:center; gap:6px;">
                                <svg width="12" height="12" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span>Joined Date</span>
                            </div>
                            <div style="font-size:13.5px; font-weight:800; color:#0f172a; margin-top:3px;">
                                {{ $viewTargetUser->created_at?->format('d M Y') ?: 'N/A' }}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ROW 2: TOTAL SCANS & PIN MAP --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    {{-- CARD 3: TOTAL SCANS --}}
                    <div style="background:#ffffff; border:1.5px solid #f1f5f9; border-radius:14px; padding:10px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size:11px; font-weight:700; color:#64748b; display:flex; align-items:center; gap:6px;">
                            <span style="width:6px; height:6px; border-radius:50%; background:#eab308;"></span>
                            <span>Total Scans</span>
                        </div>
                        <div style="font-size:13.5px; font-weight:800; color:#0f172a; margin-top:3px;">
                            {{ $viewTargetUser->soil_scans_count ?? 0 }} Scans
                        </div>
                    </div>

                    {{-- CARD 4: PIN MAP --}}
                    <div style="background:#ffffff; border:1.5px solid #f1f5f9; border-radius:14px; padding:10px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size:11px; font-weight:700; color:#64748b; display:flex; align-items:center; gap:6px;">
                            <svg width="13" height="13" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>Pin Map</span>
                        </div>
                        <div style="font-size:13.5px; font-weight:800; color:#0f172a; margin-top:3px;">
                            {{ $viewTargetUser->location_mappings_count ?? 0 }}
                        </div>
                    </div>
                </div>

                @if($isStudent)
                    {{-- ROW 3 FOR STUDENT: JOINED DATE --}}
                    <div style="display:grid; grid-template-columns:1fr; gap:10px;">
                        <div style="background:#ffffff; border:1.5px solid #f1f5f9; border-radius:14px; padding:10px 14px; box-shadow:0 1px 3px rgba(0,0,0,0.02); display:flex; align-items:center; justify-content:space-between;">
                            <div style="font-size:11px; font-weight:700; color:#64748b; display:flex; align-items:center; gap:6px;">
                                <svg width="12" height="12" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span>Joined Date</span>
                            </div>
                            <div style="font-size:13px; font-weight:800; color:#0f172a;">
                                {{ $viewTargetUser->created_at?->format('d M Y') ?: 'N/A' }}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- EMAIL ADDRESS --}}
                <div style="display:grid; grid-template-columns:1fr; gap:10px;">
                    <div style="background:#ffffff; border:1.5px solid #f1f5f9; border-radius:14px; padding:10px 14px; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size:11px; font-weight:700; color:#64748b; display:flex; align-items:center; gap:6px;">
                            <svg width="12" height="12" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Email Address</span>
                        </div>
                        <div style="font-size:12.5px; font-weight:700; color:#0f172a; margin-top:3px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;" title="{{ $viewTargetUser->email }}">
                            {{ $viewTargetUser->email }}
                        </div>
                    </div>
                </div>

                {{-- LAST MODIFIED AUDIT SECTION --}}
                <x-last-modified-card :record="$viewTargetUser" />

            </div>

            {{-- MODAL FOOTER (FIXED) --}}
            <div class="user-view-modal-footer">
                <button
                    type="button"
                    wire:click="closeViewModal"
                    class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                    title="Cancel"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="closeViewModal; openEditModal('{{ $viewTargetUser->id }}')"
                    class="flex-1 py-3 rounded-full bg-green-500 hover:bg-green-600 text-white font-bold text-sm shadow-md shadow-green-500/35 transition cursor-pointer text-center flex items-center justify-center gap-2"
                    style="box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);"
                    title="Edit Record"
                >
                    <span>Edit Record</span>
                </button>
            </div>

        </div>
    </div>
    @endif

    <style>
        /* Password Security Progress Bars & Requirement Pills */
        .admin-sec-progress-bar {
            height: 4px;
            width: 22px;
            border-radius: 9999px;
            background: #e2e8f0;
            transition: all 0.3s ease;
            display: inline-block;
        }
        .admin-req-pill-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 4px !important;
            margin-top: 6px !important;
            margin-bottom: 2px !important;
        }
        .admin-req-pill-row {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
        }
        .admin-req-pill {
            font-size: 0.70rem !important;
            padding: 3px 8px !important;
            border-radius: 6px !important;
            gap: 5px !important;
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
            user-select: none;
        }
        .admin-req-pill.is-met {
            background: #dcfce7 !important;
            color: #15803d !important;
            border-color: #86efac !important;
        }
        .admin-req-pill-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #e2e8f0;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .admin-req-pill.is-met .admin-req-pill-icon {
            background: #22c55e !important;
        }

        .user-table-container {
            width: 100% !important;
            overflow-x: auto !important;
            scrollbar-width: none !important;
        }
        .user-table-container::-webkit-scrollbar {
            display: none !important;
        }
        /* First column left padding: Desktop (≥ 680px) = 20px */
        .user-table th.col-user-id,
        .user-table td.col-user-id {
            padding-left: 20px !important;
        }
        /* ── CLEAN STICKY ACTION COLUMN (DESKTOP) ── */
        .user-table th.col-user-status,
        .user-table td.col-user-status {
            position: static !important;
            width: 176px !important;
            min-width: 176px !important;
            max-width: 176px !important;
            white-space: nowrap !important;
            text-align: center !important;
            padding: 10px 4px !important;
        }
        .user-status-dual-pill {
            display: inline-flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 4px !important;
            width: 104px !important;
            min-width: 104px !important;
            max-width: 104px !important;
            margin: 0 auto !important;
            box-sizing: border-box !important;
        }
        .user-status-dual-pill .status-pill-fixed,
        .user-status-dual-pill .agrico-countdown-pill {
            width: 104px !important;
            min-width: 104px !important;
            max-width: 104px !important;
            height: 25px !important;
            box-sizing: border-box !important;
            border-radius: 9999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
            padding: 0 8px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
        }
        .user-status-dual-pill .agrico-countdown-pill {
            font-size: 10.5px !important;
            gap: 5px !important;
            letter-spacing: -0.1px !important;
        }
        .dark .user-status-dual-pill .agrico-countdown-pill {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #fca5a5 !important;
            border: 1px solid rgba(239, 68, 68, 0.3) !important;
        }

        .user-table th.col-sticky-action,
        .user-table td.col-sticky-action {
            position: sticky !important;
            right: 0 !important;
            width: 186px !important;
            min-width: 186px !important;
            max-width: 186px !important;
            white-space: nowrap !important;
            box-shadow: none !important;
            border-left: none !important;
            border-right: none !important;
            z-index: 10 !important;
            background-color: #ffffff !important;
            text-align: center !important;
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
        .user-table th.col-sticky-action {
            z-index: 20 !important;
            background-color: #F1FBF1 !important;
            text-align: center !important;
        }
        .user-table td.col-sticky-action .user-desktop-actions {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 6px !important;
            width: 100% !important;
        }
        .user-table tbody tr:hover td.col-sticky-action,
        .user-table tbody tr.hover\:bg-green-50\/40:hover td.col-sticky-action {
            background-color: #f7fdf7 !important;
        }
        .user-table tbody tr.bg-emerald-50\/80 td.col-sticky-action {
            background-color: #ecfdf5 !important;
        }
        .dark .user-table th.col-sticky-action {
            background-color: #1e293b !important;
        }
        .dark .user-table td.col-sticky-action {
            background-color: #0f172a !important;
        }
        .user-title-desktop {
            display: inline;
        }
        .user-title-mobile {
            display: none;
        }
        .user-desktop-actions {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 6px !important;
            width: 100% !important;
        }
        .user-mobile-actions {
            display: none !important;
        }
        .user-action-title-desktop {
            display: inline;
        }
        .user-action-title-mobile {
            display: none;
        }

        .agrico-more-btn {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            min-height: 40px !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #64748b !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
        }
        .agrico-more-btn:hover {
            background: #f8fafc !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
        .dark .agrico-more-btn {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }
        .dark .agrico-more-btn:hover {
            background: #334155 !important;
            color: #f8fafc !important;
        }
        .agrico-mobile-dropdown {
            position: absolute !important;
            right: 0 !important;
            width: 165px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            box-shadow: 0 12px 28px -4px rgba(0,0,0,0.14), 0 8px 12px -6px rgba(0,0,0,0.06) !important;
            padding: 6px !important;
            z-index: 90 !important;
            text-align: left !important;
        }
        .dark .agrico-mobile-dropdown {
            background: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 12px 28px -4px rgba(0,0,0,0.4) !important;
        }
        .agrico-dropdown-item {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 8px 12px !important;
            border-radius: 10px !important;
            border: none !important;
            background: transparent !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            text-align: left !important;
            box-sizing: border-box !important;
        }
        .agrico-dropdown-item:hover {
            background: #f8fafc !important;
            color: #0f172a !important;
        }
        .dark .agrico-dropdown-item {
            color: #cbd5e1 !important;
        }
        .dark .agrico-dropdown-item:hover {
            background: #334155 !important;
            color: #ffffff !important;
        }
        .agrico-dropdown-item-danger {
            color: #ef4444 !important;
        }
        .agrico-dropdown-item-danger:hover {
            background: #fef2f2 !important;
            color: #b91c1c !important;
        }
        .dark .agrico-dropdown-item-danger:hover {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #fca5a5 !important;
        }

        /* Mobile (< 680px): Status and Action fit perfectly within screen width */
        @media (max-width: 679px) {
            .user-title-desktop {
                display: none !important;
            }
            .user-title-mobile {
                display: inline !important;
            }
            .user-action-title-desktop {
                display: none !important;
            }
            .user-action-title-mobile {
                display: inline !important;
            }
            .user-table td.col-sticky-action .user-desktop-actions,
            .user-desktop-actions {
                display: none !important;
            }
            .user-table td.col-sticky-action .user-mobile-actions,
            .user-mobile-actions {
                display: inline-flex !important;
                position: relative !important;
                justify-content: center !important;
                align-items: center !important;
            }
            .user-table-card {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
                overflow: visible !important;
            }
            .user-table-container {
                overflow: visible !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            .user-table {
                table-layout: fixed !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }
            .col-user-id,
            .col-user-email,
            .col-user-role,
            .col-user-joined {
                display: none !important;
            }
            .user-table th.col-user-name,
            .user-table td.col-user-name {
                width: auto !important;
                overflow: hidden !important;
                padding-left: 12px !important;
                padding-right: 4px !important;
            }
            .user-status-dual-pill {
                width: 98px !important;
                min-width: 98px !important;
                max-width: 98px !important;
                gap: 3px !important;
            }
            .user-status-dual-pill .status-pill-fixed,
            .user-status-dual-pill .agrico-countdown-pill {
                width: 98px !important;
                min-width: 98px !important;
                max-width: 98px !important;
                height: 23px !important;
                font-size: 10px !important;
                padding: 0 4px !important;
            }
            .user-status-dual-pill .agrico-countdown-pill {
                font-size: 9.5px !important;
                gap: 4px !important;
            }
            .user-table th.col-user-status,
            .user-table td.col-user-status {
                display: table-cell !important;
                position: static !important;
                left: auto !important;
                right: auto !important;
                width: 124px !important;
                min-width: 124px !important;
                max-width: 130px !important;
                padding: 8px 4px !important;
                text-align: center !important;
                overflow: hidden !important;
            }
            .user-table td.col-user-status .agrico-status-pill {
                padding: 3px 8px !important;
                font-size: 11px !important;
                gap: 4px !important;
            }
            .user-table th.col-sticky-action,
            .user-table td.col-sticky-action {
                display: table-cell !important;
                position: static !important;
                left: auto !important;
                right: auto !important;
                width: 58px !important;
                min-width: 58px !important;
                max-width: 58px !important;
                padding: 10px 4px !important;
                text-align: center !important;
                overflow: visible !important;
                box-shadow: none !important;
                border-left: none !important;
                border-right: none !important;
            }
            .user-table th.col-sticky-action {
                text-align: center !important;
                font-size: 10px !important;
                letter-spacing: 0.04em !important;
                padding: 12px 4px !important;
            }
            .user-action-title-mobile {
                display: inline-block !important;
                text-align: center !important;
                width: 100% !important;
            }
            .user-table td.col-sticky-action .user-mobile-actions {
                display: inline-flex !important;
                justify-content: center !important;
                align-items: center !important;
                margin: 0 auto !important;
            }
        }
    </style>
</div>
