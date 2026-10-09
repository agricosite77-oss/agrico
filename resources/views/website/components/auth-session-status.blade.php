@props(['status'])


@if ($status)

<div class="text-sm text-green-600 text-center">
    {{ $status }}
</div>

@endif
