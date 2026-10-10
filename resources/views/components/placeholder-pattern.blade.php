<svg {{ $attributes->merge(['class' => 'stroke-gray-900/20 dark:stroke-neutral-100/20']) }} fill="none">
    <defs>
        <pattern id="pattern-{{ $id = uniqid() }}" x="0" y="0" width="8" height="8" patternUnits="userSpaceOnUse">
            <path d="M-1 5L5 -1M3 9L9 3M-1 13L13 -1" stroke-width="1" />
        </pattern>
    </defs>
    <rect width="100%" height="100%" stroke="none" fill="url(#pattern-{{ $id }})" />
</svg>
