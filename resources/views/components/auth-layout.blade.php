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
<body class="bg-white font-['Plus_Jakarta_Sans',ui-sans-serif,system-ui,sans-serif] text-[#202020] antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">

        {{-- Panel ilustrasi (disembunyikan di layar kecil) --}}
        <aside class="hidden items-center justify-center bg-brand-50 p-10 lg:flex" aria-hidden="true">
            <div class="w-full max-w-xl space-y-4">
                <img src="{{ asset('images/landing_page/pillar-1.png') }}" alt="" class="w-full rounded-3xl">
                <div class="grid grid-cols-2 items-start gap-4">
                    <img src="{{ asset('images/landing_page/pillar-2.png') }}" alt="" class="w-full rounded-3xl">
                    <img src="{{ asset('images/landing_page/pillar-3.png') }}" alt="" class="w-full rounded-3xl">
                </div>
            </div>
        </aside>

        {{-- Panel formulir --}}
        <main class="flex items-center justify-center px-6 py-10 sm:px-12">
            <div class="w-full max-w-sm">
                <a href="{{ url('/') }}" class="inline-block" aria-label="Crygle Academy — beranda">
                    <img src="{{ asset('images/logo.svg') }}" alt="Crygle Academy" class="h-12 w-auto">
                </a>

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
