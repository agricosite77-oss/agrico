@props([
    'size' => 'normal',
    'click' => null,
    'ariaLabel' => 'Close',
    'title' => 'Close',
])

<x-close-button
    :size="$size"
    :click="$click"
    :ariaLabel="$ariaLabel"
    :title="$title"
    {{ $attributes }}
/>
