@props([
    'rows' => 4,
    'cols' => 7,
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'skeleton-munsell-grid ' . $class]) }} role="status" aria-busy="true" aria-label="Loading Munsell color palette...">
    @php
        $total = $rows * $cols;
    @endphp
    @for($i = 0; $i < $total; $i++)
        <div class="skeleton skeleton-color"></div>
    @endfor
</div>
