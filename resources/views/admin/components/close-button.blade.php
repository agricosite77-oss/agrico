@props([
    'click' => null,
    'size' => 'normal',
    'ariaLabel' => 'Close',
    'title' => 'Close',
    'type' => 'button',
])

@php
    $sizeClass = $size === 'sm' ? 'modal-close-circle-btn--sm' : 'modal-close-circle-btn';
    $iconSize = $size === 'sm' ? 12 : 14;
@endphp

<button 
    type="{{ $type }}"
    @if($click) @click="{{ $click }}" wire:click="{{ $click }}" @endif
    {{ $attributes->merge([
        'class' => "admin-close-btn modal-close-circle-btn {$sizeClass}",
        'aria-label' => $ariaLabel,
        'title' => $title,
    ]) }}
>
    <svg width="{{ $iconSize }}" height="{{ $iconSize }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>
