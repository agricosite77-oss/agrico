@props([
    'click' => null,
    'size' => 'normal',
    'ariaLabel' => 'Close modal',
    'title' => 'Close modal',
    'type' => 'button',
])

<x-close-button
    :click="$click"
    :size="$size"
    :ariaLabel="$ariaLabel"
    :title="$title"
    :type="$type"
    {{ $attributes }}
/>
