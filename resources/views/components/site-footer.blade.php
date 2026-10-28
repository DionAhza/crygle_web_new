@php
    $footerNav = [
        'Navigasi' => [
            ['Home', url('/').'#beranda'], ['E-Learning', route('catalog.index')], ['Courses & Classes', url('/').'#program'],
            ['Mentor', url('/').'#keunggulan'], ['Testimoni', url('/').'#testimoni'],
        ],
        'Program' => [
            ['Creative Design', url('/').'#pilar'], ['Creative Coding', url('/').'#pilar'], ['Creative Robotics', url('/').'#pilar'],
            ['Consulting', url('/').'#kontak'], ['Karir', '#'],
        ],
        'Dukungan' => [
            ['Pusat Bantuan', url('/').'#faq'], ['Kontak Kami', url('/').'#kontak'], ['Kebijakan Privasi', '#'],
            ['Syarat & Ketentuan', '#'], ['FAQ', url('/').'#faq'],
        ],
    ];
@endphp

<footer class="bg-brand text-white">
    <div class="mx-auto max-w-6xl px-4 pb-6 pt-12 sm:px-6">
        <div class="grid grid-cols-2 gap-10 lg:grid-cols-[1.6fr_1fr_1fr_1fr]">

            {{-- Brand --}}
            <div class="col-span-2 lg:col-span-1">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                    <img src="{{ asset('images/footer.svg') }}" alt="">
                </a>
                <p class="mt-5 max-w-xs text-sm leading-relaxed text-blue-100">
                    Belajar kreatif digital dari nol untuk masa depan yang lebih siap.
                </p>

                <ul class="mt-6 space-y-3 text-xs text-blue-50">
                    <li class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <a href="mailto:tanya@crygleacademy.com" class="hover:underline">tanya@crygleacademy.com</a>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0z" />
                        </svg>
                        <span class="leading-relaxed">Jl. Cipta Karya, Sidomulyo Bar., Kec. Tampan, Kota Pekanbaru, Riau 28293</span>
                    </li>
                </ul>
            </div>

            {{-- Kolom link --}}
            @foreach ($footerNav as $judul => $links)
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wide">{{ $judul }}</h3>
                    <ul class="mt-4 space-y-3 text-xs text-blue-100">
                        @foreach ($links as [$label, $href])
                            <li><a href="{{ $href }}" class="transition hover:text-white hover:underline">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="mt-10 border-t border-white/25 pt-5 text-center text-xs text-blue-100">
            &copy; {{ date('Y') }} CRYGLE Academy. All rights reserved.
        </div>
    </div>
</footer>
