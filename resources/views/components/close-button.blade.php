@props([
    'size' => 'normal',
    'click' => null,
    'ariaLabel' => 'Close',
    'title' => 'Close',
    'type' => 'button',
])

@php
    $sizeClass = $size === 'sm' ? 'agrico-close-btn--sm is-sm' : 'agrico-close-btn--normal';
@endphp

<button
    type="{{ $type }}"
    @if($click) @click="{{ $click }}" wire:click="{{ $click }}" @endif
    {{ $attributes->merge([
        'class' => "agrico-close-btn modal-close-circle-btn {$sizeClass}",
        'aria-label' => $ariaLabel,
        'title' => $title,
    ]) }}
>
    <svg fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <path d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>
