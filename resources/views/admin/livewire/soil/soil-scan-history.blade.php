<div wire:poll.3s class="space-y-6">

    {{-- HEADER --}}
    <div class="ui-reveal flex items-center justify-between gap-4 flex-wrap" style="--i:0">
        <div>
            <h1 class="ui-reveal-text text-3xl font-extrabold text-gray-900" style="--i:0">
                Soil Scan History
            </h1>
            <p class="ui-reveal-text text-sm text-gray-400 mt-1" style="--i:0">
                <span class="ui-reveal-num" style="--i:0" x-data x-init="animateCount($el, {{ (int) ($soils->total() ?? 0) }}, { delay: 400 })">{{ $soils->total() ?? 0 }}</span> soil records in database
            </p>
        </div>
        <a
            href="{{ route('dashboard') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-bold text-gray-700 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 hover:text-gray-900 transition"
            title="Back to Dashboard"
        >
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Dashboard
        </a>
    </div>

    {{-- SEARCH AND FILTER CARD --}}
    <div class="ui-reveal" style="--i:1; background:#ffffff; border-radius:20px; border:1px solid #f1f5f9; box-shadow:0 1px 3px rgba(0,0,0,0.03); padding:12px 16px; display:flex; align-items:center; gap:14px; width:100%; box-sizing:border-box; flex-wrap:wrap;">
        {{-- SEARCH INPUT WITH AUTO-SUGGESTIONS --}}
        <div x-data="{ open: true }" @click.outside="open = false" style="position:relative; flex:1; min-width:220px;">
            <svg style="position:absolute; left:16px; top:50%; transform:translateY(-50%); width:18px; height:18px; color:#94a3b8; z-index:2;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input
                wire:model.live="search"
                @focus="open = true"
                @input="open = true"
                type="text"
                placeholder="Search soil name, full code, hue, User ID..."
                style="width:100%; border:1px solid #e2e8f0; border-radius:16px; background:#ffffff; padding:10px 38px 10px 46px; font-size:14px; font-weight:500; color:#1e293b; outline:none; transition:all 0.2s ease; box-sizing:border-box;"
                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3px rgba(68,175,53,0.15)';"
                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
            />
            @if($search)
                <x-close-button size="sm" click="$set('search', '')" title="Clear search" ariaLabel="Clear search" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); z-index:3;" />
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
                        Soil Suggestions
                    </div>
                    @foreach($searchSuggestions as $s)
                        @php
                            $sCode = $s->full_code ?? $s->munsellColor?->full_code ?? '10YR 4/3';
                            $sHue = $s->hue ?? $s->munsellColor?->hue ?? '10YR';
                            $sVal = $s->munsellColor?->value ?? 4;
                            $sChr = $s->munsellColor?->chroma ?? 3;
                            $sHex = \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex($sHue, $sVal, $sChr);
                            $suggestVal = $s->soil_name ?: $sCode;
                        @endphp
                        <div
                            wire:click="selectSuggestion('{{ addslashes($suggestVal) }}')"
                            @click="open = false"
                            style="padding:8px 14px; display:flex; align-items:center; justify-content:space-between; cursor:pointer; transition:background 0.15s ease;"
                            onmouseover="this.style.background='#f8fafc';"
                            onmouseout="this.style.background='transparent';"
                        >
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span style="width:20px; height:20px; border-radius:50%; background:{{ $sHex }}; border:1.5px solid rgba(0,0,0,0.15); display:inline-block; flex-shrink:0;"></span>
                                <div>
                                    <div style="font-size:13.5px; font-weight:700; color:#1e293b;">
                                        {{ $s->soil_name ?: 'Unnamed Soil' }}
                                    </div>
                                    <div style="font-size:11.5px; color:#64748b;">
                                        Code: <strong style="color:#334155;">{{ $sCode }}</strong> &bull; Hue: {{ $sHue }}
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

        {{-- FUNNEL ICON --}}
        <svg width="18" height="18" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="flex-shrink:0;">
            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </svg>

        {{-- FILTER DROPDOWNS --}}
        <select
            wire:model.live="status"
            style="border:1px solid #e2e8f0; border-radius:14px; background:#ffffff; padding:8px 16px; font-size:13px; font-weight:600; color:#334155; outline:none; cursor:pointer; transition:all 0.2s ease; flex-shrink:0;"
            onfocus="this.style.borderColor='#22c55e';"
            onblur="this.style.borderColor='#e2e8f0';"
        >
            <option value="">All Status</option>
            <option value="Verified">Verified</option>
            <option value="Pending">Pending Review</option>
            <option value="Rejected">Rejected</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>

        <select
            wire:model.live="hue"
            style="border:1px solid #e2e8f0; border-radius:14px; background:#ffffff; padding:8px 16px; font-size:13px; font-weight:600; color:#334155; outline:none; cursor:pointer; transition:all 0.2s ease; flex-shrink:0;"
            onfocus="this.style.borderColor='#22c55e';"
            onblur="this.style.borderColor='#e2e8f0';"
        >
            <option value="">All Hues</option>
            @foreach($munsellHues as $hueOption)
                <option value="{{ $hueOption }}">{{ $hueOption }}</option>
            @endforeach
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
                wire:model.live="selectedDate"
                type="date"
                style="border:none; background:transparent; font-size:13px; font-weight:600; color:#334155; outline:none; cursor:pointer; font-family:inherit;"
                title="Filter by Date"
            />
            @if($selectedDate)
                <x-close-button size="sm" click="$set('selectedDate', '')" title="Clear date filter" ariaLabel="Clear date filter" style="margin-left:4px;" />
            @endif
        </div>

        {{-- RESULTS COUNTER --}}
        <div class="ui-reveal-num" style="--i:1; font-size:13px; font-weight:500; color:#94a3b8; flex-shrink:0;" x-data x-init="animateCount($el, {{ (int) ($soils->total() ?? 0) }}, { suffix: ' results', delay: 600 })">
            {{ $soils->total() ?? 0 }} results
        </div>
    </div>

    {{-- STYLES FOR RESPONSIVE TABLE & STICKY COLUMNS --}}
    <style>
        .soil-table-container {
            width: 100% !important;
            overflow-x: auto !important;
            overflow-y: visible !important;
            scrollbar-width: none !important; /* Firefox */
            -ms-overflow-style: none !important;  /* IE/Edge */
        }
        .soil-table-container::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .soil-table {
            width: 100% !important;
            min-width: 0 !important;
        }

        /* Responsive Column Dropping Hierarchy:
           - Desktop (>= 1024px): All columns visible with sticky navigation
           - Tablet (680px - 1023px): Soil Characteristics, Full Code, Scan Date, Action
           - Mobile (< 680px): Exactly 2 columns: Soil Characteristics (with embedded Code + Date) & Action
             guaranteeing 100% phone width fit with zero horizontal clipping!
        */
        .col-soil-id {
            display: none !important;
        }

        .soil-mobile-meta {
            display: none !important;
        }

        @media (max-width: 1023px) {
            .col-soil-id,
            .col-scan-note,
            .col-user-id {
                display: none !important;
            }
        }

        /* Tablets (680px - 849px): Drop Scan Count so Soil, Code, Date, Action breathe comfortably */
        @media (max-width: 849px) {
            .col-scan-count {
                display: none !important;
            }
        }

        /* Desktop & Tablet (≥ 680px and < 1024px): When ID is hidden, Soil Characteristics is first visible column -> 20px left padding */
        @media (min-width: 680px) and (max-width: 1023px) {
            .soil-table th.col-sticky-soil,
            .soil-table td.col-sticky-soil {
                padding-left: 20px !important;
            }
        }

        /* Mobile (< 680px): Table fits 100% of phone screen. Action is 100% visible! */
        @media (max-width: 679px) {
            .soil-table-container {
                overflow-x: hidden !important;
                width: 100% !important;
            }
            .soil-table {
                table-layout: fixed !important;
                width: 100% !important;
                min-width: 0 !important;
            }
            .col-soil-id,
            .col-full-code,
            .col-scan-count,
            .col-scan-note,
            .col-user-id,
            .col-scan-date {
                display: none !important;
            }
            .soil-mobile-meta {
                display: block !important;
            }
            .soil-table th,
            .soil-table td {
                padding: 12px 6px !important;
                box-sizing: border-box !important;
            }
            .soil-table th.col-sticky-soil,
            .soil-table td.col-sticky-soil {
                position: static !important;
                left: auto !important;
                right: auto !important;
                box-shadow: none !important;
                padding-left: 12px !important;
                padding-right: 4px !important;
                width: auto !important;
                overflow: hidden !important;
            }
            .soil-table th.col-sticky-action,
            .soil-table td.col-sticky-action {
                position: static !important;
                left: auto !important;
                right: auto !important;
                box-shadow: none !important;
                padding-left: 12px !important;
                padding-right: 12px !important;
                width: 134px !important;
                min-width: 134px !important;
                max-width: 134px !important;
                text-align: center !important;
            }
            .soil-table th.col-sticky-action {
                text-align: center !important;
            }
            .soil-table td.col-sticky-action .flex,
            .soil-table td.col-sticky-action > div {
                gap: 4px !important;
                justify-content: center !important;
                align-items: center !important;
                width: 100% !important;
            }
            .soil-table .agrico-view-btn,
            .soil-table .agrico-edit-btn,
            .soil-table .agrico-delete-btn {
                width: 34px !important;
                height: 34px !important;
                min-width: 34px !important;
                max-width: 34px !important;
                min-height: 34px !important;
                max-height: 34px !important;
                border-radius: 10px !important;
                padding: 0 !important;
            }
            .soil-table .agrico-view-btn svg,
            .soil-table .agrico-edit-btn svg,
            .soil-table .agrico-delete-btn svg {
                width: 16px !important;
                height: 16px !important;
            }
        }

        /* Default (>= 680px and < 1024px): Soil Characteristic is sticky at left: 0 */
        @media (min-width: 680px) and (max-width: 1023px) {
            .soil-table th.col-sticky-soil,
            .soil-table td.col-sticky-soil {
                position: sticky !important;
                left: 0 !important;
                min-width: 180px !important;
                white-space: normal !important;
                box-shadow: 4px 0 8px -2px rgba(0, 0, 0, 0.05) !important;
                z-index: 11 !important;
                background-color: #ffffff !important;
                padding-left: 20px !important;
            }
            .soil-table th.col-sticky-soil {
                z-index: 21 !important;
                background-color: #F1FBF1 !important;
            }
            .soil-table th.col-sticky-action,
            .soil-table td.col-sticky-action {
                position: sticky !important;
                right: 0 !important;
                width: 144px !important;
                min-width: 144px !important;
                max-width: 144px !important;
                white-space: nowrap !important;
                box-shadow: none !important;
                z-index: 10 !important;
                background-color: #ffffff !important;
                padding-left: 12px !important;
                padding-right: 12px !important;
                text-align: center !important;
            }
            .soil-table td.col-sticky-action .flex,
            .soil-table td.col-sticky-action > div {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 6px !important;
                width: 100% !important;
            }
            .soil-table th.col-sticky-action {
                z-index: 20 !important;
                background-color: #F1FBF1 !important;
                text-align: center !important;
            }
        }

        /* Action buttons standard styling */
        .soil-table .agrico-view-btn,
        .soil-table .agrico-approve-btn,
        .soil-table .agrico-reject-btn,
        .soil-table .agrico-edit-btn,
        .soil-table .agrico-delete-btn {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            max-width: 36px !important;
            min-height: 36px !important;
            max-height: 36px !important;
            border-radius: 11px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
        }
        .soil-table .agrico-view-btn svg,
        .soil-table .agrico-approve-btn svg,
        .soil-table .agrico-reject-btn svg,
        .soil-table .agrico-edit-btn svg,
        .soil-table .agrico-delete-btn svg {
            width: 17px !important;
            height: 17px !important;
        }
        .soil-table .agrico-approve-btn {
            background: #f0fdf4 !important;
            border: 1px solid #bbf7d0 !important;
            color: #16a34a !important;
        }
        .soil-table .agrico-approve-btn:hover {
            background: #16a34a !important;
            color: #ffffff !important;
        }
        .soil-table .agrico-reject-btn {
            background: #fef2f2 !important;
            border: 1px solid #fecaca !important;
            color: #ef4444 !important;
        }
        .soil-table .agrico-reject-btn:hover {
            background: #ef4444 !important;
            color: #ffffff !important;
        }

        .soil-table tbody tr:hover td.col-sticky-soil,
        .soil-table tbody tr.hover\:bg-green-50\/40:hover td.col-sticky-soil {
            background-color: #f7fdf7 !important;
        }
        .soil-table tbody tr.bg-emerald-50\/80 td.col-sticky-soil {
            background-color: #ecfdf5 !important;
        }
        .dark .soil-table th.col-sticky-soil {
            background-color: #1e293b !important;
        }
        .dark .soil-table td.col-sticky-soil {
            background-color: #0f172a !important;
        }

        .soil-table tbody tr:hover td.col-sticky-action,
        .soil-table tbody tr.hover\:bg-green-50\/40:hover td.col-sticky-action {
            background-color: #f7fdf7 !important;
        }
        .soil-table tbody tr.bg-emerald-50\/80 td.col-sticky-action {
            background-color: #ecfdf5 !important;
        }
        .dark .soil-table th.col-sticky-action {
            background-color: #1e293b !important;
        }
        .dark .soil-table td.col-sticky-action {
            background-color: #0f172a !important;
        }

        /* Desktop / Big screens (>= 1024px): ID column is visible at left: 0, Soil Characteristic is at left: 95px */
        @media (min-width: 1024px) {
            .col-soil-id {
                display: table-cell !important;
            }
            .soil-table th.col-sticky-id,
            .soil-table td.col-sticky-id {
                position: sticky !important;
                left: 0 !important;
                width: 95px !important;
                min-width: 95px !important;
                max-width: 95px !important;
                white-space: nowrap !important;
                z-index: 12 !important;
                background-color: #ffffff !important;
                padding-left: 20px !important;
            }
            .soil-table th.col-sticky-id {
                z-index: 22 !important;
                background-color: #F1FBF1 !important;
            }
            .soil-table tbody tr:hover td.col-sticky-id,
            .soil-table tbody tr.hover\:bg-green-50\/40:hover td.col-sticky-id {
                background-color: #f7fdf7 !important;
            }
            .soil-table tbody tr.bg-emerald-50\/80 td.col-sticky-id {
                background-color: #ecfdf5 !important;
            }
            .dark .soil-table th.col-sticky-id {
                background-color: #1e293b !important;
            }
            .dark .soil-table td.col-sticky-id {
                background-color: #0f172a !important;
            }

            .soil-table th.col-sticky-soil,
            .soil-table td.col-sticky-soil {
                position: sticky !important;
                left: 95px !important;
                min-width: 180px !important;
                max-width: 280px !important;
                white-space: nowrap !important;
                box-shadow: 4px 0 8px -2px rgba(0, 0, 0, 0.05) !important;
                z-index: 11 !important;
                background-color: #ffffff !important;
            }
            .soil-table th.col-sticky-soil {
                z-index: 21 !important;
                background-color: #F1FBF1 !important;
            }

            .soil-table th.col-sticky-action,
            .soil-table td.col-sticky-action {
                position: sticky !important;
                right: 0 !important;
                width: 144px !important;
                min-width: 144px !important;
                max-width: 144px !important;
                white-space: nowrap !important;
                box-shadow: none !important;
                z-index: 10 !important;
                background-color: #ffffff !important;
                padding-left: 12px !important;
                padding-right: 12px !important;
                text-align: center !important;
            }
            .soil-table td.col-sticky-action .flex,
            .soil-table td.col-sticky-action > div {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 6px !important;
                width: 100% !important;
            }
            .soil-table th.col-sticky-action {
                z-index: 20 !important;
                background-color: #F1FBF1 !important;
                text-align: center !important;
            }
        }
    </style>

    {{-- TABLE CARD --}}
    <div class="ui-reveal bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden" style="--i:2">
        {{-- CARD HEADER --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
            <h2 class="ui-reveal-text text-base font-bold text-gray-900 dark:text-slate-100" style="--i:2">
                Soil Scan History (<span class="ui-reveal-num" style="--i:2" x-data x-init="animateCount($el, {{ (int) ($soils->total() ?? 0) }}, { delay: 800 })">{{ $soils->total() ?? 0 }}</span>)
            </h2>
        </div>

        <div class="overflow-x-auto soil-table-container no-scrollbar">
            <table class="w-full soil-table">
                <thead>
                    <tr class="bg-[#F1FBF1] dark:bg-slate-800/80 border-b border-gray-100 dark:border-slate-800">
                        {{-- ID --}}
                        <th
                            wire:click="sortBy('id')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group col-sticky-id col-soil-id"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">ID</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'id')
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

                        {{-- SOL NAME --}}
                        <th
                            wire:click="sortBy('soil_name')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group col-sticky-soil whitespace-nowrap"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">SOIL CHARACTERISTICS</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'soil_name')
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

                        {{-- FULL CODE --}}
                        <th
                            wire:click="sortBy('full_code')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group col-full-code whitespace-nowrap"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">FULL CODE</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'full_code')
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


                        {{-- SCAN COUNT --}}
                        <th
                            wire:click="sortBy('scan_count')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group col-scan-count"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">SCAN COUNT</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'scan_count')
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

                        {{-- SCAN NOTE --}}
                        <th
                            wire:click="sortBy('scan_note')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group col-scan-note"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">SCAN NOTE</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'scan_note')
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

                        {{-- USER ID --}}
                        <th
                            wire:click="sortBy('admin_id')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group col-user-id"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">USER ID</span>
                                <span class="sort-arrows">
                                    @if($sortField === 'admin_id')
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

                        {{-- SCAN_DATE --}}
                        <th
                            wire:click="sortBy('created_at')"
                            class="px-5 py-4 text-left text-xs font-bold text-gray-500 cursor-pointer select-none group col-scan-date whitespace-nowrap"
                        >
                            <div class="flex items-center gap-1">
                                <span class="group-hover:text-[#22c55e] transition-colors duration-200">SCAN DATE</span>
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
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-500 whitespace-nowrap">
                            STATUS
                        </th>

                        {{-- ACTION (no sorting) --}}
                        <th class="px-3 py-4 text-center text-xs font-bold text-gray-500 whitespace-nowrap col-sticky-action">
                            ACTIONS
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($scans as $scan)
                    @php
                        $soil = $scan->soil;
                        $searchTrim = trim($search);
                        $sCode = $soil->full_code ?? $soil?->munsellColor?->full_code ?? '';
                        $sHue = $soil->hue ?? $soil?->munsellColor?->hue ?? '';
                        $sUser = $scan->user_id ?? $scan->user?->name ?? '226-0001';
                        $isSearchMatch = !empty($searchTrim) && (
                            (!empty($soil?->soil_name) && stripos($soil->soil_name, $searchTrim) !== false) ||
                            (!empty($sCode) && stripos($sCode, $searchTrim) !== false) ||
                            (!empty($sHue) && stripos($sHue, $searchTrim) !== false) ||
                            (!empty($sUser) && stripos((string)$sUser, $searchTrim) !== false) ||
                            (str_starts_with($searchTrim, '#') && '#' . $scan->soil_scan_id === $searchTrim)
                        );
                    @endphp
                    <tr class="border-b border-gray-100 transition {{ $isSearchMatch ? 'bg-emerald-50/80 border-l-4 border-l-emerald-500 font-medium' : 'hover:bg-green-50/40' }}">
                        {{-- ID --}}
                        <td class="px-5 py-5 text-gray-400 text-sm font-semibold whitespace-nowrap col-sticky-id col-soil-id">
                            #<span class="ui-reveal-num" style="--i:2" x-data x-init="animateCount($el, {{ (int) $scan->soil_scan_id }}, { delay: 1000 })">{{ $scan->soil_scan_id }}</span>
                        </td>

                        {{-- SOIL CHARACTERISTICS --}}
                        @php
                            $hueVal = $soil?->full_code ?? $soil?->munsellColor?->full_code ?? '10YR 4/3';
                            $scanDateFormatted = $scan->scan_date ? \Carbon\Carbon::parse($scan->scan_date)->format('m/d/Y') : $scan->created_at?->format('m/d/Y');
                        @endphp
                        <td class="px-5 py-5 col-sticky-soil">
                            <div>
                                <div class="text-sm font-bold text-[#475569] truncate" style="font-size: 14px; font-weight: 700; color: #475569;" title="{{ $soil?->soil_name ?? 'Scanned Soil' }}">
                                    {{ $soil?->soil_name ?? 'Scanned Soil' }}
                                </div>
                                {{-- MOBILE ONLY (<= 679px): Full Code & Date subtitle --}}
                                <div class="soil-mobile-meta text-xs font-semibold text-[#6b7280] mt-0.5">
                                    <span>{{ $hueVal }}</span>
                                    <span class="text-gray-400 font-normal ml-2">{{ $scanDateFormatted }}</span>
                                </div>
                                <div class="text-xs font-medium text-slate-400 truncate mt-0.5" style="font-size: 12px; font-weight: 500; color: #94a3b8;" title="{{ $scan->scan_note ?? $soil?->description ?? 'Single soil scan analysis record.' }}">
                                    {{ \Illuminate\Support\Str::limit($scan->scan_note ?? $soil?->description ?? 'Single soil scan analysis record.', 48, '...') }}
                                </div>
                            </div>
                        </td>

                        {{-- FULL CODE --}}
                        <td class="px-5 py-5 whitespace-nowrap col-full-code">
                            <span class="font-bold text-sm text-[#6b7280]" style="font-size: 14px; font-weight: 700; color: #6b7280;">
                                {{ $hueVal }}
                            </span>
                        </td>


                        {{-- SCAN COUNT --}}
                        <td class="px-5 py-5 text-sm text-gray-500 font-bold whitespace-nowrap col-scan-count">
                            @php
                                $fCode = $soil?->full_code ?? $soil?->munsellColor?->full_code;
                                $scanSeq = $fCode 
                                    ? \App\Models\SoilScan::whereHas('soil', function($q) use ($fCode) {
                                        $q->where('full_code', $fCode);
                                      })->where('soil_scan_id', '<=', $scan->soil_scan_id)->count()
                                    : 1;
                            @endphp
                            <span class="ui-reveal-num" style="--i:2" x-data x-init="animateCount($el, {{ (int) $scanSeq }}, { delay: 1000 })">{{ $scanSeq }}</span>
                        </td>

                        {{-- SCAN NOTE --}}
                        <td class="px-5 py-5 text-sm text-gray-500 font-bold max-w-[220px] truncate col-scan-note" title="{{ !empty($scan->scan_note) ? $scan->scan_note : '-' }}">
                            {{ !empty($scan->scan_note) ? \Illuminate\Support\Str::limit($scan->scan_note, 28, '...') : '-' }}
                        </td>

                        {{-- USER ID --}}
                        <td class="px-5 py-5 text-sm text-gray-500 font-bold whitespace-nowrap col-user-id">
                            {{ $scan->user_id ?? ($scan->user?->name ?? '226-0001') }}
                        </td>

                        {{-- SCAN_DATE / TIMESTAMP --}}
                        <td class="px-5 py-5 text-sm text-gray-500 font-bold whitespace-nowrap col-scan-date">
                            {{ $scan->scan_date ? \Carbon\Carbon::parse($scan->scan_date)->format('m/d/Y') : $scan->created_at?->format('m/d/Y') }}
                        </td>

                        {{-- STATUS --}}
                        <td class="px-4 py-5 text-center whitespace-nowrap">
                            @php
                                $modStatus = $scan->verification_status ?: 'Pending';
                            @endphp
                            @if(strtolower($modStatus) === 'verified')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Verified — Map pin is visible on public map">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Verified
                                </span>
                            @elseif(strtolower($modStatus) === 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200" title="Rejected — Map pin is hidden">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Pending — Awaiting review">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Pending
                                </span>
                            @endif
                        </td>

                        {{-- ACTION --}}
                        <td class="px-3 py-5 whitespace-nowrap col-sticky-action text-center">
                            <div class="flex justify-center items-center gap-1.5 whitespace-nowrap">
                                {{-- QUICK APPROVE --}}
                                @if(strtolower($scan->verification_status ?? '') !== 'verified')
                                <button
                                    wire:click="approveScan({{ $scan->soil_scan_id }})"
                                    class="agrico-approve-btn"
                                    title="Approve Scan & Publish Pin"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                @endif

                                {{-- QUICK REJECT --}}
                                @if(strtolower($scan->verification_status ?? '') !== 'rejected')
                                <button
                                    wire:click="rejectScan({{ $scan->soil_scan_id }})"
                                    class="agrico-reject-btn"
                                    title="Reject Scan & Hide Pin"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                @endif

                                {{-- VIEW (Line Eye) --}}
                                <button
                                    wire:click="$dispatch('view-soil',{id:{{ $scan->soil_scan_id }}})"
                                    class="agrico-view-btn"
                                    title="View Soil Scan"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                {{-- DELETE (Line Trash) --}}
                                <button
                                    wire:click="$dispatch('delete-soil',{id:{{ $scan->soil_scan_id }}})"
                                    class="agrico-delete-btn"
                                    title="Delete Soil Scan"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="100%" style="padding: 64px 24px; text-align: center; border-bottom: none;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;">
                                <div style="width: 56px; height: 56px; border-radius: 16px; background: #f0fdf4; border: 1px solid #dcfce7; display: flex; align-items: center; justify-content: center; color: #22c55e; box-shadow: 0 4px 14px rgba(22,163,74,0.12);">
                                    <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                                        <path d="M12 6v6l4 2"/>
                                    </svg>
                                </div>
                                <div style="font-size: 15.5px; font-weight: 800; color: #0f172a; margin-top: 4px;">
                                    No Soil Scans Found
                                </div>
                                <div style="font-size: 12.5px; font-weight: 500; color: #64748b; max-width: 360px; line-height: 1.4;">
                                    No soil scan history records matched your search query or active date filter.
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION FOOTER --}}
        <div style="padding: 16px 24px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: #fff;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b;">
                <span>Rows per page:</span>
                <select
                    wire:model.live="perPage"
                    style="border: 1px solid #e2e8f0; background: #fff; border-radius: 8px; padding: 4px 8px; font-size: 13px; color: #334155; font-weight: 600; outline: none; cursor: pointer;"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
                <span style="color: #94a3b8; font-size: 13px; margin-left: 4px;">
                    {{ $soils->firstItem() ?? 0 }}–{{ $soils->lastItem() ?? 0 }} of {{ $soils->total() }}
                </span>
            </div>

            @if ($soils->hasPages())
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: nowrap;">
                    @if ($soils->onFirstPage())
                        <button disabled style="padding: 5px 12px; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; color: #cbd5e1; font-size: 12.5px; font-weight: 600; cursor: not-allowed;">Prev</button>
                    @else
                        <button wire:click="previousPage" style="padding: 5px 12px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569';">Prev</button>
                    @endif

                    @php
                        $currentPage = $soils->currentPage();
                        $lastPage = $soils->lastPage();

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

                    @foreach ($soils->getUrlRange($startPage, $endPage) as $page => $url)
                        @if ($page == $currentPage)
                            <button wire:click="gotoPage({{ $page }})" style="width: 32px; height: 32px; border-radius: 8px; background: #22c55e !important; color: #fff !important; font-size: 13px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(34,197,94,0.35);">{{ $page }}</button>
                        @else
                            <button wire:click="gotoPage({{ $page }})" style="width: 32px; height: 32px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; color: #334155; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#334155';">{{ $page }}</button>
                        @endif
                    @endforeach

                    @if ($endPage < $lastPage)
                        <button wire:click="gotoPage({{ min($lastPage, $endPage + 1) }})" style="min-width: 24px; height: 32px; background: transparent; border: none; color: #64748b; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; transition: color 0.15s ease;" onmouseover="this.style.color='#22c55e';" onmouseout="this.style.color='#64748b';" title="Page {{ min($lastPage, $endPage + 1) }}">...</button>
                    @endif

                    @if ($soils->hasMorePages())
                        <button wire:click="nextPage" style="padding: 5px 12px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #475569; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#22c55e'; this.style.color='#22c55e';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#475569';">Next</button>
                    @else
                        <button disabled style="padding: 5px 12px; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; color: #cbd5e1; font-size: 12.5px; font-weight: 600; cursor: not-allowed;">Next</button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- MODALS --}}
    <livewire:admin.soil.soil-detail-modal />
    <livewire:admin.soil.soil-form-modal />
    <livewire:admin.soil.soil-delete-modal />
</div>
