@props([
    'user' => null,
    'name' => 'User',
    'subtitle' => 'Member',
    'status' => 'active',
    'photo' => null,
])

@php
    $userName = $user->name ?? $name ?? 'User';
    $userSubtitle = $user ? ucfirst($user->role ?: 'Member') : $subtitle;
    $userStatus = $user ? $user->status : $status;
    $userInitial = strtoupper(substr(trim($userName), 0, 1)) ?: 'U';
    $photoPath = $user->profile_photo_path ?? $photo ?? null;
    $photoUrl = $photoPath ? (str_starts_with($photoPath, 'http') ? $photoPath : asset('storage/' . $photoPath)) : null;

    $isSuspended = (ucfirst($userStatus) === 'Suspended' || strtolower($userStatus) === 'inactive');
@endphp

{{-- OUTER CONTAINER CARD (SOFT MINT GREEN) --}}
<div class="bg-[#f0fdf4] border border-[#dcfce7] rounded-3xl p-4 sm:p-5 flex items-center gap-4 shadow-2xs">
    
    {{-- CLEAN SOLID VIBRANT GREEN AVATAR CIRCLE --}}
    <div class="relative shrink-0 flex items-center justify-center">
        @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="{{ $userName }}" class="w-14 h-14 rounded-full object-cover shadow-2xs">
        @else
            <div class="w-14 h-14 rounded-full bg-[#22c55e] text-white font-extrabold text-2xl flex items-center justify-center shadow-2xs select-none border-none">
                <span>{{ $userInitial }}</span>
            </div>
        @endif
    </div>

    {{-- DETAILS --}}
    <div class="flex-1 min-w-0">
        <h4 class="text-lg font-extrabold text-gray-900 truncate">
            {{ $userName }}
        </h4>
        <div class="text-sm font-semibold text-gray-500 mt-0.5">
            {{ $userSubtitle }}
        </div>
        <div class="inline-flex items-center gap-1.5 mt-1.5">
            <span class="w-2 h-2 rounded-full {{ $isSuspended ? 'bg-red-500' : 'bg-green-500' }}"></span>
            <span class="text-xs font-bold {{ $isSuspended ? 'text-red-600' : 'text-green-700' }}">
                {{ $isSuspended ? 'Suspended' : 'Active' }}
            </span>
        </div>
    </div>
</div>
