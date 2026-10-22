<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $course->title }}</h2>
                <p class="text-xs text-slate-500">{{ $course->is_published ? 'Terbit' : 'Draft' }} · {{ $course->enrollments_count }} siswa terdaftar</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('mentor.courses.edit', $course) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Edit Kelas</a>
                <a href="{{ route('mentor.courses.index') }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 hover:bg-slate-50">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            @foreach ($course->modules as $module)
                <section class="rounded-xl bg-white shadow-sm border border-slate-100">
                    <div class="flex flex-wrap items-center justify-between gap-3 p-4 border-b border-slate-100">
                        <form method="POST" action="{{ route('mentor.modules.update', $module) }}" class="flex flex-1 items-center gap-2">
                            @csrf @method('PATCH')
                            <span class="text-xs font-semibold text-slate-400">#{{ $module->sort_order }}</span>
                            <input name="title" value="{{ $module->title }}" class="flex-1 border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <input name="sort_order" type="number" min="1" value="{{ $module->sort_order }}" class="w-16 border-gray-300 rounded-md text-sm" title="Urutan">
                            <button class="text-xs font-semibold text-indigo-600 hover:underline">Simpan</button>
                        </form>
                        <form method="POST" action="{{ route('mentor.modules.destroy', $module) }}" onsubmit="return confirm('Hapus bab beserta seluruh materinya?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-rose-600 hover:underline">Hapus bab</button>
                        </form>
                    </div>

                    <ul class="divide-y divide-slate-50">
                        @forelse ($module->lessons as $lesson)
                            <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3">
                                <div>
                                    <div class="text-sm font-medium text-slate-800">
                                        {{ $lesson->sort_order }}. {{ $lesson->title }}
                                        @if ($lesson->is_free_preview)<span class="ms-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] text-amber-700">Preview gratis</span>@endif
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ intdiv($lesson->duration_seconds, 60) }} menit
                                        @if ($lesson->assignment) · Tugas: {{ $lesson->assignment->title }} (lulus ≥ {{ $lesson->assignment->passing_score }}) @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 text-xs">
                                    <a href="{{ route('learn.show', [$course, $lesson]) }}" class="text-slate-500 hover:underline">Pratinjau</a>
                                    <a href="{{ route('mentor.lessons.edit', $lesson) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    @if ($lesson->assignment)
                                        <a href="{{ route('mentor.assignments.edit', $lesson->assignment) }}" class="text-indigo-600 hover:underline">Edit tugas</a>
                                    @else
                                        <a href="{{ route('mentor.assignments.create', $lesson) }}" class="text-indigo-600 hover:underline">+ Tugas</a>
                                    @endif
                                    <form method="POST" action="{{ route('mentor.lessons.destroy', $lesson) }}" onsubmit="return confirm('Hapus materi ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="px-4 py-3 text-sm text-slate-500">Belum ada materi di bab ini.</li>
                        @endforelse
                    </ul>
                    <div class="p-3 border-t border-slate-100">
                        <a href="{{ route('mentor.lessons.create', $module) }}" class="text-sm font-semibold text-indigo-600 hover:underline">+ Tambah Materi</a>
                    </div>
                </section>
            @endforeach

            <form method="POST" action="{{ route('mentor.modules.store', $course) }}" class="flex gap-2 rounded-xl border border-dashed border-slate-300 bg-white p-4">
                @csrf
                <input name="title" placeholder="Judul bab baru" required class="flex-1 border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">+ Tambah Bab</button>
            </form>
        </div>
    </div>
</x-app-layout>
