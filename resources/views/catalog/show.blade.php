@use('App\Support\Format')
@use('Illuminate\Support\Str')
@php
    $duration = Format::duration((int) $totalSeconds);
    $level = ucfirst($course->level);
    $summary = Str::limit(Str::of($course->description ?? '')->squish()->toString(), 170);
    $previewLesson = $course->modules->flatMap->lessons->firstWhere('is_free_preview', true);
    $outcomes = $course->modules->pluck('title')->take(8);
    $check = 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z';
@endphp
<x-site-layout :title="$course->title" :wide="true">
    <x-slot name="breadcrumb">
        <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-1.5">
            <a href="{{ url('/') }}" class="hover:text-brand">Home</a><span>›</span>
            <a href="{{ route('catalog.index') }}" class="hover:text-brand">Course</a><span>›</span>
            <span class="font-medium text-slate-700">{{ $course->title }}</span>
        </nav>
    </x-slot>

    @if (! $course->is_published)
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Pratinjau: kelas ini masih draft dan belum tampil di katalog.</div>
    @endif
    <x-flash />

    <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]" x-data="{ tab: 'overview' }">
        {{-- ============ Kolom kiri ============ --}}
        <div class="min-w-0">
            <div class="relative overflow-hidden rounded-2xl bg-slate-200">
                @if ($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="aspect-video w-full object-cover">
                @else
                    <div class="flex aspect-video w-full items-center justify-center bg-gradient-to-br from-brand to-blue-400 text-white/80">
                        <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                @endif
                @if ($previewLesson)
                    <a href="{{ route('learn.show', [$course, $previewLesson]) }}" class="absolute inset-0 flex items-center justify-center bg-black/10 transition hover:bg-black/20" aria-label="Tonton materi pratinjau gratis">
                        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-white/90 text-brand shadow-lg">
                            <svg class="ml-1 h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72a1 1 0 0 0 1.52.85l10.86-6.86a1 1 0 0 0 0-1.7L9.52 4.29A1 1 0 0 0 8 5.14Z"/></svg>
                        </span>
                        <span class="absolute bottom-3 right-3 rounded-full bg-black/60 px-3 py-1 text-[11px] font-semibold text-white">Preview gratis</span>
                    </a>
                @endif
            </div>

            <h1 class="mt-6 text-2xl font-extrabold leading-snug text-slate-900 sm:text-3xl">{{ $course->title }}</h1>
            @if ($summary)
                <p class="mt-3 text-sm leading-relaxed text-slate-400">{{ $summary }}</p>
            @endif

            {{-- Meta --}}
            <div class="mt-5 flex flex-wrap items-center gap-x-8 gap-y-4">
                <div>
                    <div class="text-[10px] text-slate-400">Mentor</div>
                    <div class="mt-0.5 flex items-center gap-1.5 text-xs font-semibold text-slate-800">
                        {{ $course->mentor->name }}
                        @if ($course->mentor->mentorType)<span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-brand">{{ $course->mentor->mentorType->name }}</span>@endif
                    </div>
                </div>
                <div class="border-l border-slate-200 pl-8">
                    <div class="text-[10px] text-slate-400">Durasi</div>
                    <div class="mt-0.5 text-xs font-semibold text-slate-800">{{ $duration }}</div>
                </div>
                <div class="border-l border-slate-200 pl-8">
                    <div class="text-[10px] text-slate-400">Materi</div>
                    <div class="mt-0.5 text-xs font-semibold text-slate-800">{{ $lessonCount }} materi · {{ $course->modules->count() }} bab</div>
                </div>
                <div x-data="{ copied: false }" class="sm:ml-2">
                    <button type="button"
                            @click="navigator.share ? navigator.share({ title: @js($course->title), url: location.href }).catch(() => {}) : navigator.clipboard.writeText(location.href).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="inline-flex items-center gap-2 rounded-lg border border-brand px-5 py-2 text-xs font-semibold text-brand transition hover:bg-brand-50">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/></svg>
                        <span x-text="copied ? 'Tertaut!' : 'Share'">Share</span>
                    </button>
                </div>
            </div>

            {{-- Tab --}}
            <div class="mt-8 flex gap-8 overflow-x-auto border-b border-slate-200 text-xs font-semibold" role="tablist">
                @foreach (['overview' => 'Overview', 'kurikulum' => 'Kurikulum Kelas', 'mentor' => 'Tentang Mentor', 'reviews' => 'Reviews'] as $key => $label)
                    <button type="button" role="tab" @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-brand text-brand' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            class="-mb-px shrink-0 border-b-2 pb-3">{{ $label }}</button>
                @endforeach
            </div>

            {{-- Overview --}}
            <div x-show="tab === 'overview'" class="mt-6">
                <h2 class="text-sm font-bold text-slate-900">Course Overview</h2>
                @if ($course->description)
                    <div class="mt-3 space-y-4 text-justify text-xs leading-relaxed text-slate-700">
                        @foreach (preg_split('/\R{2,}/', trim($course->description)) as $para)
                            <p>{!! nl2br(e($para)) !!}</p>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-xs text-slate-500">Deskripsi kelas belum tersedia.</p>
                @endif

                @if ($outcomes->isNotEmpty())
                    <h3 class="mt-8 text-sm font-bold text-slate-900">Apa yang Akan Kamu Dapat?</h3>
                    <ul class="mt-4 grid gap-x-8 gap-y-4 sm:grid-cols-2">
                        @foreach ($outcomes as $item)
                            <li class="flex items-start gap-2.5 text-xs leading-relaxed text-slate-700">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $check }}"/></svg>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Kurikulum --}}
            <div x-show="tab === 'kurikulum'" x-cloak class="mt-6">
                <h2 class="text-sm font-bold text-slate-900">Kurikulum Kelas</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $course->modules->count() }} bab · {{ $lessonCount }} materi · {{ $duration }}</p>
                <div class="mt-4 space-y-3">
                    @forelse ($course->modules as $module)
                        <details class="group rounded-xl border border-blue-100 bg-white" @if ($loop->first) open @endif>
                            <summary class="flex cursor-pointer items-center justify-between px-5 py-4 text-xs font-semibold text-slate-800">
                                <span>{{ $module->sort_order }}. {{ $module->title }}</span>
                                <span class="text-[11px] font-normal text-slate-500">{{ $module->lessons->count() }} materi</span>
                            </summary>
                            <ul class="divide-y divide-blue-50 border-t border-blue-50">
                                @foreach ($module->lessons as $lesson)
                                    <li class="flex items-center justify-between gap-3 px-5 py-3 text-xs">
                                        <span class="text-slate-700">{{ $lesson->title }}
                                            @if ($lesson->is_free_preview)<a href="{{ route('learn.show', [$course, $lesson]) }}" class="ms-1 rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-700 hover:bg-emerald-200">Preview</a>@endif
                                        </span>
                                        <span class="shrink-0 text-[11px] text-slate-400">{{ Format::duration($lesson->duration_seconds) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    @empty
                        <p class="rounded-xl border border-dashed border-blue-200 bg-white p-6 text-xs text-slate-500">Silabus belum tersedia.</p>
                    @endforelse
                </div>
            </div>

            {{-- Tentang Mentor --}}
            <div x-show="tab === 'mentor'" x-cloak class="mt-6">
                <h2 class="text-sm font-bold text-slate-900">Tentang Mentor</h2>
                <div class="mt-4 flex items-center gap-4 rounded-2xl border border-blue-100 bg-white p-5">
                    @if ($course->mentor->avatar_url ?? null)
                        <img src="{{ $course->mentor->avatar_url }}" alt="" class="h-14 w-14 rounded-full object-cover">
                    @else
                        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-brand text-lg font-bold text-white">{{ Str::upper(Str::substr($course->mentor->name, 0, 1)) }}</span>
                    @endif
                    <div>
                        <div class="text-sm font-bold text-slate-900">{{ $course->mentor->name }}</div>
                        <div class="text-xs text-slate-500">{{ $course->mentor->mentorType?->name ?? 'Mentor' }} · Crygle Academy</div>
                    </div>
                </div>
            </div>

            {{-- Reviews --}}
            <div x-show="tab === 'reviews'" x-cloak class="mt-6">
                <h2 class="text-sm font-bold text-slate-900">Reviews</h2>
                <p class="mt-3 rounded-xl border border-dashed border-blue-200 bg-white p-6 text-center text-xs text-slate-500">Belum ada ulasan untuk kelas ini.</p>
            </div>
        </div>

        {{-- ============ Kartu harga ============ --}}
        <aside class="lg:sticky lg:top-6 lg:self-start">
            <div class="rounded-2xl border border-blue-100/70 bg-white p-6 shadow-lg shadow-blue-900/5">
                <div class="text-[11px] text-slate-400">Investasi</div>
                <p class="mt-1 text-3xl font-extrabold {{ $course->isFree() ? 'text-emerald-600' : 'text-slate-900' }}">{{ $course->isFree() ? 'Gratis' : Format::rupiah($course->price) }}</p>

                <div class="mt-5">
                    @guest
                        <a href="{{ route('login') }}" class="block rounded-full bg-brand px-5 py-3.5 text-center text-xs font-semibold text-white shadow-lg shadow-brand/30 transition hover:bg-brand-dark">Masuk untuk Mendaftar</a>
                        <p class="mt-3 text-center text-[11px] text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-brand hover:underline">Daftar</a></p>
                    @else
                        @if (auth()->user()->hasRole('student'))
                            @if ($enrollment?->isPaid())
                                <a href="{{ route('learn.index', $course) }}" class="block rounded-full bg-brand px-5 py-3.5 text-center text-xs font-semibold text-white shadow-lg shadow-brand/30 transition hover:bg-brand-dark">{{ $enrollment->progress_percent > 0 ? 'Lanjutkan Belajar' : 'Mulai Belajar' }}</a>
                            @elseif ($enrollment)
                                <a href="{{ route('student.checkout', $enrollment) }}" class="block rounded-full bg-amber-500 px-5 py-3.5 text-center text-xs font-semibold text-white shadow-lg shadow-amber-500/30 transition hover:bg-amber-600">Selesaikan Pembayaran</a>
                            @elseif ($course->is_published)
                                <form method="POST" action="{{ route('student.enroll', $course) }}">
                                    @csrf
                                    <button class="w-full rounded-full bg-brand px-5 py-3.5 text-xs font-semibold text-white shadow-lg shadow-brand/30 transition hover:bg-brand-dark">{{ $course->isFree() ? 'Daftar Gratis' : 'Beli Kelas' }}</button>
                                </form>
                            @endif
                        @else
                            <p class="rounded-xl bg-slate-50 p-3 text-center text-xs text-slate-500">Hanya akun siswa yang dapat mendaftar kelas.</p>
                        @endif
                    @endguest
                </div>

                <ul class="mt-5 text-xs text-slate-600">
                    @foreach ([
                        [$duration.' durasi belajar', 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                        [$level.' Level Class', 'M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5'],
                        [$lessonCount.' materi video & bacaan', 'M15.75 10.5l4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z'],
                        ['Tugas dengan review mentor', 'M16.862 4.487 18.549 2.8a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z'],
                        ['Certificate of Completion', 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5'],
                    ] as [$text, $icon])
                        <li class="flex items-center gap-3 border-b border-slate-100 py-3 last:border-0">
                            <svg class="h-4 w-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                            {{ $text }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>

    {{-- ============ Course serupa ============ --}}
    @if ($similar->isNotEmpty())
        <section class="mt-16">
            <h2 class="text-2xl font-extrabold text-slate-900">Course Serupa</h2>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($similar as $item)
                    <a href="{{ route('catalog.show', $item) }}" class="group overflow-hidden rounded-2xl bg-white shadow-lg shadow-blue-900/5 transition hover:-translate-y-0.5 hover:shadow-xl">
                        @if ($item->thumbnail_url)
                            <img src="{{ $item->thumbnail_url }}" alt="" class="aspect-[4/3] w-full object-cover">
                        @else
                            <div class="aspect-[4/3] w-full bg-gradient-to-br from-brand to-blue-400"></div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-xs font-bold leading-snug text-slate-900 group-hover:text-brand">{{ $item->title }}</h3>
                            <p class="mt-2 text-[10px] text-slate-400">{{ ucfirst($item->level) }} Level Class · {{ $item->mentor->name }}</p>
                            <p class="mt-4 text-sm font-extrabold {{ $item->isFree() ? 'text-emerald-600' : 'text-slate-900' }}">{{ $item->isFree() ? 'Gratis' : Format::rupiah($item->price) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-site-layout>
