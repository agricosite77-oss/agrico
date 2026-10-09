<div
    wire:poll.30s
    x-data="{ notifOpen: false }"
    @click.outside="notifOpen = false"
    style="position: relative;"
>
    {{-- NOTIFICATION BELL BUTTON --}}
    <button
        type="button"
        @click="notifOpen = !notifOpen"
        class="mobile-notif-btn"
        aria-label="View notifications"
        style="position: relative; width: 38px; height: 38px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #334155; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 0;"
        onmouseover="this.style.backgroundColor='#f1f5f9'; this.style.borderColor='#cbd5e1';"
        onmouseout="this.style.backgroundColor='#f8fafc'; this.style.borderColor='#e2e8f0';"
    >
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>

        @if($totalNotifCount > 0)
            <span
                style="position: absolute; top: -3px; right: -3px; background: #ef4444; color: #ffffff; font-size: 10px; font-weight: 800; min-width: 17px; height: 17px; padding: 0 4px; border-radius: 9999px; display: inline-flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4); line-height: 1;"
            >
                {{ $totalNotifCount }}
            </span>
        @endif
    </button>

    {{-- NOTIFICATION DROPDOWN MODAL --}}
    <div
        x-show="notifOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-[-8px] scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-[-8px] scale-95"
        style="position: absolute; top: calc(100% + 8px); right: -36px; width: 310px; max-width: 90vw; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; box-shadow: 0 14px 35px -4px rgba(0,0,0,0.18), 0 4px 10px -2px rgba(0,0,0,0.06); z-index: 1050; overflow: hidden; padding: 0;"
    >
        {{-- DROPDOWN HEADER --}}
        <div style="padding: 12px 14px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: #fafafa;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 13.5px; font-weight: 800; color: #0f172a;">Notifications</span>
            </div>
            @if($totalNotifCount > 0)
                <span style="font-size: 10.5px; font-weight: 700; background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 2px 8px; border-radius: 9999px;">
                    {{ $totalNotifCount }} Alert{{ $totalNotifCount > 1 ? 's' : '' }}
                </span>
            @else
                <span style="font-size: 10.5px; font-weight: 700; background: #f0fdf4; color: #16a34a; padding: 2px 8px; border-radius: 9999px;">
                    All caught up
                </span>
            @endif
        </div>

        {{-- DROPDOWN BODY --}}
        <div style="max-height: 290px; overflow-y: auto; padding: 4px 0;">
            {{-- 1. SUSPENDED STUDENT ALERTS --}}
            @foreach($recentSuspendedStudents as $sUser)
                <a
                    href="{{ route('students.record') }}"
                    @click="notifOpen = false"
                    style="display: flex; align-items: flex-start; gap: 10px; padding: 10px 14px; text-decoration: none; border-bottom: 1px solid #f8fafc; background: #fff5f5; transition: background 0.15s ease;"
                    onmouseover="this.style.background='#fee2e2';"
                    onmouseout="this.style.background='#fff5f5';"
                >
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: #fee2e2; border: 1px solid #fecaca; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; color: #ef4444;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <div style="font-size: 12.5px; font-weight: 700; color: #991b1b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            Student Suspended: {{ $sUser->name }}
                        </div>
                        <div style="font-size: 11px; font-weight: 600; color: #ef4444; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $sUser->student_id ? 'ID: ' . $sUser->student_id : '' }} {{ $sUser->section ? '· Section ' . $sUser->section : '' }}
                        </div>
                        <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;">
                            Reason: {{ Str::limit($sUser->suspend_reason ?: 'Administrative policy violation', 32) }}
                        </div>
                        <div style="font-size: 10.5px; color: #e11d48; font-weight: 600; margin-top: 2px; display: inline-flex; align-items: center; gap: 4px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #f43f5e; flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>Ends in {{ $sUser->suspension_countdown }}</span>
                        </div>
                    </div>
                </a>
            @endforeach

            {{-- 2. PENDING REQUESTS --}}
            @foreach($recentPendingRequests as $req)
                <a
                    href="{{ route('user.requests') }}"
                    @click="notifOpen = false"
                    style="display: flex; align-items: flex-start; gap: 10px; padding: 10px 14px; text-decoration: none; border-bottom: 1px solid #f8fafc; transition: background 0.15s ease;"
                    onmouseover="this.style.background='#f8fafc';"
                    onmouseout="this.style.background='transparent';"
                >
                    <div style="width: 34px; height: 34px; border-radius: 10px; background: #fef3c7; border: 1px solid #fde68a; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; box-shadow: 0 1px 2px rgba(217, 119, 6, 0.08);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            <line x1="9" y1="12" x2="15" y2="12"></line>
                            <line x1="9" y1="16" x2="13" y2="16"></line>
                        </svg>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <div style="font-size: 12.5px; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $req->user_name ?: 'User Request' }}
                        </div>
                        <div style="font-size: 11px; font-weight: 500; color: #64748b; margin-top: 1px;">
                            {{ $req->type ?: 'Account Request' }}
                        </div>
                        <div style="font-size: 10px; color: #94a3b8; margin-top: 2px;">
                            {{ $req->created_at?->diffForHumans() ?: 'Just now' }}
                        </div>
                    </div>
                </a>
            @endforeach

            @if($totalNotifCount === 0)
                <div style="padding: 24px 16px; text-align: center;">
                    <div style="font-size: 24px; margin-bottom: 6px;">✨</div>
                    <div style="font-size: 12.5px; font-weight: 600; color: #64748b;">No pending notifications</div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">All student records & requests are clear</div>
                </div>
            @endif
        </div>

        {{-- DROPDOWN FOOTER --}}
        <div style="padding: 10px 14px; border-top: 1px solid #f1f5f9; background: #fafafa; display: flex; align-items: center; justify-content: space-between;">
            <a
                href="{{ route('students.record') }}"
                @click="notifOpen = false"
                style="font-size: 12px; font-weight: 700; color: #ef4444; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"
                onmouseover="this.style.textDecoration='underline';"
                onmouseout="this.style.textDecoration='none';"
            >
                <span>Student Records</span>
                <span>&rarr;</span>
            </a>
            <a
                href="{{ route('user.requests') }}"
                @click="notifOpen = false"
                style="font-size: 12px; font-weight: 700; color: #22c55e; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;"
                onmouseover="this.style.textDecoration='underline';"
                onmouseout="this.style.textDecoration='none';"
            >
                <span>User Requests</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>
</div>
