@props(['title' => 'Kelas Saya', 'heading' => 'Kelas Saya'])
@php
    $menu = [
        ['label' => 'Overview', 'icon' => 'M4 5a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm9 0a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1V5zM4 14a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1v-5zm9 0a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1v-5z', 'href' => null],
        ['label' => 'Course Saya', 'icon' => 'M15 10l4.55-2.28A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.9L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'href' => route('student.courses'), 'active' => request()->routeIs('student.*', 'learn.*')],
        ['label' => 'Bootcamp Saya', 'icon' => 'M12 6.25v11.5M7.5 4.5h9a2 2 0 012 2v11a2 2 0 01-2 2h-9a2 2 0 01-2-2v-11a2 2 0 012-2z', 'href' => null],
        ['label' => 'Explore Kelas', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'href' => route('catalog.index'), 'active' => request()->routeIs('catalog.*')],
        ['label' => 'Chat Mentor', 'icon' => 'M8 10h8M8 14h5m-9 6l3-3h11a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v14z', 'href' => null],
        ['label' => 'Affiliate', 'icon' => 'M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-3.3 0-6 1.8-6 4v2h12v-2c0-2.2-2.7-4-6-4z', 'href' => null],
        ['label' => 'Setting', 'icon' => 'M4 6h10m4 0h2M4 12h2m4 0h10M4 18h10m4 0h2M16 4v4M8 10v4M16 16v4', 'href' => route('profile.edit'), 'active' => request()->routeIs('profile.*')],
    ];
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title }} - {{ config('app.name', 'Crygle Academy') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
    </head>
    <body class="font-sans antialiased bg-white text-slate-800" x-data="{ sidebar: false }">
        {{-- Overlay mobile --}}
        <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 start-0 z-40 w-[220px] border-e border-slate-100 bg-white px-4 py-6 transition-transform duration-200">
            <a href="{{ route('student.courses') }}" class="mb-10 block px-2">
                <img src="{{ asset('images/logo.svg') }}" alt="Crygle Academy" class="h-10 w-auto">
            </a>
            <nav class="space-y-2">
                @foreach ($menu as $item)
                    @php $isActive = $item['active'] ?? false; @endphp
                    @if ($item['href'])
                        <a href="{{ $item['href'] }}"
                           class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm transition {{ $isActive ? 'bg-brand-50 font-semibold text-brand' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"/></svg>
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span title="Segera hadir" class="flex cursor-not-allowed items-center gap-3 rounded-lg px-4 py-3 text-sm text-slate-400">
                            <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $item['icon'] }}"/></svg>
                            {{ $item['label'] }}
                        </span>
                    @endif
                @endforeach
            </nav>
        </aside>

        <div class="min-h-screen lg:ps-[220px]">
            {{-- Header --}}
            <header class="flex items-center justify-between gap-4 bg-white px-5 py-6 sm:px-8">
                <div class="flex items-center gap-3">
                    <button @click="sidebar = true" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" aria-label="Buka menu">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-2xl font-bold text-slate-800 sm:text-[28px]">{{ $heading }}</h1>
                </div>

                <div class="flex items-center gap-4">
                    <button type="button" class="relative flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-slate-700" aria-label="Notifikasi">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0"/></svg>
                        <span class="absolute end-2.5 top-2.5 h-2 w-2 rounded-full bg-red-500"></span>
                    </button>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="block h-10 w-10 overflow-hidden rounded-full bg-slate-800 focus:outline-none focus:ring-2 focus:ring-brand/40" aria-label="Menu akun">
                                @if ($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="flex h-full w-full items-center justify-center text-sm font-semibold text-white">{{ \Illuminate\Support\Str::of($user->name)->substr(0, 1)->upper() }}</span>
                                @endif
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="border-b border-slate-100 px-4 py-2 text-xs text-slate-500">{{ $user->name }}</div>
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </header>

            <main>{{ $slot }}</main>
        </div>
    </body>
</html>
