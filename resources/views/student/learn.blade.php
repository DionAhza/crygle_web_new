@use('App\Support\Format')
@php
    $user = auth()->user();
    $mentor = $course->mentor;
    $totalLessons = collect($states)->count();
    $doneLessons = collect($states)->filter(fn ($s) => $s === 'completed')->count();
    $percent = $mode === 'student' ? $enrollment->progress_percent : 0;
    $currentModuleId = $lesson->module_id;
    $backUrl = $mode === 'owner' ? route('mentor.courses.show', $course)
        : ($mode === 'student' ? route('student.courses') : route('catalog.show', $course));
    $reviewed = $submissions->filter(fn ($s) => ! $s->isPending());
    $initialTab = request('tab') ?: (($lesson->assignment && (session('status') || $errors->any())) ? 'tugas' : 'materi');
    $nextAccessible = $next && ($states[$next->id] ?? 'locked') !== 'locked';
    $initial = fn ($name) => \Illuminate\Support\Str::of($name)->substr(0, 1)->upper();
@endphp
<x-learn-layout :title="$lesson->title">
    <div class="mx-auto grid max-w-[1400px] gap-5 p-4 lg:h-screen lg:grid-cols-[340px_minmax(0,1fr)] lg:gap-6 lg:p-5">

        {{-- ================= Panel kiri ================= --}}
        <aside class="order-2 flex min-h-0 flex-col gap-4 lg:order-1">
            <div class="flex items-center gap-5 px-1">
                <a href="{{ $backUrl }}" aria-label="Kembali"><img src="{{ asset('images/logo.svg') }}" alt="Crygle Academy" class="h-9 w-auto"></a>
                <a href="{{ $backUrl }}" class="flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-brand">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    {{ $mode === 'owner' ? 'Kembali ke Kelas' : 'Kembali ke Dashboard' }}
                </a>
            </div>

            {{-- Judul kelas, progres, dan daftar modul --}}
            <div class="flex min-h-0 flex-1 flex-col rounded-3xl bg-white p-5 shadow-sm">
                <h2 class="text-[15px] font-bold leading-snug text-slate-900">{{ $course->title }}</h2>

                <div class="mt-3">
                    <div class="h-1.5 rounded-full bg-slate-100"><div class="h-1.5 rounded-full bg-emerald-500 transition-all" style="width: {{ $percent }}%"></div></div>
                    <div class="mt-1.5 flex justify-between text-[11px] font-semibold text-slate-800">
                        <span>{{ $doneLessons }}/{{ $totalLessons }} Materi</span><span>{{ $percent }}%</span>
                    </div>
                </div>

                <div class="mt-4 min-h-0 flex-1 space-y-3 overflow-y-auto pe-1" x-data="{ open: [{{ $currentModuleId }}] }">
                    @foreach ($modules as $module)
                        <section class="rounded-2xl bg-slate-50">
                            <button type="button" @click="open.includes({{ $module->id }}) ? open = open.filter(i => i !== {{ $module->id }}) : open.push({{ $module->id }})"
                                    class="flex w-full items-center justify-between gap-3 px-4 py-4 text-start text-[11px] font-semibold uppercase tracking-wide text-slate-800"
                                    :aria-expanded="open.includes({{ $module->id }})">
                                <span>Modul {{ $module->sort_order }}: {{ $module->title }}</span>
                                <svg class="h-4 w-4 shrink-0 transition-transform" :class="open.includes({{ $module->id }}) ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <ul x-show="open.includes({{ $module->id }})" @if (! ($module->id === $currentModuleId)) x-cloak @endif class="space-y-0.5 px-2 pb-2">
                                @foreach ($lessons[$module->id] as $item)
                                    @php
                                        $state = $states[$item->id] ?? 'locked';
                                        $accessible = $mode === 'owner' || ($mode === 'student' && $state !== 'locked');
                                        $current = $item->id === $lesson->id;
                                        $rowClass = $current ? 'bg-brand-50 font-semibold text-brand' : ($accessible ? 'text-slate-700 hover:bg-white' : 'text-slate-400');
                                    @endphp
                                    <li>
                                        @if ($accessible)
                                            <a href="{{ route('learn.show', [$course, $item]) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs {{ $rowClass }}" @if ($current) aria-current="page" @endif>
                                        @else
                                            <div class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs {{ $rowClass }}" title="{{ $state === 'locked' ? 'Terkunci — selesaikan materi sebelumnya' : '' }}">
                                        @endif
                                            <span class="flex h-4 w-4 shrink-0 items-center justify-center">
                                                @if ($state === 'completed')
                                                    <svg class="h-4 w-4 text-emerald-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3a1 1 0 00-1.4-1.4L9 10.6 7.7 9.3a1 1 0 00-1.4 1.4l2 2a1 1 0 001.4 0l4-4z" clip-rule="evenodd"/></svg>
                                                @elseif ($state === 'locked' && $mode !== 'owner')
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 118 0v3"/></svg>
                                                @else
                                                    <svg class="h-4 w-4 {{ $current ? 'text-brand' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path fill="currentColor" stroke="none" d="M10 8.5v7l6-3.5-6-3.5z"/></svg>
                                                @endif
                                            </span>
                                            <span class="min-w-0 flex-1 truncate">{{ $item->title }}@if ($item->assignment) <span class="ms-1 text-[10px] font-normal text-slate-400">· Tugas</span>@endif</span>
                                            <span class="shrink-0 text-[11px] font-normal {{ $current ? 'text-brand' : 'text-slate-400' }}">{{ Format::clock($item->duration_seconds) }}</span>
                                        @if ($accessible) </a> @else </div> @endif
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>
            </div>

            {{-- Kartu mentor --}}
            <div class="rounded-3xl bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-800 text-lg font-semibold text-white">
                        @if ($mentor->avatar_url)<img src="{{ $mentor->avatar_url }}" alt="" class="h-full w-full object-cover">@else{{ $initial($mentor->name) }}@endif
                    </span>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold text-slate-900">{{ $mentor->name }}</div>
                        <div class="truncate text-[11px] text-slate-500">Mentor{{ $mentor->mentorType ? ' · '.$mentor->mentorType->name : '' }}</div>
                    </div>
                </div>
                <button type="button" disabled title="Fitur chat mentor segera hadir"
                        class="mt-4 flex h-11 w-full cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-slate-900 text-xs font-semibold text-white opacity-90">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M8 10h8M8 14h5m-9 6l3-3h11a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v14z"/></svg>
                    Chat Mentor Terkait
                </button>
            </div>
        </aside>

        {{-- ================= Konten utama ================= --}}
        <main class="order-1 min-w-0 lg:order-2 lg:overflow-y-auto lg:pe-1">
            <div class="flex items-center justify-between gap-4 pb-4">
                <h1 class="text-lg font-bold text-slate-900 sm:text-xl">Semangat dan mulai Belajar! 💪</h1>
                <div class="flex items-center gap-3">
                    <button type="button" class="relative flex h-10 w-10 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm" aria-label="Notifikasi">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0"/></svg>
                    </button>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-3 focus:outline-none" aria-label="Menu akun">
                                <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-slate-800 text-sm font-semibold text-white">
                                    @if ($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="" class="h-full w-full object-cover">@else{{ $initial($user->name) }}@endif
                                </span>
                                <span class="hidden text-xs font-bold text-slate-900 sm:block">{{ \Illuminate\Support\Str::limit($user->name, 22) }}</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <x-flash />

            @if ($mode === 'student' && $enrollment->certificate_code)
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <div>
                        <div class="font-semibold text-emerald-800">🎓 Selamat, Anda telah menyelesaikan kelas ini!</div>
                        <div class="text-xs text-emerald-700">Sertifikat Anda sudah siap diunduh.</div>
                    </div>
                    <a href="{{ route('certificates.download', $enrollment) }}" class="rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Unduh Sertifikat (PDF)</a>
                </div>
            @endif

            {{-- Video --}}
            @if ($video && $video['provider'] === 'file')
                <div class="overflow-hidden rounded-3xl bg-black shadow-sm" style="--plyr-color-main: #235f9c">
                    <video id="lesson-player" playsinline controls><source src="{{ $video['url'] }}"></video>
                </div>
            @elseif ($video)
                <div class="overflow-hidden rounded-3xl bg-black shadow-sm" style="--plyr-color-main: #235f9c">
                    <div id="lesson-player" class="plyr__video-embed" data-plyr-provider="{{ $video['provider'] }}" data-plyr-embed-id="{{ $video['id'] }}"></div>
                </div>
            @elseif ($lesson->video_url)
                <p class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">Format URL video tidak dikenali. <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener" class="font-semibold underline">Buka video di tab baru</a>.</p>
            @endif

            {{-- Kartu materi: judul, tombol navigasi, tab --}}
            <section class="mt-4 rounded-3xl bg-white p-6 shadow-sm sm:p-8" x-data="{ tab: @js($initialTab) }">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h2 class="text-2xl font-bold text-slate-900 sm:text-[28px]">{{ $lesson->title }}</h2>
                        <p class="mt-1 text-xs text-slate-500">Materi {{ $position['current'] }} dari {{ $position['total'] }} · {{ $lesson->module->title }} · {{ Format::duration($lesson->duration_seconds) }}</p>
                        @if ($mode !== 'student')
                            <span class="mt-2 inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">{{ $mode === 'owner' ? 'Pratinjau mentor' : 'Preview gratis' }}</span>
                        @endif
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        @if ($prev && ($states[$prev->id] ?? '') !== 'locked')
                            <a href="{{ route('learn.show', [$course, $prev]) }}" title="Materi sebelumnya" aria-label="Materi sebelumnya"
                               class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            </a>
                        @endif

                        @php $nextBtn = 'flex h-11 items-center gap-3 rounded-full bg-brand px-6 text-sm font-semibold text-white transition hover:bg-brand-dark'; $arrow = '<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>'; @endphp
                        @if ($canComplete)
                            <form method="POST" action="{{ route('learn.complete', [$course, $lesson]) }}">
                                @csrf
                                <button class="{{ $nextBtn }}">Selesai &amp; Lanjut {!! $arrow !!}</button>
                            </form>
                        @elseif ($mode === 'student' && $lesson->assignment && ! $isCompleted)
                            <button type="button" @click="tab = 'tugas'" class="{{ $nextBtn }} !bg-slate-300 !text-slate-700 hover:!bg-slate-400" title="Materi berikutnya terbuka setelah tugas disetujui mentor">Selesaikan Tugas</button>
                        @elseif ($next && $nextAccessible)
                            <a href="{{ route('learn.show', [$course, $next]) }}" class="{{ $nextBtn }}">Next Modul {!! $arrow !!}</a>
                        @elseif ($mode === 'preview')
                            <a href="{{ route('catalog.show', $course) }}" class="{{ $nextBtn }}">Daftar untuk Akses Penuh</a>
                        @endif
                    </div>
                </div>

                {{-- Tab --}}
                <div class="mt-6 flex gap-6 overflow-x-auto border-b border-slate-200 text-sm font-semibold" role="tablist">
                    @php $tabs = ['materi' => 'Materi', 'ringkasan' => 'Ringkasan']; if ($lesson->assignment) { $tabs = ['materi' => 'Materi', 'tugas' => 'Tugas', 'ringkasan' => 'Ringkasan', 'review' => 'Review']; } @endphp
                    @foreach ($tabs as $key => $label)
                        <button type="button" role="tab" @click="tab = '{{ $key }}'" :aria-selected="tab === '{{ $key }}'"
                                :class="tab === '{{ $key }}' ? 'border-brand text-brand' : 'border-transparent text-slate-500 hover:text-slate-700'"
                                class="-mb-px whitespace-nowrap border-b-2 pb-3">{{ $label }}@if ($key === 'review' && $reviewed->isNotEmpty()) <span class="ms-1 rounded-full bg-brand-50 px-1.5 text-[10px] text-brand">{{ $reviewed->count() }}</span>@endif</button>
                    @endforeach
                </div>

                {{-- Materi --}}
                <div x-show="tab === 'materi'" class="pt-6">
                    @if ($contentHtml)
                        <article class="lesson-prose prose prose-slate max-w-none">{!! $contentHtml !!}</article>
                    @else
                        <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">Belum ada materi bacaan untuk pelajaran ini{{ $video ? ' — tonton videonya di atas.' : '.' }}</p>
                    @endif
                </div>

                {{-- Tugas --}}
                @if ($lesson->assignment)
                    <div x-show="tab === 'tugas'" x-cloak class="pt-6">
                        <section class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-5">
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
                    </div>
                @endif

                {{-- Ringkasan --}}
                <div x-show="tab === 'ringkasan'" x-cloak class="space-y-5 pt-6 text-sm">
                    <dl class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs text-slate-500">Durasi materi</dt><dd class="mt-1 font-semibold">{{ Format::duration($lesson->duration_seconds) }}</dd></div>
                        <div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs text-slate-500">Posisi di kelas</dt><dd class="mt-1 font-semibold">{{ $position['current'] }} / {{ $position['total'] }}</dd></div>
                        <div class="rounded-2xl bg-slate-50 p-4"><dt class="text-xs text-slate-500">Tugas</dt><dd class="mt-1 font-semibold">{{ $lesson->assignment ? 'Lulus ≥ '.$lesson->assignment->passing_score : 'Tidak ada' }}</dd></div>
                    </dl>
                    @if ($course->description)
                        <div>
                            <h3 class="mb-1 font-semibold text-slate-900">Tentang kelas</h3>
                            <p class="whitespace-pre-line leading-relaxed text-slate-600">{{ $course->description }}</p>
                        </div>
                    @endif
                    @if ($mode === 'student' && $enrollment->isCompleted())
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                            🎓 Kelas selesai pada {{ $enrollment->completed_at->translatedFormat('d F Y') }}. Kode sertifikat: <span class="font-mono font-semibold">{{ $enrollment->certificate_code }}</span>
                        </div>
                    @endif
                </div>

                {{-- Review (masukan mentor) --}}
                @if ($lesson->assignment)
                    <div x-show="tab === 'review'" x-cloak class="space-y-3 pt-6 text-sm">
                        @forelse ($reviewed as $item)
                            <div class="rounded-2xl border-l-4 {{ $item->isApproved() ? 'border-emerald-400 bg-emerald-50' : 'border-rose-400 bg-rose-50' }} p-4">
                                <div class="font-semibold text-slate-900">
                                    Skor {{ $item->score }}/100 · {{ $item->isApproved() ? 'Disetujui' : 'Perlu revisi' }}
                                    <span class="ms-1 text-xs font-normal text-slate-500">oleh {{ $item->reviewer?->name ?? 'Mentor' }} · {{ $item->reviewed_at?->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                <p class="mt-1 whitespace-pre-line text-slate-700">{{ $item->mentor_feedback ?: 'Tidak ada catatan tambahan.' }}</p>
                            </div>
                        @empty
                            <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-slate-500">Belum ada review dari mentor{{ $mode === 'student' ? '. Kirim tugas pada tab Tugas untuk mendapat masukan.' : '.' }}</p>
                        @endforelse
                    </div>
                @endif
            </section>
        </main>
    </div>
</x-learn-layout>
