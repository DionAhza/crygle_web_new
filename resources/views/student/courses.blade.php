@use('App\Support\Format')
@php
    $levelLabels = ['basic' => 'Basic', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'];
    $cards = $active->map(function ($enrollment) use ($lessonTotals, $lessonDone, $levelLabels) {
        $course = $enrollment->course;

        return [
            'enrollment' => $enrollment,
            'course' => $course,
            'title' => $course->title,
            'mentor' => $course->mentor->name ?? '',
            'level' => $course->level,
            'category' => $course->mentor->mentorType->name ?? '',
            'popular' => $course->enrollments_count ?? 0,
            'total' => (int) ($lessonTotals[$course->id] ?? 0),
            'done' => (int) ($lessonDone[$course->id] ?? 0),
        ];
    })->values();
    $categories = $cards->pluck('category')->filter()->unique()->values();
    $filterData = $cards->map(fn ($c) => [
        'id' => $c['enrollment']->id,
        'text' => \Illuminate\Support\Str::lower($c['title'].' '.$c['mentor']),
        'level' => $c['level'],
        'category' => $c['category'],
        'progress' => $c['enrollment']->progress_percent,
    ])->values();
@endphp
<x-student-layout title="Kelas Saya" heading="Kelas Saya">
    <div class="px-5 pb-10 sm:px-8"
         x-data="{
            items: @js($filterData),
            q: '', level: '', category: '', sort: 'popular',
            levelOpen: false, catOpen: false, sortOpen: false,
            get visible() {
                let list = this.items.filter(i =>
                    (!this.q || i.text.includes(this.q.toLowerCase())) &&
                    (!this.level || i.level === this.level) &&
                    (!this.category || i.category === this.category));
                if (this.sort === 'progress') list = [...list].sort((a, b) => b.progress - a.progress);
                return list.map(i => i.id);
            },
            closeAll() { this.levelOpen = this.catOpen = this.sortOpen = false; }
         }"
         @click.outside="closeAll()">

        {{-- Toolbar: pencarian + filter --}}
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <label class="relative block w-full lg:max-w-[340px]">
                <input type="text" x-model="q" placeholder="Search Course Name/Mentor"
                       class="w-full rounded-xl border border-slate-200 bg-white py-3 ps-4 pe-11 text-xs text-slate-700 placeholder:text-slate-400 focus:border-brand focus:ring-brand">
                <svg class="pointer-events-none absolute end-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M20 20l-3.5-3.5"/></svg>
            </label>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Level --}}
                <div class="relative">
                    <button type="button" @click="levelOpen = !levelOpen; catOpen = sortOpen = false"
                            class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="currentColor" viewBox="0 0 16 16"><rect x="1" y="9" width="3" height="6" rx="1"/><rect x="6.5" y="5" width="3" height="10" rx="1"/><rect x="12" y="1" width="3" height="14" rx="1"/></svg>
                        <span x-text="level ? ({basic:'Basic',intermediate:'Intermediate',advanced:'Advanced'})[level] : 'Level'"></span>
                    </button>
                    <div x-show="levelOpen" x-cloak class="absolute end-0 z-20 mt-2 w-44 rounded-xl border border-slate-100 bg-white p-1 text-xs shadow-lg">
                        <button type="button" @click="level = ''; levelOpen = false" class="block w-full rounded-lg px-3 py-2 text-start hover:bg-slate-50">Semua Level</button>
                        @foreach ($levelLabels as $value => $label)
                            <button type="button" @click="level = '{{ $value }}'; levelOpen = false" class="block w-full rounded-lg px-3 py-2 text-start hover:bg-slate-50">{{ $label }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Category (tipe mentor) --}}
                <div class="relative">
                    <button type="button" @click="catOpen = !catOpen; levelOpen = sortOpen = false"
                            class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 16 16"><rect x="1.5" y="1.5" width="5" height="13" rx="1"/><rect x="9.5" y="1.5" width="5" height="5.5" rx="1"/><rect x="9.5" y="9.5" width="5" height="5" rx="1"/></svg>
                        <span x-text="category || 'Category'"></span>
                    </button>
                    <div x-show="catOpen" x-cloak class="absolute end-0 z-20 mt-2 w-48 rounded-xl border border-slate-100 bg-white p-1 text-xs shadow-lg">
                        <button type="button" @click="category = ''; catOpen = false" class="block w-full rounded-lg px-3 py-2 text-start hover:bg-slate-50">Semua Kategori</button>
                        @foreach ($categories as $cat)
                            <button type="button" @click="category = @js($cat); catOpen = false" class="block w-full rounded-lg px-3 py-2 text-start hover:bg-slate-50">{{ $cat }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Sort --}}
                <div class="relative">
                    <button type="button" @click="sortOpen = !sortOpen; levelOpen = catOpen = false"
                            class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" viewBox="0 0 16 16"><path d="M2 4h12M4 8h8M6 12h4"/></svg>
                        Sort By : <span x-text="sort === 'progress' ? 'Progres' : 'Popular'"></span>
                    </button>
                    <div x-show="sortOpen" x-cloak class="absolute end-0 z-20 mt-2 w-40 rounded-xl border border-slate-100 bg-white p-1 text-xs shadow-lg">
                        <button type="button" @click="sort = 'popular'; sortOpen = false" class="block w-full rounded-lg px-3 py-2 text-start hover:bg-slate-50">Popular</button>
                        <button type="button" @click="sort = 'progress'; sortOpen = false" class="block w-full rounded-lg px-3 py-2 text-start hover:bg-slate-50">Progres</button>
                    </div>
                </div>
            </div>
        </div>

        <x-flash />

        {{-- Menunggu pembayaran (tetap dipertahankan dari versi lama) --}}
        @if ($unpaid->isNotEmpty())
            <section class="mt-6">
                <h2 class="mb-3 text-sm font-semibold text-slate-800">Menunggu Pembayaran</h2>
                <div class="space-y-2">
                    @foreach ($unpaid as $enrollment)
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                            <div>
                                <div class="text-sm font-semibold text-slate-800">{{ $enrollment->course->title }}</div>
                                <div class="text-xs text-slate-500">{{ Format::rupiah($enrollment->course->price) }} · {{ $enrollment->payment_status === 'failed' ? 'Pembayaran gagal/kedaluwarsa' : 'Belum dibayar' }}</div>
                            </div>
                            <a href="{{ route('student.checkout', $enrollment) }}" class="rounded-full bg-amber-500 px-5 py-2 text-xs font-semibold text-white hover:bg-amber-600">Bayar Sekarang</a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Grid kartu kelas --}}
        <section class="-mx-5 mt-6 min-h-[60vh] rounded-none bg-[#f0f5fc] px-5 py-6 sm:-mx-8 sm:px-8">
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($cards as $card)
                    @php
                        $enrollment = $card['enrollment'];
                        $percent = $enrollment->progress_percent;
                        $barColor = $percent >= 60 ? 'bg-green-500' : 'bg-amber-400';
                        $levelText = ($levelLabels[$card['level']] ?? ucfirst($card['level'])).' Level Class';
                        $cta = $enrollment->isCompleted() ? 'Tinjau Kelas' : ($percent > 0 ? 'Lanjutkan Belajar' : 'Mulai Belajar');
                    @endphp
                    <article x-show="visible.includes({{ $enrollment->id }})" x-transition.opacity
                             class="flex flex-col rounded-[20px] bg-white p-5 shadow-sm">
                        <a href="{{ route('learn.index', $card['course']) }}" class="block">
                            @if ($card['course']->thumbnail_url)
                                <img src="{{ $card['course']->thumbnail_url }}" alt="" class="h-[104px] w-full rounded-xl object-cover sm:h-[110px]">
                            @else
                                <div class="h-[104px] w-full rounded-xl bg-gradient-to-br from-brand to-slate-700 sm:h-[110px]"></div>
                            @endif
                        </a>

                        <h3 class="mt-5 line-clamp-2 text-base font-bold leading-snug text-slate-800">{{ $card['title'] }}</h3>
                        <p class="mt-3 text-sm text-slate-500">{{ $levelText }}</p>

                        <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-brand-50">
                            <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-[11px] font-bold text-slate-800">
                            <span>{{ $card['done'] }}/{{ $card['total'] }} Modul</span>
                            <span>{{ $percent }}%</span>
                        </div>

                        @if ($enrollment->certificate_code)
                            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center">
                                <div class="text-xs font-semibold text-emerald-700">🎓 Kelas selesai</div>
                                <a href="{{ route('certificates.download', $enrollment) }}" class="mt-2 block rounded-full bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Unduh Sertifikat (PDF)</a>
                                <a href="{{ route('certificates.verify', $enrollment->certificate_code) }}" class="mt-2 block text-xs font-medium text-emerald-700 hover:underline">Lihat halaman verifikasi</a>
                            </div>
                        @endif

                        <a href="{{ route('learn.index', $card['course']) }}" class="mt-4 rounded-full bg-brand px-4 py-2.5 text-center text-xs font-semibold text-white transition hover:bg-brand-dark">{{ $cta }}</a>
                    </article>
                @empty
                    <p class="col-span-full text-sm text-slate-500">Anda belum mengikuti kelas apa pun. <a href="{{ route('catalog.index') }}" class="font-semibold text-brand hover:underline">Cari kelas</a> untuk memulai.</p>
                @endforelse
            </div>

            @if ($cards->isNotEmpty())
                <p x-show="visible.length === 0" x-cloak class="py-10 text-center text-sm text-slate-500">Tidak ada kelas yang cocok dengan pencarian/filter.</p>
            @endif
        </section>
    </div>
</x-student-layout>
