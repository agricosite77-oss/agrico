<div>
@if($showAddStudentModal)
{{-- BACKDROP BLUR OVERLAY --}}
<div
    x-data
    x-init="document.body.classList.add('overflow-hidden')"
    x-on:unmount="document.body.classList.remove('overflow-hidden')"
    class="fixed inset-0 z-50 flex items-center justify-center"
    style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 100001; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; padding: 16px; box-sizing: border-box;"
    wire:click.self="closeAddStudentModal"
>
    {{-- MODAL CARD CONTAINER (MATCHING SOIL SCAN HISTORY & MAPPING DETAILS POP UP EXACTLY) --}}
    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 28px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3); width: 100%; max-width: 680px; margin: auto; display: flex; flex-direction: column; height: 75vh; max-height: 75vh; overflow: hidden; position: relative;">

        {{-- 1. FIXED MODAL HEADER --}}
        <div style="padding: 20px 26px 16px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; background: #ffffff; flex-shrink: 0;">
            <div>
                <h3 style="margin: 0; font-size: 17.5px; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;">
                    Add Student to Records
                </h3>
                <p style="font-size: 11.5px; color: #94a3b8; margin: 2px 0 0 0; font-weight: 500;">
                    Select student accounts to add to active student records roster
                </p>
            </div>
            <x-modal-close-button click="closeAddStudentModal" />
        </div>

        {{-- 2. SCROLLABLE BODY CONTENT --}}
        <div style="padding: 18px 24px 20px; overflow-y: auto; flex: 1 1 auto; box-sizing: border-box; display: flex; flex-direction: column; gap: 12px; scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">

            {{-- SEARCH BAR + SECTION FILTER ROW --}}
            <div style="display: flex; gap: 10px;">
                <div style="position: relative; flex: 1;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <path d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="studentSearch"
                        @keydown.escape="$wire.set('studentSearch', ''); $el.value = '';"
                        placeholder="Search by name, Student ID, or section..."
                        style="width: 100%; height: 42px; padding: 0 44px 0 36px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 13.5px; color: #0f172a; outline: none; box-sizing: border-box; font-family: inherit;"
                    />
                    @if($studentSearch)
                        <x-search-clear-button wireModel="studentSearch" />
                    @endif
                </div>
                <select
                    wire:model.live="modalSectionFilter"
                    style="width: 130px; height: 42px; padding: 0 10px; border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 13px; color: #0f172a; outline: none; background: #ffffff; font-family: inherit; flex-shrink: 0;"
                >
                    <option value="">All Sections</option>
                    @foreach($modalAvailableSections as $section)
                        <option value="{{ $section }}">{{ $section }}</option>
                    @endforeach
                </select>
            </div>

            <div style="font-size: 11px; font-weight: 600; color: #94a3b8; letter-spacing: 0.02em;">
                Showing {{ $searchResults->count() }} registered student{{ $searchResults->count() !== 1 ? 's' : '' }}
            </div>

            {{-- RESULT ROWS --}}
            <div style="display: flex; flex-direction: column; gap: 8px; flex: 1; overflow-y: auto;">
                @forelse($searchResults as $student)
                    @php
                        $isAlreadyAdded = ($student->student_record_status === 'Active') || in_array($student->user_id, $addedStudentIds ?? []);
                        $assignedAdmin = $isAlreadyAdded ? $student->assigned_admin : null;
                        $assignedAdminName = $assignedAdmin ? ($assignedAdmin->full_name ?? $assignedAdmin->name ?? null) : null;
                        $photoPath = $student->profile_photo_path;
                        $photoUrl = $photoPath ? (str_starts_with($photoPath, 'http') ? $photoPath : asset('storage/' . $photoPath)) : null;
                        $initial = strtoupper(substr(trim($student->name), 0, 1)) ?: 'S';
                    @endphp
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border: 1.5px solid {{ $isAlreadyAdded ? '#e2e8f0' : '#f1f5f9' }}; background: {{ $isAlreadyAdded ? '#f8fafc' : '#ffffff' }}; border-radius: 16px; box-shadow: 0 1px 4px rgba(0,0,0,0.03); box-sizing: border-box; transition: all 0.2s ease;">
                        <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="{{ $student->name }}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; opacity: {{ $isAlreadyAdded ? '0.75' : '1' }};" />
                            @else
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: {{ $isAlreadyAdded ? '#94a3b8' : '#22c55e' }}; color: #ffffff; font-size: 14px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.08); user-select: none;">
                                    {{ $initial }}
                                </div>
                            @endif
                            <div style="min-width: 0;">
                                <div style="font-size: 13.5px; font-weight: 700; color: {{ $isAlreadyAdded ? '#64748b' : '#0f172a' }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $student->name }}</div>
                                <div style="font-size: 11.5px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px; display: flex; align-items: center; gap: 6px;">
                                    <span>{{ $student->student_id }} &middot; Section {{ $student->section ?: 'N/A' }}</span>
                                    @if($isAlreadyAdded && $assignedAdminName)
                                        <span style="display: inline-flex; align-items: center; gap: 3px; font-size: 10.5px; font-weight: 700; background: #e2e8f0; color: #475569; padding: 1px 7px; border-radius: 9999px;">
                                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                            {{ $assignedAdminName }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            @if($isAlreadyAdded)
                                disabled
                            @else
                                wire:click="addStudentToRecords('{{ $student->user_id }}')"
                            @endif
                            style="background: {{ $isAlreadyAdded ? '#f1f5f9' : '#f0fdf4' }}; color: {{ $isAlreadyAdded ? '#94a3b8' : '#16a34a' }}; border: 1px solid {{ $isAlreadyAdded ? '#e2e8f0' : '#bbf7d0' }}; border-radius: 9999px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: {{ $isAlreadyAdded ? 'not-allowed' : 'pointer' }}; flex-shrink: 0; font-family: inherit; display: inline-flex; align-items: center; gap: 5px; transition: all 0.15s ease;"
                            title="{{ $isAlreadyAdded ? 'Student already added to records' : 'Add to student records' }}"
                        >
                            @if($isAlreadyAdded)
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Added</span>
                            @else
                                <span style="font-size: 14px; line-height: 1;">+</span>
                                <span>Add</span>
                            @endif
                        </button>
                    </div>
                @empty
                    <div style="text-align: center; padding: 36px 12px; color: #94a3b8; font-size: 13.5px;">
                        No matching registered students found.
                    </div>
                @endforelse
            </div>

            {{-- PENDING STATUS NOTE --}}
            <div style="background: #f8fafc; border-radius: 12px; padding: 10px 12px; font-size: 12px; color: #64748b; line-height: 1.5; box-sizing: border-box; border: 1px solid #f1f5f9;">
                Added students appear in your Student Records as <strong style="color: #16a34a;">Active</strong> roster members.
            </div>
        </div>

        {{-- 3. FOOTER ACTION BUTTON --}}
        <div style="padding: 16px 24px 20px; background: #ffffff; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: center; flex-shrink: 0; width: 100%; box-sizing: border-box;">
            <button
                wire:click="closeAddStudentModal"
                type="button"
                class="w-full py-3 rounded-full bg-green-500 hover:bg-green-600 text-white font-bold text-sm shadow-md shadow-green-500/35 transition cursor-pointer text-center"
                style="box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);"
            >
                Done
            </button>
        </div>
    </div>
</div>
@endif
</div>
