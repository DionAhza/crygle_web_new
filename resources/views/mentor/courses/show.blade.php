<x-panel-layout :title="$course->title" :heading="$course->title"
                :back="['label' => 'Kelas Saya', 'href' => route('mentor.courses.index')]" width="max-w-4xl">
    <x-slot name="actions">
        <x-badge :tone="$course->is_published ? 'green' : 'slate'" class="!px-3 !py-1">{{ $course->is_published ? 'Terbit' : 'Draft' }}</x-badge>
        <x-badge tone="brand" class="!px-3 !py-1">{{ $course->enrollments_count }} siswa terdaftar</x-badge>
        <x-btn :href="route('mentor.courses.edit', $course)" variant="secondary" size="sm">Edit Kelas</x-btn>
    </x-slot>

    <x-flash />

    <div class="space-y-5">
        @foreach ($course->modules as $module)
            <section class="overflow-hidden rounded-[20px] border border-slate-100 bg-white shadow-sm">
                {{-- Kepala bab --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-[#f8fbff] px-5 py-4">
                    <form method="POST" action="{{ route('mentor.modules.update', $module) }}" class="flex min-w-[260px] flex-1 items-center gap-2">
                        @csrf @method('PATCH')
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand text-xs font-bold text-white">{{ $module->sort_order }}</span>
                        <x-text-input name="title" :value="$module->title" class="!h-10 flex-1" aria-label="Judul bab" />
                        <x-text-input name="sort_order" type="number" min="1" :value="$module->sort_order" class="!h-10 !w-16 text-center" title="Urutan" aria-label="Urutan bab" />
                        <x-btn variant="soft" size="sm">Simpan</x-btn>
                    </form>
                    <form method="POST" action="{{ route('mentor.modules.destroy', $module) }}" onsubmit="return confirm('Hapus bab beserta seluruh materinya?')">
                        @csrf @method('DELETE')
                        <x-btn variant="danger-soft" size="sm">Hapus bab</x-btn>
                    </form>
                </div>

                {{-- Daftar materi --}}
                <ul class="divide-y divide-slate-50">
                    @forelse ($module->lessons as $lesson)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[11px] font-bold text-slate-500">{{ $lesson->sort_order }}</span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-800">
                                        {{ $lesson->title }}
                                        @if ($lesson->is_free_preview)<x-badge tone="amber">Preview gratis</x-badge>@endif
                                    </div>
                                    <div class="mt-0.5 text-xs text-slate-500">
                                        {{ intdiv($lesson->duration_seconds, 60) }} menit
                                        @if ($lesson->assignment) · Tugas: {{ $lesson->assignment->title }} (lulus ≥ {{ $lesson->assignment->passing_score }}) @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <x-btn :href="route('learn.show', [$course, $lesson])" variant="secondary" size="sm">Pratinjau</x-btn>
                                <x-btn :href="route('mentor.lessons.edit', $lesson)" variant="soft" size="sm">Edit</x-btn>
                                @if ($lesson->assignment)
                                    <x-btn :href="route('mentor.assignments.edit', $lesson->assignment)" variant="soft" size="sm">Edit tugas</x-btn>
                                @else
                                    <x-btn :href="route('mentor.assignments.create', $lesson)" variant="soft" size="sm">+ Tugas</x-btn>
                                @endif
                                <form method="POST" action="{{ route('mentor.lessons.destroy', $lesson) }}" onsubmit="return confirm('Hapus materi ini?')">
                                    @csrf @method('DELETE')
                                    <x-btn variant="danger-soft" size="sm">Hapus</x-btn>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="px-5 py-5 text-center text-sm text-slate-500">Belum ada materi di bab ini.</li>
                    @endforelse
                </ul>

                <div class="border-t border-slate-100 px-5 py-3">
                    <a href="{{ route('mentor.lessons.create', $module) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand hover:underline">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                        Tambah Materi
                    </a>
                </div>
            </section>
        @endforeach

        {{-- Tambah bab --}}
        <form method="POST" action="{{ route('mentor.modules.store', $course) }}" class="flex flex-col gap-3 rounded-[20px] border-2 border-dashed border-brand-border bg-white/60 p-5 sm:flex-row sm:items-center">
            @csrf
            <x-text-input name="title" placeholder="Judul bab baru" required class="flex-1" aria-label="Judul bab baru" />
            <x-btn>+ Tambah Bab</x-btn>
        </form>
    </div>
</x-panel-layout>
