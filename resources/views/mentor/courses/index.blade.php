<x-panel-layout title="Kelas Saya" heading="Kelas Saya" subtitle="Kelola kelas, bab, materi, dan tugas Anda.">
    <x-slot name="actions">
        <x-btn :href="route('mentor.courses.create')">+ Kelas Baru</x-btn>
    </x-slot>

    <x-flash />

    @if ($courses->isEmpty())
        <x-card>
            <x-empty-state title="Belum ada kelas" description="Buat kelas pertama Anda dan mulai tambahkan bab serta materi.">
                <x-btn :href="route('mentor.courses.create')" size="sm">+ Kelas Baru</x-btn>
            </x-empty-state>
        </x-card>
    @else
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($courses as $course)
                <article class="flex flex-col rounded-[20px] border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <a href="{{ route('mentor.courses.show', $course) }}" class="block">
                        @if ($course->thumbnail_url)
                            <img src="{{ $course->thumbnail_url }}" alt="" class="h-[110px] w-full rounded-xl object-cover">
                        @else
                            <div class="h-[110px] w-full rounded-xl bg-gradient-to-br from-brand to-slate-700"></div>
                        @endif
                    </a>

                    <div class="mt-4 flex items-center gap-2">
                        <x-badge tone="brand" class="capitalize">{{ $course->level }}</x-badge>
                        <x-badge :tone="$course->is_published ? 'green' : 'slate'">{{ $course->is_published ? 'Terbit' : 'Draft' }}</x-badge>
                    </div>

                    <h3 class="mt-3 line-clamp-2 text-base font-bold leading-snug text-slate-800">{{ $course->title }}</h3>
                    <p class="mt-2 text-xs text-slate-500">{{ $course->modules_count }} bab · {{ $course->lessons_count }} materi · {{ $course->enrollments_count }} siswa</p>
                    <p class="mt-1 text-sm font-bold text-brand">{{ $course->isFree() ? 'Gratis' : 'Rp '.number_format($course->price, 0, ',', '.') }}</p>

                    <div class="mt-auto flex gap-2 pt-5">
                        <x-btn :href="route('mentor.courses.show', $course)" size="sm" class="flex-1">Kelola</x-btn>
                        <x-btn :href="route('mentor.courses.edit', $course)" variant="secondary" size="sm" class="flex-1">Edit</x-btn>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <div class="mt-6">{{ $courses->links() }}</div>
</x-panel-layout>
