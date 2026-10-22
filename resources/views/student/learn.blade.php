@use('App\Support\Format')
<x-app-layout>
    @push('head')
        @vite('resources/js/player.js')
    @endpush

    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <a href="{{ $mode === 'owner' ? route('mentor.courses.show', $course) : route('catalog.show', $course) }}" class="text-xs font-medium text-indigo-600 hover:underline">← {{ $course->title }}</a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $lesson->title }}</h2>
            </div>
            @if ($mode === 'student')
                <div class="w-48">
                    <div class="mb-1 flex justify-between text-xs text-slate-500"><span>Progres kelas</span><span>{{ $enrollment->progress_percent }}%</span></div>
                    <div class="h-2 rounded-full bg-slate-200"><div class="h-2 rounded-full bg-indigo-600" style="width: {{ $enrollment->progress_percent }}%"></div></div>
                </div>
            @else
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">{{ $mode === 'owner' ? 'Pratinjau mentor' : 'Preview gratis' }}</span>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[1fr_320px] lg:px-8">

            {{-- ============ Konten utama ============ --}}
            <div class="min-w-0 space-y-6">
                <x-flash />

                @if ($video && $video['provider'] === 'file')
                    <div class="overflow-hidden rounded-xl bg-black">
                        <video id="lesson-player" playsinline controls>
                            <source src="{{ $video['url'] }}">
                        </video>
                    </div>
                @elseif ($video)
                    <div class="overflow-hidden rounded-xl bg-black">
                        <div id="lesson-player" class="plyr__video-embed" data-plyr-provider="{{ $video['provider'] }}" data-plyr-embed-id="{{ $video['id'] }}"></div>
                    </div>
                @elseif ($lesson->video_url)
                    <p class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">Format URL video tidak dikenali. <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener" class="font-semibold underline">Buka video di tab baru</a>.</p>
                @endif

                @if ($contentHtml)
                    <article class="lesson-prose prose prose-slate max-w-none rounded-xl border border-slate-100 bg-white p-6 shadow-sm">{!! $contentHtml !!}</article>
                @elseif (! $video)
                    <p class="rounded-xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500">Belum ada konten untuk materi ini.</p>
                @endif

                @if ($lesson->assignment)
                    <section class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-6">
                        <h3 class="font-semibold text-indigo-900">📝 Tugas: {{ $lesson->assignment->title }}</h3>
                        <p class="mt-1 text-xs text-indigo-700">Nilai kelulusan: {{ $lesson->assignment->passing_score }} · Materi ini selesai setelah tugas disetujui mentor.</p>
                        @if ($instructionsHtml)
                            <div class="prose prose-sm prose-slate mt-4 max-w-none">{!! $instructionsHtml !!}</div>
                        @endif
                        @if ($mode === 'student')
                            @include('student.partials.submission-panel')
                        @elseif ($mode === 'owner')
                            <p class="mt-4 rounded-lg bg-white p-3 text-sm text-slate-500">Pengiriman siswa untuk tugas ini dinilai di menu <a href="{{ route('mentor.submissions.index') }}" class="font-semibold text-indigo-600 hover:underline">Review Tugas</a>.</p>
                        @endif
                    </section>
                @endif

                {{-- Navigasi --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        @if ($prev && ($states[$prev->id] ?? '') !== 'locked')
                            <a href="{{ route('learn.show', [$course, $prev]) }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Sebelumnya</a>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($canComplete)
                            <form method="POST" action="{{ route('learn.complete', [$course, $lesson]) }}">
                                @csrf
                                <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Tandai Selesai &amp; Lanjut →</button>
                            </form>
                        @elseif ($next)
                            <a href="{{ route('learn.show', [$course, $next]) }}" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Berikutnya →</a>
                        @elseif ($mode === 'preview')
                            <a href="{{ route('catalog.show', $course) }}" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Daftar untuk Akses Penuh</a>
                        @endif
                    </div>
                </div>

                @if ($mode === 'student' && $enrollment->isCompleted())
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                        🎓 Kelas selesai pada {{ $enrollment->completed_at->translatedFormat('d F Y') }}. Kode sertifikat: <span class="font-mono font-semibold">{{ $enrollment->certificate_code }}</span>
                    </div>
                @endif
            </div>

            {{-- ============ Sidebar silabus ============ --}}
            <aside class="lg:sticky lg:top-6 lg:self-start">
                <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-4 py-3 text-sm font-semibold text-slate-800">
                        Daftar Materi <span class="font-normal text-slate-400">({{ $position['current'] }}/{{ $position['total'] }})</span>
                    </div>
                    <div class="max-h-[70vh] overflow-y-auto">
                        @foreach ($modules as $module)
                            <div class="bg-slate-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $module->sort_order }}. {{ $module->title }}</div>
                            <ul>
                                @foreach ($lessons[$module->id] as $item)
                                    @php
                                        $state = $states[$item->id] ?? 'locked';
                                        $accessible = $mode === 'owner' || ($mode === 'student' && $state !== 'locked');
                                        $current = $item->id === $lesson->id;
                                    @endphp
                                    <li>
                                        @if ($accessible)
                                            <a href="{{ route('learn.show', [$course, $item]) }}" class="flex items-start gap-3 px-4 py-3 text-sm hover:bg-slate-50 {{ $current ? 'bg-indigo-50 font-semibold text-indigo-800' : 'text-slate-700' }}">
                                        @else
                                            <div class="flex items-start gap-3 px-4 py-3 text-sm text-slate-400 {{ $current ? 'bg-indigo-50' : '' }}" title="{{ $state === 'locked' ? 'Terkunci' : '' }}">
                                        @endif
                                            <span class="mt-0.5 shrink-0 text-base leading-none">
                                                @if ($state === 'completed') <span class="text-emerald-600">✓</span>
                                                @elseif ($state === 'locked' && $mode !== 'owner') 🔒
                                                @else <span class="text-indigo-500">●</span> @endif
                                            </span>
                                            <span class="min-w-0">
                                                <span class="block">{{ $item->title }}</span>
                                                <span class="text-xs font-normal text-slate-400">{{ Format::duration($item->duration_seconds) }}@if ($item->assignment) · Tugas @endif</span>
                                            </span>
                                        @if ($accessible) </a> @else </div> @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
