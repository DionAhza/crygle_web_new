<x-panel-layout :title="$assignment->exists ? 'Edit Tugas' : 'Tugas Baru'"
                :heading="$assignment->exists ? 'Edit Tugas' : 'Tugas Baru'"
                :subtitle="'Materi: '.$lesson->title"
                :back="['label' => $course->title, 'href' => route('mentor.courses.show', $course)]"
                width="max-w-3xl">
    <x-flash />

    <form method="POST"
          action="{{ $assignment->exists ? route('mentor.assignments.update', $assignment) : route('mentor.assignments.store', $lesson) }}" class="space-y-6">
        @csrf
        @if ($assignment->exists) @method('PUT') @endif

        <x-card class="space-y-5">
            <x-section-title title="Detail Tugas" description="Siswa baru dianggap selesai setelah tugas disetujui."
                             icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />

            <x-form-field label="Judul Tugas" for="title">
                <x-text-input id="title" name="title" :value="old('title', $assignment->title)" required />
            </x-form-field>

            <x-form-field label="Instruksi (Markdown)" for="instructions">
                <x-textarea id="instructions" name="instructions" rows="8" class="font-mono">{{ old('instructions', $assignment->instructions) }}</x-textarea>
            </x-form-field>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-form-field label="Jenis Pengumpulan" for="submission_type">
                    <x-select id="submission_type" name="submission_type">
                        @foreach (['file' => 'Unggah File', 'repo_link' => 'Tautan Repository', 'text' => 'Teks'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('submission_type', $assignment->submission_type) === $value)>{{ $label }}</option>
                        @endforeach
                    </x-select>
                </x-form-field>

                <x-form-field label="Nilai Kelulusan (0–100)" for="passing_score">
                    <x-text-input id="passing_score" name="passing_score" type="number" min="0" max="100" :value="old('passing_score', $assignment->passing_score)" required />
                </x-form-field>
            </div>
        </x-card>

        <div class="flex items-center gap-3">
            <x-btn>Simpan</x-btn>
            <x-btn :href="route('mentor.courses.show', $course)" variant="secondary">Batal</x-btn>
        </div>
    </form>

    @if ($assignment->exists)
        <x-card class="mt-8 !border-rose-100">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Hapus Tugas</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Tugas ini akan dihapus dari materi.</p>
                </div>
                <form method="POST" action="{{ route('mentor.assignments.destroy', $assignment) }}" onsubmit="return confirm('Hapus tugas ini?')">
                    @csrf @method('DELETE')
                    <x-btn variant="danger-soft">Hapus tugas</x-btn>
                </form>
            </div>
        </x-card>
    @endif
</x-panel-layout>
