<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $assignment->exists ? 'Edit Tugas' : 'Tugas Baru' }} <span class="text-sm font-normal text-slate-500">— {{ $lesson->title }}</span></h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash />
            <form method="POST"
                  action="{{ $assignment->exists ? route('mentor.assignments.update', $assignment) : route('mentor.assignments.store', $lesson) }}"
                  class="space-y-5 rounded-xl bg-white p-6 shadow-sm border border-slate-100">
                @csrf
                @if ($assignment->exists) @method('PUT') @endif

                <div>
                    <x-input-label for="title" value="Judul Tugas" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $assignment->title)" required />
                </div>
                <div>
                    <x-input-label for="instructions" value="Instruksi (Markdown)" />
                    <textarea id="instructions" name="instructions" rows="8" class="mt-1 block w-full font-mono text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('instructions', $assignment->instructions) }}</textarea>
                </div>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="submission_type" value="Jenis Pengumpulan" />
                        <select id="submission_type" name="submission_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @foreach (['file' => 'Unggah File', 'repo_link' => 'Tautan Repository', 'text' => 'Teks'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('submission_type', $assignment->submission_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="passing_score" value="Nilai Kelulusan (0–100)" />
                        <x-text-input id="passing_score" name="passing_score" type="number" min="0" max="100" class="mt-1 block w-full" :value="old('passing_score', $assignment->passing_score)" required />
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button class="!bg-indigo-600 hover:!bg-indigo-700">Simpan</x-primary-button>
                    <a href="{{ route('mentor.courses.show', $course) }}" class="text-sm text-slate-600 hover:underline">Batal</a>
                </div>
            </form>

            @if ($assignment->exists)
                <form method="POST" action="{{ route('mentor.assignments.destroy', $assignment) }}" class="mt-6 px-4 sm:px-0" onsubmit="return confirm('Hapus tugas ini?')">
                    @csrf @method('DELETE')
                    <button class="text-sm text-rose-600 hover:underline">Hapus tugas</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
