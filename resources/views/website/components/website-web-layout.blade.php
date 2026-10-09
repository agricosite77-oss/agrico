@props(['title' => 'AgriCo'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
        </div>
    </header>
    <main class="py-6">
        {{ $slot }}
    </main>
    <footer class="bg-gray-800 text-white py-4">
        <div class="max-w-7xl mx-auto text-center">
            © {{ date('Y') }} AgriCo. All rights reserved.
        </div>
    </footer>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
