@props([
    'click' => null,
    'size' => 'normal',
    'ariaLabel' => 'Close',
    'title' => 'Close',
])

<x-close-button
    :click="$click"
    :size="$size"
    :ariaLabel="$ariaLabel"
    :title="$title"
    {{ $attributes }}
/>
