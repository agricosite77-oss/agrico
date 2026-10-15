@props([
    'size' => 'normal',
    'click' => null,
    'ariaLabel' => 'Close modal',
    'title' => 'Close modal',
])

@php
    $sizeClass = $size === 'sm' ? 'agrico-close-btn--sm' : 'agrico-close-btn--normal';
@endphp

<button
    type="button"
    @if($click) @click="{{ $click }}" wire:click="{{ $click }}" @endif
    {{ $attributes->merge([
        'class' => "agrico-close-btn {$sizeClass}",
        'aria-label' => $ariaLabel,
        'title' => $title,
    ]) }}
>
    <svg class="agrico-close-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>

