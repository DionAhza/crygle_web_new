<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $course->exists ? 'Edit Kelas' : 'Kelas Baru' }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash />
            <form method="POST" enctype="multipart/form-data"
                  action="{{ $course->exists ? route('mentor.courses.update', $course) : route('mentor.courses.store') }}"
                  class="space-y-5 rounded-xl bg-white p-6 shadow-sm border border-slate-100">
                @csrf
                @if ($course->exists) @method('PUT') @endif

                <div>
                    <x-input-label for="title" value="Judul Kelas" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $course->title)" required />
                </div>
                <div>
                    <x-input-label for="description" value="Deskripsi" />
                    <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $course->description) }}</textarea>
                </div>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="price" value="Harga (Rp, isi 0 untuk gratis)" />
                        <x-text-input id="price" name="price" type="number" min="0" step="1000" class="mt-1 block w-full" :value="old('price', (int) $course->price)" required />
                    </div>
                    <div>
                        <x-input-label for="level" value="Level" />
                        <select id="level" name="level" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @foreach (['basic' => 'Basic', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('level', $course->level) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <x-input-label for="thumbnail" value="Thumbnail (maks. 2 MB)" />
                    @if ($course->thumbnail_url)
                        <img src="{{ $course->thumbnail_url }}" alt="" class="mt-2 h-24 rounded-lg object-cover">
                    @endif
                    <input id="thumbnail" name="thumbnail" type="file" accept="image/*" class="mt-2 block w-full text-sm text-slate-600">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_published" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_published', $course->is_published))>
                    Terbitkan kelas (tampil di katalog)
                </label>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button class="!bg-indigo-600 hover:!bg-indigo-700">Simpan</x-primary-button>
                    <a href="{{ $course->exists ? route('mentor.courses.show', $course) : route('mentor.courses.index') }}" class="text-sm text-slate-600 hover:underline">Batal</a>
                </div>
            </form>

            @if ($course->exists)
                <form method="POST" action="{{ route('mentor.courses.destroy', $course) }}" class="mt-6 px-4 sm:px-0" onsubmit="return confirm('Hapus kelas ini beserta seluruh bab dan materinya?')">
                    @csrf @method('DELETE')
                    <button class="text-sm text-rose-600 hover:underline">Hapus kelas</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
