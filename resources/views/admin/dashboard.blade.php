<x-panel-layout title="Dashboard Admin">
    <x-flash />

    {{-- Banner sambutan --}}
    <section class="relative overflow-hidden rounded-[20px] bg-gradient-to-br from-brand to-[#1b4d82] px-6 py-8 text-white sm:px-8">
        <div class="pointer-events-none absolute -end-10 -top-10 h-48 w-48 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-16 end-24 h-40 w-40 rounded-full bg-white/5"></div>
        <div class="relative max-w-xl">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-200">Panel Administrator</p>
            <h1 class="mt-2 text-2xl font-bold sm:text-3xl">Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }} 👋</h1>
            <p class="mt-2 text-sm text-blue-100">Kelola akun mentor dan spesialisasi mentor Crygle Academy dari satu tempat.</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('admin.mentors.create') }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-brand shadow-sm transition hover:bg-blue-50">+ Mentor Baru</a>
                <a href="{{ route('admin.mentor-types.index') }}" class="rounded-full border border-white/30 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">Kelola Tipe</a>
            </div>
        </div>
    </section>

    {{-- Statistik --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat label="Total Mentor" :value="$stats['mentors']" tone="brand"
                icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                :href="route('admin.mentors.index')" />
        <x-stat label="Total Siswa" :value="$stats['students']" tone="green"
                icon="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 17.5M12 14l-6.16-3.422A12.083 12.083 0 003 17.5M12 14v7" />
        <x-stat label="Total Kelas" :value="$stats['courses']" tone="amber"
                icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                :href="route('catalog.index')" />
        <x-stat label="Tipe Mentor" :value="$stats['mentor_types']" tone="violet"
                icon="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                :href="route('admin.mentor-types.index')" />
    </div>

    {{-- Pintasan --}}
    <h2 class="mb-3 mt-8 text-sm font-bold text-slate-800">Pintasan</h2>
    <div class="grid gap-4 sm:grid-cols-2">
        <a href="{{ route('admin.mentors.index') }}" class="group flex items-start gap-4 rounded-[20px] border border-slate-100 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-border hover:shadow-md">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <div class="font-bold text-slate-800 group-hover:text-brand">Kelola Akun Mentor</div>
                <p class="mt-1 text-sm text-slate-500">Daftarkan mentor baru dan tetapkan tipe mentornya.</p>
            </div>
            <svg class="mt-1 h-5 w-5 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="{{ route('admin.mentor-types.index') }}" class="group flex items-start gap-4 rounded-[20px] border border-slate-100 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-border hover:shadow-md">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <div class="font-bold text-slate-800 group-hover:text-brand">Kelola Tipe Mentor</div>
                <p class="mt-1 text-sm text-slate-500">Tambah, ubah, atau hapus spesialisasi mentor (3D, UI/UX, WebDev, dll.).</p>
            </div>
            <svg class="mt-1 h-5 w-5 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</x-panel-layout>
