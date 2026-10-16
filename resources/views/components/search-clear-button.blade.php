@props([
    'wireModel' => null,
    'click' => null,
    'id' => null,
    'title' => 'Clear search',
    'ariaLabel' => 'Clear search',
    'inputSelector' => null,
])

@php
    $wireProperty = $wireModel ?: ($click && str_starts_with($click, '$set(') ? trim(explode(',', $click)[0], " \$set('\"") : null);
    $wireAction = $click ?: ($wireProperty ? "\$set('{$wireProperty}', '')" : null);
@endphp

<button
    type="button"
    @if($id) id="{{ $id }}" @endif
    {{ $attributes->merge([
        'class' => 'agrico-search-clear-btn',
        'aria-label' => $ariaLabel,
        'title' => $title,
    ]) }}
    @if($wireAction && !str_starts_with($wireAction, 'clear('))
        wire:click="{{ $wireAction }}"
    @endif
    @click="
        @if($wireAction && str_starts_with($wireAction, 'clear('))
            {{ $wireAction }};
        @endif
        @if($wireProperty)
            if (window.Livewire && typeof $wire !== 'undefined') {
                try { $wire.set('{{ $wireProperty }}', ''); } catch (e) {}
            }
        @endif
        const container = $el.closest('.pin-search-box, .map-search-box, .munsell-search, .faq-search-wrapper, div[style*=\"position:relative\"], div[style*=\"position: relative\"], div');
        const targetInput = @if($inputSelector) document.querySelector('{{ $inputSelector }}') || @endif (container ? container.querySelector('input') : null) || $el.parentElement.querySelector('input');
        if (targetInput) {
            targetInput.value = '';
            targetInput.focus();
            targetInput.dispatchEvent(new Event('input', { bubbles: true }));
            targetInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    "
    @keydown.escape.stop="
        $el.click();
    "
>
    <span class="agrico-search-clear-circle" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 18L18 6M6 6l12 12" />
        </svg>
    </span>
</button>
