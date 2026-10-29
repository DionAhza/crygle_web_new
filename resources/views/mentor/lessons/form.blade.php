<x-panel-layout :title="$lesson->exists ? 'Edit Materi' : 'Materi Baru'"
                :heading="$lesson->exists ? 'Edit Materi' : 'Materi Baru'"
                :subtitle="'Bab: '.$module->title"
                :back="['label' => $module->course->title, 'href' => route('mentor.courses.show', $module->course)]"
                width="max-w-3xl">
    <x-flash />

    <form method="POST"
          action="{{ $lesson->exists ? route('mentor.lessons.update', $lesson) : route('mentor.lessons.store', $module) }}" class="space-y-6">
        @csrf
        @if ($lesson->exists) @method('PUT') @endif

        <x-card class="space-y-5">
            <x-section-title title="Video & Durasi" description="Judul materi dan sumber videonya."
                             icon="M15 10l4.55-2.28A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.9L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />

            <x-form-field label="Judul Materi" for="title">
                <x-text-input id="title" name="title" :value="old('title', $lesson->title)" required />
            </x-form-field>

            <x-form-field label="URL Video" for="video_url" hint="YouTube, Vimeo, atau HLS.">
                <x-text-input id="video_url" name="video_url" type="url" :value="old('video_url', $lesson->video_url)" placeholder="https://" />
            </x-form-field>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-form-field label="Durasi (detik)" for="duration_seconds">
                    <x-text-input id="duration_seconds" name="duration_seconds" type="number" min="0" :value="old('duration_seconds', $lesson->duration_seconds)" />
                </x-form-field>
                @if ($lesson->exists)
                    <x-form-field label="Urutan" for="sort_order">
                        <x-text-input id="sort_order" name="sort_order" type="number" min="1" :value="old('sort_order', $lesson->sort_order)" />
                    </x-form-field>
                @endif
            </div>
        </x-card>

        <x-card class="space-y-5">
            <x-section-title title="Materi Bacaan" description="Tulis dengan Markdown — blok kode otomatis diberi highlight."
                             icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />

            <x-form-field label="Konten (Markdown)" for="content_text">
                <x-textarea id="content_text" name="content_text" rows="12" class="font-mono">{{ old('content_text', $lesson->content_text) }}</x-textarea>
            </x-form-field>

            <x-toggle name="is_free_preview" label="Jadikan preview gratis" description="Calon siswa bisa melihat materi ini tanpa mendaftar."
                      :checked="(bool) old('is_free_preview', $lesson->is_free_preview)" />
        </x-card>

        <div class="flex items-center gap-3">
            <x-btn>Simpan</x-btn>
            <x-btn :href="route('mentor.courses.show', $module->course)" variant="secondary">Batal</x-btn>
        </div>
    </form>
</x-panel-layout>
