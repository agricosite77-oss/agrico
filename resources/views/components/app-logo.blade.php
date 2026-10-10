@props([
    'sidebar' => false,
    'href' => route('dashboard'),
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <img src="{{ asset('images/agrico-logo.png') }}" alt="AgriCo" class="h-8 w-auto object-contain" />
    <span class="font-bold text-lg text-emerald-600 dark:text-emerald-400">AgriCo</span>
</a>
