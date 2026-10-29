<x-panel-layout title="Dashboard Mentor">
    <x-flash />

    {{-- Banner sambutan --}}
    <section class="relative overflow-hidden rounded-[20px] bg-gradient-to-br from-brand to-[#1b4d82] px-6 py-8 text-white sm:px-8">
        <div class="pointer-events-none absolute -end-10 -top-10 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 end-24 h-40 w-40 rounded-full bg-white/5"></div>
        <div class="relative flex flex-wrap items-end justify-between gap-5">
            <div class="max-w-xl">
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-200">Panel Mentor</p>
                <h1 class="mt-2 text-2xl font-bold sm:text-3xl">Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }} 👋</h1>
                <p class="mt-2 text-sm text-blue-100">
                    @if ($stats['pending'] > 0)
                        Ada <span class="font-bold text-white">{{ $stats['pending'] }} tugas</span> menunggu review Anda.
                    @else
                        Semua tugas sudah ditinjau. Saatnya menyiapkan materi baru!
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if ($stats['pending'] > 0)
                    <a href="{{ route('mentor.submissions.index') }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-brand shadow-sm transition hover:bg-blue-50">Review Tugas</a>
                @endif
                <a href="{{ route('mentor.courses.create') }}" class="rounded-full border border-white/30 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">+ Kelas Baru</a>
            </div>
        </div>
    </section>

    {{-- Statistik --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat label="Total Kelas" :value="$stats['courses']" tone="brand" :href="route('mentor.courses.index')"
                icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        <x-stat label="Kelas Terbit" :value="$stats['published']" tone="green"
                icon="M5 13l4 4L19 7" />
        <x-stat label="Total Siswa" :value="$stats['students']" tone="violet"
                icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        <x-stat label="Menunggu Review" :value="$stats['pending']" tone="amber" :href="route('mentor.submissions.index')"
                icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
    </div>

    {{-- Kelas terbaru --}}
    <x-card padding="p-0" class="mt-8 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-bold text-slate-800">Kelas Terbaru</h2>
            <div class="flex items-center gap-2">
                <x-btn :href="route('mentor.courses.index')" variant="secondary" size="sm">Lihat Semua</x-btn>
                <x-btn :href="route('mentor.courses.create')" size="sm">+ Kelas Baru</x-btn>
            </div>
        </div>

        @forelse ($courses->take(5) as $course)
            <a href="{{ route('mentor.courses.show', $course) }}" class="flex items-center gap-4 border-b border-slate-50 px-6 py-4 transition last:border-0 hover:bg-slate-50/70">
                @if ($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="" class="h-14 w-20 shrink-0 rounded-xl object-cover">
                @else
                    <div class="h-14 w-20 shrink-0 rounded-xl bg-gradient-to-br from-brand to-slate-700"></div>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="truncate font-semibold text-slate-800">{{ $course->title }}</div>
                    <div class="text-xs text-slate-500">{{ $course->modules_count }} bab · {{ $course->lessons_count }} materi · {{ $course->enrollments_count }} siswa</div>
                </div>
                <x-badge :tone="$course->is_published ? 'green' : 'slate'">{{ $course->is_published ? 'Terbit' : 'Draft' }}</x-badge>
            </a>
        @empty
            <x-empty-state title="Belum ada kelas" description="Mulai dengan membuat kelas pertama Anda.">
                <x-btn :href="route('mentor.courses.create')" size="sm">+ Kelas Baru</x-btn>
            </x-empty-state>
        @endforelse
    </x-card>
</x-panel-layout>
