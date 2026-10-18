<div>
@if($showSectionRecordsModal)
@php
    $cards = !empty($activityCards) ? $activityCards : [[
        'id' => 1,
        'activity_name' => 'Activity #1',
        'scan_name' => $sectionRecordsColumnName ?: 'SCAN SOIL ACTIVITY',
        'pin_name' => 'PIN LOCATION ACTIVITY',
        'date' => $sectionRecordsDateFilter ?: now()->format('Y-m-d'),
        'start_time' => $sectionRecordsStartTime ?: '',
        'end_time' => $sectionRecordsEndTime ?: '',
        'show_scans' => $sectionRecordsShowScans ?? true,
        'show_pins' => $sectionRecordsShowPins ?? true,
    ]];
@endphp

{{-- BACKDROP BLUR OVERLAY --}}
<div
    x-data
    x-init="document.body.classList.add('overflow-hidden')"
    x-on:unmount="document.body.classList.remove('overflow-hidden')"
    class="fixed inset-0 z-50 flex items-center justify-center"
    style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 100001; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; padding: 16px; box-sizing: border-box;"
    wire:click.self="closeSectionRecordsModal"
>
    {{-- MODAL CARD CONTAINER (LIGHT MODE MATCHING ADMIN THEME) --}}
    <div class="section-records-dialog" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 28px; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.25); width: 100%; max-width: 680px; margin: auto; display: flex; flex-direction: column; height: auto; max-height: min(85vh, calc(100dvh - 24px)); overflow: hidden; position: relative;">

        {{-- 1. FIXED MODAL HEADER (LIGHT MODE) --}}
        <div class="section-records-header" style="padding: 20px 28px 16px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; background: #ffffff; flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 14px; background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #16a34a; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(34, 197, 94, 0.15);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;">
                        Save as Copy &mdash; Section Records &amp; Activity
                    </h3>
                    <p style="font-size: 12px; color: #64748b; margin: 3px 0 0 0; font-weight: 500;">
                        Student activity report per section &bull; Filtered by scan date, time and section
                    </p>
                </div>
            </div>
            <x-modal-close-button click="closeSectionRecordsModal" />
        </div>

        {{-- 2. SCROLLABLE BODY CONTENT (LIGHT MODE) --}}
        <div class="section-records-body" style="padding: 20px 28px 24px; overflow-y: auto; flex: 1 1 auto; box-sizing: border-box; display: flex; flex-direction: column; gap: 16px; background: #f8fafc; scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">

            {{-- TOP: GLOBAL SECTION FILTER --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px 18px; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
                <label style="display: block; font-size: 11.5px; font-weight: 800; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.03em;">
                    Filter Section
                </label>
                <select
                    wire:model.live="sectionRecordsSectionFilter"
                    style="width: 100%; height: 42px; padding: 0 14px; border: 1.5px solid #cbd5e1; border-radius: 12px; font-size: 13.5px; font-weight: 600; color: #0f172a; outline: none; background: #ffffff; font-family: inherit; box-sizing: border-box;"
                >
                    <option value="" style="background: #ffffff; color: #0f172a;">All Sections</option>
                    @if(!empty($availableSections))
                        @foreach($availableSections as $sec)
                            <option value="{{ $sec }}" style="background: #ffffff; color: #0f172a;">Section: {{ $sec }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- ACTIVITY CARDS LIST --}}
            <div style="display: flex; flex-direction: column; gap: 14px;">
                @foreach($cards as $index => $card)
                    <div
                        wire:key="activity-card-{{ $card['id'] ?? $index }}"
                        style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 18px; padding: 18px 20px; display: flex; flex-direction: column; gap: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); position: relative;"
                    >
                        {{-- CARD HEADER / BADGE AS EDITABLE TEXT BOX (SIZED LIKE STANDARD INPUTS) --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <div style="display: inline-flex; align-items: center; position: relative;">
                                    <input
                                        type="text"
                                        wire:model.live.debounce.300ms="activityCards.{{ $index }}.activity_name"
                                        value="{{ $card['activity_name'] ?? ('Activity #' . ($index + 1)) }}"
                                        placeholder="Activity #{{ $index + 1 }}"
                                        style="width: 240px; height: 42px; padding: 0 16px; border-radius: 12px; background: #f0fdf4; border: 1.5px solid #86efac; color: #166534; font-size: 13.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; outline: none; box-shadow: 0 1px 2px rgba(22, 163, 74, 0.08); transition: all 0.15s ease; box-sizing: border-box; font-family: inherit;"
                                        onfocus="this.style.borderColor='#16a34a'; this.style.background='#ffffff'; this.style.boxShadow='0 0 0 3px rgba(34, 197, 94, 0.2)';"
                                        onblur="this.style.borderColor='#86efac'; this.style.background='#f0fdf4'; this.style.boxShadow='0 1px 2px rgba(22, 163, 74, 0.08)';"
                                        title="Click to rename Activity"
                                    />
                                </div>
                                @if(count($cards) > 1)
                                    <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                                        Creates dedicated report column{{ (!empty($card['show_scans']) && !empty($card['show_pins'])) ? 's' : '' }}
                                    </span>
                                @endif
                            </div>

                            @if(count($cards) > 1)
                                <button
                                    type="button"
                                    wire:click="removeActivityCard({{ $index }})"
                                    style="height: 38px; padding: 0 14px; font-size: 12px; font-weight: 700; color: #dc2626; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s ease;"
                                    onmouseover="this.style.background='#fee2e2';"
                                    onmouseout="this.style.background='#fef2f2';"
                                    title="Remove this activity card"
                                >
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                    <span>Remove</span>
                                </button>
                            @endif
                        </div>

                        {{-- UNIFIED FILTER ROW: Column Name on left of Activity Date, followed by Start Time, End Time, and Activity Checkboxes --}}
                        <div style="display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;">
                            {{-- 1. Activity / Column Name (Scan) --}}
                            @if(!empty($card['show_scans']))
                                <div style="flex: 1.2; min-width: 200px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                        <label style="font-size: 11.5px; font-weight: 800; color: #166534; text-transform: uppercase; letter-spacing: 0.03em; display: inline-flex; align-items: center; gap: 6px;">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #16a34a; display: inline-block;"></span>
                                            Column Name (Scan)
                                        </label>
                                        @if(!empty($card['scan_name']) && $card['scan_name'] !== ('ACTIVITY #' . ($index + 1) . ' (SCAN)') && $card['scan_name'] !== 'SCAN SOIL ACTIVITY')
                                            <button
                                                type="button"
                                                wire:click="$set('activityCards.{{ $index }}.scan_name', '{{ !empty($card['activity_name']) ? ($card['activity_name'] . ' (SCAN)') : ('ACTIVITY #' . ($index + 1) . ' (SCAN)') }}')"
                                                style="font-size: 10.5px; font-weight: 700; color: #64748b; background: none; border: none; cursor: pointer; padding: 0;"
                                                title="Reset to default title"
                                            >
                                                Reset
                                            </button>
                                        @endif
                                    </div>
                                    <input
                                        type="text"
                                        wire:model.live.debounce.300ms="activityCards.{{ $index }}.scan_name"
                                        placeholder="e.g. SCAN SOIL ACTIVITY"
                                        style="width: 100%; height: 42px; padding: 0 14px; border: 1.5px solid #cbd5e1; border-radius: 12px; font-size: 13.5px; font-weight: 600; color: #0f172a; outline: none; background: #ffffff; font-family: inherit; box-sizing: border-box;"
                                    />
                                </div>
                            @endif

                            {{-- 2. Activity / Column Name (Pin Location) --}}
                            @if(!empty($card['show_pins']))
                                <div style="flex: 1.2; min-width: 200px;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                        <label style="font-size: 11.5px; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 0.03em; display: inline-flex; align-items: center; gap: 6px;">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #d97706; display: inline-block;"></span>
                                            Column Name (Pin)
                                        </label>
                                        @if(!empty($card['pin_name']) && $card['pin_name'] !== ('ACTIVITY #' . ($index + 1) . ' (PIN LOCATION)') && $card['pin_name'] !== 'PIN LOCATION ACTIVITY')
                                            <button
                                                type="button"
                                                wire:click="$set('activityCards.{{ $index }}.pin_name', '{{ !empty($card['activity_name']) ? ($card['activity_name'] . ' (PIN LOCATION)') : ('ACTIVITY #' . ($index + 1) . ' (PIN LOCATION)') }}')"
                                                style="font-size: 10.5px; font-weight: 700; color: #64748b; background: none; border: none; cursor: pointer; padding: 0;"
                                                title="Reset to default title"
                                            >
                                                Reset
                                            </button>
                                        @endif
                                    </div>
                                    <input
                                        type="text"
                                        wire:model.live.debounce.300ms="activityCards.{{ $index }}.pin_name"
                                        placeholder="e.g. PIN LOCATION ACTIVITY"
                                        style="width: 100%; height: 42px; padding: 0 14px; border: 1.5px solid #cbd5e1; border-radius: 12px; font-size: 13.5px; font-weight: 600; color: #0f172a; outline: none; background: #ffffff; font-family: inherit; box-sizing: border-box;"
                                    />
                                </div>
                            @endif

                            {{-- 3. Activity Date (placed on right of Column Name) --}}
                            <div style="flex: 1; min-width: 170px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                    <label style="font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.03em;">
                                        Activity Date
                                    </label>
                                    <div style="display: flex; gap: 6px;">
                                        <button
                                            type="button"
                                            wire:click="setActivityCardDateToday({{ $index }})"
                                            style="font-size: 11px; font-weight: 700; color: #16a34a; background: #dcfce7; border: 1px solid #86efac; border-radius: 6px; padding: 2px 8px; cursor: pointer;"
                                            title="Set date to today"
                                        >
                                            Today
                                        </button>
                                        @if(!empty($card['date']))
                                            <button
                                                type="button"
                                                wire:click="clearActivityCardDate({{ $index }})"
                                                style="font-size: 11px; font-weight: 700; color: #475569; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px 8px; cursor: pointer;"
                                                title="View all dates"
                                            >
                                                All Dates
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <input
                                    type="date"
                                    wire:model.live="activityCards.{{ $index }}.date"
                                    style="width: 100%; height: 42px; padding: 0 14px; border: 1.5px solid #cbd5e1; border-radius: 12px; font-size: 13.5px; font-weight: 600; color: #0f172a; outline: none; background: #ffffff; font-family: inherit; box-sizing: border-box; color-scheme: light;"
                                />
                            </div>

                            {{-- 4. Start Time (after Activity Date) --}}
                            <div style="flex: 0.75; min-width: 120px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                    <label style="font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.03em;">
                                        Start Time
                                    </label>
                                    @if(!empty($card['start_time']))
                                        <button
                                            type="button"
                                            wire:click="$set('activityCards.{{ $index }}.start_time', '')"
                                            style="font-size: 10.5px; font-weight: 700; color: #dc2626; background: none; border: none; cursor: pointer; padding: 0;"
                                            title="Clear start time"
                                        >
                                            Clear
                                        </button>
                                    @endif
                                </div>
                                <input
                                    type="time"
                                    wire:model.live="activityCards.{{ $index }}.start_time"
                                    style="width: 100%; height: 42px; padding: 0 12px; border: 1.5px solid #cbd5e1; border-radius: 12px; font-size: 13.5px; font-weight: 600; color: #0f172a; outline: none; background: #ffffff; font-family: inherit; box-sizing: border-box; color-scheme: light;"
                                />
                            </div>

                            {{-- 5. End Time (after Start Time) --}}
                            <div style="flex: 0.75; min-width: 120px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                    <label style="font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.03em;">
                                        End Time
                                    </label>
                                    @if(!empty($card['end_time']))
                                        <button
                                            type="button"
                                            wire:click="$set('activityCards.{{ $index }}.end_time', '')"
                                            style="font-size: 10.5px; font-weight: 700; color: #dc2626; background: none; border: none; cursor: pointer; padding: 0;"
                                            title="Clear end time"
                                        >
                                            Clear
                                        </button>
                                    @endif
                                </div>
                                <input
                                    type="time"
                                    wire:model.live="activityCards.{{ $index }}.end_time"
                                    style="width: 100%; height: 42px; padding: 0 12px; border: 1.5px solid #cbd5e1; border-radius: 12px; font-size: 13.5px; font-weight: 600; color: #0f172a; outline: none; background: #ffffff; font-family: inherit; box-sizing: border-box; color-scheme: light;"
                                />
                            </div>

                            {{-- 6. Checkboxes for Scan & Pin Location (Elegant Modern Capsule Toggles) --}}
                            <div style="flex: 1.1; min-width: 210px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                    <label style="font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.03em;">
                                        Include Activity
                                    </label>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; height: 42px;">
                                    {{-- Scan Capsule --}}
                                    @php $scansChecked = !empty($card['show_scans']); @endphp
                                    <label
                                        class="xl-metric-pill"
                                        style="flex: 1; height: 42px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 12px; border-radius: 12px; cursor: pointer; user-select: none; font-size: 13px; font-weight: 700; box-sizing: border-box; border: 1.5px solid {{ $scansChecked ? '#86efac' : '#e2e8f0' }}; background: {{ $scansChecked ? '#f0fdf4' : '#ffffff' }}; color: {{ $scansChecked ? '#15803d' : '#64748b' }}; box-shadow: {{ $scansChecked ? '0 1px 3px rgba(22, 163, 74, 0.12)' : '0 1px 2px rgba(0,0,0,0.02)' }};"
                                        title="Toggle Scan Column"
                                    >
                                        <input
                                            type="checkbox"
                                            wire:model.live="activityCards.{{ $index }}.show_scans"
                                            style="position: absolute; opacity: 0; pointer-events: none; width: 0; height: 0;"
                                        />
                                        <span style="width: 18px; height: 18px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease; border: 1.5px solid {{ $scansChecked ? '#16a34a' : '#cbd5e1' }}; background: {{ $scansChecked ? '#16a34a' : '#f8fafc' }}; color: #ffffff; flex-shrink: 0;">
                                            @if($scansChecked)
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"/>
                                                </svg>
                                            @endif
                                        </span>
                                        <span style="font-weight: 700; letter-spacing: -0.01em;">Scan</span>
                                    </label>

                                    {{-- Pin Location Capsule --}}
                                    @php $pinsChecked = !empty($card['show_pins']); @endphp
                                    <label
                                        class="xl-metric-pill"
                                        style="flex: 1.25; height: 42px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 12px; border-radius: 12px; cursor: pointer; user-select: none; font-size: 13px; font-weight: 700; box-sizing: border-box; border: 1.5px solid {{ $pinsChecked ? '#fde68a' : '#e2e8f0' }}; background: {{ $pinsChecked ? '#fffbeb' : '#ffffff' }}; color: {{ $pinsChecked ? '#b45309' : '#64748b' }}; box-shadow: {{ $pinsChecked ? '0 1px 3px rgba(217, 119, 6, 0.12)' : '0 1px 2px rgba(0,0,0,0.02)' }};"
                                        title="Toggle Pin Location Column"
                                    >
                                        <input
                                            type="checkbox"
                                            wire:model.live="activityCards.{{ $index }}.show_pins"
                                            style="position: absolute; opacity: 0; pointer-events: none; width: 0; height: 0;"
                                        />
                                        <span style="width: 18px; height: 18px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease; border: 1.5px solid {{ $pinsChecked ? '#d97706' : '#cbd5e1' }}; background: {{ $pinsChecked ? '#d97706' : '#f8fafc' }}; color: #ffffff; flex-shrink: 0;">
                                            @if($pinsChecked)
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"/>
                                                </svg>
                                            @endif
                                        </span>
                                        <span style="font-weight: 700; letter-spacing: -0.01em;">Pin Location</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- THE "+" CARD: ADDS ANOTHER ACTIVITY CARD, WILL NOT HIDE --}}
                <button
                    type="button"
                    wire:click="addActivityCard"
                    style="width: 100%; border: 2px dashed #cbd5e1; border-radius: 18px; background: #ffffff; padding: 16px 22px; display: flex; align-items: center; justify-content: center; gap: 12px; cursor: pointer; transition: all 0.2s ease; color: #16a34a; font-size: 14px; font-weight: 800; box-shadow: 0 1px 3px rgba(0,0,0,0.02);"
                    onmouseover="this.style.borderColor='#16a34a'; this.style.background='#f0fdf4';"
                    onmouseout="this.style.borderColor='#cbd5e1'; this.style.background='#ffffff';"
                    title="Add another activity card"
                >
                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #dcfce7; border: 1.5px solid #86efac; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #16a34a; line-height: 1;">
                        +
                    </div>
                    <span>Add Another Activity</span>
                </button>
            </div>

            {{-- DOCUMENT TITLE INPUT (DIRECTLY AFTER FILTER SECTION) --}}
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px 18px; box-shadow: 0 1px 4px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 6px;">
                <label style="display: block; font-size: 11.5px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.03em;">
                    Document Title
                </label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="documentTitle"
                    placeholder="GENERAL ACTION PLAN FOR ACADEMIC YEAR 2025-2026"
                    style="width: 100%; min-height: 42px; padding: 0 14px; border: 1.5px solid #cbd5e1; border-radius: 12px; font-size: 13.5px; font-weight: 600; color: #0f172a; outline: none; background: #ffffff; font-family: inherit; box-sizing: border-box; transition: all 0.15s ease;"
                    onfocus="this.style.borderColor='#16a34a'; this.style.boxShadow='0 0 0 3px rgba(34, 197, 94, 0.2)';"
                    onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                />
            </div>

            {{-- SUMMARY PILLS BAR --}}
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; padding: 12px 16px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; font-size: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span style="font-weight: 800; color: #0f172a;">
                        {{ $sectionRecords->count() }} student{{ $sectionRecords->count() !== 1 ? 's' : '' }} found
                    </span>

                    @if($sectionRecordsSectionFilter)
                        <span style="background: #f0fdf4; color: #16a34a; border: 1px solid #86efac; padding: 2px 9px; border-radius: 9999px; font-weight: 700; font-size: 11px;">
                            Section: {{ $sectionRecordsSectionFilter }}
                        </span>
                    @else
                        <span style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; padding: 2px 9px; border-radius: 9999px; font-weight: 600; font-size: 11px;">
                            All Sections
                        </span>
                    @endif

                    <span style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 2px 9px; border-radius: 9999px; font-weight: 700; font-size: 11px;">
                        {{ count($cards) }} Activit{{ count($cards) !== 1 ? 'ies' : 'y' }}
                    </span>
                </div>

                @if(!empty($cards[0]['show_scans']))
                    <div style="color: #16a34a; font-weight: 700; font-size: 11.5px;">
                        {{ !empty($cards[0]['activity_name']) ? $cards[0]['activity_name'] : 'Activity #1' }} Total Scans: {{ $sectionRecords->sum('card_0_scans_count') }}
                    </div>
                @endif
            </div>

            {{-- EXCEL-STYLE PARTICIPATION GRID --}}
            <div class="xl-wrap" style="flex-shrink: 0; display: block; width: 100%; min-height: 280px; max-height: 480px; overflow: auto; border: 1.5px solid #cbd5e1; border-radius: 14px; margin-top: 10px; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.03); box-sizing: border-box;">
                <table class="xl-table">
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Student Name</th>
                            <th rowspan="2">Section</th>
                            @foreach($cards as $i => $card)
                                @php
                                    $cols = (!empty($card['show_scans']) ? 1 : 0) + (!empty($card['show_pins']) ? 1 : 0);

                                    // Date label: "All Dates" toggle, a single date, or fallback
                                    $dateLabel = !empty($card['all_dates'])
                                        ? 'All Dates'
                                        : (!empty($card['date']) ? \Carbon\Carbon::parse($card['date'])->format('m/d/Y') : 'All Dates');

                                    // Time label: "8:00 AM – 10:00 AM", "From 8:00 AM", "Until 10:00 AM", or "All Day"
                                    $start = !empty($card['start_time']) ? \Carbon\Carbon::parse($card['start_time'])->format('g:i A') : null;
                                    $end   = !empty($card['end_time'])   ? \Carbon\Carbon::parse($card['end_time'])->format('g:i A')   : null;
                                    $timeLabel = $start && $end ? "{$start} – {$end}"
                                               : ($start ? "From {$start}" : ($end ? "Until {$end}" : 'All Day'));
                                @endphp
                                @if($cols)
                                    <th colspan="{{ $cols }}" class="xl-group">
                                        <div>{{ !empty($card['activity_name']) ? $card['activity_name'] : ('Activity #' . ($i + 1)) }}</div>
                                        <div class="xl-window">{{ trim($dateLabel . ' · ' . $timeLabel, ' ·') }}</div>
                                    </th>
                                @endif
                            @endforeach
                            <th rowspan="2">Total</th>
                        </tr>
                        <tr>
                            @foreach($cards as $i => $card)
                                @if(!empty($card['show_scans']))
                                    <th class="xl-scan">{{ $card['scan_name'] ?? 'Scan' }}</th>
                                @endif
                                @if(!empty($card['show_pins']))
                                    <th class="xl-pin">{{ $card['pin_name'] ?? 'Pin Location' }}</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sectionRecords as $n => $student)
                            @php $rowTotal = 0; @endphp
                            <tr>
                                <td class="num">{{ $n + 1 }}</td>
                                <td style="font-weight: 600; color: #0f172a;">{{ $student->name }}</td>
                                <td>{{ $student->section }}</td>
                                @foreach($cards as $i => $card)
                                    @if(!empty($card['show_scans']))
                                        @php $s = (int) ($student->{"card_{$i}_scans_count"} ?? 0); $rowTotal += $s; @endphp
                                        <td class="num">
                                            @if($s > 0)
                                                <span style="color: #16a34a; font-weight: 700;">{{ $s }}</span>
                                            @else
                                                <span style="color: #94a3b8;">0</span>
                                            @endif
                                        </td>
                                    @endif
                                    @if(!empty($card['show_pins']))
                                        @php $p = (int) ($student->{"card_{$i}_pins_count"} ?? 0); $rowTotal += $p; @endphp
                                        <td class="num">
                                            @if($p > 0)
                                                <span style="color: #d97706; font-weight: 700;">{{ $p }}</span>
                                            @else
                                                <span style="color: #94a3b8;">0</span>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                                <td class="num"><b>{{ $rowTotal }}</b></td>
                            </tr>
                        @empty
                            <tr><td colspan="99" style="text-align:center; color:#94a3b8; padding: 24px;">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <style>
            .xl-wrap { flex-shrink: 0 !important; display: block !important; width: 100% !important; min-height: 280px !important; max-height: 480px !important; overflow: auto !important; border: 1.5px solid #cbd5e1 !important; border-radius: 14px !important; margin-top: 10px !important; background: #ffffff !important; box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important; box-sizing: border-box !important; }
            .xl-table { border-collapse: collapse !important; width: 100% !important; min-width: 100% !important; font-size: 13px !important; display: table !important; }
            .xl-table th, .xl-table td { border: 1px solid #cbd5e1 !important; padding: 10px 14px !important; white-space: nowrap !important; }
            .xl-table thead th { background: #f1f5f9 !important; color: #334155 !important; font-weight: 700 !important; text-align: center !important; position: sticky !important; top: 0 !important; z-index: 2 !important; }
            .xl-table thead tr:nth-child(2) th { top: 48px !important; }
            .xl-table thead th.xl-group { background: #e2e8f0 !important; color: #1e293b !important; font-weight: 800 !important; letter-spacing: 0.03em !important; }
            .xl-window { font-size: 10.5px !important; font-weight: 600 !important; color: #64748b !important; margin-top: 2px !important; }
            .xl-table tbody tr:nth-child(even) { background: #f8fafc !important; }
            .xl-table tbody tr:hover { background: #f1f5f9 !important; }
            .xl-table td.num { text-align: center !important; font-variant-numeric: tabular-nums !important; }
            .xl-scan { color: #16a34a !important; }
            .xl-pin  { color: #d97706 !important; }
            .xl-metric-pill { transition: all 0.15s ease !important; }
            .xl-metric-pill:hover { filter: brightness(0.98) !important; transform: translateY(-1px) !important; }
            .section-records-dialog {
                width: 100% !important;
                max-width: 680px !important;
                height: auto !important;
                max-height: min(85vh, calc(100dvh - 24px)) !important;
                border-radius: 28px !important;
                margin: auto !important;
            }
            @media (max-width: 640px) {
                .section-records-dialog {
                    width: 100% !important;
                    max-width: calc(100vw - 24px) !important;
                    height: auto !important;
                    max-height: calc(100dvh - 20px) !important;
                    border-radius: 24px !important;
                    margin: auto !important;
                }
                .section-records-header {
                    padding: 14px 16px !important;
                }
                .section-records-header h3 {
                    font-size: 15px !important;
                }
                .section-records-header p {
                    font-size: 11px !important;
                }
                .section-records-body {
                    padding: 14px 12px 18px !important;
                    gap: 12px !important;
                }
                .section-records-footer {
                    padding: 12px 14px 16px !important;
                    flex-direction: column !important;
                    gap: 10px !important;
                }
                .section-records-footer > div {
                    width: 100% !important;
                    justify-content: center !important;
                }
            }
            @media (max-height: 500px) and (orientation: landscape) {
                .section-records-dialog {
                    width: 100% !important;
                    max-width: 680px !important;
                    height: auto !important;
                    max-height: calc(100dvh - 20px) !important;
                    border-radius: 24px !important;
                }
            }
            #sectionRecordsPrintArea {
                display: none !important;
            }
            @media print {
                @page {
                    size: A4 landscape;
                    margin: 0 !important;
                }
                html, body {
                    background: #ffffff !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    width: 100% !important;
                }
                body * {
                    visibility: hidden !important;
                }
                .fixed, [class*="fixed"], .section-records-dialog {
                    display: none !important;
                }
                #sectionRecordsPrintArea, #sectionRecordsPrintArea * {
                    visibility: visible !important;
                }
                #sectionRecordsPrintArea {
                    display: block !important;
                    position: static !important;
                    left: 0 !important;
                    top: 0 !important;
                    width: 100% !important;
                    box-sizing: border-box !important;
                    background: #ffffff !important;
                    color: #000000 !important;
                    padding: 5mm 8mm !important;
                    margin: 0 !important;
                    font-family: Arial, sans-serif !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
                #sectionRecordsPrintArea table {
                    width: 100% !important;
                    border-collapse: collapse !important;
                    page-break-inside: auto !important;
                }
                #sectionRecordsPrintArea thead {
                    display: table-header-group !important;
                }
                #sectionRecordsPrintArea tr {
                    page-break-inside: avoid !important;
                    page-break-after: auto !important;
                }
                #sectionRecordsPrintArea th,
                #sectionRecordsPrintArea td {
                    border: 1px solid #000000 !important;
                    background: #ffffff !important;
                    color: #000000 !important;
                    padding: 4px 6px !important;
                    font-size: 8pt !important;
                }
                #sectionRecordsPrintArea th {
                    font-weight: bold !important;
                }
            }
            </style>
        </div>

        {{-- 3. FIXED MODAL FOOTER (LIGHT MODE) --}}
        <div class="section-records-footer" style="padding: 16px 28px 20px; background: #ffffff; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; width: 100%; box-sizing: border-box; position: relative;">
            <div style="font-size: 12.5px; color: #64748b;">
                Showing <strong style="color: #0f172a;">{{ $sectionRecords->count() }}</strong> active student record{{ $sectionRecords->count() !== 1 ? 's' : '' }}
            </div>
            <div style="display: flex; align-items: center; gap: 12px; position: relative;">
                {{-- 3-OPTION MENU FOR PRINT / SAVE COPY --}}
                <div x-data="{ openMenu: false }" style="position: relative;" @click.outside="openMenu = false" @keydown.escape.window="openMenu = false">
                    <button
                        type="button"
                        @click="openMenu = !openMenu"
                        style="min-height: 42px; min-width: 40px; padding: 0 16px; border-radius: 12px; background: #f8fafc; border: 1.5px solid #cbd5e1; color: #0f172a; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s ease; box-sizing: border-box;"
                        onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#94a3b8';"
                        onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';"
                        title="Print or Save Copy"
                    >
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Print / Save Copy</span>
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="transition: transform 0.2s ease;" :style="openMenu ? 'transform: rotate(180deg)' : ''">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    <div
                        x-show="openMenu"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                        style="display: none; position: absolute; bottom: calc(100% + 8px); right: 0; min-width: 210px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 14px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1); padding: 6px; z-index: 10000; box-sizing: border-box;"
                    >
                        {{-- OPTION 1: PRINT --}}
                        <button
                            type="button"
                            @click="openMenu = false; if (typeof window.printSectionRecords === 'function') { window.printSectionRecords(); } else { window.print(); }"
                            style="width: 100%; min-height: 40px; padding: 8px 14px; border-radius: 10px; border: none; background: transparent; color: #0f172a; font-size: 13px; font-weight: 600; text-align: left; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: background 0.15s ease; box-sizing: border-box;"
                            onmouseover="this.style.background='#f1f5f9';"
                            onmouseout="this.style.background='transparent';"
                        >
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="color: #475569;">
                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                <rect x="6" y="14" width="12" height="8"></rect>
                            </svg>
                            <span>Print</span>
                        </button>

                        {{-- OPTION 2: MS WORD (.docx) --}}
                        <button
                            type="button"
                            wire:click="exportSectionDocx"
                            @click="openMenu = false"
                            style="width: 100%; min-height: 40px; padding: 8px 14px; border-radius: 10px; border: none; background: transparent; color: #0f172a; font-size: 13px; font-weight: 600; text-align: left; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: background 0.15s ease; box-sizing: border-box;"
                            onmouseover="this.style.background='#f1f5f9';"
                            onmouseout="this.style.background='transparent';"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            <span>MS Word (.docx)</span>
                        </button>

                        {{-- OPTION 3: MS EXCEL (.xlsx) --}}
                        <button
                            type="button"
                            wire:click="exportSectionXlsx"
                            @click="openMenu = false"
                            style="width: 100%; min-height: 40px; padding: 8px 14px; border-radius: 10px; border: none; background: transparent; color: #0f172a; font-size: 13px; font-weight: 600; text-align: left; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: background 0.15s ease; box-sizing: border-box;"
                            onmouseover="this.style.background='#f1f5f9';"
                            onmouseout="this.style.background='transparent';"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="M8 13h8M8 17h8M10 9h4"></path>
                            </svg>
                            <span>MS Excel (.xlsx)</span>
                        </button>
                    </div>
                </div>

                <button
                    wire:click="closeSectionRecordsModal"
                    type="button"
                    style="height: 42px; min-width: 40px; padding: 0 24px; border-radius: 12px; background: linear-gradient(135deg, #22c55e, #16a34a); border: none; color: #ffffff; font-size: 13.5px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35); box-sizing: border-box;"
                    onmouseover="this.style.background='#16a34a';"
                    onmouseout="this.style.background='linear-gradient(135deg, #22c55e, #16a34a)';"
                >
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

{{-- DEDICATED PRINT-ONLY LAYOUT (LANDSCAPE, BLACK & WHITE ONLY, CSU HEADER, DOCUMENT TITLE, TABLE, FOOTER) --}}
<div id="sectionRecordsPrintArea">
        {{-- TOP 3-COLUMN HEADER TABLE (Left: Republic & CSU info, Center: Logo, Right: Contact info) --}}
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; box-sizing: border-box; font-family: Arial, sans-serif;">
            {{-- 1. Left: Republic & CSU Addresses --}}
            <div style="flex: 1; text-align: left; font-size: 7.5pt; line-height: 1.25; color: #000000;">
                <div style="font-weight: bold; font-size: 8pt; letter-spacing: 0.02em;">REPUBLIC OF THE PHILIPPINES</div>
                <div style="font-weight: bold; font-size: 9pt; margin-top: 1px;">CAGAYAN STATE UNIVERSITY</div>
                <div style="font-size: 6.5pt; margin-top: 1px;">Andrews Campus, Tuguegarao City, Cagayan (Central Office)</div>
                <div style="font-size: 6.5pt;">CSU Lal-lo Campus, Sta. Maria, Lal-lo, Cagayan (Sub Office)</div>
            </div>

            {{-- 2. Center: CSU Logo --}}
            <div style="flex: 0 0 auto; text-align: center; padding: 0 10px;">
                @if(file_exists(public_path('images/csu-logo.png')))
                    <img src="{{ asset('images/csu-logo.png') }}" alt="CSU Logo" style="height: 42px; width: auto; object-fit: contain; display: inline-block;" />
                @endif
            </div>

            {{-- 3. Right: Contact Info (labels only) --}}
            <div style="flex: 1; text-align: right; font-size: 7pt; line-height: 1.3; color: #000000;">
                <div style="text-align: right;"><strong style="font-weight: bold;">Phone Number:</strong></div>
                <div style="text-align: right;"><strong style="font-weight: bold;">Email Address:</strong></div>
                <div style="text-align: right;"><strong style="font-weight: bold;">Facebook page:</strong></div>
            </div>
        </div>

        {{-- CAMPUS MAIN TITLE --}}
        <div style="text-align: center; font-size: 10pt; font-weight: bold; color: #000000; margin-top: 4px; margin-bottom: 3px; letter-spacing: 0.02em; font-family: Arial, sans-serif;">
            CAGAYAN STATE UNIVERSITY Lal-lo Campus
        </div>

        {{-- HORIZONTAL DIVIDER LINE --}}
        <div style="width: 100%; border-bottom: 1.5px solid #000000; margin-bottom: 5px;"></div>

        {{-- DOCUMENT TITLE (AFTER THE LINE, WITH SPACE BEFORE AND AFTER) --}}
        @if(!empty(trim($documentTitle)))
            <div style="text-align: center; font-size: 10pt; font-weight: bold; color: #000000; margin-top: 5px; margin-bottom: 6px; text-transform: uppercase; font-family: Arial, sans-serif;">
                {{ trim($documentTitle) }}
            </div>
        @else
            <div style="margin-bottom: 4px;"></div>
        @endif

        {{-- PRINT TABLE --}}
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000000; font-family: Arial, sans-serif; font-size: 8pt; color: #000000;">
            <thead>
                <tr style="page-break-inside: avoid;">
                    <th rowspan="2" style="border: 1px solid #000000; padding: 4px 6px; text-align: center; font-weight: bold; background: #ffffff; color: #000000; width: 36px;">#</th>
                    <th rowspan="2" style="border: 1px solid #000000; padding: 4px 6px; text-align: left; font-weight: bold; background: #ffffff; color: #000000;">Student Name</th>
                    <th rowspan="2" style="border: 1px solid #000000; padding: 4px 6px; text-align: center; font-weight: bold; background: #ffffff; color: #000000; width: 90px;">Section</th>
                    @foreach($cards as $i => $card)
                        @php
                            $cols = (!empty($card['show_scans']) ? 1 : 0) + (!empty($card['show_pins']) ? 1 : 0);
                            $dateLabel = !empty($card['all_dates'])
                                ? 'All Dates'
                                : (!empty($card['date']) ? \Carbon\Carbon::parse($card['date'])->format('m/d/Y') : 'All Dates');
                            $start = !empty($card['start_time']) ? \Carbon\Carbon::parse($card['start_time'])->format('g:i A') : null;
                            $end   = !empty($card['end_time'])   ? \Carbon\Carbon::parse($card['end_time'])->format('g:i A')   : null;
                            $timeLabel = $start && $end ? "{$start} – {$end}"
                                       : ($start ? "From {$start}" : ($end ? "Until {$end}" : 'All Day'));
                        @endphp
                        @if($cols)
                            <th colspan="{{ $cols }}" style="border: 1px solid #000000; padding: 4px 6px; text-align: center; font-weight: bold; background: #ffffff; color: #000000;">
                                <div>{{ !empty($card['activity_name']) ? $card['activity_name'] : ('Activity #' . ($i + 1)) }}</div>
                                <div style="font-size: 7pt; font-weight: normal; margin-top: 1px;">{{ trim($dateLabel . ' · ' . $timeLabel, ' ·') }}</div>
                            </th>
                        @endif
                    @endforeach
                    <th rowspan="2" style="border: 1px solid #000000; padding: 4px 6px; text-align: center; font-weight: bold; background: #ffffff; color: #000000; width: 55px;">Total</th>
                </tr>
                <tr style="page-break-inside: avoid;">
                    @foreach($cards as $i => $card)
                        @if(!empty($card['show_scans']))
                            <th style="border: 1px solid #000000; padding: 3px 5px; text-align: center; font-weight: bold; background: #ffffff; color: #000000; font-size: 7.5pt;">
                                {{ $card['scan_name'] ?? 'Scan' }}
                            </th>
                        @endif
                        @if(!empty($card['show_pins']))
                            <th style="border: 1px solid #000000; padding: 3px 5px; text-align: center; font-weight: bold; background: #ffffff; color: #000000; font-size: 7.5pt;">
                                {{ $card['pin_name'] ?? 'Pin Location' }}
                            </th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($sectionRecords as $n => $student)
                    @php $rowTotal = 0; @endphp
                    <tr style="page-break-inside: avoid;">
                        <td style="border: 1px solid #000000; padding: 4px 6px; text-align: center; color: #000000;">{{ $n + 1 }}</td>
                        <td style="border: 1px solid #000000; padding: 4px 6px; font-weight: bold; color: #000000;">{{ $student->name }}</td>
                        <td style="border: 1px solid #000000; padding: 4px 6px; text-align: center; color: #000000;">{{ $student->section }}</td>
                        @foreach($cards as $i => $card)
                            @if(!empty($card['show_scans']))
                                @php $s = (int) ($student->{"card_{$i}_scans_count"} ?? 0); $rowTotal += $s; @endphp
                                <td style="border: 1px solid #000000; padding: 4px 6px; text-align: center; color: #000000;">
                                    {{ $s }}
                                </td>
                            @endif
                            @if(!empty($card['show_pins']))
                                @php $p = (int) ($student->{"card_{$i}_pins_count"} ?? 0); $rowTotal += $p; @endphp
                                <td style="border: 1px solid #000000; padding: 4px 6px; text-align: center; color: #000000;">
                                    {{ $p }}
                                </td>
                            @endif
                        @endforeach
                        <td style="border: 1px solid #000000; padding: 4px 6px; text-align: center; font-weight: bold; color: #000000;">{{ $rowTotal }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="99" style="border: 1px solid #000000; text-align: center; color: #000000; padding: 12px;">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PRINT FOOTER (Left: Prepared By, Center: Date) --}}
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; font-size: 8pt; color: #000000; font-family: Arial, sans-serif; width: 100%;">
            <div style="flex: 1; text-align: left; font-weight: 500;">
                Prepared By: <strong>{{ $loggedInAdminName ?? 'AgriCo Administrator' }}</strong>
            </div>
            <div style="flex: 1; text-align: center; font-weight: 500;">
                {{ date('F j, Y') }}
            </div>
            <div style="flex: 1; text-align: right;">
            </div>
        </div>
    </div>

<script>
window.printSectionRecords = function() {
    var area = document.getElementById('sectionRecordsPrintArea');
    if (!area) {
        window.print();
        return;
    }

    var iframe = document.getElementById('sectionRecordsPrintIframe');
    if (iframe) {
        iframe.remove();
    }

    iframe = document.createElement('iframe');
    iframe.id = 'sectionRecordsPrintIframe';
    iframe.style.position = 'fixed';
    iframe.style.top = '-99999px';
    iframe.style.left = '-99999px';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    document.body.appendChild(iframe);

    var doc = iframe.contentWindow.document;
    doc.open();
    doc.write('<!DOCTYPE html><html><head><title><\/title>');
    doc.write('<style>');
    doc.write('@page { size: A4 landscape; margin: 0 !important; }');
    doc.write('html, body { margin: 0 !important; padding: 0 !important; background: #ffffff !important; color: #000000 !important; font-family: Arial, sans-serif !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }');
    doc.write('.print-wrap { padding: 5mm 8mm; box-sizing: border-box; width: 100%; }');
    doc.write('table { width: 100%; border-collapse: collapse; font-size: 8pt; }');
    doc.write('th, td { border: 1px solid #000000; padding: 4px 6px; }');
    doc.write('thead { display: table-header-group; }');
    doc.write('tr { page-break-inside: avoid; }');
    doc.write('<\/style>');
    doc.write('<\/head><body>');
    doc.write('<div class="print-wrap">' + area.innerHTML + '<\/div>');
    doc.write('<\/body><\/html>');
    doc.close();

    var triggerPrint = function() {
        try {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } catch (e) {
            console.error('Print error:', e);
            window.print();
        }
    };

    var imgs = iframe.contentDocument ? iframe.contentDocument.querySelectorAll('img') : [];
    if (imgs && imgs.length > 0) {
        var loaded = 0;
        var total = imgs.length;
        var fired = false;
        var checkDone = function() {
            loaded++;
            if (!fired && loaded >= total) {
                fired = true;
                setTimeout(triggerPrint, 80);
            }
        };
        imgs.forEach(function(img) {
            if (img.complete) {
                checkDone();
            } else {
                img.onload = checkDone;
                img.onerror = checkDone;
            }
        });
        setTimeout(function() {
            if (!fired) {
                fired = true;
                triggerPrint();
            }
        }, 500);
    } else {
        setTimeout(triggerPrint, 150);
    }
};
</script>
@endif
</div>
