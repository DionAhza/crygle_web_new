{{-- ============================== NAVBAR ============================== --}}
<header class="sticky top-0 z-50 border-b border-blue-100 bg-white/80 backdrop-blur">
    <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
        {{-- Logo --}}
        <a href="#beranda" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.svg') }}" alt="">
        </a>

        {{-- Menu desktop --}}
        <ul class="hidden items-center gap-8 text-sm font-medium text-slate-600 lg:flex">
            <li><a href="#beranda" class="transition hover:text-brand">Beranda</a></li>
            <li><a href="#program" class="transition hover:text-brand">Program</a></li>
            <li><a href="#karya" class="transition hover:text-brand">Karya Siswa</a></li>
            <li><a href="#keunggulan" class="transition hover:text-brand">Tentang Crygle</a></li>
        </ul>

        {{-- CTA desktop --}}
        <div class="hidden items-center gap-2 lg:flex">
            @guest
                <a href="{{ route('login') }}"
                   class="rounded-full px-5 py-2 text-sm font-semibold text-brand transition hover:text-blue-500">Masuk</a>
            @endguest
            @auth
                <a href="{{ route('dashboard') }}"
                   class="rounded-full px-5 py-2 text-sm font-semibold text-brand transition hover:text-blue-500">Dashboard</a>
            @endauth
            <a href="{{ route('catalog.index') }}"
               class="rounded-full bg-brand px-5 py-2 text-sm font-semibold text-white transition hover:bg-brand-dark">Lihat Kelas</a>
        </div>

        {{-- Tombol hamburger (mobile) --}}
        <button id="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full text-brand hover:bg-blue-50 lg:hidden">
            <svg id="icon-open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg id="icon-close" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </nav>

    {{-- Menu mobile --}}
    <div id="nav-menu" class="hidden border-t border-blue-100 bg-white lg:hidden">
        <ul class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-3 text-sm font-medium text-slate-600 sm:px-6">
            <li><a href="#beranda" class="block rounded-lg px-3 py-2 hover:bg-blue-50">Beranda</a></li>
            <li><a href="#program" class="block rounded-lg px-3 py-2 hover:bg-blue-50">Program</a></li>
            <li><a href="#karya" class="block rounded-lg px-3 py-2 hover:bg-blue-50">Karya Siswa</a></li>
            <li><a href="#keunggulan" class="block rounded-lg px-3 py-2 hover:bg-blue-50">Tentang Crygle</a></li>
            @guest
                <li class="pt-2">
                    <a href="{{ route('login') }}" class="block rounded-full border border-brand px-5 py-2.5 text-center font-semibold text-brand hover:bg-blue-50">Masuk</a>
                </li>
            @endguest
            @auth
                <li class="pt-2">
                    <a href="{{ route('dashboard') }}" class="block rounded-full border border-brand px-5 py-2.5 text-center font-semibold text-brand hover:bg-blue-50">Dashboard</a>
                </li>
            @endauth
            <li>
                <a href="{{ route('catalog.index') }}" class="block rounded-full bg-brand px-5 py-2.5 text-center font-semibold text-white hover:bg-brand-dark">Lihat Kelas</a>
            </li>
        </ul>
    </div>
</header>

{{-- Toggle menu mobile --}}
<script>
    (() => {
        const toggle = document.getElementById('nav-toggle');
        const menu = document.getElementById('nav-menu');
        const iconOpen = document.getElementById('icon-open');
        const iconClose = document.getElementById('icon-close');

        toggle.addEventListener('click', () => {
            const isHidden = menu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden', !isHidden);
            iconClose.classList.toggle('hidden', isHidden);
            toggle.setAttribute('aria-expanded', String(!isHidden));
        });

        // Tutup menu setelah salah satu link diklik
        menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
            menu.classList.add('hidden');
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
        }));
    })();
</script>