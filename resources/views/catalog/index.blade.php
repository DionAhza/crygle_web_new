@use('App\Support\Format')
<x-public-layout title="Katalog Kelas">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-blue-800">Katalog Kelas</h1>
        <p class="mt-1 text-sm text-slate-600">Belajar bertahap dengan materi video, tugas, dan sertifikat.</p>
    </div>

    <form method="GET" class="mb-8 grid gap-3 rounded-2xl border border-blue-100 bg-white p-4 sm:grid-cols-[1fr_auto_auto_auto]">
        <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kelas…" class="rounded-lg border-slate-200 text-sm focus:border-blue-600 focus:ring-blue-600">
        <select name="level" class="rounded-lg border-slate-200 text-sm focus:border-blue-600 focus:ring-blue-600">
            <option value="">Semua level</option>
            @foreach (['basic' => 'Basic', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $v => $l)
                <option value="{{ $v }}" @selected(($filters['level'] ?? '') === $v)>{{ $l }}</option>
            @endforeach
        </select>
        <select name="price" class="rounded-lg border-slate-200 text-sm focus:border-blue-600 focus:ring-blue-600">
            <option value="">Semua harga</option>
            <option value="free" @selected(($filters['price'] ?? '') === 'free')>Gratis</option>
            <option value="paid" @selected(($filters['price'] ?? '') === 'paid')>Berbayar</option>
        </select>
        <button class="rounded-lg bg-blue-800 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-900">Cari</button>
    </form>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($courses as $course)
            <a href="{{ route('catalog.show', $course) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-blue-100 bg-white transition hover:shadow-md">
                @if ($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="" class="h-40 w-full object-cover">
                @else
                    <div class="h-40 bg-gradient-to-br from-blue-700 to-blue-400"></div>
                @endif
                <div class="flex flex-1 flex-col p-5">
                    <span class="w-fit rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-semibold capitalize text-blue-700">{{ $course->level }}</span>
                    <h2 class="mt-2 font-bold text-slate-800 group-hover:text-blue-800">{{ $course->title }}</h2>
                    <p class="mt-1 text-xs text-slate-500">oleh {{ $course->mentor->name }}</p>
                    <p class="mt-2 text-xs text-slate-500">{{ $course->modules_count }} bab · {{ $course->lessons_count }} materi · {{ Format::duration((int) $course->lessons_sum_duration_seconds) }}</p>
                    <p class="mt-auto pt-4 text-lg font-extrabold {{ $course->isFree() ? 'text-emerald-600' : 'text-blue-800' }}">{{ Format::rupiah($course->price) }}</p>
                </div>
            </a>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-blue-200 bg-white p-10 text-center text-sm text-slate-500">Tidak ada kelas yang cocok dengan pencarian Anda.</p>
        @endforelse
    </div>

    <div class="mt-8">{{ $courses->links() }}</div>
</x-public-layout>
