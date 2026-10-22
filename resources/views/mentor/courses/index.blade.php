<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Kelas Saya</h2>
            <a href="{{ route('mentor.courses.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">+ Kelas Baru</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-flash />
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 px-4 sm:px-0">
                @forelse ($courses as $course)
                    <div class="rounded-xl bg-white shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                        @if ($course->thumbnail_url)
                            <img src="{{ $course->thumbnail_url }}" alt="" class="h-36 w-full object-cover">
                        @else
                            <div class="h-36 bg-gradient-to-br from-indigo-500 to-slate-700"></div>
                        @endif
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-indigo-700 capitalize">{{ $course->level }}</span>
                                <span class="rounded-full px-2 py-0.5 {{ $course->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $course->is_published ? 'Terbit' : 'Draft' }}</span>
                            </div>
                            <h3 class="mt-2 font-semibold text-slate-800">{{ $course->title }}</h3>
                            <p class="mt-1 text-xs text-slate-500">{{ $course->modules_count }} bab · {{ $course->lessons_count }} materi · {{ $course->enrollments_count }} siswa</p>
                            <p class="mt-1 text-sm font-medium text-slate-700">{{ $course->isFree() ? 'Gratis' : 'Rp '.number_format($course->price, 0, ',', '.') }}</p>
                            <div class="mt-4 flex gap-2 pt-2">
                                <a href="{{ route('mentor.courses.show', $course) }}" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700">Kelola</a>
                                <a href="{{ route('mentor.courses.edit', $course) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada kelas.</p>
                @endforelse
            </div>
            <div class="mt-6 px-4 sm:px-0">{{ $courses->links() }}</div>
        </div>
    </div>
</x-app-layout>
