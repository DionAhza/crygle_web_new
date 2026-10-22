<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — ' : '' }}Crygle Academy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-blue-50/60 font-['Plus_Jakarta_Sans',ui-sans-serif,system-ui,sans-serif] text-slate-700 antialiased">
    <header class="sticky top-0 z-50 border-b border-blue-100 bg-white/80 backdrop-blur">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2"><img src="{{ asset('images/logo.svg') }}" alt="Crygle Academy"></a>
            <div class="flex items-center gap-2 text-sm font-semibold">
                <a href="{{ url('/') }}" class="hidden px-3 py-2 text-slate-600 hover:text-blue-800 sm:inline">Beranda</a>
                <a href="{{ route('catalog.index') }}" class="px-3 py-2 text-blue-800">Katalog</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-blue-800 px-5 py-2 text-white hover:bg-blue-900">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 text-blue-800 hover:text-blue-500">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-blue-800 px-5 py-2 text-white hover:bg-blue-900">Daftar</a>
                @endauth
            </div>
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        {{ $slot }}
    </main>
</body>
</html>
