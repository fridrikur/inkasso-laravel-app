<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Security Setup' }}</title>

    @vite('resources/css/app.css')

    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <main class="w-full max-w-2xl px-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>