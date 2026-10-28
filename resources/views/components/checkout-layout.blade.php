@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' — ' : '' }}Crygle Academy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-[#f5f9ff] font-['Plus_Jakarta_Sans',ui-sans-serif,system-ui,sans-serif] text-slate-700 antialiased">
    <header class="border-b border-blue-100/70 bg-white/80 backdrop-blur print:hidden">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center"><img src="{{ asset('images/logo.svg') }}" alt="Crygle Academy"></a>

            <ul class="hidden items-center gap-8 text-xs font-semibold text-slate-400 md:flex">
                <li><a href="{{ url('/') }}" class="text-brand">Home</a></li>
                <li><a href="{{ route('catalog.index') }}" class="hover:text-brand">Course</a></li>
                <li><a href="{{ url('/') }}#program" class="hover:text-brand">Bootcamp Intensif</a></li>
                <li><a href="{{ url('/') }}#keunggulan" class="hover:text-brand">Mentor</a></li>
                <li><a href="{{ url('/') }}#kontak" class="hover:text-brand">Tentang</a></li>
            </ul>

            <div class="flex items-center gap-3 text-xs font-semibold">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-brand px-6 py-2.5 text-white shadow-md shadow-brand/30 hover:bg-brand-dark">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 text-brand">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-brand px-6 py-2.5 text-white shadow-md shadow-brand/30 hover:bg-brand-dark">Daftar</a>
                @endauth
            </div>
        </nav>
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6">
        {{ $slot }}
    </main>

    <div class="print:hidden"><x-site-footer /></div>
</body>
</html>
