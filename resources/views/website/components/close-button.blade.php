@props([
    'size' => 'normal',
    'click' => null,
    'ariaLabel' => 'Close',
    'title' => 'Close',
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
