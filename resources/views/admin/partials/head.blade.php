<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="color-scheme" content="light" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'AgriCo') : config('app.name', 'AgriCo') }}
</title>

<link rel="icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/images/agrico-logo.png" type="image/png">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Leaflet CSS & JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/lallo-boundary.js') }}"></script>

<script>
    try {
        localStorage.removeItem('theme');
        localStorage.removeItem('agrico_theme');
        localStorage.removeItem('agrico-theme');
        localStorage.removeItem('flux.appearance');
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.remove('light-mode');
        document.documentElement.classList.add('light');
        document.documentElement.style.colorScheme = 'light';
    } catch(e) {}
</script>
