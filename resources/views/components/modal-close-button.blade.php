@props([
    'size' => 'normal',
    'click' => null,
    'ariaLabel' => 'Close modal',
    'title' => 'Close modal',
])

<x-close-button
    :size="$size"
    :click="$click"
    :ariaLabel="$ariaLabel"
    :title="$title"
    {{ $attributes }}
/>
