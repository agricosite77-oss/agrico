@props([
    'user' => null,
    'name' => 'User',
    'photo' => null,
    'size' => 'md',
    'showStatus' => true,
    'isOnline' => true,
    'status' => 'active',
])

@php
    $userName = $user->name ?? $name ?? 'User';
    $userInitial = strtoupper(substr(trim($userName), 0, 1)) ?: 'A';
    $photoPath = $user->profile_photo_path ?? $photo ?? null;
    $photoUrl = $photoPath ? (str_starts_with($photoPath, 'http') ? $photoPath : asset('storage/' . $photoPath)) : null;

    $dimensions = match($size) {
        'xs' => 'w-7 h-7 text-xs',
        'sm' => 'w-9 h-9 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-14 h-14 text-xl',
        default => 'w-10 h-10 text-sm',
    };

    $badgeDimensions = match($size) {
        'xs', 'sm' => 'w-2.5 h-2.5 border-2',
        'md' => 'w-3.5 h-3.5 border-2',
        'lg' => 'w-4 h-4 border-2.5',
        'xl' => 'w-4.5 h-4.5 border-2.5',
        default => 'w-3.5 h-3.5 border-2',
    };

    $statusColor = match(strtolower($status ?? 'active')) {
        'active', 'online' => 'bg-[#22c55e]',
        'suspended', 'inactive' => 'bg-[#ef4444]',
        default => $isOnline ? 'bg-[#22c55e]' : 'bg-[#cbd5e1]',
    };
@endphp

{{-- SOLID VIBRANT GREEN AVATAR BADGE WITH WHITE INITIAL --}}
<div class="relative inline-flex items-center justify-center shrink-0">
    @if($photoUrl)
        <img
            src="{{ $photoUrl }}"
            alt="{{ $userName }}"
            class="{{ $dimensions }} rounded-full object-cover shadow-sm ring-2 ring-white"
        />
    @else
        <div class="{{ $dimensions }} rounded-full bg-[#22c55e] text-white font-black flex items-center justify-center shadow-sm select-none border-none">
            <span>{{ $userInitial }}</span>
        </div>
    @endif

    {{-- OPTIONAL STATUS DOT --}}
    @if($showStatus)
        <span class="absolute bottom-0 right-0 {{ $badgeDimensions }} {{ $statusColor }} border-white rounded-full shadow-xs"></span>
    @endif
</div>
