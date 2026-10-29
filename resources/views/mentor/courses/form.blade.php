<x-panel-layout :title="$course->exists ? 'Edit Kelas' : 'Kelas Baru'"
                :heading="$course->exists ? 'Edit Kelas' : 'Kelas Baru'"
                :subtitle="$course->exists ? 'Perbarui informasi kelas Anda.' : 'Isi informasi dasar kelas. Bab dan materi ditambahkan setelah kelas dibuat.'"
                :back="['label' => $course->exists ? $course->title : 'Kelas Saya', 'href' => $course->exists ? route('mentor.courses.show', $course) : route('mentor.courses.index')]"
                width="max-w-3xl">
    <x-flash />

    <form method="POST" enctype="multipart/form-data"
          action="{{ $course->exists ? route('mentor.courses.update', $course) : route('mentor.courses.store') }}" class="space-y-6">
        @csrf
        @if ($course->exists) @method('PUT') @endif

        <x-card class="space-y-5">
            <x-section-title title="Informasi Kelas" description="Judul dan deskripsi yang tampil di katalog."
                             icon="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />

            <x-form-field label="Judul Kelas" for="title">
                <x-text-input id="title" name="title" :value="old('title', $course->title)" required />
            </x-form-field>

            <x-form-field label="Deskripsi" for="description">
                <x-textarea id="description" name="description" rows="5">{{ old('description', $course->description) }}</x-textarea>
            </x-form-field>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-form-field label="Harga (Rp)" for="price" hint="Isi 0 untuk kelas gratis.">
                    <x-text-input id="price" name="price" type="number" min="0" step="1000" :value="old('price', (int) $course->price)" required />
                </x-form-field>

                <x-form-field label="Level" for="level">
                    <x-select id="level" name="level">
                        @foreach (['basic' => 'Basic', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('level', $course->level) === $value)>{{ $label }}</option>
                        @endforeach
                    </x-select>
                </x-form-field>
            </div>
        </x-card>

        <x-card class="space-y-5">
            <x-section-title title="Tampilan & Publikasi" description="Thumbnail dan status kelas di katalog."
                             icon="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />

            <x-form-field label="Thumbnail" for="thumbnail" hint="Gambar maksimal 2 MB.">
                @if ($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="" class="mb-3 h-28 rounded-xl object-cover">
                @endif
                <input id="thumbnail" name="thumbnail" type="file" accept="image/*"
                       class="block w-full text-sm text-slate-600 file:me-4 file:cursor-pointer file:rounded-full file:border-0 file:bg-brand-50 file:px-5 file:py-2.5 file:text-sm file:font-semibold file:text-brand hover:file:bg-blue-100">
            </x-form-field>

            <x-toggle name="is_published" label="Terbitkan kelas" description="Kelas yang terbit akan tampil di katalog publik."
                      :checked="(bool) old('is_published', $course->is_published)" />
        </x-card>

        <div class="flex items-center gap-3">
            <x-btn>Simpan</x-btn>
            <x-btn :href="$course->exists ? route('mentor.courses.show', $course) : route('mentor.courses.index')" variant="secondary">Batal</x-btn>
        </div>
    </form>

    @if ($course->exists)
        <x-card class="mt-8 !border-rose-100">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Hapus Kelas</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Kelas beserta seluruh bab dan materinya akan dihapus.</p>
                </div>
                <form method="POST" action="{{ route('mentor.courses.destroy', $course) }}" onsubmit="return confirm('Hapus kelas ini beserta seluruh bab dan materinya?')">
                    @csrf @method('DELETE')
                    <x-btn variant="danger-soft">Hapus kelas</x-btn>
                </form>
            </div>
        </x-card>
    @endif
</x-panel-layout>
