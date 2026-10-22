<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $lesson->exists ? 'Edit Materi' : 'Materi Baru' }} <span class="text-sm font-normal text-slate-500">— {{ $module->title }}</span></h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash />
            <form method="POST"
                  action="{{ $lesson->exists ? route('mentor.lessons.update', $lesson) : route('mentor.lessons.store', $module) }}"
                  class="space-y-5 rounded-xl bg-white p-6 shadow-sm border border-slate-100">
                @csrf
                @if ($lesson->exists) @method('PUT') @endif

                <div>
                    <x-input-label for="title" value="Judul Materi" />
                    <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $lesson->title)" required />
                </div>
                <div>
                    <x-input-label for="video_url" value="URL Video (YouTube/Vimeo/HLS)" />
                    <x-text-input id="video_url" name="video_url" type="url" class="mt-1 block w-full" :value="old('video_url', $lesson->video_url)" />
                </div>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="duration_seconds" value="Durasi (detik)" />
                        <x-text-input id="duration_seconds" name="duration_seconds" type="number" min="0" class="mt-1 block w-full" :value="old('duration_seconds', $lesson->duration_seconds)" />
                    </div>
                    @if ($lesson->exists)
                        <div>
                            <x-input-label for="sort_order" value="Urutan" />
                            <x-text-input id="sort_order" name="sort_order" type="number" min="1" class="mt-1 block w-full" :value="old('sort_order', $lesson->sort_order)" />
                        </div>
                    @endif
                </div>
                <div>
                    <x-input-label for="content_text" value="Materi Bacaan (Markdown)" />
                    <textarea id="content_text" name="content_text" rows="12" class="mt-1 block w-full font-mono text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('content_text', $lesson->content_text) }}</textarea>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_free_preview" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_free_preview', $lesson->is_free_preview))>
                    Jadikan preview gratis
                </label>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button class="!bg-indigo-600 hover:!bg-indigo-700">Simpan</x-primary-button>
                    <a href="{{ route('mentor.courses.show', $module->course) }}" class="text-sm text-slate-600 hover:underline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
