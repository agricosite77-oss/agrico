@props([
    'click' => null,
])

<button
    type="button"
    @if($click) wire:click="{{ $click }}" @endif
    {{ $attributes->merge([
        'class' => 'modal-close-circle-btn'
    ]) }}
    aria-label="Close modal"
    title="Close modal"
>
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
        <path d="M6 18L18 6M6 6l12 12"/>
    </svg>
</button>

