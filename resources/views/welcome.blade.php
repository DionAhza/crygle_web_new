<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crygle Academy - IT & Creative Bootcamp for Kids</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-blue-50/60 font-['Plus_Jakarta_Sans',ui-sans-serif,system-ui,sans-serif] text-slate-700 antialiased">

    <x-navbar></x-navbar>

    {{-- ============================== HERO ============================== --}}
    <section id="beranda" class="mx-auto max-w-6xl px-4 pb-12 pt-8 sm:px-6 lg:pt-12">

        <div class="grid grid-cols-1 items-center gap-6 lg:grid-cols-2 lg:gap-12">
            {{-- Kolom kiri --}}
            <div>
                <span class="inline-block rounded-full bg-brand-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-brand">
                    IT &amp; Creative Bootcamp for Kids
                </span>
                <h1 class="mt-4 text-3xl font-extrabold leading-tight text-brand sm:text-4xl lg:text-5xl">
                    Bangun Skill Digital Anak Sejak Dini, Bukan Sekadar Screen Time.
                </h1>
            </div>

            {{-- Kolom kanan --}}
            <div class="lg:pt-8">
                <p class="text-sm leading-relaxed text-slate-600 sm:text-base">
                    Pelajari Design, Coding &amp; Robotics untuk siswa SD, SMP, dan SMK dengan
                    pembelajaran berbasis proyek, level yang bertahap, dan hasil karya yang bisa
                    menjadi portfolio.
                </p>
                <a href="https://wa.me/62XXXXXXXXXXX?text=Halo%20Crygle%20Academy,%20saya%20ingin%20konsultasi%20program%20untuk%20anak"
                   target="_blank" rel="noopener"
                   class="mt-5 inline-flex items-center gap-2 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-dark">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35zM12.05 21.8h-.01a9.9 9.9 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88 2.64 0 5.12 1.03 6.99 2.9a9.82 9.82 0 0 1 2.89 6.99c0 5.45-4.44 9.88-9.88 9.88zM20.52 3.45A11.8 11.8 0 0 0 12.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 0 0 5.68 1.45h.01c6.55 0 11.89-5.34 11.89-11.89 0-3.18-1.24-6.17-3.48-8.41z"/>
                    </svg>
                    Konsultasikan Program Anak
                </a>
            </div>
        </div>

        {{-- Gambar / video --}}
        <div class="relative mt-8 overflow-hidden rounded-3xl bg-slate-200 shadow-lg shadow-brand-dark/5 lg:mt-10">
            <img src="{{ asset('images/hero-crygle.jpg') }}"
                 alt="Siswa belajar bersama mentor di Crygle Academy"
                 class="aspect-[4/3] w-full object-cover sm:aspect-video">

            <a href="#video" aria-label="Putar video profil Crygle Academy"
               class="absolute left-1/2 top-1/2 flex h-16 w-16 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-brand text-white shadow-xl ring-4 ring-white/60 transition hover:scale-105 hover:bg-brand-dark sm:h-20 sm:w-20">
                <svg class="ml-1 h-7 w-7 sm:h-8 sm:w-8" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M8 5v14l11-7z" />
                </svg>
            </a>
        </div>

        {{-- Statistik singkat --}}
        <div class="mt-8 grid grid-cols-2 gap-x-4 gap-y-6 lg:grid-cols-4">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-brand">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19a4 4 0 0 0-8 0M11 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm8 8a4 4 0 0 0-3-3.87M16 5.13a3 3 0 0 1 0 5.74" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-slate-800">100+</p>
                    <p class="text-xs text-slate-500">Siswa &amp; Alumni</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-brand">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1z" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-slate-800">Sejak 2024</p>
                    <p class="text-xs text-slate-500">Sudah Aktif Membantu</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-brand">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5c1.75 0 3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18c-1.75 0-3.33.48-4.5 1.25" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-slate-800">Project-Based Learning</p>
                    <p class="text-xs text-slate-500">Belajar Lewat Praktik</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-brand">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 5.11a.56.56 0 0 0 .48.35l5.52.44c.5.04.7.66.32.99l-4.2 3.6a.56.56 0 0 0-.18.56l1.28 5.39a.56.56 0 0 1-.84.61l-4.73-2.89a.56.56 0 0 0-.58 0l-4.73 2.89a.56.56 0 0 1-.84-.61l1.28-5.39a.56.56 0 0 0-.18-.56l-4.2-3.6a.56.56 0 0 1 .32-.99l5.52-.44a.56.56 0 0 0 .48-.35l2.13-5.11z" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-slate-800">Program Unggul</p>
                    <p class="text-xs text-slate-500">Design • Coding • Robotics</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== MASALAH ORANG TUA ============================== --}}
    <section id="masalah" class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:py-16">
        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-2 lg:gap-12">

            {{-- Kolom kiri --}}
            <div>
                <span class="inline-block rounded-full bg-brand-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-brand">
                    Masalah Orang Tua
                </span>
                <h2 class="mt-4 text-2xl font-extrabold leading-tight text-brand sm:text-3xl lg:text-4xl">
                    Anak Sudah Dekat dengan Teknologi. Sekarang Saatnya Belajar Menciptakan dengan Teknologi.
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                    Orang tua mungkin melihat anak semakin dekat dengan gadget, game, internet, dan teknologi.
                </p>

                <blockquote class="mt-5 rounded-r-xl border-l-4 border-brand bg-white px-4 py-3 text-sm font-medium leading-relaxed text-slate-700 shadow-sm">
                    Apakah screen time anak hanya menjadi konsumsi, atau bisa berubah menjadi kemampuan yang menghasilkan karya?
                </blockquote>

                <a href="#program"
                   class="mt-6 inline-flex items-center rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-dark">
                    Cari Jalur Belajar Anak
                </a>
            </div>

            {{-- Kolom kanan: 3 kartu masalah --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm sm:col-span-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[11px] font-bold text-brand">01</span>
                    <h3 class="mt-3 text-sm font-bold text-slate-800">Screen Time Tanpa Arah</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
                        Anak menghabiskan banyak waktu dengan gadget, tetapi belum diarahkan untuk membuat sesuatu yang produktif.
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[11px] font-bold text-brand">02</span>
                    <h3 class="mt-3 text-sm font-bold text-slate-800">Belajar IT di Sekolah Belum Cukup</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
                        Anak membutuhkan pengalaman praktik dan proyek untuk mengembangkan skill digital secara bertahap.
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-[11px] font-bold text-brand">03</span>
                    <h3 class="mt-3 text-sm font-bold text-slate-800">Orang Tua Bingung Mulai dari Mana</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
                        Coding, design, robotika, UI/UX dan lain-lain. Pilih yang mana dan mulai dari mana?
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== KEUNGGULAN CRYGLE ============================== --}}
    @php
        $alur = [
            ['Learn',    'Pelajari Dasar',   'M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5c1.75 0 3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18c-1.75 0-3.33.48-4.5 1.25'],
            ['Practice', 'Latihan Terarah',  'M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931z'],
            ['Build',    'Buat Project',     'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9'],
            ['Showcase', 'Tampilkan Karya',  'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09z'],
        ];

        $keunggulan = [
            ['Belajar Bertahap',    'Materi disusun dari dasar menuju level yang lebih tinggi.'],
            ['Mentor & Guidance',   'Anak tidak belajar sendirian, selalu ada mentor yang membimbing.'],
            ['Kurikulum',           'Kurikulum dirancang sesuai dengan usia dan kebutuhan anak.'],
            ['Project & Portfolio', 'Pembelajaran diarahkan menjadi project nyata untuk portfolio.'],
        ];
    @endphp

    <section id="keunggulan" class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:py-16">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2 lg:gap-12">

            {{-- Kolom kiri --}}
            <div>
                <span class="inline-block rounded-full bg-brand-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-brand">
                    Apa yang membuat Crygle berbeda
                </span>
                <h2 class="mt-4 text-2xl font-extrabold leading-tight text-brand sm:text-3xl lg:text-4xl">
                    Bukan Sekadar Belajar. Anak Dibimbing Sampai Menghasilkan Karya.
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                    Di Crygle Academy, anak belajar melalui Project-Based Learning. Bukan hanya memahami materi,
                    tetapi menerapkannya melalui project yang dapat menjadi bagian dari portfolio.
                </p>
            </div>

            {{-- Kolom kanan --}}
            <div>
                {{-- Alur belajar --}}
                <ol class="grid grid-cols-2 gap-y-5 sm:grid-cols-4">
                    @foreach ($alur as $i => $step)
                        <li class="relative flex flex-col items-center text-center">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-100 text-brand ring-4 ring-white">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $step[2] }}" />
                                </svg>
                            </span>
                            <p class="mt-2 text-xs font-bold text-slate-800">{{ $step[0] }}</p>
                            <p class="text-[11px] text-slate-500">{{ $step[1] }}</p>

                            {{-- Panah antar langkah (desktop saja) --}}
                            @if (! $loop->last)
                                <span class="absolute -right-2 top-3 hidden text-brand sm:block" aria-hidden="true">&rarr;</span>
                            @endif
                        </li>
                    @endforeach
                </ol>

                {{-- Kartu keunggulan 2x2 --}}
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($keunggulan as $item)
                        <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">
                            <h3 class="text-sm font-bold text-brand">{{ $item[0] }}</h3>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ $item[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== JALUR BELAJAR BERDASARKAN USIA ============================== --}}
    @php
        $jalur = [
            [
                'badge'   => 'SD',
                'judul'   => 'Explore',
                'fokus'   => 'Fokus eksplorasi',
                'items'   => ['Creative Exploration', 'Basic Coding', 'Design', 'Robotics', 'Logic & Problem Solving'],
                'cta'     => 'Lihat Program SD',
                'href'    => '#program-sd',
                'unggulan'=> false,
            ],
            [
                'badge'   => 'SMP',
                'judul'   => 'Build',
                'fokus'   => 'Fokus membangun skill',
                'items'   => ['Coding', 'UI/UX Design', 'Creative Design', 'Robotics', 'Project Portfolio'],
                'cta'     => 'Lihat Program SMP',
                'href'    => '#program-smp',
                'unggulan'=> true,
            ],
            [
                'badge'   => 'SMA/SMK',
                'judul'   => 'Career Ready',
                'fokus'   => 'Fokus kesiapan karier',
                'items'   => ['Advanced Digital Skill', 'Project', 'CV & Portfolio', 'Industry Oriented Practice', 'Entrepreneurship'],
                'cta'     => 'Lihat Program SMA/SMK',
                'href'    => '#program-smk',
                'unggulan'=> false,
            ],
        ];
    @endphp

    <section id="program" class="bg-white py-12 lg:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">

            {{-- Heading --}}
            <div class="text-center">
                <span class="inline-block rounded-full bg-brand-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-brand">
                    Jalur Belajar Berdasarkan Usia
                </span>
                <h2 class="mt-4 text-2xl font-extrabold text-brand sm:text-3xl lg:text-4xl">
                    Setiap Anak Punya Starting Point yang Berbeda
                </h2>
                <p class="mt-3 text-sm text-slate-600 sm:text-base">
                    Pilih jalur belajar berdasarkan usia, pengalaman, dan minat anak.
                </p>
            </div>

            {{-- Kartu jalur --}}
            <div class="mt-10 grid grid-cols-1 items-stretch gap-5 md:grid-cols-3">
                @foreach ($jalur as $j)
                    <div class="flex flex-col rounded-2xl p-6 {{ $j['unggulan'] ? 'bg-brand text-white shadow-xl shadow-brand-dark/20 md:-my-3 md:py-9' : 'border border-blue-100 bg-blue-50/50' }}">

                        <span class="inline-flex w-fit items-center rounded-full px-3 py-1 text-[11px] font-bold {{ $j['unggulan'] ? 'bg-white text-brand' : 'bg-amber-400 text-blue-900' }}">
                            {{ $j['badge'] }}
                        </span>

                        <h3 class="mt-4 text-lg font-bold {{ $j['unggulan'] ? 'text-white' : 'text-slate-800' }}">{{ $j['judul'] }}</h3>
                        <p class="mt-1 text-xs {{ $j['unggulan'] ? 'text-blue-100' : 'text-slate-500' }}">{{ $j['fokus'] }}</p>

                        <ul class="mt-5 flex-1 space-y-2.5">
                            @foreach ($j['items'] as $item)
                                <li class="flex items-center gap-2 text-xs {{ $j['unggulan'] ? 'text-white' : 'text-slate-600' }}">
                                    <svg class="h-4 w-4 shrink-0 {{ $j['unggulan'] ? 'text-green-300' : 'text-green-500' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm3.707-9.293a1 1 0 0 0-1.414-1.414L9 10.586 7.707 9.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ $j['href'] }}"
                           class="mt-6 inline-flex items-center justify-center gap-2 rounded-full border px-5 py-2.5 text-xs font-semibold transition {{ $j['unggulan'] ? 'border-white bg-white text-brand hover:bg-blue-50' : 'border-brand bg-white text-brand hover:bg-brand hover:text-white' }}">
                            {{ $j['cta'] }}
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
        {{-- ============================== 3 PILAR ============================== --}}
    @php
        $pilar = [
            [
                'judul' => 'Creative Design',
                'teks'  => 'Menyalurkan imajinasi menjadi karya. Di Crygle Academy, kreativitas adalah kunci untuk menciptakan solusi unik. Dari imajinasi menjadi karya. Anak menyalurkan ide melalui visual, design, dan karya digital.',
                'icon'  => 'M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42',
            ],
            [
                'judul' => 'Creative Coding',
                'teks'  => 'Bahasa masa depan untuk membangun ide. Di sini anak diajarkan coding dengan cara kreatif dan aplikatif. Dari kode menjadi produk. Anak belajar coding secara kreatif dan aplikatif melalui project.',
                'icon'  => 'M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5',
            ],
            [
                'judul' => 'Creative Robotics',
                'teks'  => 'Mengenal teknologi cerdas sejak dini. Belajar merancang dan mengendalikan robot secara kreatif dan menyenangkan. Dari ide menjadi teknologi yang bergerak. Anak mengenal teknologi, logika, dan robotics.',
                'icon'  => 'M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18',
            ],
        ];
    @endphp

    <section id="pilar" class="mx-auto max-w-6xl scroll-mt-20 px-4 py-12 sm:px-6 lg:py-16">
        <span class="inline-block rounded-full bg-brand-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-brand">
            3 Pilar Crygle Academy
        </span>
        <h2 class="mt-4 max-w-3xl text-2xl font-extrabold leading-tight text-brand sm:text-3xl lg:text-4xl">
            Anak Bisa Memilih Dunia Digital yang Paling Sesuai dengan Minatnya
        </h2>

        <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-2">

            {{-- Kolom kiri: kolase gambar --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 overflow-hidden rounded-2xl bg-blue-100">
                    <img src="{{ asset('images/landing_page/pillar-1.png') }}" alt="Creative Design"
                         class="aspect-[619/330] w-full object-cover">
                </div>
                <div class="overflow-hidden rounded-2xl bg-blue-100">
                    <img src="{{ asset('images/landing_page/pillar-2.png') }}" alt="Creative Robotics"
                         class="aspect-[295/335] w-full object-cover">
                </div>
                <div class="overflow-hidden rounded-2xl bg-blue-100">
                    <img src="{{ asset('images/landing_page/pillar-3.png') }}" alt="Creative Coding"
                         class="aspect-[295/335] w-full object-cover">
                </div>
            </div>

            {{-- Kolom kanan: 3 kartu --}}
            <div class="flex flex-col gap-4">
                @foreach ($pilar as $p)
                    <div class="rounded-2xl border p-5 {{ $loop->first ? 'border-brand bg-brand text-white shadow-lg shadow-brand-dark/20' : 'border-blue-100 bg-white text-slate-700' }}">
                        <svg class="h-6 w-6 {{ $loop->first ? 'text-white' : 'text-brand' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $p['icon'] }}" />
                        </svg>
                        <h3 class="mt-3 text-sm font-bold {{ $loop->first ? 'text-white' : 'text-slate-800' }}">{{ $p['judul'] }}</h3>
                        <p class="mt-1 text-xs leading-relaxed {{ $loop->first ? 'text-blue-100' : 'text-slate-500' }}">{{ $p['teks'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== KARYA SISWA ============================== --}}
    @php
        $karya = [
            ['img' => 'images/karya/1.jpg', 'nama' => 'Almer Hadyan Raditya Deni', 'bidang' => 'Illustration Design',  'sekolah' => 'SMP IDN Bogor'],
            ['img' => 'images/karya/2.jpg', 'nama' => 'Ahmad Musyaffa Hannan',     'bidang' => 'UI/UX Design',          'sekolah' => 'SMK Telkom Jakarta'],
            ['img' => 'images/karya/3.jpg', 'nama' => 'Naufal Hidayat Saputra',    'bidang' => 'Website development',   'sekolah' => 'SMP IDN Bogor'],
        ];
    @endphp

    <section id="karya" class="relative scroll-mt-20 overflow-hidden bg-brand py-12 lg:py-16">
        {{-- Dekorasi --}}
        <div class="pointer-events-none absolute -left-24 top-1/2 h-96 w-96 rounded-full bg-white/5" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/5" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <div class="text-center">
                <span class="inline-block rounded-full bg-white px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-brand">
                    Karya Siswa
                </span>
                <h2 class="mt-4 text-2xl font-extrabold text-white sm:text-3xl lg:text-4xl">
                    Bayangkan Kalau Anak Pulang Membawa Karya Seperti Ini
                </h2>
                <p class="mt-3 text-sm text-blue-100 sm:text-base">
                    Lihat beberapa karya yang dapat menjadi bukti proses belajar dan berkembang menjadi portfolio.
                </p>
            </div>

            <div data-carousel class="mt-10">
                <div data-track class="flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    @foreach ($karya as $k)
                        <article data-item class="w-full shrink-0 snap-start overflow-hidden rounded-2xl bg-white shadow-lg md:w-[calc((100%-2.5rem)/3)]">
                            <img src="{{ asset($k['img']) }}" alt="Karya {{ $k['nama'] }}" class="aspect-[16/10] w-full bg-blue-100 object-cover">
                            <div class="p-5">
                                <h3 class="text-sm font-bold text-slate-800">{{ $k['nama'] }}</h3>
                                <p class="mt-2 text-xs text-slate-500">
                                    {{ $k['bidang'] }} &bull; Siswa <span class="font-semibold text-brand">{{ $k['sekolah'] }}</span>
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Kontrol --}}
                <div data-controls class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" data-prev aria-label="Sebelumnya"
                            class="flex h-8 w-8 items-center justify-center rounded-full border border-white/70 text-white transition hover:bg-white hover:text-brand">&lsaquo;</button>
                    <div data-dots class="flex items-center gap-1.5"
                         data-dot-active="bg-white w-2 h-2" data-dot-idle="bg-white/40 w-1.5 h-1.5"></div>
                    <button type="button" data-next aria-label="Berikutnya"
                            class="flex h-8 w-8 items-center justify-center rounded-full border border-white/70 text-white transition hover:bg-white hover:text-brand">&rsaquo;</button>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== APA KATA MEREKA (VIDEO) ============================== --}}
    @php
        // Ganti 'video' dengan link embed YouTube: https://www.youtube.com/embed/ID_VIDEO
        $video = [
            ['nama' => 'Almer Hadyan Raditya Deni', 'thumb' => 'images/video/1.jpg', 'video' => 'https://www.youtube.com/embed/XXXXXXXXXXX'],
            ['nama' => 'Ahmad Musyaffa Hannan',     'thumb' => 'images/video/2.jpg', 'video' => 'https://www.youtube.com/embed/XXXXXXXXXXX'],
            ['nama' => 'Naufal Hidayat Saputra',    'thumb' => 'images/video/3.jpg', 'video' => 'https://www.youtube.com/embed/XXXXXXXXXXX'],
            ['nama' => 'Syakir Fajar Rahayu',       'thumb' => 'images/video/4.jpg', 'video' => 'https://www.youtube.com/embed/XXXXXXXXXXX'],
            ['nama' => 'Denendra Kenzie Mulya Hariadi', 'thumb' => 'images/video/5.jpg', 'video' => 'https://www.youtube.com/embed/XXXXXXXXXXX'],
        ];
    @endphp

    <section id="video" class="scroll-mt-20 bg-white py-12 lg:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="text-center">
                <h2 class="text-2xl font-extrabold text-brand sm:text-3xl lg:text-4xl">Apa Kata Mereka</h2>
                <p class="mt-3 text-sm text-slate-600 sm:text-base">
                    Dari nggak tahu apa-apa, sekarang bisa bikin karya sendiri.
                </p>
            </div>

            <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($video as $v)
                    <button type="button" data-video="{{ $v['video'] }}"
                            class="group relative aspect-[9/16] overflow-hidden rounded-xl bg-slate-200 text-left shadow-sm">
                        <img src="{{ asset($v['thumb']) }}" alt="Video testimoni {{ $v['nama'] }}"
                             class="absolute inset-0 h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        <span class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent"></span>

                        <span class="absolute left-1/2 top-1/2 flex h-12 w-12 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-brand text-white shadow-lg ring-4 ring-white/50 transition group-hover:scale-110">
                            <svg class="ml-0.5 h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z" /></svg>
                        </span>

                        <span class="absolute inset-x-0 bottom-0 p-3 text-sm font-bold leading-snug text-white">{{ $v['nama'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Modal video --}}
    <dialog id="video-modal" class="m-auto w-[min(92vw,380px)] overflow-hidden rounded-2xl bg-black p-0 backdrop:bg-black/70">
        <button type="button" id="video-close" aria-label="Tutup video"
                class="absolute right-2 top-2 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-lg font-bold text-slate-800 hover:bg-white">&times;</button>
        <iframe id="video-frame" class="aspect-[9/16] w-full" src="" title="Video testimoni"
                allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
    </dialog>

        {{-- ============================== TESTIMONI ============================== --}}
    @php
        $testimoni = [
            ['inisial' => 'RS', 'nama' => 'Rina Susanti', 'peran' => 'Orang Tua Almer Hidayat',
             'teks' => 'Sebelumnya anak lebih banyak menggunakan gadget untuk bermain. Setelah mengikuti program, dia mulai punya kegiatan yang lebih terarah dan menghasilkan karya.',
             'program' => 'Illustration Design', 'durasi' => '6 Bulan'],
            ['inisial' => 'JA', 'nama' => 'Julia Ananta', 'peran' => 'Orang Tua Banu Suanta',
             'teks' => 'Programnya sangat terstruktur, mentornya komunikatif, dan hasil project anak saya benar-benar bisa dijadikan portfolio.',
             'program' => 'UI/UX Design', 'durasi' => '12 Bulan'],
            ['inisial' => 'AF', 'nama' => 'Anto Firmasyah', 'peran' => 'Orang Tua Anto Sumanta',
             'teks' => 'Awalnya aku nggak ngerti coding sama sekali. Setelah belajar step by step, sekarang aku sudah bisa membuat project sendiri.',
             'program' => 'Web Development', 'durasi' => '3 Bulan'],
        ];
    @endphp

    <section id="testimoni" class="mx-auto max-w-6xl scroll-mt-20 px-4 py-12 sm:px-6 lg:py-16">
        <div class="text-center">
            <span class="inline-block rounded-full bg-brand-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-brand">
                Testimoni
            </span>
            <h2 class="mt-4 text-2xl font-extrabold text-brand sm:text-3xl lg:text-4xl">
                Bukan Hanya Anak yang Merasakan Perubahannya
            </h2>
            <p class="mt-3 text-sm text-slate-600 sm:text-base">
                Kepuasan mereka adalah semangat kami untuk terus berkembang.
            </p>
        </div>

        <div data-carousel class="mt-10">
            <div data-track class="flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth py-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach ($testimoni as $t)
                    <article data-item class="flex w-full shrink-0 snap-start flex-col rounded-3xl border border-blue-100 bg-white p-6 shadow-sm md:w-[calc((100%-2.5rem)/3)]">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-slate-800">{{ $t['inisial'] }}</span>
                            <div>
                                <p class="text-sm font-bold text-brand">{{ $t['nama'] }}</p>
                                <p class="text-[11px] text-slate-500">{{ $t['peran'] }}</p>
                            </div>
                        </div>
                        <p class="mt-5 flex-1 text-xs leading-relaxed text-slate-600">&ldquo;{{ $t['teks'] }}&rdquo;</p>
                        <p class="mt-5 text-[11px] text-brand">{{ $t['program'] }} &bull; {{ $t['durasi'] }}</p>
                    </article>
                @endforeach
            </div>

            <div data-controls class="mt-8 flex items-center justify-center gap-3">
                <button type="button" data-prev aria-label="Sebelumnya"
                        class="flex h-8 w-8 items-center justify-center rounded-full border border-brand text-brand transition hover:bg-brand hover:text-white">&lsaquo;</button>
                <div data-dots class="flex items-center gap-1.5"
                     data-dot-active="bg-brand w-2 h-2" data-dot-idle="bg-blue-200 w-1.5 h-1.5"></div>
                <button type="button" data-next aria-label="Berikutnya"
                        class="flex h-8 w-8 items-center justify-center rounded-full border border-brand text-brand transition hover:bg-brand hover:text-white">&rsaquo;</button>
            </div>
        </div>
    </section>
        {{-- ============================== FAQ ============================== --}}
    @php
        // Dua kolom, masing-masing 4 pertanyaan. Item pertama di tiap kolom terbuka secara default.
        $faq = [
            [
                ['q' => 'Siapa saja yang bisa ikut kelas di Crygle Academy?',
                 'a' => 'Crygle Academy dirancang khusus untuk pemula mulai dari tingkat SD, SMP, hingga SMK. Materi kami disusun bertahap (step-by-step) dari pemahaman dasar hingga menghasilkan karya nyata yang dapat diandalkan.'],
                ['q' => 'Apakah saya membutuhkan laptop berspesifikasi tinggi?',
                 'a' => 'Untuk tahap awal, laptop dengan spesifikasi standar umumnya sudah cukup. Kebutuhan perangkat untuk tiap program akan kami informasikan saat konsultasi.'],
                ['q' => 'Bagaimana ritme belajar untuk siswa sekolah asrama (Boarding School)?',
                 'a' => 'Jadwal belajar dapat disesuaikan dengan aktivitas di asrama. Hubungi kami untuk membahas ritme yang paling cocok.'],
                ['q' => 'Apakah materi kelas bisa diakses selamanya?',
                 'a' => 'Masa akses materi mengikuti program yang dipilih. Detailnya akan dijelaskan oleh tim kami saat konsultasi.'],
            ],
            [
                ['q' => 'Apakah ada sertifikat resmi setelah menyelesaikan kelas?',
                 'a' => 'Setiap peserta yang menyelesaikan 100% modul dan mengunggah tugas/portofolio akhir akan mendapatkan Sertifikat Kompetensi Digital resmi dari Crygle Academy yang dapat dilampirkan dalam portofolio akademik.'],
                ['q' => 'Bagaimana jika saya mengalami kendala saat mempraktikkan materi?',
                 'a' => 'Anak tidak belajar sendirian. Mentor kami siap membantu dan membimbing saat ada kendala dalam praktik.'],
                ['q' => 'Apa metode pembayaran yang didukung?',
                 'a' => 'Silakan hubungi admin kami melalui WhatsApp untuk informasi metode pembayaran terbaru.'],
                ['q' => 'Apakah sekolah saya bisa bekerja sama secara institusi?',
                 'a' => 'Bisa. Silakan hubungi kami untuk mendiskusikan bentuk kerja sama yang sesuai dengan kebutuhan sekolah.'],
            ],
        ];
    @endphp

    <section id="faq" class="mx-auto max-w-6xl scroll-mt-20 px-4 py-12 sm:px-6 lg:py-16">
        <div class="text-center">
            <span class="inline-block rounded-full bg-brand-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-brand">
                Parent FAQ
            </span>
            <h2 class="mt-4 text-2xl font-extrabold text-brand sm:text-3xl lg:text-4xl">
                Pertanyaan yang Sering Ditanyakan Orang Tua
            </h2>
            <p class="mt-3 text-sm text-slate-600 sm:text-base">
                Ayah &amp; bunda masih bingung? Kami bantu jawab di sini.
            </p>
        </div>

        <div class="mt-10 grid grid-cols-1 items-start gap-4 lg:grid-cols-2">
            @foreach ($faq as $kolom)
                <div class="flex flex-col gap-4">
                    @foreach ($kolom as $item)
                        <details class="group rounded-2xl border border-blue-100 bg-white px-5 py-4 shadow-sm transition open:border-blue-600" @if ($loop->first) open @endif>
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-sm font-bold text-slate-800 [&::-webkit-details-marker]:hidden">
                                <span>{{ $item['q'] }}</span>
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm text-brand" aria-hidden="true">
                                    <span class="group-open:hidden">+</span>
                                    <span class="hidden group-open:inline">&times;</span>
                                </span>
                            </summary>
                            <p class="mt-3 text-xs leading-relaxed text-slate-500">{{ $item['a'] }}</p>
                        </details>
                    @endforeach
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============================== CTA PENUTUP ============================== --}}
    <section id="kontak" class="mx-auto max-w-6xl scroll-mt-20 px-4 py-12 sm:px-6 lg:py-16">
        <div class="relative overflow-hidden rounded-3xl border border-blue-200 bg-gradient-to-b from-blue-50 to-blue-100 px-6 py-14 text-center sm:px-10 lg:py-16">

            {{-- Ilustrasi opsional: letakkan di public/images/landing_page/ --}}
            <img src="{{ asset('images/landing_page/cta-left.png') }}" alt=""
                 class="pointer-events-none absolute bottom-0 left-0 hidden h-full w-auto md:block" aria-hidden="true">
            <img src="{{ asset('images/landing_page/cta-right.png') }}" alt=""
                 class="pointer-events-none absolute bottom-0 right-0 hidden h-full w-auto md:block" aria-hidden="true">

            <div class="relative mx-auto max-w-xl">
                <h2 class="text-2xl font-extrabold leading-tight text-slate-900 sm:text-3xl lg:text-4xl">
                    Saatnya Berinvestasi untuk Masa Depan Anak
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-500 sm:text-base">
                    Bantu anak mulai mengenal teknologi, membangun skill, dan menghasilkan karya sesuai usia dan minatnya.
                </p>
                <a href="https://wa.me/62XXXXXXXXXXX?text=Halo%20Crygle%20Academy,%20saya%20ingin%20konsultasi%20program%20untuk%20anak"
                   target="_blank" rel="noopener"
                   class="mt-7 inline-flex items-center gap-2 rounded-full bg-brand px-7 py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-brand-dark">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35zM12.05 21.8h-.01a9.9 9.9 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88 2.64 0 5.12 1.03 6.99 2.9a9.82 9.82 0 0 1 2.89 6.99c0 5.45-4.44 9.88-9.88 9.88zM20.52 3.45A11.8 11.8 0 0 0 12.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 0 0 5.68 1.45h.01c6.55 0 11.89-5.34 11.89-11.89 0-3.18-1.24-6.17-3.48-8.41z"/>
                    </svg>
                    Konsultasikan Program Anak
                </a>
            </div>
        </div>
    </section>

    <x-site-footer />

    {{-- ============================== SCRIPT: CAROUSEL + MODAL VIDEO ============================== --}}
    <script>
        // Carousel generik: berlaku untuk semua elemen [data-carousel]
        document.querySelectorAll('[data-carousel]').forEach(root => {
            const track    = root.querySelector('[data-track]');
            const items    = [...root.querySelectorAll('[data-item]')];
            const dotsBox  = root.querySelector('[data-dots]');
            const controls = root.querySelector('[data-controls]');
            const activeCls = dotsBox.dataset.dotActive;
            const idleCls   = dotsBox.dataset.dotIdle;

            const step    = () => items.length > 1 ? items[1].offsetLeft - items[0].offsetLeft : track.clientWidth;
            const visible = () => Math.max(1, Math.round(track.clientWidth / step()));
            const pages   = () => Math.max(1, items.length - visible() + 1);
            const current = () => Math.min(pages() - 1, Math.round(track.scrollLeft / step()));

            function renderDots() {
                const total = pages();
                controls.classList.toggle('hidden', total <= 1);
                dotsBox.innerHTML = '';
                for (let i = 0; i < total; i++) {
                    const dot = document.createElement('button');
                    dot.type = 'button';
                    dot.setAttribute('aria-label', 'Slide ' + (i + 1));
                    dot.addEventListener('click', () => track.scrollTo({ left: i * step() }));
                    dotsBox.appendChild(dot);
                }
                updateDots();
            }

            function updateDots() {
                const active = current();
                [...dotsBox.children].forEach((dot, i) => {
                    dot.className = 'rounded-full transition ' + (i === active ? activeCls : idleCls);
                });
            }

            root.querySelector('[data-prev]').addEventListener('click', () => track.scrollBy({ left: -step() }));
            root.querySelector('[data-next]').addEventListener('click', () => track.scrollBy({ left: step() }));
            track.addEventListener('scroll', () => requestAnimationFrame(updateDots), { passive: true });
            window.addEventListener('resize', renderDots);

            renderDots();
        });

        // Modal video testimoni
        (() => {
            const modal = document.getElementById('video-modal');
            const frame = document.getElementById('video-frame');

            document.querySelectorAll('[data-video]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const url = btn.dataset.video + (btn.dataset.video.includes('?') ? '&' : '?') + 'autoplay=1';
                    frame.src = url;
                    modal.showModal();
                });
            });

            const close = () => { modal.close(); frame.src = ''; };
            document.getElementById('video-close').addEventListener('click', close);
            modal.addEventListener('click', e => { if (e.target === modal) close(); });
            modal.addEventListener('cancel', () => { frame.src = ''; }); // tombol Esc
        })();
    </script>

</body>
</html>