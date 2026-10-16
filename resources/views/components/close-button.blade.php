@props([
    'size' => 'normal',
    'click' => null,
    'ariaLabel' => 'Close',
    'title' => 'Close',
    'type' => 'button',
])

@php
    $isAdmin = request()->is('admin*') 
        || request()->is('*/admin*')
        || request()->routeIs('admin.*') 
        || (request()->is('livewire*') && str_contains((string) request()->header('referer', ''), '/admin'))
        || str_contains(url()->current(), '/admin')
        || auth()->guard('admin')->check();
    $sizeClass = $size === 'sm' ? 'modal-close-circle-btn--sm is-sm' : 'modal-close-circle-btn--normal';
    $iconSize = $size === 'sm' ? 14 : 18;
@endphp

@if($isAdmin)
    <button
        type="{{ $type }}"
        @if($click) @click="{{ $click }}" wire:click="{{ $click }}" @endif
        {{ $attributes->merge([
            'class' => "modal-close-circle-btn admin-close-btn {$sizeClass}",
            'aria-label' => $ariaLabel,
            'title' => $title,
        ]) }}
    >
        <svg width="{{ $iconSize }}" height="{{ $iconSize }}" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
@else
    <button
        type="{{ $type }}"
        @if($click) @click="{{ $click }}" wire:click="{{ $click }}" @endif
        {{ $attributes->merge([
            'class' => "agrico-close-btn modal-close-circle-btn {$sizeClass}",
            'aria-label' => $ariaLabel,
            'title' => $title,
        ]) }}
    >
        <svg class="agrico-close-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
@endif

