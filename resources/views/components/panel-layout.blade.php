@props(['title' => 'Panel', 'heading' => null, 'subtitle' => null, 'width' => 'max-w-6xl', 'back' => null])
@php
    $user = auth()->user();
    $role = $user->hasRole('admin') ? 'admin' : ($user->hasRole('mentor') ? 'mentor' : 'student');
    $roleLabel = ['admin' => 'Administrator', 'mentor' => 'Mentor', 'student' => 'Siswa'][$role];

    $icon = [
        'grid' => 'M4 5a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm9 0a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1V5zM4 14a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1v-5zm9 0a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1v-5z',
        'users' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'tag' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'book' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        'check' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'video' => 'M15 10l4.55-2.28A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.9L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
        'bolt' => 'M13 10V3L4 14h7v7l9-11h-7z',
        'chat' => 'M8 10h8M8 14h5m-9 6l3-3h11a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v14z',
        'person' => 'M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-3.3 0-6 1.8-6 4v2h12v-2c0-2.2-2.7-4-6-4z',
        'sliders' => 'M4 6h10m4 0h2M4 12h2m4 0h10M4 18h10m4 0h2M16 4v4M8 10v4M16 16v4',
        'logout' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
    ];

    // Menu per peran. 'href' => null berarti "segera hadir" (sama seperti sidebar siswa).
    $menus = [
        'admin' => [
            ['Dashboard', 'grid', route('admin.dashboard'), request()->routeIs('admin.dashboard')],
            ['Akun Mentor', 'users', route('admin.mentors.index'), request()->routeIs('admin.mentors.*')],
            ['Tipe Mentor', 'tag', route('admin.mentor-types.index'), request()->routeIs('admin.mentor-types.*')],
            ['Katalog Kelas', 'bolt', route('catalog.index'), request()->routeIs('catalog.*')],
            ['Pengaturan', 'sliders', route('profile.edit'), request()->routeIs('profile.*')],
        ],
        'mentor' => [
            ['Dashboard', 'grid', route('mentor.dashboard'), request()->routeIs('mentor.dashboard')],
            ['Kelas Saya', 'book', route('mentor.courses.index'), request()->routeIs('mentor.courses.*', 'mentor.modules.*', 'mentor.lessons.*', 'mentor.assignments.*')],
            ['Review Tugas', 'check', route('mentor.submissions.index'), request()->routeIs('mentor.submissions.*')],
            ['Katalog Kelas', 'bolt', route('catalog.index'), request()->routeIs('catalog.*')],
            ['Pengaturan', 'sliders', route('profile.edit'), request()->routeIs('profile.*')],
        ],
        'student' => [
            ['Overview', 'grid', null, false],
            ['Course Saya', 'video', route('student.courses'), request()->routeIs('student.*', 'learn.*')],
            ['Bootcamp Saya', 'book', null, false],
            ['Explore Kelas', 'bolt', route('catalog.index'), request()->routeIs('catalog.*')],
            ['Chat Mentor', 'chat', null, false],
            ['Affiliate', 'person', null, false],
            ['Setting', 'sliders', route('profile.edit'), request()->routeIs('profile.*')],
        ],
    ];
    $menu = $menus[$role];
    $home = ['admin' => route('admin.dashboard'), 'mentor' => route('mentor.dashboard'), 'student' => route('student.courses')][$role];
    $initial = \Illuminate\Support\Str::of($user->name)->substr(0, 1)->upper();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name', 'Crygle Academy') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-[#f5f9ff] font-['Plus_Jakarta_Sans',ui-sans-serif,system-ui,sans-serif] text-slate-700 antialiased" x-data="{ sidebar: false }">
    {{-- Overlay mobile --}}
    <div x-show="sidebar" x-cloak x-transition.opacity @click="sidebar = false" class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 start-0 z-40 flex w-[240px] flex-col border-e border-slate-100 bg-white px-4 py-6 transition-transform duration-200">
        <a href="{{ $home }}" class="mb-8 block px-2">
            <img src="{{ asset('images/logo.svg') }}" alt="Crygle Academy" class="h-10 w-auto">
        </a>

        <p class="mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $roleLabel }}</p>
        <nav class="flex-1 space-y-1.5 overflow-y-auto">
            @foreach ($menu as [$label, $key, $href, $active])
                @if ($href)
                    <a href="{{ $href }}" @if ($active) aria-current="page" @endif
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition {{ $active ? 'bg-brand-50 font-semibold text-brand' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
                        <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $icon[$key] }}"/></svg>
                        {{ $label }}
                    </a>
                @else
                    <span title="Segera hadir" class="flex cursor-not-allowed items-center gap-3 rounded-xl px-4 py-3 text-sm text-slate-400">
                        <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $icon[$key] }}"/></svg>
                        {{ $label }}
                    </span>
                @endif
            @endforeach
        </nav>

        {{-- Kartu akun + keluar --}}
        <div class="mt-4 rounded-2xl bg-brand-50 p-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand text-sm font-bold text-white">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="" class="h-full w-full object-cover">
                    @else
                        {{ $initial }}
                    @endif
                </span>
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-slate-800">{{ $user->name }}</div>
                    <div class="truncate text-[11px] text-slate-500">{{ $user->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button class="flex w-full items-center justify-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm transition hover:text-rose-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $icon['logout'] }}"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="min-h-screen lg:ps-[240px]">
        {{-- Bilah atas --}}
        <header class="flex items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <div class="flex items-center gap-3">
                <button @click="sidebar = true" class="rounded-xl bg-white p-2 text-slate-500 shadow-sm hover:text-brand lg:hidden" aria-label="Buka menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="hidden rounded-full bg-white px-4 py-1.5 text-xs font-semibold text-brand shadow-sm sm:inline-block">{{ $roleLabel }}</span>
            </div>

            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center gap-3 rounded-full bg-white py-1.5 ps-1.5 pe-4 shadow-sm transition hover:shadow focus:outline-none focus:ring-2 focus:ring-brand/40" aria-label="Menu akun">
                        <span class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-brand text-xs font-bold text-white">
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="" class="h-full w-full object-cover">
                            @else
                                {{ $initial }}
                            @endif
                        </span>
                        <span class="hidden max-w-[140px] truncate text-xs font-semibold text-slate-700 sm:block">{{ $user->name }}</span>
                        <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <div class="border-b border-slate-100 px-4 py-2.5">
                        <div class="truncate text-xs font-semibold text-slate-800">{{ $user->name }}</div>
                        <div class="truncate text-[11px] text-slate-500">{{ $user->email }}</div>
                    </div>
                    <x-dropdown-link :href="route('profile.edit')">Pengaturan Akun</x-dropdown-link>
                    <x-dropdown-link :href="url('/')">Beranda Situs</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Keluar</x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </header>

        <main class="px-5 pb-14 pt-2 sm:px-8">
            <div class="mx-auto w-full {{ $width }}">
                {{-- Judul halaman + aksi --}}
                @if ($heading || isset($actions))
                    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                        <div class="min-w-0">
                            @if ($back)
                                <a href="{{ $back['href'] }}" class="mb-2 inline-flex items-center gap-1 text-xs font-semibold text-brand hover:underline">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                    {{ $back['label'] }}
                                </a>
                            @endif
                            @if ($heading)
                                <h1 class="text-2xl font-bold leading-tight text-slate-800 sm:text-[28px]">{{ $heading }}</h1>
                            @endif
                            @if ($subtitle)
                                <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
                            @endif
                        </div>
                        @isset($actions)
                            <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
                        @endisset
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
