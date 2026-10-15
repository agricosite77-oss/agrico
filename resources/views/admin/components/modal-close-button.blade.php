@props([
    'click' => null,
    'size' => 'normal',
    'ariaLabel' => 'Close modal',
    'title' => 'Close modal',
])

<x-close-button
    :click="$click"
    :size="$size"
    :ariaLabel="$ariaLabel"
    :title="$title"
    {{ $attributes }}
/>
